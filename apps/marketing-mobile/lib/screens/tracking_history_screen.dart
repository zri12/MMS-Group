import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../api/api_client.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/tracking_service.dart';
import '../theme/app_colors.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';

/// "Riwayat Perjalanan" — a plain list of the marketing user's own past
/// tracking sessions (`GET /api/v1/tracking/sessions`, already scoped
/// server-side to the caller). `TrackingService.history()`/`detail()` were
/// built in the Tracking phase but had no consuming screen until now — see
/// BACKEND_INTEGRATION_TASKS.md's Tracking section. Tapping a session opens
/// `JourneyDetailScreen` (`GET /tracking/sessions/{id}`), which previously
/// had no real navigation path leading to it.
class TrackingHistoryScreen extends StatefulWidget {
  final NavController nav;
  final TrackingService? service;
  const TrackingHistoryScreen({super.key, required this.nav, this.service});

  @override
  State<TrackingHistoryScreen> createState() => _TrackingHistoryScreenState();
}

class _TrackingHistoryScreenState extends State<TrackingHistoryScreen> {
  late final TrackingService _service = widget.service ?? TrackingService();
  bool _loading = true;
  String? _error;
  List<TrackingSession> _sessions = [];

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
      final sessions = await _service.history();
      if (!mounted) return;
      setState(() {
        _sessions = sessions;
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
            PageHeader(title: 'Riwayat Perjalanan', subtitle: 'Sesi tracking Anda', onBack: widget.nav.goBack),
            Expanded(child: _buildBody()),
          ],
        ),
      ),
    );
  }

  Widget _buildBody() {
    if (_loading) {
      return const Center(child: CircularProgressIndicator(color: AppColors.gold));
    }
    if (_error != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(_error!, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.mutedForeground, fontSize: 13)),
              const SizedBox(height: 16),
              SecondaryButton(onPressed: _load, child: const Text('Coba Lagi')),
            ],
          ),
        ),
      );
    }
    if (_sessions.isEmpty) {
      return const EmptyState(icon: LucideIcons.navigation, title: 'Belum Ada Riwayat', desc: 'Belum ada sesi tracking yang tersimpan.');
    }
    return RefreshIndicator(
      onRefresh: _load,
      color: AppColors.gold,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _sessions.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, i) {
          final s = _sessions[i];
          return GestureDetector(
            onTap: () => widget.nav.navigate(AppScreen.journeyDetail, NavParams(journeyId: s.id.toString())),
            child: Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(color: AppColors.card, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.border)),
              child: Row(
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Text(s.startedAt != null ? DateFormat('d MMM yyyy', 'id_ID').format(s.startedAt!) : '-', style: const TextStyle(color: AppColors.foreground, fontSize: 13, fontWeight: FontWeight.w700)),
                            const SizedBox(width: 8),
                            StatusBadge(status: s.status, sm: true),
                          ],
                        ),
                        const SizedBox(height: 6),
                        Text(
                          '${s.startedAt != null ? DateFormat('HH:mm').format(s.startedAt!) : '-'} - ${s.endedAt != null ? DateFormat('HH:mm').format(s.endedAt!) : 'Sekarang'}',
                          style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12, fontFamily: 'monospace'),
                        ),
                        const SizedBox(height: 4),
                        Text('${formatDistanceKm(s.distanceMeters)} · ${s.visitCount} kunjungan', style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
                      ],
                    ),
                  ),
                  const Icon(LucideIcons.chevronRight, size: 18, color: AppColors.disabled),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
