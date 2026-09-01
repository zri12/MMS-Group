import 'dart:async';

import 'package:flutter/material.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'app_shell.dart';
import 'data/local/sync_dispatcher.dart';
import 'data/local/sync_queue.dart';
import 'navigation/nav_controller.dart';
import 'services/auth_service.dart';
import 'services/location_service.dart';
import 'services/member_service.dart';
import 'services/operational_report_service.dart';
import 'services/photo_service.dart';
import 'services/prospect_service.dart';
import 'services/schedule_service.dart';
import 'services/tracking_service.dart';
import 'services/visit_report_service.dart';
import 'state/auth_controller.dart';
import 'state/tracking_controller.dart';
import 'theme/app_theme.dart';

void main() {
  runApp(const MmsMarketingApp());
}

class MmsMarketingApp extends StatefulWidget {
  /// Overridable for tests so a fake [AuthController]/`AuthService` can be
  /// injected instead of hitting a real network + secure storage.
  final AuthController? authController;

  /// Overridable for tests so a fake [LocationService] can be injected
  /// instead of hitting the real permission_handler/geolocator platform
  /// channels (unmocked under plain `flutter test`).
  final LocationService? locationService;

  /// Overridable for tests so the periodic `/health` connectivity check
  /// doesn't hit the network (see `_startHealthChecks`).
  final AuthService? healthService;

  /// Overridable for tests so ConsumersScreen/ReportsScreen/etc. (reached
  /// only through AppShell's router) don't hit the network either.
  final ProspectService? prospectService;
  final MemberService? memberService;
  final VisitReportService? visitReportService;
  final OperationalReportService? operationalReportService;
  final PhotoService? photoService;
  final ScheduleService? scheduleService;

  /// Overridable for tests so the periodic post-health-check queue flush
  /// doesn't touch the real `sqflite` platform channel (unmocked under
  /// plain `flutter test`, same footgun as `flutter_secure_storage`).
  final SyncQueueStore? syncQueueStore;
  final SyncDispatcher? syncDispatcher;

  /// Overridable for tests so the tracking-session lifecycle doesn't touch
  /// the real geolocator platform channel or network.
  final TrackingController? trackingController;

  /// Overridable for tests so `JourneyDetailScreen`'s history lookup
  /// (independent of the live session `trackingController` owns) doesn't
  /// hit the network either.
  final TrackingService? trackingService;
  const MmsMarketingApp({
    super.key,
    this.authController,
    this.locationService,
    this.healthService,
    this.prospectService,
    this.memberService,
    this.visitReportService,
    this.operationalReportService,
    this.photoService,
    this.scheduleService,
    this.syncQueueStore,
    this.syncDispatcher,
    this.trackingController,
    this.trackingService,
  });

  @override
  State<MmsMarketingApp> createState() => _MmsMarketingAppState();
}

class _MmsMarketingAppState extends State<MmsMarketingApp>
    with WidgetsBindingObserver {
  final _nav = NavController();
  late final AuthController _auth = widget.authController ?? AuthController();
  late final LocationService _location =
      widget.locationService ?? LocationService();
  late final AuthService _health = widget.healthService ?? AuthService();
  late final SyncQueueStore _syncStore =
      widget.syncQueueStore ?? createDefaultSyncQueueStore();
  late final TrackingService _trackingService =
      widget.trackingService ?? TrackingService();
  late final SyncDispatcher _syncDispatcher =
      widget.syncDispatcher ??
      SyncDispatcher(trackingService: _trackingService);
  // Shares `_syncStore` (not its own default) so GPS points queued on a
  // failed flush land in the same queue `_syncDispatcher` drains on every
  // healthy tick — a separate default here would silently create an
  // isolated, never-drained queue (the exact bug already fixed once for
  // per-screen sync stores, see createDefaultSyncQueueStore's doc comment).
  late final TrackingController _tracking =
      widget.trackingController ??
      TrackingController(syncQueueStore: _syncStore);
  Timer? _healthTimer;
  AuthStatus _lastAuthStatus = AuthStatus.unknown;
  int? _trackingUserId;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    // `DateFormat(pattern, 'id_ID')` (JourneyDetailScreen, TrackingHistoryScreen)
    // throws until this locale's symbol data is loaded — fire-and-forget
    // since the screens that format dates are only reached after
    // navigation, well after this resolves. `initializeDateFormatting` is
    // idempotent, so this is safe to call even though tests construct
    // MmsMarketingApp directly and never run main()'s own call path.
    unawaited(initializeDateFormatting('id_ID'));
    _nav.addListener(() => setState(() {}));
    _auth.addListener(_onAuthChanged);
    _tracking.addListener(() => setState(() {}));
    _auth.bootstrap();
    _startHealthChecks();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed && _auth.isAuthenticated) {
      unawaited(_tracking.ensureStarted());
    }
  }

  /// Starts/stops the tracking session on auth transitions — covers both a
  /// fresh login and a bootstrap-restored session (both land on
  /// `authenticated`), and both an explicit logout and a forced 401 logout
  /// (both land on `unauthenticated`). See BACKEND_INTEGRATION_TASKS.md's
  /// Tracking section for the "auto-start on login" product decision.
  void _onAuthChanged() {
    if (_auth.status == AuthStatus.authenticated) {
      final userId = _auth.user?.id;
      final accountChanged =
          _trackingUserId != null && _trackingUserId != userId;
      if (accountChanged) _tracking.resetForAccountChange();
      if (_lastAuthStatus != AuthStatus.authenticated || accountChanged) {
        unawaited(_tracking.ensureStarted());
      }
      _trackingUserId = userId;
    } else if (_auth.status == AuthStatus.unauthenticated &&
        _lastAuthStatus == AuthStatus.authenticated) {
      unawaited(_tracking.stop());
      _trackingUserId = null;
    }
    _lastAuthStatus = _auth.status;
    setState(() {});
  }

  /// Drives the offline banner from a real `GET /health` check instead of
  /// the old manual-only toggle (BACKEND_INTEGRATION_TASKS.md Phase 1), and
  /// also flushes the offline sync queue on every healthy tick (Phase 2) —
  /// so `SyncDispatcher`'s exponential backoff actually gets exercised
  /// automatically, not only when the user taps "Sinkronkan Sekarang". The
  /// 30s interval is a reasonable placeholder, not a business-confirmed
  /// number — same caveat as the tracking GPS sampling interval.
  void _startHealthChecks() {
    _checkHealth();
    _healthTimer = Timer.periodic(
      const Duration(seconds: 30),
      (_) => _checkHealth(),
    );
  }

  Future<void> _checkHealth() async {
    final ok = await _health.checkHealth();
    _nav.setOffline(!ok);
    if (ok) {
      // Flush any queued offline creates now that we know we're reachable —
      // respects each entry's own backoff schedule (SyncDispatcher), so
      // this is a cheap no-op most ticks once the queue is empty or waiting.
      await _syncDispatcher.dispatchAll(_syncStore);
      // Retry starting the tracking session if the initial attempt (on
      // login) failed while offline — ensureStarted() is a cheap no-op if
      // a session is already active.
      if (_auth.isAuthenticated) await _tracking.ensureStarted();
    }
  }

  @override
  void dispose() {
    _healthTimer?.cancel();
    WidgetsBinding.instance.removeObserver(this);
    _nav.dispose();
    _auth.removeListener(_onAuthChanged);
    _auth.dispose();
    _tracking.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'MMS Marketing',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.dark,
      darkTheme: AppTheme.dark,
      themeMode: ThemeMode.dark,
      home: AppShell(
        nav: _nav,
        auth: _auth,
        location: _location,
        prospectService: widget.prospectService,
        memberService: widget.memberService,
        visitReportService: widget.visitReportService,
        operationalReportService: widget.operationalReportService,
        photoService: widget.photoService,
        scheduleService: widget.scheduleService,
        tracking: _tracking,
        trackingService: _trackingService,
      ),
    );
  }
}
