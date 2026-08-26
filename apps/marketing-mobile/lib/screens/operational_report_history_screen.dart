import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../api/api_client.dart';
import '../navigation/nav_controller.dart';
import '../services/operational_report_service.dart';
import '../theme/app_colors.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/buttons.dart';

/// "Rekap Operasional" dashboard shortcut, per Phase 0 DEC-021/022: a plain
/// history of the marketing user's own submitted Laporan Operasional
/// (`GET /api/v1/operational-reports`, already scoped server-side to the
/// caller) — NOT a formula-based rekap and NOT cross-marketing (that
/// aggregate view is web-admin-only). See BACKEND_INTEGRATION_TASKS.md
/// Phase 6.
///
/// Hosts the entry point into `RekapTunaiScreen` (a "Lihat Rekap Tunai"
/// link, not a Dashboard shortcut — DEC-021/022 explicitly forbids Laporan
/// Tunai from ever appearing as a Dashboard shortcut).
class OperationalReportHistoryScreen extends StatefulWidget {
  final NavController nav;
  final OperationalReportService? service;
  const OperationalReportHistoryScreen({
    super.key,
    required this.nav,
    this.service,
  });

  @override
  State<OperationalReportHistoryScreen> createState() =>
      _OperationalReportHistoryScreenState();
}

class _OperationalReportHistoryScreenState
    extends State<OperationalReportHistoryScreen> {
  late final OperationalReportService _service =
      widget.service ?? OperationalReportService();
  bool _loading = true;
  String? _error;
  List<OperationalReport> _reports = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final reports = await _service.list();
      if (!mounted) return;
      setState(() {
        _reports = reports;
        _loading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.message;
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
              title: 'Rekap Operasional',
              subtitle: 'Riwayat laporan operasional Anda',
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
    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _error!,
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
    if (_reports.isEmpty) {
      return const EmptyState(
        icon: LucideIcons.clipboardList,
        title: 'Belum Ada Laporan',
        desc: 'Belum ada laporan operasional yang tersimpan.',
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      color: AppColors.gold,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _reports.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, i) {
          final r = _reports[i];
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
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      '${r.day ?? '-'}, ${r.date ?? '-'}',
                      style: const TextStyle(
                        color: AppColors.foreground,
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                    Text(
                      r.resort ?? '-',
                      style: const TextStyle(
                        color: AppColors.mutedForeground,
                        fontSize: 11,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 10),
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  mainAxisSpacing: 6,
                  crossAxisSpacing: 6,
                  childAspectRatio: 3.4,
                  children: [
                    _field('Storting', formatRupiah(r.storting)),
                    _field('Drop', formatRupiah(r.drop)),
                    _field('Total Target', formatRupiah(r.totalTargetAmount)),
                    _field('Tabungan Keluar', formatRupiah(r.withdrawalSaving)),
                  ],
                ),
                if (r.notes?.isNotEmpty == true) ...[
                  const SizedBox(height: 8),
                  Text(
                    r.notes!,
                    style: const TextStyle(
                      color: AppColors.mutedForeground,
                      fontSize: 12,
                    ),
                  ),
                ],
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _field(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: const TextStyle(color: AppColors.disabled, fontSize: 10),
        ),
        Text(
          value,
          style: const TextStyle(
            color: AppColors.foreground,
            fontSize: 12,
            fontWeight: FontWeight.w600,
            fontFamily: 'monospace',
          ),
        ),
      ],
    );
  }
}
