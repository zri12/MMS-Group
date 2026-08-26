import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../api/api_client.dart';
import '../navigation/nav_controller.dart';
import '../services/schedule_service.dart';
import '../theme/app_colors.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';

/// NOTE: JadwalScreen is kept fully built and routed (per "hide, don't
/// delete") but has no live entry point from the current nav flow -- not
/// linked from Dashboard or anywhere else. See task.md. Wired to the real
/// `/api/v1/schedules*` endpoints (BACKEND_INTEGRATION_TASKS.md Phase 3).
/// `SetoranScreen` was removed per Phase 0 DEC-021/022 — its value is the
/// `storting` field on `DailyInputScreen`, not a standalone screen.
class JadwalScreen extends StatefulWidget {
  final NavController nav;
  final ScheduleService? service;
  const JadwalScreen({super.key, required this.nav, this.service});

  @override
  State<JadwalScreen> createState() => _JadwalScreenState();
}

class _JadwalScreenState extends State<JadwalScreen> {
  late final ScheduleService _service = widget.service ?? ScheduleService();

  bool _loading = true;
  String? _error;
  List<Schedule> _schedules = [];
  final Set<int> _updating = {};

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
      final schedules = await _service.listToday();
      if (!mounted) return;
      setState(() {
        _schedules = schedules;
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

  Future<void> _updateStatus(Schedule schedule, String newStatus) async {
    setState(() => _updating.add(schedule.id));
    try {
      final updated = await _service.updateStatus(schedule.id, newStatus);
      if (!mounted) return;
      setState(() {
        final i = _schedules.indexWhere((s) => s.id == schedule.id);
        if (i != -1) _schedules[i] = updated;
        _updating.remove(schedule.id);
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _updating.remove(schedule.id));
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.message)));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(title: 'Jadwal Marketing', subtitle: 'Jadwal Anda hari ini', onBack: widget.nav.goBack),
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
    if (_schedules.isEmpty) {
      return const EmptyState(icon: LucideIcons.calendarDays, title: 'Belum Ada Jadwal', desc: 'Tidak ada jadwal kunjungan hari ini.');
    }
    return RefreshIndicator(
      onRefresh: _load,
      color: AppColors.gold,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _schedules.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, i) {
          final s = _schedules[i];
          final transitions = ScheduleService.allowedTransitions[s.status] ?? const [];
          final isUpdating = _updating.contains(s.id);
          return Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(color: AppColors.card, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.border)),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(children: [
                  Expanded(child: Text(s.consumerName ?? s.agenda ?? '—', style: const TextStyle(color: AppColors.foreground, fontSize: 14, fontWeight: FontWeight.w700))),
                  StatusBadge(status: s.status, sm: true),
                ]),
                const SizedBox(height: 2),
                Text(s.agenda ?? '—', style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
                const SizedBox(height: 10),
                GridView.count(
                  crossAxisCount: 3,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  mainAxisSpacing: 6,
                  crossAxisSpacing: 6,
                  childAspectRatio: 2.2,
                  children: [
                    _field('Hari', s.day),
                    _field('Tanggal', s.date),
                    _field('Resort', s.resort),
                    _field('Jam Mulai', s.startTime),
                    _field('Jam Selesai', s.endTime),
                    _field('Area Kerja', s.area),
                  ],
                ),
                if (s.destination != null) ...[
                  const SizedBox(height: 8),
                  Row(children: [
                    const Icon(LucideIcons.mapPin, size: 12, color: AppColors.mutedForeground),
                    const SizedBox(width: 4),
                    Expanded(child: Text(s.destination!, style: const TextStyle(color: AppColors.mutedForeground, fontSize: 11), overflow: TextOverflow.ellipsis)),
                  ]),
                ],
                if (transitions.isNotEmpty) ...[
                  const SizedBox(height: 10),
                  Row(
                    children: transitions
                        .map((t) => Padding(
                              padding: const EdgeInsets.only(right: 8),
                              child: OutlinedButton(
                                onPressed: isUpdating ? null : () => _updateStatus(s, t),
                                style: OutlinedButton.styleFrom(
                                  side: const BorderSide(color: AppColors.borderGold),
                                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                  minimumSize: Size.zero,
                                  tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                                ),
                                child: Text(t, style: const TextStyle(color: AppColors.foreground, fontSize: 11, fontWeight: FontWeight.w600)),
                              ),
                            ))
                        .toList(),
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
        Text(label, style: const TextStyle(color: AppColors.disabled, fontSize: 10)),
        Text(value, style: const TextStyle(color: AppColors.foreground, fontSize: 12, fontWeight: FontWeight.w600)),
      ],
    );
  }
}
