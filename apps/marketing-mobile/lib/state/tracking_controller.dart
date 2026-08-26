import 'dart:async';
import 'dart:convert';

import 'package:flutter/widgets.dart';
import 'package:geolocator/geolocator.dart';

import '../api/api_client.dart';
import '../data/local/sync_queue.dart';
import '../services/location_service.dart';
import '../services/tracking_service.dart';

/// Owns the field marketer's tracking-session lifecycle for the whole app
/// run, per the product decision recorded in BACKEND_INTEGRATION_TASKS.md
/// (2026-08-21): one continuous session per work day, started automatically
/// on login/app-open and stopped on logout — not a per-visit session, and
/// not silently tracked in the background (GPS sampling only runs while the
/// app is actually in the foreground, honoring the standing "do NOT
/// implement background/silent tracking" rule; the *session* spans the
/// whole day, but *points* are only ever sampled while the app is open).
///
/// The GPS sample interval below is a placeholder, not a business-confirmed
/// number (see BACKEND_INTEGRATION_TASKS.md's "Do NOT implement" list).
class TrackingController extends ChangeNotifier with WidgetsBindingObserver {
  TrackingController({TrackingService? service, LocationService? locationService, SyncQueueStore? syncQueueStore})
      : _service = service ?? TrackingService(),
        _locationService = locationService ?? LocationService(),
        _syncStore = syncQueueStore ?? createDefaultSyncQueueStore() {
    WidgetsBinding.instance.addObserver(this);
  }

  final TrackingService _service;
  final LocationService _locationService;
  final SyncQueueStore _syncStore;

  static const _sampleInterval = Duration(seconds: 30);
  // Flush after every sample (not batched) so a new point reaches the
  // server within one sample interval — the admin dashboard polls
  // GET /tracking/... every 30s (see docs/37_TRACKING_MODULE.md,
  // config('mms.tracking.polling_seconds', 30)), so batching 10 points
  // before sending (the old default) left the admin map showing GPS
  // positions up to ~10 minutes stale even though it "looks realtime".
  static const _flushEveryPoints = 1;

  TrackingSession? _session;
  TrackingSession? get session => _session;
  bool get isActive => _session != null;

  Position? _lastPosition;
  DateTime? _lastSampleAt;
  DateTime? get lastSampleAt => _lastSampleAt;
  String? get lastLocationLabel => _lastPosition == null ? null : '${_lastPosition!.latitude.toStringAsFixed(4)}, ${_lastPosition!.longitude.toStringAsFixed(4)}';

  double _distanceMeters = 0;
  double get distanceMeters => _distanceMeters;
  int get visitCount => _visitCount;
  int _visitCount = 0;

  final List<TrackingPoint> _pendingPoints = [];
  Timer? _sampleTimer;
  bool _starting = false;

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _resumeSampling();
    } else {
      _pauseSampling();
    }
  }

  /// Resumes an in-progress session (`GET current`) or starts a new one.
  /// No-op if a session is already active or already starting, and no-op
  /// on Sunday (the server rejects starts that day). Safe to call
  /// repeatedly — e.g. on every healthy `/health` tick — as a cheap retry
  /// if the initial attempt failed while offline.
  Future<void> ensureStarted() async {
    if (_starting || isActive) return;
    if (DateTime.now().weekday == DateTime.sunday) return;
    _starting = true;
    try {
      final current = await _service.current();
      _session = current ?? await _service.start(localUuid: newLocalUuid(), startedAt: DateTime.now());
      notifyListeners();
      _resumeSampling();
    } on ApiException {
      // Offline or a real server error at start time — silently retried on
      // the next call to ensureStarted() (main.dart wires this to the
      // periodic health-check tick).
    } finally {
      _starting = false;
    }
  }

  /// Manual refresh (e.g. a "Perbarui Lokasi" button) — samples immediately
  /// instead of waiting for the next periodic tick.
  Future<void> refreshNow() => _sample();

  void _resumeSampling() {
    if (!isActive) return;
    _sampleTimer?.cancel();
    _sampleTimer = Timer.periodic(_sampleInterval, (_) => _sample());
    unawaited(_sample());
  }

  void _pauseSampling() {
    _sampleTimer?.cancel();
    _sampleTimer = null;
  }

  Future<void> _sample() async {
    if (!isActive) return;
    try {
      final pos = await _locationService.getCurrentPosition();
      final now = DateTime.now();
      if (_lastPosition != null) {
        _distanceMeters += Geolocator.distanceBetween(_lastPosition!.latitude, _lastPosition!.longitude, pos.latitude, pos.longitude);
      }
      _lastPosition = pos;
      _lastSampleAt = now;
      _pendingPoints.add(TrackingPoint(latitude: pos.latitude, longitude: pos.longitude, pointType: 'Perjalanan', recordedAt: now));
      notifyListeners();
      if (_pendingPoints.length >= _flushEveryPoints) await _flush();
    } on LocationServiceException {
      // GPS unavailable this tick (disabled/denied/timeout) — skip, retried
      // automatically on the next periodic tick.
    }
  }

  /// Sends the buffered points immediately. On a connection failure, they
  /// are handed off to the durable offline sync queue instead of being
  /// left sitting only in this in-memory list — otherwise they'd be lost
  /// for good if the app is fully closed before the next successful flush
  /// (fixed 2026-08-21; see BACKEND_INTEGRATION_TASKS.md). Either way
  /// `_pendingPoints` is cleared immediately: retrying is
  /// `SyncDispatcher`'s job now (via its own exponential backoff), not
  /// this controller re-attempting the same points on the next sample.
  Future<void> _flush() async {
    final session = _session;
    if (session == null || _pendingPoints.isEmpty) return;
    final toSend = List<TrackingPoint>.from(_pendingPoints);
    _pendingPoints.removeRange(0, toSend.length);
    try {
      await _service.pointsBatch(session.id, toSend);
    } on ApiException catch (e) {
      if (e.statusCode != null) return; // permanent server-side rejection — nothing honest to retry
      final now = DateTime.now();
      await _syncStore.enqueue(SyncQueueEntry(
        entityType: 'tracking_points',
        localUuid: newLocalUuid(),
        operation: 'create',
        payloadJson: jsonEncode({'session_id': session.id, 'points': toSend.map((p) => p.toJson()).toList()}),
        status: SyncQueueStatus.menungguSinkronisasi,
        createdAt: now,
        updatedAt: now,
      ));
    }
  }

  /// Flushes any remaining points and stops the session — called on
  /// logout (both explicit and forced).
  Future<void> stop() async {
    final session = _session;
    if (session == null) return;
    await _flush();
    try {
      await _service.stop(session.id, endedAt: DateTime.now(), visitCount: _visitCount, distanceMeters: _distanceMeters);
    } on ApiException {
      // Best-effort — if this fails, the session simply stays active
      // server-side until the next successful stop.
    }
    _pauseSampling();
    _session = null;
    _lastPosition = null;
    _lastSampleAt = null;
    _pendingPoints.clear();
    _distanceMeters = 0;
    _visitCount = 0;
    notifyListeners();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _sampleTimer?.cancel();
    super.dispose();
  }
}
