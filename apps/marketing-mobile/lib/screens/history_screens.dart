import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lucide_icons/lucide_icons.dart';

import '../api/api_client.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/tracking_service.dart';
import '../state/auth_controller.dart';
import '../state/tracking_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/buttons.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';

class HistoryScreen extends StatelessWidget {
  final NavController nav;
  final AuthController? auth;
  final TrackingController tracking;

  const HistoryScreen({
    super.key,
    required this.nav,
    this.auth,
    required this.tracking,
  });

  @override
  Widget build(BuildContext context) {
    final active = tracking.isActive;
    final startedAt = tracking.session?.startedAt;
    final area = auth?.user?.marketing?.area ?? '-';

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            Text(
              area.toUpperCase(),
              style: const TextStyle(
                color: AppColors.goldSoft,
                fontSize: 11,
                fontWeight: FontWeight.w700,
                letterSpacing: 1,
              ),
            ),
            const SizedBox(height: 2),
            const Text(
              'Riwayat Aktivitas',
              style: TextStyle(
                color: AppColors.foreground,
                fontSize: 20,
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 16),
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: AppColors.card,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: AppColors.border),
              ),
              child: Row(
                children: [
                  Icon(
                    active ? LucideIcons.navigation : LucideIcons.mapPinOff,
                    color: active
                        ? AppColors.success
                        : AppColors.mutedForeground,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          active ? 'Tracking Aktif' : 'Tracking Tidak Aktif',
                          style: const TextStyle(
                            color: AppColors.foreground,
                            fontSize: 14,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 2),
                        Text(
                          startedAt == null
                              ? 'Belum ada sesi hari ini.'
                              : 'Mulai ${DateFormat('HH:mm').format(startedAt)} · ${formatDistanceKm(tracking.distanceMeters)}',
                          style: const TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 12,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),
            _HistoryLink(
              icon: LucideIcons.map,
              label: 'Riwayat Perjalanan',
              onTap: () => nav.navigate(AppScreen.trackingHistory),
            ),
            const SizedBox(height: 10),
            _HistoryLink(
              icon: LucideIcons.clipboardList,
              label: 'Riwayat Laporan Operasional',
              onTap: () => nav.navigate(AppScreen.operationalReportHistory),
            ),
            const SizedBox(height: 10),
            _HistoryLink(
              icon: LucideIcons.refreshCw,
              label: 'Status Sinkronisasi',
              onTap: () => nav.navigate(AppScreen.syncStatus),
            ),
          ],
        ),
      ),
    );
  }
}

class _HistoryLink extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  const _HistoryLink({
    required this.icon,
    required this.label,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(8),
        child: Ink(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 16),
          decoration: BoxDecoration(
            color: AppColors.card,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: AppColors.border),
          ),
          child: Row(
            children: [
              Icon(icon, color: AppColors.gold, size: 18),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  label,
                  style: const TextStyle(
                    color: AppColors.foreground,
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
              const Icon(
                LucideIcons.chevronRight,
                color: AppColors.disabled,
                size: 18,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class JourneyDetailScreen extends StatefulWidget {
  final NavController nav;
  final TrackingService? trackingService;

  const JourneyDetailScreen({
    super.key,
    required this.nav,
    this.trackingService,
  });

  @override
  State<JourneyDetailScreen> createState() => _JourneyDetailScreenState();
}

class _JourneyDetailScreenState extends State<JourneyDetailScreen> {
  late final TrackingService _service =
      widget.trackingService ?? TrackingService();
  bool _loading = true;
  String? _error;
  TrackingSession? _session;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final id = int.tryParse(widget.nav.current.params?.journeyId ?? '');
    if (id == null) {
      setState(() {
        _loading = false;
        _error = 'Perjalanan tidak ditemukan.';
      });
      return;
    }

    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final session = await _service.detail(id);
      if (!mounted) return;
      setState(() {
        _session = session;
        _loading = false;
      });
    } on ApiException catch (error) {
      if (!mounted) return;
      setState(() {
        _error = error.message;
        _loading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(
              title: 'Detail Perjalanan',
              subtitle: _session?.startedAt == null
                  ? null
                  : DateFormat(
                      'd MMM yyyy',
                      'id_ID',
                    ).format(_session!.startedAt!),
              onBack: widget.nav.goBack,
            ),
            Expanded(child: _buildBody()),
          ],
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (_loading) {
      return const Center(
        child: CircularProgressIndicator(color: AppColors.gold),
      );
    }
    if (_error != null || _session == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _error ?? 'Perjalanan tidak ditemukan.',
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: AppColors.mutedForeground,
                  fontSize: 13,
                ),
              ),
              const SizedBox(height: 16),
              SecondaryButton(onPressed: _load, child: const Text('Coba Lagi')),
            ],
          ),
        ),
      );
    }

    final session = _session!;
    final pointCounts = <String, int>{};
    for (final point in session.points) {
      pointCounts[point.pointType] = (pointCounts[point.pointType] ?? 0) + 1;
    }
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: AppColors.card,
              borderRadius: BorderRadius.circular(8),
              border: Border.all(color: AppColors.border),
            ),
            child: Row(
              children: [
                StatusBadge(status: session.status),
                const Spacer(),
                const Icon(
                  LucideIcons.navigation,
                  color: AppColors.gold,
                  size: 20,
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          GridView.count(
            crossAxisCount: 2,
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            mainAxisSpacing: 10,
            crossAxisSpacing: 10,
            childAspectRatio: 1.8,
            children: [
              _statTile(
                'Waktu',
                '${session.startedAt == null ? '-' : DateFormat('HH:mm').format(session.startedAt!)} - ${session.endedAt == null ? 'Sekarang' : DateFormat('HH:mm').format(session.endedAt!)}',
              ),
              _statTile('Jarak', formatDistanceKm(session.distanceMeters)),
              _statTile('Kunjungan', '${session.visitCount}'),
              _statTile('Jumlah Titik GPS', '${session.points.length}'),
            ],
          ),
          if (pointCounts.isNotEmpty) ...[
            const SizedBox(height: 16),
            SectionCard(
              title: 'Ringkasan Titik GPS',
              children: pointCounts.entries
                  .map(
                    (entry) => Padding(
                      padding: const EdgeInsets.only(bottom: 8),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text(
                            entry.key,
                            style: const TextStyle(
                              color: AppColors.foreground,
                              fontSize: 13,
                            ),
                          ),
                          Text(
                            '${entry.value}',
                            style: const TextStyle(
                              color: AppColors.mutedForeground,
                              fontSize: 13,
                              fontFamily: 'monospace',
                            ),
                          ),
                        ],
                      ),
                    ),
                  )
                  .toList(),
            ),
          ],
        ],
      ),
    );
  }

  Widget _statTile(String label, String value) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: AppColors.card,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: const TextStyle(
              color: AppColors.mutedForeground,
              fontSize: 11,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            value,
            style: const TextStyle(
              color: AppColors.foreground,
              fontSize: 13,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }
}
