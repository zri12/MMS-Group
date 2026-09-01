import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/member_service.dart';
import '../services/operational_report_service.dart';
import '../services/schedule_service.dart';
import '../state/auth_controller.dart';
import '../state/tracking_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';

const _allowSundayOperations = bool.fromEnvironment('ALLOW_SUNDAY_TRACKING');
const _days = [
  'Senin',
  'Selasa',
  'Rabu',
  'Kamis',
  'Jumat',
  'Sabtu',
  if (_allowSundayOperations) 'Minggu',
];
const _dayWeekdayIndex = {
  'Senin': 1,
  'Selasa': 2,
  'Rabu': 3,
  'Kamis': 4,
  'Jumat': 5,
  'Sabtu': 6,
  'Minggu': 7,
};

String _todayDayName() {
  final weekday = DateTime.now().weekday; // 1=Senin..7=Minggu
  if (weekday >= 1 && weekday <= 6) return _days[weekday - 1];
  return 'Minggu';
}

String _isoDate(DateTime d) =>
    '${d.year}-${d.month.toString().padLeft(2, '0')}-${d.day.toString().padLeft(2, '0')}';

/// `Schedule.startTime` comes from the backend as `H:i:s` -- trims the
/// seconds for display, matching the `HH:mm` convention used everywhere
/// else in this app.
String _hm(String time) => time.length >= 5 ? time.substring(0, 5) : time;

class DashboardScreen extends StatefulWidget {
  final NavController nav;
  final AuthController auth;
  final TrackingController tracking;
  final OperationalReportService? operationalReportService;
  final MemberService? memberService;
  final ScheduleService? scheduleService;
  const DashboardScreen({
    super.key,
    required this.nav,
    required this.auth,
    required this.tracking,
    this.operationalReportService,
    this.memberService,
    this.scheduleService,
  });

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  late final OperationalReportService _reportService =
      widget.operationalReportService ?? OperationalReportService();
  late final MemberService _memberService =
      widget.memberService ?? MemberService();
  late final ScheduleService _scheduleService =
      widget.scheduleService ?? ScheduleService();

  String selectedDay = _todayDayName();

  List<OperationalReport> _todayReports = const [];
  List<OperationalReport> _selectedReports = const [];
  int _totalAnggota = 0;

  final Map<String, List<Schedule>> _scheduleCache = {};
  String? _scheduleError;

  Future<void> _startTracking() async {
    final started = await widget.tracking.ensureStarted(force: true);
    if (!mounted || started) return;
    final issue =
        widget.tracking.startIssueMessage ??
        'Sesi tracking belum dapat dimulai.';
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(issue),
        action: widget.tracking.needsLocationSettings
            ? SnackBarAction(
                label: 'PENGATURAN',
                onPressed: () {
                  widget.tracking.openLocationSettings();
                },
              )
            : null,
      ),
    );
  }

  @override
  void initState() {
    super.initState();
    _loadSummary();
    _loadSchedule(selectedDay);
  }

  /// Real "today"/"selected day" operational numbers replace the old
  /// hardcoded mock grid (BACKEND_INTEGRATION_TASKS.md/task.md 2026-08-24
  /// dashboard-audit entry). One fetch of the marketing's own report list
  /// (already scoped server-side to the caller) covers both "Ringkasan
  /// Hari Ini" (today's report) and "Ringkasan Operasional" (the
  /// day-picker's selected day's report) — no separate endpoint per
  /// section needed.
  Future<void> _loadSummary() async {
    final requestedDay = selectedDay;
    try {
      final today = DateTime.now();
      final selectedDate = _dateForDay(requestedDay);
      final todayReports = await _reportService.list(
        dateFrom: _isoDate(today),
        dateTo: _isoDate(today),
        perPage: 100,
      );
      final selectedReports = _isoDate(today) == _isoDate(selectedDate)
          ? todayReports
          : await _reportService.list(
              dateFrom: _isoDate(selectedDate),
              dateTo: _isoDate(selectedDate),
              perPage: 100,
            );
      final members = await _memberService.count();
      if (!mounted) return;
      setState(() {
        _todayReports = todayReports;
        if (selectedDay == requestedDay) {
          _selectedReports = selectedReports;
        }
        _totalAnggota = members;
      });
    } catch (_) {
      // Keep the latest successful values visible when a refresh fails.
    }
  }

  Future<void> _loadSchedule(String day) async {
    if (_scheduleCache.containsKey(day)) return;
    setState(() => _scheduleError = null);
    try {
      final list = await _scheduleService.list(
        day: day,
        date: _isoDate(_dateForDay(day)),
      );
      if (!mounted) return;
      setState(() {
        _scheduleCache[day] = list;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _scheduleError = 'Gagal memuat jadwal.';
      });
    }
  }

  DateTime _dateForDay(String day) {
    final weekday = _dayWeekdayIndex[day] ?? DateTime.monday;
    final today = DateUtils.dateOnly(DateTime.now());
    return today.subtract(Duration(days: today.weekday - weekday));
  }

  int _reportTotal(
    List<OperationalReport> reports,
    int Function(OperationalReport report) value,
  ) => reports.fold(0, (total, report) => total + value(report));

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        bottom: false,
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildHeader(),
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    _buildTrackingCard(),
                    const SizedBox(height: 14),
                    const Text(
                      'Ringkasan Hari Ini',
                      style: TextStyle(
                        color: AppColors.foreground,
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: 10),
                    _buildRingkasanHariIni(),
                    const SizedBox(height: 14),
                    PrimaryButton(
                      onPressed: () =>
                          widget.nav.navigate(AppScreen.inputHarian),
                      child: const Text('Input Data Hari Ini'),
                    ),
                    const SizedBox(height: 20),
                    const Text(
                      'Ringkasan Operasional',
                      style: TextStyle(
                        color: AppColors.foreground,
                        fontSize: 16,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    const SizedBox(height: 10),
                    _buildRingkasanOperasional(),
                    const SizedBox(height: 20),
                    _buildDayPicker(),
                    const SizedBox(height: 20),
                    _buildTimeline(),
                    const SizedBox(height: 20),
                    _buildAksesCepat(),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildHeader() {
    final user = widget.auth.user;
    final name = user?.name ?? '-';
    final code = user?.marketing?.code ?? '-';
    final area = user?.marketing?.area ?? '-';
    return Container(
      color: AppColors.backgroundSecondary,
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Row(
            children: [
              const KspLogo(size: KspLogoSize.sm),
              const SizedBox(width: 10),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'KSP Manunggal Makmur Sejahtera',
                      style: TextStyle(
                        color: AppColors.foreground,
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    Text(
                      'Marketing Monitoring',
                      style: TextStyle(
                        color: AppColors.mutedForeground,
                        fontSize: 11,
                      ),
                    ),
                  ],
                ),
              ),
              GestureDetector(
                onTap: () => widget.nav.navigate(AppScreen.profile),
                child: Container(
                  width: 40,
                  height: 40,
                  decoration: const BoxDecoration(
                    color: AppColors.gold,
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: Text(
                      initials(name),
                      style: const TextStyle(
                        color: AppColors.primaryForeground,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 18),
          Text.rich(
            TextSpan(
              style: const TextStyle(
                color: AppColors.mutedForeground,
                fontSize: 14,
              ),
              children: [
                const TextSpan(text: 'Selamat pagi, '),
                TextSpan(
                  text: name,
                  style: const TextStyle(
                    color: AppColors.foreground,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 6),
          Row(
            children: [
              const StatusDot(),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  'Kode $code · $area, Bandung',
                  style: const TextStyle(
                    color: AppColors.mutedForeground,
                    fontSize: 12,
                  ),
                ),
              ),
              GestureDetector(
                onTap: () => widget.nav.navigate(AppScreen.profile),
                child: const Text(
                  'Profil →',
                  style: TextStyle(
                    color: AppColors.goldLight,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildTrackingCard() {
    final sessionActive = widget.tracking.isActive;
    final synced = widget.tracking.hasFreshSyncedLocation;
    final issue = widget.tracking.trackingIssue;
    final headline = !sessionActive
        ? 'Tracking Tidak Aktif'
        : synced
        ? 'Tracking Aktif'
        : issue != null
        ? 'GPS Belum Tersinkron'
        : 'Mencari Lokasi';
    final desc = !sessionActive
        ? 'Sesi tracking belum berjalan. Pastikan izin lokasi sudah diberikan.'
        : synced
        ? 'Lokasi Anda telah diterima server dan tampil di peta admin.'
        : issue ??
              'Menunggu titik GPS pertama diterima server. Buka detail untuk memperbarui lokasi.';
    return GestureDetector(
      onTap: () => widget.nav.navigate(AppScreen.trackingDetail),
      child: Container(
        decoration: BoxDecoration(
          color: AppColors.card,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Container(
              height: 4,
              decoration: const BoxDecoration(
                color: AppColors.gold,
                borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            StatusDot(
                              type: synced
                                  ? StatusDotType.online
                                  : sessionActive
                                  ? StatusDotType.warning
                                  : StatusDotType.offline,
                            ),
                            const SizedBox(width: 6),
                            const Text(
                              'Status Tracking',
                              style: TextStyle(
                                color: AppColors.mutedForeground,
                                fontSize: 12,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text(
                          headline,
                          style: const TextStyle(
                            color: AppColors.foreground,
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          desc,
                          style: const TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 12,
                          ),
                        ),
                        const SizedBox(height: 10),
                        if (!sessionActive)
                          GestureDetector(
                            onTap: _startTracking,
                            child: Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 12,
                                vertical: 8,
                              ),
                              decoration: BoxDecoration(
                                color: AppColors.gold,
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: const Text(
                                'Mulai Tracking',
                                style: TextStyle(
                                  color: AppColors.primaryForeground,
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ),
                          )
                        else
                          const Text(
                            'Lihat detail →',
                            style: TextStyle(
                              color: AppColors.goldLight,
                              fontSize: 12,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                      ],
                    ),
                  ),
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: AppColors.cardElevated,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: const Icon(
                      LucideIcons.navigation,
                      color: AppColors.gold,
                      size: 20,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _statCard(IconData icon, Color color, String label, String value) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.card,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: 32,
            height: 32,
            decoration: BoxDecoration(
              color: color.withValues(alpha: 0.15),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(icon, color: color, size: 16),
          ),
          const SizedBox(height: 10),
          Text(
            value,
            style: const TextStyle(
              color: AppColors.foreground,
              fontSize: 14,
              fontWeight: FontWeight.w700,
              fontFamily: 'monospace',
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: const TextStyle(
              color: AppColors.mutedForeground,
              fontSize: 11,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRingkasanHariIni() {
    final resort = widget.auth.user?.marketing?.area ?? '-';
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        GridView.count(
          crossAxisCount: 2,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 10,
          crossAxisSpacing: 10,
          childAspectRatio: 1.5,
          children: [
            _statCard(
              LucideIcons.building2,
              AppColors.chart3,
              'Resort',
              resort,
            ),
            _statCard(
              LucideIcons.wallet,
              AppColors.success,
              'Storting Hari Ini',
              formatRupiah(_reportTotal(_todayReports, (r) => r.storting)),
            ),
            _statCard(
              LucideIcons.arrowDownCircle,
              AppColors.warning,
              'Drop Hari Ini',
              formatRupiah(_reportTotal(_todayReports, (r) => r.drop)),
            ),
            _statCard(
              LucideIcons.target,
              AppColors.gold,
              'Target Hari Ini',
              formatRupiah(
                _reportTotal(_todayReports, (r) => r.totalTargetAmount),
              ),
            ),
          ],
        ),
        if (_todayReports.isEmpty) ...[
          const SizedBox(height: 8),
          const Text(
            'Belum ada laporan operasional untuk hari ini.',
            style: TextStyle(color: AppColors.mutedForeground, fontSize: 11),
          ),
        ],
      ],
    );
  }

  Widget _buildRingkasanOperasional() {
    final keluar = _reportTotal(
      _selectedReports,
      (r) => r.outgoingTargetAmount,
    );
    final masuk = _reportTotal(_selectedReports, (r) => r.incomingTargetAmount);
    final items = [
      ('Total Anggota', '$_totalAnggota', LucideIcons.users, AppColors.chart3),
      (
        'Total Target',
        formatRupiah(
          _reportTotal(_selectedReports, (r) => r.totalTargetAmount),
        ),
        LucideIcons.target,
        AppColors.gold,
      ),
      (
        'Total Drop',
        formatRupiah(_reportTotal(_selectedReports, (r) => r.drop)),
        LucideIcons.arrowDownCircle,
        AppColors.warning,
      ),
      (
        'Total Storting',
        formatRupiah(_reportTotal(_selectedReports, (r) => r.storting)),
        LucideIcons.wallet,
        AppColors.success,
      ),
      (
        'Target Keluar',
        formatRupiah(keluar),
        LucideIcons.trendingDown,
        AppColors.chart5,
      ),
      (
        'Target Masuk',
        formatRupiah(masuk),
        LucideIcons.trendingUp,
        AppColors.success,
      ),
      (
        'Sirkulasi',
        formatRupiah(keluar + masuk),
        LucideIcons.refreshCw,
        AppColors.chart3,
      ),
    ];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        GridView.count(
          crossAxisCount: 2,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 10,
          crossAxisSpacing: 10,
          childAspectRatio: 1.5,
          children: items
              .map((i) => _statCard(i.$3, i.$4, i.$1, i.$2))
              .toList(),
        ),
        if (_selectedReports.isEmpty) ...[
          const SizedBox(height: 8),
          Text(
            'Belum ada laporan operasional untuk hari $selectedDay.',
            style: const TextStyle(
              color: AppColors.mutedForeground,
              fontSize: 11,
            ),
          ),
        ],
      ],
    );
  }

  Widget _buildDayPicker() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            const Text(
              'Pilih Hari Operasional',
              style: TextStyle(
                color: AppColors.foreground,
                fontSize: 16,
                fontWeight: FontWeight.w700,
              ),
            ),
            GestureDetector(
              onTap: () => widget.nav.navigate(AppScreen.history),
              child: const Text(
                'Rekap →',
                style: TextStyle(
                  color: AppColors.goldLight,
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
          ],
        ),
        const SizedBox(height: 10),
        GridView.count(
          crossAxisCount: 3,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 8,
          crossAxisSpacing: 8,
          childAspectRatio: 2.4,
          children: _days.map((d) {
            final active = d == selectedDay;
            return GestureDetector(
              onTap: () {
                if (active) return;
                setState(() {
                  selectedDay = d;
                  _selectedReports = const [];
                });
                _loadSchedule(d);
                _loadSummary();
              },
              child: Container(
                alignment: Alignment.center,
                decoration: BoxDecoration(
                  color: active ? AppColors.gold : AppColors.card,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(
                    color: active ? AppColors.gold : AppColors.border,
                  ),
                ),
                child: Text(
                  d,
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: active
                        ? AppColors.primaryForeground
                        : AppColors.mutedForeground,
                  ),
                ),
              ),
            );
          }).toList(),
        ),
      ],
    );
  }

  Color _stopColor(String status) => switch (status) {
    'Selesai' => AppColors.success,
    'Berlangsung' => AppColors.gold,
    _ => AppColors.mutedForeground,
  };

  Widget _buildTimeline() {
    final stops = _scheduleCache[selectedDay] ?? const <Schedule>[];
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Linimasa Perjalanan $selectedDay',
          style: const TextStyle(
            color: AppColors.foreground,
            fontSize: 16,
            fontWeight: FontWeight.w700,
          ),
        ),
        const SizedBox(height: 12),
        if (_scheduleError != null)
          Text(
            _scheduleError!,
            style: const TextStyle(color: AppColors.badgeRedText, fontSize: 12),
          )
        else if (stops.isEmpty)
          const Text(
            'Belum ada jadwal pada hari ini.',
            style: TextStyle(color: AppColors.mutedForeground, fontSize: 13),
          )
        else ...[
          Column(
            children: List.generate(stops.length, (i) {
              final s = stops[i];
              final isLast = i == stops.length - 1;
              final title = s.consumerName?.isNotEmpty == true
                  ? s.consumerName!
                  : (s.agenda?.isNotEmpty == true ? s.agenda! : '-');
              return IntrinsicHeight(
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column(
                      children: [
                        Container(
                          width: 10,
                          height: 10,
                          decoration: BoxDecoration(
                            color: _stopColor(s.status),
                            shape: BoxShape.circle,
                          ),
                        ),
                        if (!isLast)
                          Expanded(
                            child: Container(width: 2, color: AppColors.border),
                          ),
                      ],
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Padding(
                        padding: EdgeInsets.only(bottom: isLast ? 0 : 16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              '${_hm(s.startTime)}  $title',
                              style: const TextStyle(
                                color: AppColors.foreground,
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                            const SizedBox(height: 2),
                            Text(
                              s.area,
                              style: const TextStyle(
                                color: AppColors.mutedForeground,
                                fontSize: 12,
                              ),
                            ),
                            const SizedBox(height: 4),
                            StatusBadge(status: s.status, sm: true),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
              );
            }),
          ),
          const SizedBox(height: 14),
          Container(
            decoration: BoxDecoration(
              color: AppColors.card,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: AppColors.border),
            ),
            padding: const EdgeInsets.all(12),
            child: Table(
              columnWidths: const {
                0: FlexColumnWidth(1.4),
                1: FlexColumnWidth(1),
                2: FlexColumnWidth(1.2),
              },
              children: [
                const TableRow(
                  children: [
                    Padding(
                      padding: EdgeInsets.symmetric(vertical: 6),
                      child: Text(
                        'Marketing',
                        style: TextStyle(
                          color: AppColors.mutedForeground,
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                    Padding(
                      padding: EdgeInsets.symmetric(vertical: 6),
                      child: Text(
                        'Waktu',
                        style: TextStyle(
                          color: AppColors.mutedForeground,
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                    Padding(
                      padding: EdgeInsets.symmetric(vertical: 6),
                      child: Text(
                        'Status',
                        style: TextStyle(
                          color: AppColors.mutedForeground,
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                        ),
                      ),
                    ),
                  ],
                ),
                for (final s in stops)
                  TableRow(
                    children: [
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 6),
                        child: Text(
                          widget.auth.user?.name ?? '-',
                          style: const TextStyle(
                            color: AppColors.foreground,
                            fontSize: 12,
                          ),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 6),
                        child: Text(
                          _hm(s.startTime),
                          style: const TextStyle(
                            color: AppColors.foreground,
                            fontSize: 12,
                            fontFamily: 'monospace',
                          ),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.symmetric(vertical: 6),
                        child: StatusBadge(status: s.status, sm: true),
                      ),
                    ],
                  ),
              ],
            ),
          ),
        ],
      ],
    );
  }

  Widget _quickAccessTile(IconData icon, String label, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.card,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: AppColors.border),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: AppColors.gold, size: 20),
            const SizedBox(height: 8),
            Text(
              label,
              style: const TextStyle(
                color: AppColors.foreground,
                fontSize: 12,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// Only the modules confirmed active per Phase 0 DEC-021/022 (see
  /// BACKEND_INTEGRATION_TASKS.md Phase 6) appear here: Tracking Lokasi is
  /// already the tracking card above, Laporan Operasional is already the
  /// "Input Data Hari Ini" button above, so this grid covers the rest —
  /// Data Prospek, Data Anggota (real `Member`, labelled "Anggota Resmi"
  /// here to disambiguate from the bottom nav's "Data Anggota" tab, which
  /// still points at `Prospect` per the un-restructured nav — see Phase 0's
  /// implementation note), Laporan Kunjungan, Rekap Operasional.
  /// Laporan Tunai/Rekap Target/Ubah Password must never appear here.
  Widget _buildAksesCepat() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Akses Cepat',
          style: TextStyle(
            color: AppColors.foreground,
            fontSize: 16,
            fontWeight: FontWeight.w700,
          ),
        ),
        const SizedBox(height: 10),
        GridView.count(
          crossAxisCount: 2,
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          mainAxisSpacing: 10,
          crossAxisSpacing: 10,
          childAspectRatio: 1.6,
          children: [
            _quickAccessTile(
              LucideIcons.search,
              'Data Prospek',
              () => widget.nav.navigate(AppScreen.consumers),
            ),
            _quickAccessTile(
              LucideIcons.users,
              'Anggota Resmi',
              () => widget.nav.navigate(AppScreen.members),
            ),
            _quickAccessTile(
              LucideIcons.clipboardList,
              'Laporan Kunjungan',
              () => widget.nav.navigate(AppScreen.reports),
            ),
            _quickAccessTile(
              LucideIcons.fileBarChart,
              'Rekap Operasional',
              () => widget.nav.navigate(AppScreen.operationalReportHistory),
            ),
          ],
        ),
      ],
    );
  }
}
