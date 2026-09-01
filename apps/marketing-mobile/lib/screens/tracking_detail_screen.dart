import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../state/tracking_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';

class TrackingDetailScreen extends StatefulWidget {
  final NavController nav;
  final TrackingController tracking;
  const TrackingDetailScreen({
    super.key,
    required this.nav,
    required this.tracking,
  });

  @override
  State<TrackingDetailScreen> createState() => _TrackingDetailScreenState();
}

class _TrackingDetailScreenState extends State<TrackingDetailScreen> {
  bool _refreshing = false;
  bool _stopping = false;

  Future<void> _start() async {
    setState(() => _refreshing = true);
    final started = await widget.tracking.ensureStarted(force: true);
    if (!mounted) return;
    setState(() => _refreshing = false);
    if (started) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          widget.tracking.startIssueMessage ??
              'Sesi tracking belum dapat dimulai.',
        ),
      ),
    );
  }

  Future<void> _refresh() async {
    setState(() => _refreshing = true);
    await widget.tracking.refreshNow();
    if (!mounted) return;
    setState(() => _refreshing = false);
    if (!widget.tracking.hasFreshSyncedLocation) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.tracking.trackingIssue ??
                'Titik lokasi belum diterima server. Coba lagi beberapa saat.',
          ),
        ),
      );
    }
  }

  Future<void> _stop() async {
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: AppColors.card,
        title: const Text('Hentikan Tracking'),
        content: const Text(
          'Lokasi tidak akan lagi dikirim sampai Anda memilih mulai tracking kembali.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () => Navigator.of(context).pop(true),
            child: const Text('Hentikan'),
          ),
        ],
      ),
    );
    if (confirmed != true || !mounted) return;

    setState(() => _stopping = true);
    final stopped = await widget.tracking.stop(manual: true);
    if (!mounted) return;
    setState(() => _stopping = false);
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          stopped
              ? 'Tracking telah dihentikan.'
              : 'Tracking belum dapat dihentikan. Periksa koneksi lalu coba lagi.',
        ),
      ),
    );
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
            style: TextStyle(
              color: ok ? AppColors.badgeGreenText : AppColors.badgeAmberText,
              fontSize: 13,
              fontWeight: FontWeight.w700,
            ),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final offline = widget.nav.offline;
    final tracking = widget.tracking;
    final active = tracking.isActive;
    final synced = tracking.hasFreshSyncedLocation;
    final issue = tracking.trackingIssue;
    final startedAt = tracking.session?.startedAt;
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(
              title: 'Detail Tracking',
              subtitle: 'Perjalanan Hari Ini',
              onBack: widget.nav.goBack,
            ),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    Container(
                      decoration: BoxDecoration(
                        color: AppColors.card,
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(color: AppColors.border),
                      ),
                      child: Column(
                        children: [
                          Container(
                            height: 4,
                            decoration: const BoxDecoration(
                              color: AppColors.gold,
                              borderRadius: BorderRadius.vertical(
                                top: Radius.circular(16),
                              ),
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    StatusDot(
                                      type: !active
                                          ? StatusDotType.offline
                                          : (synced
                                                ? StatusDotType.online
                                                : StatusDotType.warning),
                                    ),
                                    const SizedBox(width: 8),
                                    const Text(
                                      'Status Tracking',
                                      style: TextStyle(
                                        color: AppColors.mutedForeground,
                                        fontSize: 12,
                                      ),
                                    ),
                                    const Spacer(),
                                    StatusBadge(
                                      status: !active
                                          ? 'Tidak Aktif'
                                          : (synced
                                                ? 'Aktif'
                                                : 'Menunggu Sinkronisasi'),
                                      sm: true,
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 10),
                                Text(
                                  !active
                                      ? 'Sesi Belum Berjalan'
                                      : (synced
                                            ? 'Tracking Aktif'
                                            : 'GPS Belum Tersinkron'),
                                  style: const TextStyle(
                                    color: AppColors.foreground,
                                    fontSize: 18,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  !active
                                      ? 'Sesi tracking hari ini belum berhasil dimulai.'
                                      : (synced
                                            ? 'Titik lokasi terbaru sudah diterima server.'
                                            : issue ??
                                                  'Menunggu titik GPS pertama diterima server.'),
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
                    GridView.count(
                      crossAxisCount: 2,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      mainAxisSpacing: 10,
                      crossAxisSpacing: 10,
                      childAspectRatio: 2.0,
                      children: [
                        _indicator(
                          'GPS',
                          synced ? 'Aktif' : 'Belum Terkirim',
                          synced,
                        ),
                        _indicator(
                          'Internet',
                          offline ? 'Terputus' : 'Tersambung',
                          !offline,
                        ),
                        _indicator('Izin Lokasi', 'Diizinkan', true),
                        _indicator(
                          'Sinkronisasi',
                          synced
                              ? 'Tersinkron'
                              : (offline ? 'Menunggu' : 'Belum Terkirim'),
                          synced,
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    SectionCard(
                      title: 'Informasi Perjalanan',
                      children: [
                        InfoRow(
                          label: 'Waktu Mulai',
                          value: startedAt != null
                              ? DateFormat('HH:mm').format(startedAt)
                              : '-',
                        ),
                        InfoRow(
                          label: 'Lokasi Terakhir',
                          value: tracking.lastSyncedLocationLabel ?? '-',
                        ),
                        InfoRow(
                          label: 'Diperbarui',
                          value: tracking.lastSyncedAt != null
                              ? DateFormat(
                                  'HH:mm',
                                ).format(tracking.lastSyncedAt!)
                              : '-',
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.all(12),
                      decoration: BoxDecoration(
                        color: AppColors.badgeBlueBg,
                        borderRadius: BorderRadius.circular(12),
                      ),
                      child: Row(
                        children: const [
                          Icon(
                            LucideIcons.shield,
                            size: 16,
                            color: AppColors.badgeBlueText,
                          ),
                          SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Titik lokasi dikirim berkala selama GPS, izin lokasi, dan koneksi tersedia.',
                              style: TextStyle(
                                color: AppColors.badgeBlueText,
                                fontSize: 12,
                              ),
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
                          Icon(
                            active ? LucideIcons.refreshCw : LucideIcons.play,
                            size: 16,
                            color: AppColors.primaryForeground,
                          ),
                          SizedBox(width: 8),
                          Text(active ? 'Perbarui Lokasi' : 'Mulai Tracking'),
                        ],
                      ),
                    ),
                    if (active) ...[
                      const SizedBox(height: 10),
                      PrimaryButton(
                        danger: true,
                        loading: _stopping,
                        onPressed: _stopping ? null : _stop,
                        child: const Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(LucideIcons.square, size: 16),
                            SizedBox(width: 8),
                            Text('Hentikan Tracking'),
                          ],
                        ),
                      ),
                    ],
                    if (offline) ...[
                      const SizedBox(height: 10),
                      SecondaryButton(
                        onPressed: () =>
                            widget.nav.navigate(AppScreen.syncStatus),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: const [
                            Icon(
                              LucideIcons.cloud,
                              size: 16,
                              color: AppColors.foreground,
                            ),
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
