import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart' show XFile;
import 'package:lucide_icons/lucide_icons.dart';
import '../api/api_client.dart';
import '../data/local/attachment_store.dart';
import '../data/local/offline_submit.dart';
import '../data/local/sync_queue.dart';
import '../data/form_options.dart' show consumerStatusOptions;
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/photo_service.dart';
import '../services/prospect_service.dart';
import '../services/visit_report_service.dart';
import '../state/auth_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';
import '../widgets/fields.dart';

const _reportFilters = ['Semua', ...visitResultOptions];

/// Wired to the real `/api/v1/visit-reports*` endpoints
/// (BACKEND_INTEGRATION_TASKS.md Phase 3). The "select consumer" step now
/// lists real `Prospect` records (Phase 0 DEC-021/022 confirmed visit
/// reports reference a Prospect, not the old unified `Consumer` mock model).
class ReportsScreen extends StatefulWidget {
  final NavController nav;
  final VisitReportService? service;
  const ReportsScreen({super.key, required this.nav, this.service});

  @override
  State<ReportsScreen> createState() => _ReportsScreenState();
}

class _ReportsScreenState extends State<ReportsScreen> {
  late final VisitReportService _service =
      widget.service ?? VisitReportService();
  String _filter = 'Semua';

  bool _loading = true;
  String? _error;
  List<VisitReport> _reports = [];

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
      final reports = await _service.list(
        result: _filter == 'Semua' ? null : _filter,
      );
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
            Container(
              color: AppColors.backgroundSecondary,
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'AKTIVITAS LAPANGAN',
                            style: TextStyle(
                              color: AppColors.goldSoft,
                              fontSize: 11,
                              fontWeight: FontWeight.w700,
                              letterSpacing: 1,
                            ),
                          ),
                          SizedBox(height: 2),
                          Text(
                            'Laporan Kunjungan',
                            style: TextStyle(
                              color: AppColors.foreground,
                              fontSize: 20,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ],
                      ),
                      ElevatedButton.icon(
                        onPressed: () =>
                            widget.nav.navigate(AppScreen.newReport),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: AppColors.gold,
                          foregroundColor: AppColors.primaryForeground,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                        icon: const Icon(LucideIcons.plus, size: 16),
                        label: const Text(
                          'Buat',
                          style: TextStyle(fontWeight: FontWeight.w700),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: AppColors.card,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: Row(
                      children: [
                        const Icon(
                          LucideIcons.fileText,
                          color: AppColors.gold,
                          size: 18,
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            '${_reports.length} laporan kunjungan',
                            style: const TextStyle(
                              color: AppColors.mutedForeground,
                              fontSize: 12,
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 14),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: Row(
                      children: _reportFilters
                          .map(
                            (f) => Padding(
                              padding: const EdgeInsets.only(right: 8),
                              child: AppFilterChip(
                                label: f,
                                active: f == _filter,
                                onTap: () {
                                  setState(() => _filter = f);
                                  _load();
                                },
                              ),
                            ),
                          )
                          .toList(),
                    ),
                  ),
                ],
              ),
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
        desc: 'Laporan yang cocok tidak ditemukan.',
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
          return GestureDetector(
            onTap: () => widget.nav.navigate(
              AppScreen.reportDetail,
              NavParams(reportId: r.id.toString()),
            ),
            child: Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: AppColors.card,
                borderRadius: BorderRadius.circular(14),
                border: Border.all(color: AppColors.border),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: BoxDecoration(
                      color: AppColors.cardElevated,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: const Icon(
                      LucideIcons.image,
                      color: AppColors.disabled,
                      size: 18,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Expanded(
                              child: Text(
                                r.prospectName ?? 'Prospek #${r.prospectId}',
                                style: const TextStyle(
                                  color: AppColors.foreground,
                                  fontSize: 14,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ),
                            StatusBadge(status: r.visitResult, sm: true),
                          ],
                        ),
                        const SizedBox(height: 4),
                        Text(
                          r.visitPurpose ?? '-',
                          style: const TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 12,
                          ),
                        ),
                        const SizedBox(height: 6),
                        Row(
                          children: [
                            const Icon(
                              LucideIcons.mapPin,
                              size: 11,
                              color: AppColors.disabled,
                            ),
                            const SizedBox(width: 4),
                            Expanded(
                              child: Text(
                                '${r.locationAddress ?? '-'} · ${r.time ?? '-'}',
                                style: const TextStyle(
                                  color: AppColors.disabled,
                                  fontSize: 11,
                                ),
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

class ReportDetailScreen extends StatefulWidget {
  final NavController nav;
  final AuthController? auth;
  final VisitReportService? service;
  const ReportDetailScreen({
    super.key,
    required this.nav,
    this.auth,
    this.service,
  });

  @override
  State<ReportDetailScreen> createState() => _ReportDetailScreenState();
}

class _ReportDetailScreenState extends State<ReportDetailScreen> {
  late final VisitReportService _service =
      widget.service ?? VisitReportService();
  bool _loading = true;
  String? _error;
  VisitReport? _report;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final id = int.tryParse(widget.nav.current.params?.reportId ?? '');
    if (id == null) {
      setState(() {
        _loading = false;
        _error = 'Laporan tidak ditemukan.';
      });
      return;
    }
    try {
      final report = await _service.detail(id);
      if (!mounted) return;
      setState(() {
        _report = report;
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
              title: 'Detail Laporan',
              subtitle: _report != null
                  ? '${_report!.date ?? '-'} · ${_report!.time ?? '-'}'
                  : null,
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
    if (_error != null || _report == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _error ?? 'Laporan tidak ditemukan.',
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
    final report = _report!;
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: AppColors.card,
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: AppColors.border),
            ),
            child: Row(
              children: [
                StatusBadge(status: report.visitResult),
                const Spacer(),
                const Icon(
                  LucideIcons.clipboardList,
                  color: AppColors.gold,
                  size: 20,
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          SectionCard(
            title: 'Informasi Anggota',
            children: [
              InfoRow(
                label: 'Nama',
                value: report.prospectName ?? 'Prospek #${report.prospectId}',
              ),
              InfoRow(label: 'Lokasi', value: report.locationAddress ?? '-'),
            ],
          ),
          const SizedBox(height: 16),
          SectionCard(
            title: 'Hasil Kunjungan',
            children: [
              InfoRow(label: 'Hasil', value: report.visitResult),
              InfoRow(
                label: 'Status Prospek',
                value: report.prospectStatus ?? '-',
              ),
              const SizedBox(height: 8),
              Text(
                report.notes?.isNotEmpty == true
                    ? report.notes!
                    : 'Tidak ada catatan.',
                style: const TextStyle(
                  color: AppColors.mutedForeground,
                  fontSize: 13,
                ),
              ),
            ],
          ),
          if (report.photoUrl != null) ...[
            const SizedBox(height: 16),
            SectionCard(
              title: 'Foto Kunjungan',
              children: [
                ClipRRect(
                  borderRadius: BorderRadius.circular(12),
                  child: AspectRatio(
                    aspectRatio: 16 / 10,
                    child: Image.asset(
                      'assets/images/report-visit.jpg',
                      fit: BoxFit.cover,
                    ),
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  report.photoCaption ?? 'Dokumentasi kunjungan lapangan',
                  style: const TextStyle(
                    color: AppColors.mutedForeground,
                    fontSize: 12,
                  ),
                ),
              ],
            ),
          ],
          const SizedBox(height: 16),
          SectionCard(
            title: 'Info Pengiriman',
            children: [
              InfoRow(label: 'Tanggal', value: report.date ?? '-'),
              InfoRow(label: 'Waktu', value: report.time ?? '-'),
              InfoRow(
                label: 'Marketing',
                value: widget.auth?.user?.name ?? '-',
              ),
              InfoRow(label: 'Resort', value: report.resort ?? '-'),
            ],
          ),
        ],
      ),
    );
  }
}

enum _ReportStep { selectConsumer, visitResult, addPhoto, confirm, success }

class NewReportScreen extends StatefulWidget {
  final NavController nav;
  final AuthController? auth;
  final ProspectService? prospectService;
  final VisitReportService? service;
  final PhotoService? photoService;
  final SyncQueueStore? syncStore;
  final AttachmentStore? attachmentStore;
  const NewReportScreen({
    super.key,
    required this.nav,
    this.auth,
    this.prospectService,
    this.service,
    this.photoService,
    this.syncStore,
    this.attachmentStore,
  });

  @override
  State<NewReportScreen> createState() => _NewReportScreenState();
}

class _NewReportScreenState extends State<NewReportScreen> {
  late final ProspectService _prospectService =
      widget.prospectService ?? ProspectService();
  late final VisitReportService _service =
      widget.service ?? VisitReportService();
  late final PhotoService _photoService = widget.photoService ?? PhotoService();
  late final SyncQueueStore _syncStore =
      widget.syncStore ?? createDefaultSyncQueueStore();
  late final AttachmentStore _attachmentStore =
      widget.attachmentStore ?? createDefaultAttachmentStore();
  late _ReportStep _step;
  Prospect? _selected;
  final _search = TextEditingController();
  final _tujuan = TextEditingController();
  final _catatan = TextEditingController();
  final _keterangan = TextEditingController();
  String? _hasil;
  String? _status;
  XFile? _photo;
  bool _photoLoading = false;
  String? _photoError;
  bool _submitting = false;
  bool _initialized = false;
  bool _queued = false;
  String? _submitError;

  bool _loadingProspects = true;
  String? _prospectsError;
  List<Prospect> _prospects = [];

  void _init() {
    if (_initialized) return;
    final consumerId = int.tryParse(
      widget.nav.current.params?.consumerId ?? '',
    );
    _initialized = true;
    if (consumerId != null) {
      _step = _ReportStep.visitResult;
      _prospectService
          .detail(consumerId)
          .then((p) {
            if (mounted) setState(() => _selected = p);
          })
          .catchError((_) {});
    } else {
      _step = _ReportStep.selectConsumer;
    }
    _loadProspects();
  }

  Future<void> _loadProspects() async {
    try {
      final prospects = await _prospectService.list();
      if (!mounted) return;
      setState(() {
        _prospects = prospects;
        _loadingProspects = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _prospectsError = e.message;
        _loadingProspects = false;
      });
    }
  }

  Future<void> _pickPhoto(bool fromCamera) async {
    setState(() {
      _photoLoading = true;
      _photoError = null;
    });
    try {
      final file = fromCamera
          ? await _photoService.pickFromCamera()
          : await _photoService.pickFromGallery();
      if (!mounted) return;
      setState(() {
        _photoLoading = false;
        if (file != null) _photo = file;
      });
    } on PhotoServiceException catch (e) {
      if (!mounted) return;
      setState(() {
        _photoLoading = false;
        _photoError = e.message;
      });
    }
  }

  int get _stepIndex => switch (_step) {
    _ReportStep.selectConsumer => 1,
    _ReportStep.visitResult => 2,
    _ReportStep.addPhoto => 3,
    _ReportStep.confirm => 4,
    _ReportStep.success => 4,
  };

  Future<void> _submit() async {
    if (_selected == null || _hasil == null || _status == null) return;
    if (_tujuan.text.trim().isEmpty) {
      setState(() => _submitError = 'Tujuan kunjungan wajib diisi');
      return;
    }
    setState(() {
      _submitting = true;
      _submitError = null;
    });
    final now = DateTime.now();
    final localUuid = newLocalUuid();
    final day = const [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat',
      'Sabtu',
      'Minggu',
    ][now.weekday - 1];
    final date =
        '${now.year}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}';
    final time = apiTimeFormat(now);
    final payload = _service.buildCreatePayload(
      localUuid: localUuid,
      prospectId: _selected!.id,
      visitPurpose: _tujuan.text.trim(),
      visitResult: _hasil!,
      prospectStatus: _status,
      notes: _catatan.text.trim(),
      photoCaption: _keterangan.text.trim(),
      resort: 'Resort 3',
      day: day,
      date: date,
      time: time,
    );
    try {
      await submitAttachmentWithOfflineFallback(
        store: _syncStore,
        attachmentStore: _attachmentStore,
        entityType: 'visit_report',
        localUuid: localUuid,
        payload: payload,
        photos: {'photo': _photo},
        send: () => _service.create(
          localUuid: localUuid,
          prospectId: _selected!.id,
          visitPurpose: _tujuan.text.trim(),
          visitResult: _hasil!,
          prospectStatus: _status,
          notes: _catatan.text.trim(),
          photoCaption: _keterangan.text.trim(),
          photo: _photo,
          resort: 'Resort 3',
          day: day,
          date: date,
          time: time,
        ),
      );
      if (!mounted) return;
      setState(() {
        _submitting = false;
        _step = _ReportStep.success;
      });
    } on OfflineQueuedException {
      if (!mounted) return;
      setState(() {
        _submitting = false;
        _queued = true;
        _step = _ReportStep.success;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _submitting = false;
        _submitError = e.message;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    _init();
    if (_step == _ReportStep.success) return _buildSuccess();

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(
              title: 'Buat Laporan',
              onBack: () {
                if (_step == _ReportStep.selectConsumer) {
                  widget.nav.goBack();
                } else if (_step == _ReportStep.visitResult) {
                  setState(() => _step = _ReportStep.selectConsumer);
                } else if (_step == _ReportStep.addPhoto) {
                  setState(() => _step = _ReportStep.visitResult);
                } else {
                  setState(() => _step = _ReportStep.addPhoto);
                }
              },
            ),
            if (_step != _ReportStep.selectConsumer)
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
                child: Row(
                  children: [
                    Expanded(
                      child: Row(
                        children: List.generate(4, (i) {
                          final active = i < _stepIndex;
                          return Expanded(
                            child: Container(
                              margin: EdgeInsets.only(right: i == 3 ? 0 : 4),
                              height: 4,
                              decoration: BoxDecoration(
                                color: active
                                    ? AppColors.gold
                                    : AppColors.border,
                                borderRadius: BorderRadius.circular(2),
                              ),
                            ),
                          );
                        }),
                      ),
                    ),
                    const SizedBox(width: 10),
                    Text(
                      '$_stepIndex/4',
                      style: const TextStyle(
                        color: AppColors.mutedForeground,
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ],
                ),
              ),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: switch (_step) {
                  _ReportStep.selectConsumer => _buildSelectConsumer(),
                  _ReportStep.visitResult => _buildVisitResult(),
                  _ReportStep.addPhoto => _buildAddPhoto(),
                  _ReportStep.confirm => _buildConfirm(),
                  _ReportStep.success => const SizedBox.shrink(),
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSelectConsumer() {
    if (_loadingProspects) {
      return const Center(
        child: Padding(
          padding: EdgeInsets.all(24),
          child: CircularProgressIndicator(color: AppColors.gold),
        ),
      );
    }
    if (_prospectsError != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _prospectsError!,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: AppColors.mutedForeground,
                  fontSize: 13,
                ),
              ),
              const SizedBox(height: 16),
              SecondaryButton(
                onPressed: () {
                  setState(() => _loadingProspects = true);
                  _loadProspects();
                },
                child: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }
    final q = _search.text.trim().toLowerCase();
    final filtered = _prospects
        .where((c) => q.isEmpty || c.name.toLowerCase().contains(q))
        .toList();
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        TextField(
          controller: _search,
          onChanged: (_) => setState(() {}),
          style: const TextStyle(color: AppColors.foreground, fontSize: 14),
          decoration: InputDecoration(
            hintText: 'Cari anggota...',
            hintStyle: const TextStyle(color: AppColors.disabled, fontSize: 13),
            prefixIcon: const Icon(
              LucideIcons.search,
              size: 18,
              color: AppColors.mutedForeground,
            ),
            filled: true,
            fillColor: AppColors.inputBackground,
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: AppColors.inputBorder),
            ),
          ),
        ),
        const SizedBox(height: 12),
        GestureDetector(
          onTap: () => widget.nav.navigate(AppScreen.addConsumer),
          child: Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(14),
              border: Border.all(color: AppColors.borderGold, width: 1.2),
            ),
            child: Row(
              children: const [
                Icon(LucideIcons.plus, color: AppColors.gold, size: 18),
                SizedBox(width: 10),
                Text(
                  'Tambah Anggota Baru',
                  style: TextStyle(
                    color: AppColors.gold,
                    fontWeight: FontWeight.w700,
                    fontSize: 13,
                  ),
                ),
              ],
            ),
          ),
        ),
        const SizedBox(height: 12),
        if (filtered.isEmpty)
          const EmptyState(
            icon: LucideIcons.users,
            title: 'Belum Ada Anggota',
            desc: 'Tidak ada anggota yang cocok.',
          )
        else
          ...filtered.map(
            (c) => Padding(
              padding: const EdgeInsets.only(bottom: 10),
              child: GestureDetector(
                onTap: () => setState(() {
                  _selected = c;
                  _step = _ReportStep.visitResult;
                }),
                child: Container(
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: AppColors.card,
                    borderRadius: BorderRadius.circular(14),
                    border: Border.all(color: AppColors.border),
                  ),
                  child: Row(
                    children: [
                      Container(
                        width: 40,
                        height: 40,
                        decoration: const BoxDecoration(
                          color: AppColors.cardElevated,
                          shape: BoxShape.circle,
                        ),
                        child: Center(
                          child: Text(
                            c.initials,
                            style: const TextStyle(
                              color: AppColors.gold,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          c.name,
                          style: const TextStyle(
                            color: AppColors.foreground,
                            fontWeight: FontWeight.w600,
                            fontSize: 13,
                          ),
                        ),
                      ),
                      const Icon(
                        LucideIcons.chevronRight,
                        color: AppColors.mutedForeground,
                        size: 18,
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
      ],
    );
  }

  Widget _buildVisitResult() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        if (_selected != null)
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(
              color: AppColors.cardElevated,
              borderRadius: BorderRadius.circular(999),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  _selected!.name,
                  style: const TextStyle(
                    color: AppColors.foreground,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
                const SizedBox(width: 8),
                GestureDetector(
                  onTap: () =>
                      setState(() => _step = _ReportStep.selectConsumer),
                  child: const Icon(
                    LucideIcons.x,
                    size: 14,
                    color: AppColors.mutedForeground,
                  ),
                ),
              ],
            ),
          ),
        InputField(
          label: 'Tujuan Kunjungan',
          controller: _tujuan,
          required: true,
        ),
        const SizedBox(height: 12),
        SelectField(
          label: 'Hasil Kunjungan',
          required: true,
          value: _hasil,
          options: visitResultOptions,
          onChanged: (v) => setState(() => _hasil = v),
        ),
        const SizedBox(height: 12),
        SelectField(
          label: 'Status Anggota',
          required: true,
          value: _status,
          options: consumerStatusOptions,
          onChanged: (v) => setState(() => _status = v),
        ),
        const SizedBox(height: 12),
        TextareaField(label: 'Catatan', controller: _catatan),
        const SizedBox(height: 20),
        PrimaryButton(
          onPressed: _hasil != null && _status != null
              ? () => setState(() => _step = _ReportStep.addPhoto)
              : null,
          child: const Text('Lanjutkan → Foto'),
        ),
      ],
    );
  }

  Widget _buildAddPhoto() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        PhotoPickerField(
          label: 'Foto Kunjungan',
          hasPhoto: _photo != null,
          loading: _photoLoading,
          onCamera: () => _pickPhoto(true),
          onGallery: () => _pickPhoto(false),
          onRemove: () => setState(() => _photo = null),
        ),
        if (_photoError != null) ...[
          const SizedBox(height: 6),
          Text(
            _photoError!,
            style: const TextStyle(color: AppColors.badgeRedText, fontSize: 11),
          ),
        ],
        const SizedBox(height: 12),
        TextareaField(
          label: 'Keterangan Foto',
          controller: _keterangan,
          rows: 2,
        ),
        const SizedBox(height: 8),
        const Text(
          'Foto akan dikompres otomatis sebelum disinkronkan.',
          style: TextStyle(color: AppColors.disabled, fontSize: 11),
        ),
        const SizedBox(height: 20),
        PrimaryButton(
          onPressed: () => setState(() => _step = _ReportStep.confirm),
          child: const Text('Lanjutkan → Konfirmasi'),
        ),
      ],
    );
  }

  Widget _buildConfirm() {
    final offline = widget.nav.offline;
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        if (_submitError != null) ...[
          Container(
            margin: const EdgeInsets.only(bottom: 14),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppColors.badgeRedBg,
              borderRadius: BorderRadius.circular(12),
            ),
            child: Text(
              _submitError!,
              style: const TextStyle(
                color: AppColors.badgeRedText,
                fontSize: 12,
              ),
            ),
          ),
        ],
        if (offline)
          Container(
            margin: const EdgeInsets.only(bottom: 14),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: AppColors.badgeAmberBg,
              borderRadius: BorderRadius.circular(12),
            ),
            child: const Text(
              'Anda sedang offline. Pengiriman mungkin gagal sampai koneksi tersedia.',
              style: TextStyle(color: AppColors.warning, fontSize: 12),
            ),
          ),
        SectionCard(
          children: [
            InfoRow(label: 'Anggota', value: _selected?.name ?? '-'),
            InfoRow(label: 'Hasil', value: _hasil ?? '-'),
            InfoRow(label: 'Status Anggota', value: _status ?? '-'),
            InfoRow(
              label: 'Catatan',
              value: _catatan.text.isEmpty ? '-' : _catatan.text,
            ),
            InfoRow(
              label: 'Foto',
              value: _photo != null ? '1 foto' : 'Tidak ada',
            ),
            InfoRow(
              label: 'Marketing · Resort',
              value:
                  '${widget.auth?.user?.name ?? '-'} · ${widget.auth?.user?.marketing?.area ?? '-'}',
            ),
          ],
        ),
        const SizedBox(height: 20),
        PrimaryButton(
          onPressed: _submitting ? null : _submit,
          loading: _submitting,
          child: const Text('Kirim Laporan'),
        ),
        const SizedBox(height: 10),
        SecondaryButton(
          onPressed: () => setState(() => _step = _ReportStep.addPhoto),
          child: const Text('Kembali'),
        ),
      ],
    );
  }

  Widget _buildSuccess() {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Center(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const Icon(
                  LucideIcons.checkCircle,
                  color: AppColors.success,
                  size: 56,
                ),
                const SizedBox(height: 16),
                Text(
                  _queued
                      ? 'Laporan Disimpan (Menunggu Sinkronisasi)'
                      : 'Laporan Berhasil Dikirim',
                  style: const TextStyle(
                    color: AppColors.foreground,
                    fontSize: 18,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  _queued
                      ? 'Laporan akan otomatis dikirim saat koneksi tersedia.'
                      : 'Laporan kunjungan Anda telah tersimpan.',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: AppColors.mutedForeground,
                    fontSize: 13,
                  ),
                ),
                const SizedBox(height: 24),
                PrimaryButton(
                  onPressed: () => widget.nav.resetTo(AppScreen.reports),
                  child: const Text('Lihat Daftar Laporan'),
                ),
                const SizedBox(height: 12),
                GestureDetector(
                  onTap: () => widget.nav.resetTo(AppScreen.dashboard),
                  child: const Text(
                    'Kembali ke Dashboard',
                    style: TextStyle(
                      color: AppColors.mutedForeground,
                      fontSize: 13,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
