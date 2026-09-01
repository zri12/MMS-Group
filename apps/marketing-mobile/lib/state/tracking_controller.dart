import 'dart:async';
import 'dart:convert';

import 'package:flutter/widgets.dart';
import 'package:geolocator/geolocator.dart';

import '../api/api_client.dart';
import '../data/local/sync_queue.dart';
import '../services/location_service.dart';
import '../services/tracking_service.dart';

enum TrackingStartIssue {
  nonOperationalDay,
  locationServiceDisabled,
  locationPermissionDenied,
  serverUnavailable,
}

/// Owns the field marketer's tracking-session lifecycle for the whole app
/// run, per the product decision recorded in BACKEND_INTEGRATION_TASKS.md
/// (2026-08-21): one continuous session per work day, started automatically
/// on login/app-open and stopped on logout — not a per-visit session, and
/// receives updates in the background after the PDL grants the explicitly
/// requested location-always permission. Android keeps tracking visible
/// through a foreground notification while the session remains active.
class TrackingController extends ChangeNotifier with WidgetsBindingObserver {
  static const _allowSundayTracking = bool.fromEnvironment(
    'ALLOW_SUNDAY_TRACKING',
  );

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
  static const _serverGpsStaleDuration = Duration(minutes: 3);
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
  String? _serverStartMessage;
  String? get startIssueMessage =>
      _serverStartMessage ??
      switch (_startIssue) {
        TrackingStartIssue.nonOperationalDay =>
          'Tracking hanya dapat dimulai pada hari operasional, Senin sampai Sabtu.',
        TrackingStartIssue.locationServiceDisabled =>
          'GPS perangkat belum aktif. Aktifkan lokasi lalu mulai tracking lagi.',
        TrackingStartIssue.locationPermissionDenied =>
          'Izin lokasi belum tersedia. Aktifkan izin Lokasi di pengaturan aplikasi.',
        TrackingStartIssue.serverUnavailable =>
          'Sesi tracking belum dapat dimulai. Periksa koneksi ke server lalu coba lagi.',
        null => null,
      };
  bool get needsLocationSettings =>
      _startIssue == TrackingStartIssue.locationPermissionDenied;

  Position? _lastPosition;
  DateTime? _lastSampleAt;
  TrackingPoint? _lastSyncedPoint;
  DateTime? _lastSyncedAt;
  String? _locationIssue;
  String? _syncIssue;
  DateTime? get lastSampleAt => _lastSampleAt;
  DateTime? get lastSyncedAt => _lastSyncedAt;
  String? get locationIssue => _locationIssue;
  String? get syncIssue => _syncIssue;
  String? get trackingIssue => _locationIssue ?? _syncIssue;
  bool get hasFreshSyncedLocation {
    final syncedAt = _lastSyncedAt;
    if (!isActive || syncedAt == null) return false;
    return DateTime.now().difference(syncedAt) <= _serverGpsStaleDuration;
  }

  String? get lastLocationLabel => _lastPosition == null
      ? lastSyncedLocationLabel
      : '${_lastPosition!.latitude.toStringAsFixed(4)}, ${_lastPosition!.longitude.toStringAsFixed(4)}';
  String? get lastSyncedLocationLabel => _lastSyncedPoint == null
      ? null
      : '${_lastSyncedPoint!.latitude.toStringAsFixed(4)}, ${_lastSyncedPoint!.longitude.toStringAsFixed(4)}';

  double _distanceMeters = 0;
  double get distanceMeters => _distanceMeters;
  int get visitCount => _visitCount;
  int _visitCount = 0;

  final List<TrackingPoint> _pendingPoints = [];
  Timer? _sampleTimer;
  StreamSubscription<Position>? _positionSubscription;
  bool _starting = false;
  bool _flushing = false;
  Future<void>? _sampling;
  bool _manuallyStopped = false;

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
  Future<bool> ensureStarted({bool force = false}) async {
    if (_starting) return isActive;
    if (_manuallyStopped && !force) return false;
    _starting = true;
    _serverStartMessage = null;
    try {
      final current = await _service.current();
      if (current != null) {
        _adoptSession(current);
        return true;
      }

      // The session in memory does not belong to the currently signed-in
      // account anymore. Clear it before creating a replacement so uploads
      // cannot remain stuck on a 404 response from the server.
      if (isActive) _clearLocalSession();

      if (DateTime.now().weekday == DateTime.sunday && !_allowSundayTracking) {
        _startIssue = TrackingStartIssue.nonOperationalDay;
        notifyListeners();
        return false;
      }
      final locationStatus = await _locationService.trackingLocationStatus();
      if (locationStatus != TrackingLocationStatus.ready) {
        _startIssue = locationStatus == TrackingLocationStatus.serviceDisabled
            ? TrackingStartIssue.locationServiceDisabled
            : TrackingStartIssue.locationPermissionDenied;
        notifyListeners();
        return false;
      }

      _adoptSession(
        await _service.start(
          localUuid: newLocalUuid(),
          startedAt: DateTime.now(),
        ),
      );
      return true;
    } on ApiException catch (error) {
      if (isActive) {
        _syncIssue = _apiMessage(error);
        notifyListeners();
        return true;
      }
      _serverStartMessage = _apiMessage(error);
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

  void _adoptSession(TrackingSession session) {
    _session = session;
    _manuallyStopped = false;
    _lastSyncedPoint = session.latestPoint;
    _lastSyncedAt = _lastSyncedPoint?.receivedAt;
    _locationIssue = null;
    _syncIssue = null;
    _startIssue = null;
    _serverStartMessage = null;
    notifyListeners();
    _resumeSampling();
  }

  /// Forget a session held for a previous authenticated account. This never
  /// calls the server because the bearer token may already be replaced.
  void resetForAccountChange() {
    _clearLocalSession();
    _manuallyStopped = false;
    notifyListeners();
  }

  void _clearLocalSession() {
    _pauseSampling();
    _session = null;
    _lastPosition = null;
    _lastSampleAt = null;
    _lastSyncedPoint = null;
    _lastSyncedAt = null;
    _pendingPoints.clear();
    _distanceMeters = 0;
    _visitCount = 0;
  }

  /// Manual refresh (e.g. a "Perbarui Lokasi" button) — samples immediately
  /// instead of waiting for the next periodic tick.
  Future<bool> openLocationSettings() =>
      _locationService.openLocationSettings();

  Future<void> refreshNow() => _sample();

  void _resumeSampling() {
    if (!isActive) return;
    _sampleTimer ??= Timer.periodic(_sampleInterval, (_) => _sample());
    _positionSubscription ??= _locationService.positionStream().listen(
      (position) => unawaited(_recordPosition(position)),
      onError: (_, __) {
        // A permission change can invalidate the existing stream. Clear it
        // so the periodic health check can subscribe again after the PDL
        // enables location access in device settings.
        _positionSubscription?.cancel();
        _positionSubscription = null;
        _locationIssue =
            'GPS belum dapat membaca lokasi. Periksa GPS dan izin lokasi.';
        notifyListeners();
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

  Future<void> _sample() {
    if (!isActive) return Future.value();
    return _sampling ??= _sampleOnce().whenComplete(() => _sampling = null);
  }

  Future<void> _sampleOnce() async {
    try {
      await _recordPosition(await _locationService.getCurrentPosition());
    } on LocationServiceException catch (error) {
      _locationIssue = error.message;
      notifyListeners();
      return;
      // GPS unavailable this tick (disabled/denied/timeout) — skip, retried
      // automatically on the next periodic tick.
    } catch (_) {
      _locationIssue = 'GPS belum dapat membaca lokasi. Coba perbarui lagi.';
      notifyListeners();
    }
  }

  Future<void> _recordPosition(Position position) async {
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
    _locationIssue = null;
    _pendingPoints.add(
      TrackingPoint(
        localUuid: newLocalUuid(),
        latitude: position.latitude,
        longitude: position.longitude,
        accuracyMeters: position.accuracy >= 0 ? position.accuracy : null,
        speedMps: position.speed >= 0 ? position.speed : null,
        heading: position.heading >= 0 && position.heading <= 360
            ? position.heading
            : null,
        altitudeMeters: position.altitude,
        pointType: 'Perjalanan',
        recordedAt: now,
      ),
    );
    notifyListeners();
    if (_pendingPoints.length >= _flushEveryPoints) await _flush();
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
    if (_flushing) return;
    final session = _session;
    if (session == null || _pendingPoints.isEmpty) return;
    _flushing = true;
    final toSend = List<TrackingPoint>.from(_pendingPoints);
    _pendingPoints.removeRange(0, toSend.length);
    try {
      final serverSession = await _service.current();
      if (serverSession == null) {
        _clearLocalSession();
        _manuallyStopped = false;
        _syncIssue =
            'Sesi tracking belum tersedia di server. Mulai tracking kembali.';
        notifyListeners();
        return;
      }
      if (serverSession.id != session.id) {
        _session = serverSession;
        _lastSyncedPoint = serverSession.latestPoint;
        _lastSyncedAt = _lastSyncedPoint?.receivedAt;
      }
      await _service.pointsBatch(serverSession.id, toSend);
      _lastSyncedPoint = toSend.last;
      _lastSyncedAt = DateTime.now();
      _syncIssue = null;
      notifyListeners();
    } on ApiException catch (e) {
      if (e.statusCode == 404) {
        _clearLocalSession();
        _manuallyStopped = false;
        _syncIssue =
            'Sesi tracking sebelumnya tidak ditemukan. Mulai tracking kembali.';
        notifyListeners();
        return;
      }
      _syncIssue = e.message;
      notifyListeners();
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
    } finally {
      _flushing = false;
    }
  }

  /// Flushes any remaining points and stops the session — called on
  /// logout (both explicit and forced).
  Future<bool> stop({bool manual = false}) async {
    final session = _session;
    if (session == null) {
      _manuallyStopped = manual;
      return true;
    }
    try {
      await _flush();
      if (_session == null) {
        _manuallyStopped = manual;
        return true;
      }
      final serverSession = await _service.current();
      if (serverSession == null) {
        _clearLocalSession();
        _manuallyStopped = manual;
        notifyListeners();
        return true;
      }
      await _service.stop(
        serverSession.id,
        endedAt: DateTime.now(),
        visitCount: _visitCount,
        distanceMeters: _distanceMeters,
      );
    } on ApiException catch (e) {
      if (e.statusCode == 404) {
        _clearLocalSession();
        _manuallyStopped = manual;
        _syncIssue = null;
        notifyListeners();
        return true;
      }
      if (manual) return false;
    } catch (_) {
      // Best-effort — if this fails, the session simply stays active
      // Logout remains best-effort; a manual stop must be confirmed.
      if (manual) return false;
    }
    _manuallyStopped = manual;
    _clearLocalSession();
    _locationIssue = null;
    _syncIssue = null;
    notifyListeners();
    return true;
  }

  String _apiMessage(ApiException error) {
    final errors = error.errors;
    if (errors != null) {
      for (final messages in errors.values) {
        if (messages.isNotEmpty) return messages.first;
      }
    }
    return error.message;
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _pauseSampling();
    super.dispose();
  }
}
