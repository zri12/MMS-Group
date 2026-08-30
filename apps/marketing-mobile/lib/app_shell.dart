import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import 'navigation/nav_controller.dart';
import 'navigation/screen.dart';
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
import 'theme/app_colors.dart';
import 'widgets/primitives.dart';
import 'screens/auth_screens.dart';
import 'screens/dashboard_screen.dart';
import 'screens/tracking_detail_screen.dart';
import 'screens/consumer_screens.dart';
import 'screens/member_screens.dart';
import 'screens/schedule_screens.dart';
import 'screens/daily_input_screen.dart';
import 'screens/operational_report_history_screen.dart';
import 'screens/report_screens.dart';
import 'screens/history_screens.dart';
import 'screens/tracking_history_screen.dart';
import 'screens/sync_status_screen.dart';
import 'screens/profile_screens.dart';

class _NavTab {
  final AppScreen screen;
  final String label;
  final IconData icon;
  const _NavTab(this.screen, this.label, this.icon);
}

/// Bottom nav tabs, ported from NAV_TABS in App.tsx.
const _navTabs = [
  _NavTab(AppScreen.dashboard, 'Beranda', LucideIcons.home),
  _NavTab(AppScreen.consumers, 'Data Anggota', LucideIcons.users),
  _NavTab(AppScreen.reports, 'Laporan', LucideIcons.clipboardList),
  _NavTab(AppScreen.history, 'Riwayat Resort', LucideIcons.milestone),
  _NavTab(AppScreen.profile, 'Profil', LucideIcons.userCircle),
];

/// Screen -> active tab, ported from SCREEN_TAB_MAP in App.tsx.
/// members/memberDetail/addMember/jadwal are intentionally absent (orphaned
/// screens fall back to the dashboard tab, same as the source app).
const _screenTabMap = <AppScreen, AppScreen>{
  AppScreen.dashboard: AppScreen.dashboard,
  AppScreen.trackingDetail: AppScreen.dashboard,
  AppScreen.inputHarian: AppScreen.dashboard,
  AppScreen.operationalReportHistory: AppScreen.dashboard,
  AppScreen.consumers: AppScreen.consumers,
  AppScreen.consumerDetail: AppScreen.consumers,
  AppScreen.addConsumer: AppScreen.consumers,
  AppScreen.editConsumer: AppScreen.consumers,
  AppScreen.reports: AppScreen.reports,
  AppScreen.reportDetail: AppScreen.reports,
  AppScreen.newReport: AppScreen.reports,
  AppScreen.history: AppScreen.history,
  AppScreen.trackingHistory: AppScreen.history,
  AppScreen.journeyDetail: AppScreen.history,
  AppScreen.syncStatus: AppScreen.history,
  AppScreen.profile: AppScreen.profile,
  AppScreen.accountInfo: AppScreen.profile,
  AppScreen.permissionStatus: AppScreen.profile,
  AppScreen.usageGuide: AppScreen.profile,
  AppScreen.about: AppScreen.profile,
};

class AppShell extends StatefulWidget {
  final NavController nav;
  final AuthController auth;
  final LocationService location;
  final ProspectService? prospectService;
  final MemberService? memberService;
  final VisitReportService? visitReportService;
  final OperationalReportService? operationalReportService;
  final PhotoService? photoService;
  final ScheduleService? scheduleService;
  final TrackingController tracking;
  final TrackingService? trackingService;
  const AppShell({
    super.key,
    required this.nav,
    required this.auth,
    required this.location,
    required this.tracking,
    this.prospectService,
    this.memberService,
    this.visitReportService,
    this.operationalReportService,
    this.photoService,
    this.scheduleService,
    this.trackingService,
  });

  @override
  State<AppShell> createState() => _AppShellState();
}

class _AppShellState extends State<AppShell> {
  final Map<AppScreen, Widget> _cachedTabScreens = {};
  int? _cachedUserId;

  NavController get nav => widget.nav;
  AuthController get auth => widget.auth;
  LocationService get location => widget.location;
  TrackingController get tracking => widget.tracking;
  ProspectService? get prospectService => widget.prospectService;
  MemberService? get memberService => widget.memberService;
  VisitReportService? get visitReportService => widget.visitReportService;
  OperationalReportService? get operationalReportService =>
      widget.operationalReportService;
  PhotoService? get photoService => widget.photoService;
  ScheduleService? get scheduleService => widget.scheduleService;
  TrackingService? get trackingService => widget.trackingService;

  Widget _createTabScreen(AppScreen screen) => switch (screen) {
    AppScreen.dashboard => DashboardScreen(
      nav: nav,
      auth: auth,
      tracking: tracking,
      operationalReportService: operationalReportService,
      memberService: memberService,
      scheduleService: scheduleService,
    ),
    AppScreen.consumers => ConsumersScreen(
      nav: nav,
      service: prospectService,
      memberService: memberService,
    ),
    AppScreen.reports => ReportsScreen(nav: nav, service: visitReportService),
    AppScreen.history => HistoryScreen(
      nav: nav,
      auth: auth,
      tracking: tracking,
    ),
    AppScreen.profile => ProfileScreen(nav: nav, auth: auth),
    _ => throw ArgumentError.value(screen, 'screen', 'Bukan tab utama'),
  };

  Widget _tabStack(AppScreen? activeScreen) {
    if (activeScreen != null) {
      _cachedTabScreens.putIfAbsent(
        activeScreen,
        () => _createTabScreen(activeScreen),
      );
    }

    if (_cachedTabScreens.isEmpty) {
      return const SizedBox.shrink();
    }

    return Stack(
      fit: StackFit.expand,
      children: _cachedTabScreens.entries.map((entry) {
        final isActive = entry.key == activeScreen;
        return Offstage(
          offstage: !isActive,
          child: TickerMode(enabled: isActive, child: entry.value),
        );
      }).toList(growable: false),
    );
  }

  Widget _renderBody() {
    final screen = nav.current.screen;
    final isRootTab = _navTabs.any((tab) => tab.screen == screen);

    if (isRootTab) {
      return _tabStack(screen);
    }

    if (authScreens.contains(screen)) {
      return _renderScreen();
    }

    return Stack(
      fit: StackFit.expand,
      children: [
        if (_cachedTabScreens.isNotEmpty)
          Offstage(offstage: true, child: _tabStack(null)),
        _renderScreen(),
      ],
    );
  }

  Widget _renderScreen() {
    final screen = nav.current.screen;
    return switch (screen) {
      AppScreen.splash => SplashScreen(nav: nav, auth: auth),
      AppScreen.login => LoginScreen(nav: nav, auth: auth, locationService: location),
      AppScreen.permLocation ||
      AppScreen.permBg ||
      AppScreen.permNotification ||
      AppScreen.permCamera => PermissionScreen(
        nav: nav,
        screen: screen,
        service: location,
        tracking: tracking,
      ),
      AppScreen.dashboard => DashboardScreen(
        nav: nav,
        auth: auth,
        tracking: tracking,
        operationalReportService: operationalReportService,
        memberService: memberService,
        scheduleService: scheduleService,
      ),
      AppScreen.trackingDetail => TrackingDetailScreen(
        nav: nav,
        tracking: tracking,
      ),
      AppScreen.consumers => ConsumersScreen(
        nav: nav,
        service: prospectService,
        memberService: memberService,
      ),
      AppScreen.consumerDetail => ConsumerDetailScreen(
        nav: nav,
        service: prospectService,
      ),
      AppScreen.addConsumer => AddConsumerScreen(
        nav: nav,
        auth: auth,
        service: prospectService,
      ),
      AppScreen.editConsumer => EditConsumerScreen(
        nav: nav,
        service: prospectService,
      ),
      AppScreen.members => MembersScreen(nav: nav, service: memberService),
      AppScreen.memberDetail => MemberDetailScreen(
        nav: nav,
        service: memberService,
      ),
      AppScreen.addMember => AddMemberScreen(
        nav: nav,
        auth: auth,
        service: memberService,
        locationService: location,
        photoService: photoService,
        prospectService: prospectService,
      ),
      AppScreen.jadwal => JadwalScreen(nav: nav, service: scheduleService),
      AppScreen.inputHarian => DailyInputScreen(
        nav: nav,
        auth: auth,
        service: operationalReportService,
        photoService: photoService,
      ),
      AppScreen.operationalReportHistory => OperationalReportHistoryScreen(
        nav: nav,
        service: operationalReportService,
      ),
      AppScreen.reports => ReportsScreen(nav: nav, service: visitReportService),
      AppScreen.newReport => NewReportScreen(
        nav: nav,
        auth: auth,
        prospectService: prospectService,
        service: visitReportService,
        photoService: photoService,
      ),
      AppScreen.reportDetail => ReportDetailScreen(
        nav: nav,
        auth: auth,
        service: visitReportService,
      ),
      AppScreen.history => HistoryScreen(
        nav: nav,
        auth: auth,
        tracking: tracking,
      ),
      AppScreen.trackingHistory => TrackingHistoryScreen(
        nav: nav,
        service: trackingService,
      ),
      AppScreen.journeyDetail => JourneyDetailScreen(
        nav: nav,
        trackingService: trackingService,
      ),
      AppScreen.syncStatus => SyncStatusScreen(nav: nav),
      AppScreen.profile => ProfileScreen(nav: nav, auth: auth),
      AppScreen.permissionStatus => PermissionStatusScreen(nav: nav),
      AppScreen.accountInfo => AccountInfoScreen(nav: nav, auth: auth),
      AppScreen.usageGuide => UsageGuideScreen(nav: nav),
      AppScreen.about => AboutScreen(nav: nav),
    };
  }

  @override
  Widget build(BuildContext context) {
    final userId = auth.user?.id;
    if (_cachedUserId != userId) {
      _cachedTabScreens.clear();
      _cachedUserId = userId;
    }

    final isAuth = authScreens.contains(nav.current.screen);
    final activeTab = _screenTabMap[nav.current.screen] ?? AppScreen.dashboard;

    return Scaffold(
      backgroundColor: AppColors.background,
      body: Column(
        children: [
          if (!isAuth) OfflineBanner(offline: nav.offline),
          Expanded(child: _renderBody()),
        ],
      ),
      bottomNavigationBar: isAuth ? null : _buildBottomNav(activeTab),
    );
  }

  Widget _buildBottomNav(AppScreen activeTab) {
    return Container(
      decoration: const BoxDecoration(
        color: AppColors.backgroundSecondary,
        border: Border(top: BorderSide(color: AppColors.border)),
      ),
      child: SafeArea(
        top: false,
        child: SizedBox(
          height: 64,
          child: Row(
            children: _navTabs.map((tab) {
              final active = tab.screen == activeTab;
              return Expanded(
                child: GestureDetector(
                  onTap: () => nav.switchTab(tab.screen),
                  behavior: HitTestBehavior.opaque,
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Container(
                        width: 32,
                        height: 32,
                        decoration: BoxDecoration(
                          color: active ? AppColors.gold : Colors.transparent,
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Icon(
                          tab.icon,
                          size: 18,
                          color: active
                              ? AppColors.primaryForeground
                              : AppColors.mutedForeground.withValues(
                                  alpha: 0.7,
                                ),
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        tab.label,
                        style: TextStyle(
                          fontSize: 10,
                          fontWeight: active
                              ? FontWeight.w700
                              : FontWeight.w500,
                          color: active
                              ? AppColors.goldLight
                              : AppColors.mutedForeground.withValues(
                                  alpha: 0.7,
                                ),
                        ),
                      ),
                    ],
                  ),
                ),
              );
            }).toList(),
          ),
        ),
      ),
    );
  }
}
