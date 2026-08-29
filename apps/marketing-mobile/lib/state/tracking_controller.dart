import 'dart:async';
import 'dart:convert';

import 'package:flutter/widgets.dart';
import 'package:geolocator/geolocator.dart';

import '../api/api_client.dart';
import '../data/local/sync_queue.dart';
import '../services/location_service.dart';
import '../services/tracking_service.dart';

enum TrackingStartIssue { nonOperationalDay, locationServiceDisabled, locationPermissionDenied, serverUnavailable }

/// Owns the field marketer's tracking-session lifecycle for the whole app
/// run, per the product decision recorded in BACKEND_INTEGRATION_TASKS.md
/// (2026-08-21): one continuous session per work day, started automatically
/// on login/app-open and stopped on logout — not a per-visit session, and
/// receives updates in the background after the PDL grants the explicitly
/// requested location-always permission. Android keeps tracking visible
/// through a foreground notification while the session remains active.
class TrackingController extends ChangeNotifier with WidgetsBindingObserver {
  TrackingController({
    TrackingService? service,
    LocationService? locationService,
    SyncQueueStore? syncQueueStore,
  }) : _service = service ?? TrackingService(),
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
  TrackingStartIssue? _startIssue;
  String? get startIssueMessage => switch (_startIssue) {
    TrackingStartIssue.nonOperationalDay => 'Tracking hanya dapat dimulai pada hari operasional, Senin sampai Sabtu.',
    TrackingStartIssue.locationServiceDisabled => 'GPS perangkat belum aktif. Aktifkan lokasi lalu mulai tracking lagi.',
    TrackingStartIssue.locationPermissionDenied => 'Izin lokasi belum tersedia. Aktifkan izin Lokasi di pengaturan aplikasi.',
    TrackingStartIssue.serverUnavailable => 'Sesi tracking belum dapat dimulai. Periksa koneksi ke server lalu coba lagi.',
    null => null,
  };
  bool get needsLocationSettings => _startIssue == TrackingStartIssue.locationPermissionDenied;

  Position? _lastPosition;
  DateTime? _lastSampleAt;
  DateTime? get lastSampleAt => _lastSampleAt;
  String? get lastLocationLabel => _lastPosition == null
      ? null
      : '${_lastPosition!.latitude.toStringAsFixed(4)}, ${_lastPosition!.longitude.toStringAsFixed(4)}';

  double _distanceMeters = 0;
  double get distanceMeters => _distanceMeters;
  int get visitCount => _visitCount;
  int _visitCount = 0;

  final List<TrackingPoint> _pendingPoints = [];
  Timer? _sampleTimer;
  StreamSubscription<Position>? _positionSubscription;
  bool _starting = false;

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      _resumeSampling();
    }
  }

  /// Resumes an in-progress session (`GET current`) or starts a new one.
  /// No-op if a session is already active or already starting, and no-op
  /// on Sunday (the server rejects starts that day). Safe to call
  /// repeatedly — e.g. on every healthy `/health` tick — as a cheap retry
  /// if the initial attempt failed while offline.
  Future<bool> ensureStarted() async {
    if (_starting) return isActive;
    if (isActive) {
      _startIssue = null;
      _resumeSampling();
      return true;
    }
    if (DateTime.now().weekday == DateTime.sunday) {
      _startIssue = TrackingStartIssue.nonOperationalDay;
      notifyListeners();
      return false;
    }
    final locationStatus = await _locationService.trackingLocationStatus();
    if (locationStatus != TrackingLocationStatus.ready) {
      _startIssue = locationStatus == TrackingLocationStatus.serviceDisabled ? TrackingStartIssue.locationServiceDisabled : TrackingStartIssue.locationPermissionDenied;
      notifyListeners();
      return false;
    }
    _starting = true;
    try {
      final current = await _service.current();
      _session =
          current ??
          await _service.start(
            localUuid: newLocalUuid(),
            startedAt: DateTime.now(),
          );
      _startIssue = null;
      notifyListeners();
      _resumeSampling();
      return true;
    } on ApiException {
      _startIssue = TrackingStartIssue.serverUnavailable;
      notifyListeners();
      return false;
      // Offline or a real server error at start time — silently retried on
      // the next call to ensureStarted() (main.dart wires this to the
      // periodic health-check tick).
    } finally {
      _starting = false;
    }
  }

  /// Manual refresh (e.g. a "Perbarui Lokasi" button) — samples immediately
  /// instead of waiting for the next periodic tick.
  Future<bool> openLocationSettings() => _locationService.openLocationSettings();

  Future<void> refreshNow() => _sample();

  void _resumeSampling() {
    if (!isActive) return;
    _sampleTimer ??= Timer.periodic(_sampleInterval, (_) => _sample());
    _positionSubscription ??= _locationService.positionStream().listen(
      _recordPosition,
      onError: (_, __) {
        // A permission change can invalidate the existing stream. Clear it
        // so the periodic health check can subscribe again after the PDL
        // enables location access in device settings.
        _positionSubscription?.cancel();
        _positionSubscription = null;
      },
    );
    unawaited(_sample());
  }

  void _pauseSampling() {
    _sampleTimer?.cancel();
    _sampleTimer = null;
    _positionSubscription?.cancel();
    _positionSubscription = null;
  }

  Future<void> _sample() async {
    if (!isActive) return;
    try {
      _recordPosition(await _locationService.getCurrentPosition());
    } on LocationServiceException {
      // GPS unavailable this tick (disabled/denied/timeout) — skip, retried
      // automatically on the next periodic tick.
    }
  }

  void _recordPosition(Position position) {
    if (!isActive) return;

    final now = DateTime.now();
    if (_lastPosition != null) {
      _distanceMeters += Geolocator.distanceBetween(
        _lastPosition!.latitude,
        _lastPosition!.longitude,
        position.latitude,
        position.longitude,
      );
    }
    _lastPosition = position;
    _lastSampleAt = now;
    _pendingPoints.add(
      TrackingPoint(
        localUuid: newLocalUuid(),
        latitude: position.latitude,
        longitude: position.longitude,
        pointType: 'Perjalanan',
        recordedAt: now,
      ),
    );
    notifyListeners();
    if (_pendingPoints.length >= _flushEveryPoints) unawaited(_flush());
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
      if (e.statusCode != null) {
        return; // permanent server-side rejection — nothing honest to retry
      }
      final now = DateTime.now();
      await _syncStore.enqueue(
        SyncQueueEntry(
          entityType: 'tracking_points',
          localUuid: newLocalUuid(),
          operation: 'create',
          payloadJson: jsonEncode({
            'session_id': session.id,
            'points': toSend.map((p) => p.toJson()).toList(),
          }),
          status: SyncQueueStatus.menungguSinkronisasi,
          createdAt: now,
          updatedAt: now,
        ),
      );
    }
  }

  /// Flushes any remaining points and stops the session — called on
  /// logout (both explicit and forced).
  Future<void> stop() async {
    final session = _session;
    if (session == null) return;
    await _flush();
    try {
      await _service.stop(
        session.id,
        endedAt: DateTime.now(),
        visitCount: _visitCount,
        distanceMeters: _distanceMeters,
      );
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
    _pauseSampling();
    super.dispose();
  }
}
