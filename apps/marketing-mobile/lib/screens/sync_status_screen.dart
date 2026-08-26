import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../data/local/sync_dispatcher.dart';
import '../data/local/sync_queue.dart';
import '../navigation/nav_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';

class SyncStatusScreen extends StatefulWidget {
  final NavController nav;
  final SyncQueueStore? store;
  final SyncDispatcher? dispatcher;
  const SyncStatusScreen({super.key, required this.nav, this.store, this.dispatcher});

  @override
  State<SyncStatusScreen> createState() => _SyncStatusScreenState();
}

class _SyncStatusScreenState extends State<SyncStatusScreen> {
  late final SyncQueueStore _store = widget.store ?? createDefaultSyncQueueStore();
  late final SyncDispatcher _dispatcher = widget.dispatcher ?? SyncDispatcher();

  bool _loading = true;
  bool _syncing = false;
  List<SyncQueueEntry> _pending = [];
  int _failedCount = 0;
  DateTime? _lastChecked;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final all = await _store.listAll();
    if (!mounted) return;
    setState(() {
      _pending = all.where((e) => e.status != SyncQueueStatus.terkirim).toList();
      _failedCount = all.where((e) => e.status == SyncQueueStatus.gagal).length;
      _lastChecked = DateTime.now();
      _loading = false;
    });
  }

  Future<void> _sync() async {
    setState(() => _syncing = true);
    await _dispatcher.dispatchAll(_store);
    await _load();
    if (!mounted) return;
    setState(() => _syncing = false);
  }

  Widget _counter(String label, String value) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(color: AppColors.card, borderRadius: BorderRadius.circular(12), border: Border.all(color: AppColors.border)),
      child: Column(
        children: [
          Text(value, style: const TextStyle(color: AppColors.foreground, fontSize: 17, fontWeight: FontWeight.w700, fontFamily: 'monospace')),
          Text(label, style: const TextStyle(color: AppColors.mutedForeground, fontSize: 10)),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final offline = widget.nav.offline;
    final synced = !_loading && _pending.isEmpty;
    final headline = _loading
        ? 'Memeriksa Antrean...'
        : _syncing
            ? 'Sedang Sinkronisasi...'
            : synced
                ? 'Semua Data Tersinkron'
                : offline
                    ? 'Tidak Ada Koneksi'
                    : 'Data Menunggu Sinkronisasi';
    final icon = _syncing
        ? LucideIcons.refreshCw
        : synced
            ? LucideIcons.checkCircle
            : offline
                ? LucideIcons.cloudOff
                : LucideIcons.cloud;

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(title: 'Status Sinkronisasi', onBack: widget.nav.goBack),
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
                          Container(
                            height: 4,
                            decoration: BoxDecoration(
                              color: synced ? AppColors.success : (offline ? AppColors.warning : AppColors.gold),
                              borderRadius: const BorderRadius.vertical(top: Radius.circular(16)),
                            ),
                          ),
                          Padding(
                            padding: const EdgeInsets.all(20),
                            child: Column(
                              children: [
                                Icon(icon, color: synced ? AppColors.success : (offline ? AppColors.warning : AppColors.gold), size: 36),
                                const SizedBox(height: 12),
                                Text(headline, style: const TextStyle(color: AppColors.foreground, fontSize: 16, fontWeight: FontWeight.w700)),
                                const SizedBox(height: 4),
                                Text(
                                  _lastChecked == null ? '—' : 'Terakhir diperiksa ${DateFormat('HH:mm').format(_lastChecked!)}',
                                  style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(child: _counter('Menunggu', '${_pending.length}')),
                        const SizedBox(width: 10),
                        Expanded(child: _counter('Gagal', '$_failedCount')),
                        const SizedBox(width: 10),
                        Expanded(child: _counter('Status', offline ? 'Offline' : 'Online')),
                      ],
                    ),
                    if (!synced && _pending.isNotEmpty) ...[
                      const SizedBox(height: 16),
                      SectionCard(
                        title: 'Antrean Sinkronisasi',
                        children: _pending
                            .map((entry) => Padding(
                                  padding: const EdgeInsets.only(bottom: 10),
                                  child: Row(
                                    children: [
                                      const Icon(LucideIcons.fileText, size: 16, color: AppColors.mutedForeground),
                                      const SizedBox(width: 10),
                                      Expanded(child: Text(entry.entityType, style: const TextStyle(color: AppColors.foreground, fontSize: 13))),
                                      Text(DateFormat('HH:mm').format(entry.createdAt), style: const TextStyle(color: AppColors.disabled, fontSize: 11, fontFamily: 'monospace')),
                                      const SizedBox(width: 8),
                                      StatusBadge(status: entry.status.label, sm: true),
                                    ],
                                  ),
                                ))
                            .toList(),
                      ),
                    ],
                    if (!_loading && synced) ...[
                      const SizedBox(height: 16),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(color: AppColors.badgeGreenBg, borderRadius: BorderRadius.circular(14)),
                        child: Row(children: const [
                          Icon(LucideIcons.checkCircle, color: AppColors.badgeGreenText, size: 20),
                          SizedBox(width: 10),
                          Expanded(child: Text('Tidak ada data yang menunggu dikirim.', style: TextStyle(color: AppColors.badgeGreenText, fontWeight: FontWeight.w700))),
                        ]),
                      ),
                    ],
                    if (offline && !synced) ...[
                      const SizedBox(height: 16),
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(color: AppColors.badgeAmberBg, borderRadius: BorderRadius.circular(12)),
                        child: const Text('Tidak ada koneksi internet. Data akan dikirim otomatis saat online.', style: TextStyle(color: AppColors.warning, fontSize: 12)),
                      ),
                    ],
                    if (!synced) ...[
                      const SizedBox(height: 20),
                      PrimaryButton(onPressed: _sync, loading: _syncing, child: const Text('Sinkronkan Sekarang')),
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
