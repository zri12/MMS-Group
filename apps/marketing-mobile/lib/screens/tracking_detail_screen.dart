import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../navigation/nav_controller.dart';
import '../state/tracking_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';

class TrackingDetailScreen extends StatefulWidget {
  final NavController nav;
  final TrackingController tracking;
  const TrackingDetailScreen({super.key, required this.nav, required this.tracking});

  @override
  State<TrackingDetailScreen> createState() => _TrackingDetailScreenState();
}

class _TrackingDetailScreenState extends State<TrackingDetailScreen> {
  bool _refreshing = false;

  Future<void> _start() async {
    setState(() => _refreshing = true);
    final started = await widget.tracking.ensureStarted();
    if (!mounted) return;
    setState(() => _refreshing = false);
    if (started) return;
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(widget.tracking.startIssueMessage ?? 'Sesi tracking belum dapat dimulai.')));
  }

  Future<void> _refresh() async {
    setState(() => _refreshing = true);
    await widget.tracking.refreshNow();
    if (!mounted) return;
    setState(() => _refreshing = false);
  }

  Widget _indicator(String label, String value, bool ok) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: ok ? AppColors.badgeGreenBg : AppColors.badgeAmberBg,
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(color: AppColors.mutedForeground, fontSize: 11)),
          const SizedBox(height: 4),
          Text(value, style: TextStyle(color: ok ? AppColors.badgeGreenText : AppColors.badgeAmberText, fontSize: 13, fontWeight: FontWeight.w700)),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final offline = widget.nav.offline;
    final tracking = widget.tracking;
    final active = tracking.isActive;
    final startedAt = tracking.session?.startedAt;
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(title: 'Detail Tracking', subtitle: 'Perjalanan Hari Ini', onBack: widget.nav.goBack),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Container(
                      decoration: BoxDecoration(color: AppColors.card, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.border)),
                      child: Column(
                        children: [
                          Container(height: 4, decoration: const BoxDecoration(color: AppColors.gold, borderRadius: BorderRadius.vertical(top: Radius.circular(16)))),
                          Padding(
                            padding: const EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(children: [
                                  StatusDot(type: !active ? StatusDotType.offline : (offline ? StatusDotType.warning : StatusDotType.online)),
                                  const SizedBox(width: 8),
                                  const Text('Status Tracking', style: TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
                                  const Spacer(),
                                  StatusBadge(status: !active ? 'Tidak Aktif' : (offline ? 'Menunggu Sinkronisasi' : 'Aktif'), sm: true),
                                ]),
                                const SizedBox(height: 10),
                                Text(
                                  !active ? 'Sesi Belum Berjalan' : (offline ? 'Rekam Offline' : 'Tracking Aktif'),
                                  style: const TextStyle(color: AppColors.foreground, fontSize: 18, fontWeight: FontWeight.w700),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  !active
                                      ? 'Sesi tracking hari ini belum berhasil dimulai.'
                                      : (offline ? 'Titik lokasi tetap direkam dan akan disinkronkan saat online.' : 'Titik lokasi Anda sedang direkam secara berkala.'),
                                  style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12),
                                ),
                              ],
                            ),
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
                      childAspectRatio: 2.0,
                      children: [
                        _indicator('GPS', tracking.lastLocationLabel != null ? 'Aktif' : 'Belum Ada Titik', tracking.lastLocationLabel != null),
                        _indicator('Internet', offline ? 'Terputus' : 'Tersambung', !offline),
                        _indicator('Izin Lokasi', 'Diizinkan', true),
                        _indicator('Sinkronisasi', offline ? 'Menunggu' : 'Tersinkron', !offline),
                      ],
                    ),
                    const SizedBox(height: 16),
                    SectionCard(
                      title: 'Informasi Perjalanan',
                      children: [
                        InfoRow(label: 'Waktu Mulai', value: startedAt != null ? DateFormat('HH:mm').format(startedAt) : '-'),
                        InfoRow(label: 'Lokasi Terakhir', value: tracking.lastLocationLabel ?? '-'),
                        InfoRow(label: 'Diperbarui', value: tracking.lastSampleAt != null ? DateFormat('HH:mm').format(tracking.lastSampleAt!) : '-'),
                      ],
                    ),
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(color: AppColors.badgeBlueBg, borderRadius: BorderRadius.circular(12)),
                      child: Row(
                        children: const [
                          Icon(LucideIcons.shield, size: 16, color: AppColors.badgeBlueText),
                          SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Titik lokasi dicatat secara berkala selagi aplikasi terbuka. Tracking berhenti bila aplikasi ditutup atau dijalankan di latar belakang.',
                              style: TextStyle(color: AppColors.badgeBlueText, fontSize: 12),
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),
                    PrimaryButton(
                      onPressed: active ? _refresh : _start,
                      loading: _refreshing,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(active ? LucideIcons.refreshCw : LucideIcons.play, size: 16, color: AppColors.primaryForeground),
                          SizedBox(width: 8),
                          Text(active ? 'Perbarui Lokasi' : 'Mulai Tracking'),
                        ],
                      ),
                    ),
                    if (offline) ...[
                      const SizedBox(height: 10),
                      SecondaryButton(
                        onPressed: () {},
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: const [
                            Icon(LucideIcons.cloud, size: 16, color: AppColors.foreground),
                            SizedBox(width: 8),
                            Text('Coba Sinkronkan'),
                          ],
                        ),
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
