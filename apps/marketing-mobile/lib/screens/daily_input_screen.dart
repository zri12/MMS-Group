import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart' show XFile;
import 'package:lucide_icons/lucide_icons.dart';
import '../api/api_client.dart';
import '../data/local/attachment_store.dart';
import '../data/local/offline_submit.dart';
import '../data/local/sync_queue.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/operational_report_service.dart';
import '../services/photo_service.dart';
import '../state/auth_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/buttons.dart';
import '../widgets/fields.dart';

const _dayNames = [
  'Senin',
  'Selasa',
  'Rabu',
  'Kamis',
  'Jumat',
  'Sabtu',
  'Minggu',
];

/// Rebuilt to match the real `POST /api/v1/operational-reports` field list
/// exactly (BACKEND_INTEGRATION_TASKS.md Phase 3). The reference app's
/// original "Data Anggota" and "Laporan Tunai" sections are gone — per Phase
/// 0 DEC-021/022, an operational report has no member data at all, and
/// "Laporan Tunai" has no backend equivalent (it's not a marketing feature).
/// Foto Nasabah/Foto Transfer become the optional attachment fields (not yet
/// wired to real bytes — see Phase 5). "Simpan Draft" was removed: there is
/// no local draft table for this entity and no server-side draft state to
/// honestly save it to (see BACKEND_INTEGRATION_TASKS.md Phase 2).
class DailyInputScreen extends StatefulWidget {
  final NavController nav;
  final AuthController? auth;
  final OperationalReportService? service;
  final SyncQueueStore? syncStore;
  final AttachmentStore? attachmentStore;
  final PhotoService? photoService;
  const DailyInputScreen({
    super.key,
    required this.nav,
    this.auth,
    this.service,
    this.syncStore,
    this.attachmentStore,
    this.photoService,
  });

  @override
  State<DailyInputScreen> createState() => _DailyInputScreenState();
}

class _DailyInputScreenState extends State<DailyInputScreen> {
  late final OperationalReportService _service =
      widget.service ?? OperationalReportService();
  late final SyncQueueStore _syncStore =
      widget.syncStore ?? createDefaultSyncQueueStore();
  late final AttachmentStore _attachmentStore =
      widget.attachmentStore ?? createDefaultAttachmentStore();
  late final PhotoService _photoService = widget.photoService ?? PhotoService();

  XFile? _fotoPencairan;
  XFile? _fotoTransfer;
  bool _fotoPencairanLoading = false;
  bool _fotoTransferLoading = false;
  String? _photoError;
  final _storting = TextEditingController();
  final _asuransi = TextEditingController();
  final _drop = TextEditingController();
  final _dropBaru = TextEditingController();
  final _dropLanjut = TextEditingController();
  final _tabunganKeluar = TextEditingController();
  final _targetLamaNominal = TextEditingController();
  final _targetLamaOrang = TextEditingController();
  final _targetMasukNominal = TextEditingController();
  final _targetMasukOrang = TextEditingController();
  final _targetKeluarNominal = TextEditingController();
  final _targetKeluarOrang = TextEditingController();
  final _totalTargetNominal = TextEditingController();
  final _totalTargetOrang = TextEditingController();
  final _captionPencairan = TextEditingController();
  final _captionTransfer = TextEditingController();
  final _catatan = TextEditingController();

  bool _saving = false;
  bool _saved = false;
  bool _queued = false;
  String? _submitError;

  final _keyLampiran = GlobalKey();
  final _keyTargetMasuk = GlobalKey();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final section = widget.nav.current.params?.section;
      final key = switch (section) {
        InputSection.targetMasuk => _keyTargetMasuk,
        null => null,
      };
      if (key?.currentContext != null) {
        Scrollable.ensureVisible(
          key!.currentContext!,
          duration: const Duration(milliseconds: 400),
        );
      }
    });
  }

  @override
  void dispose() {
    for (final controller in [
      _storting,
      _asuransi,
      _drop,
      _dropBaru,
      _dropLanjut,
      _tabunganKeluar,
      _targetLamaNominal,
      _targetLamaOrang,
      _targetMasukNominal,
      _targetMasukOrang,
      _targetKeluarNominal,
      _targetKeluarOrang,
      _totalTargetNominal,
      _totalTargetOrang,
      _captionPencairan,
      _captionTransfer,
      _catatan,
    ]) {
      controller.dispose();
    }
    super.dispose();
  }

  int _num(TextEditingController c) =>
      int.tryParse(c.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;

  Future<void> _pickPencairan(bool fromCamera) =>
      _pickInto(fromCamera, isPencairan: true);
  Future<void> _pickTransfer(bool fromCamera) =>
      _pickInto(fromCamera, isPencairan: false);

  Future<void> _pickInto(bool fromCamera, {required bool isPencairan}) async {
    setState(() {
      if (isPencairan) {
        _fotoPencairanLoading = true;
      } else {
        _fotoTransferLoading = true;
      }
      _photoError = null;
    });
    try {
      final file = fromCamera
          ? await _photoService.pickFromCamera()
          : await _photoService.pickFromGallery();
      if (!mounted) return;
      setState(() {
        if (isPencairan) {
          _fotoPencairanLoading = false;
          if (file != null) _fotoPencairan = file;
        } else {
          _fotoTransferLoading = false;
          if (file != null) _fotoTransfer = file;
        }
      });
    } on PhotoServiceException catch (e) {
      if (!mounted) return;
      setState(() {
        if (isPencairan) {
          _fotoPencairanLoading = false;
        } else {
          _fotoTransferLoading = false;
        }
        _photoError = e.message;
      });
    }
  }

  Future<void> _save() async {
    setState(() {
      _saving = true;
      _submitError = null;
    });
    final now = DateTime.now();
    final localUuid = newLocalUuid();
    final payload = _service.buildCreatePayload(
      localUuid: localUuid,
      date:
          '${now.year}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}',
      time: apiTimeFormat(now),
      day: _dayNames[now.weekday - 1],
      resort: widget.auth?.user?.marketing?.area ?? '-',
      storting: _num(_storting),
      insuranceAmount: _num(_asuransi),
      drop: _num(_drop),
      withdrawalSaving: _num(_tabunganKeluar),
      previousTargetAmount: _num(_targetLamaNominal),
      previousTargetPeople: _num(_targetLamaOrang),
      incomingTargetAmount: _num(_targetMasukNominal),
      incomingTargetPeople: _num(_targetMasukOrang),
      outgoingTargetAmount: _num(_targetKeluarNominal),
      outgoingTargetPeople: _num(_targetKeluarOrang),
      totalTargetAmount: _num(_totalTargetNominal),
      totalTargetPeople: _num(_totalTargetOrang),
      newDrop: _num(_dropBaru),
      continuedDrop: _num(_dropLanjut),
      notes: _catatan.text.trim(),
    );
    final hasAttachments = _fotoPencairan != null || _fotoTransfer != null;
    try {
      if (hasAttachments) {
        await submitAttachmentWithOfflineFallback(
          store: _syncStore,
          attachmentStore: _attachmentStore,
          entityType: 'operational_report_attachment',
          localUuid: localUuid,
          payload: payload,
          photos: {
            'disbursement': _fotoPencairan,
            'transfer_proof': _fotoTransfer,
          },
          attachmentCaptions: {
            'disbursement': _captionPencairan.text.trim(),
            'transfer_proof': _captionTransfer.text.trim(),
          },
          send: () => _service.createWithAttachments(
            payload,
            disbursementPhoto: _fotoPencairan,
            disbursementCaption: _captionPencairan.text.trim(),
            transferProofPhoto: _fotoTransfer,
            transferProofCaption: _captionTransfer.text.trim(),
          ),
        );
      } else {
        await submitWithOfflineFallback(
          store: _syncStore,
          entityType: 'operational_report',
          localUuid: localUuid,
          payload: payload,
          send: () => _service.createRaw(payload),
        );
      }
      if (!mounted) return;
      setState(() {
        _saving = false;
        _saved = true;
      });
      Future.delayed(const Duration(milliseconds: 1300), () {
        if (!mounted) return;
        widget.nav.resetTo(AppScreen.history);
      });
    } on OfflineQueuedException {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _saved = true;
        _queued = true;
      });
      Future.delayed(const Duration(milliseconds: 1300), () {
        if (!mounted) return;
        widget.nav.resetTo(AppScreen.history);
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _submitError = e.message;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_saved) {
      return Scaffold(
        backgroundColor: AppColors.background,
        body: Center(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Icon(
                LucideIcons.checkCircle,
                color: AppColors.success,
                size: 48,
              ),
              const SizedBox(height: 12),
              Text(
                _queued
                    ? 'Data harian tersimpan (menunggu sinkronisasi)'
                    : 'Data harian berhasil disimpan',
                style: const TextStyle(
                  color: AppColors.foreground,
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(
              title: 'Input Data Hari Ini',
              subtitle: 'Laporan Operasional Harian',
              onBack: widget.nav.goBack,
            ),
            OfflineBanner(offline: widget.nav.offline),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    if (_submitError != null) ...[
                      Container(
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
                      const SizedBox(height: 14),
                    ],
                    Container(
                      key: _keyLampiran,
                      child: SectionCard(
                        title: '1. Lampiran (Opsional)',
                        children: [
                          PhotoPickerField(
                            label: 'Foto Pencairan',
                            hasPhoto: _fotoPencairan != null,
                            loading: _fotoPencairanLoading,
                            onCamera: () => _pickPencairan(true),
                            onGallery: () => _pickPencairan(false),
                            onRemove: () =>
                                setState(() => _fotoPencairan = null),
                          ),
                          const SizedBox(height: 8),
                          InputField(
                            label: 'Keterangan Foto Pencairan',
                            controller: _captionPencairan,
                          ),
                          const SizedBox(height: 12),
                          PhotoPickerField(
                            label: 'Foto Bukti Transfer',
                            hasPhoto: _fotoTransfer != null,
                            loading: _fotoTransferLoading,
                            onCamera: () => _pickTransfer(true),
                            onGallery: () => _pickTransfer(false),
                            onRemove: () =>
                                setState(() => _fotoTransfer = null),
                          ),
                          const SizedBox(height: 8),
                          InputField(
                            label: 'Keterangan Foto Bukti Transfer',
                            controller: _captionTransfer,
                          ),
                          if (_photoError != null) ...[
                            const SizedBox(height: 6),
                            Text(
                              _photoError!,
                              style: const TextStyle(
                                color: AppColors.badgeRedText,
                                fontSize: 11,
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '2. Setoran & Asuransi',
                      children: [
                        CurrencyField(label: 'Storting', controller: _storting),
                        const SizedBox(height: 12),
                        CurrencyField(label: 'Asuransi', controller: _asuransi),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '3. Drop',
                      children: [
                        CurrencyField(label: 'Drop', controller: _drop),
                        const SizedBox(height: 12),
                        CurrencyField(
                          label: 'Drop Baru',
                          controller: _dropBaru,
                        ),
                        const SizedBox(height: 12),
                        CurrencyField(
                          label: 'Drop Lanjut',
                          controller: _dropLanjut,
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '4. Penarikan Simpanan',
                      children: [
                        CurrencyField(
                          label: 'Tabungan Keluar',
                          controller: _tabunganKeluar,
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '5. Target Sebelumnya',
                      children: [
                        CurrencyField(
                          label: 'Nominal',
                          controller: _targetLamaNominal,
                        ),
                        const SizedBox(height: 12),
                        InputField(
                          label: 'Jumlah Orang',
                          controller: _targetLamaOrang,
                          inputMode: TextInputType.number,
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    Container(
                      key: _keyTargetMasuk,
                      child: SectionCard(
                        title: '6. Target Masuk',
                        children: [
                          CurrencyField(
                            label: 'Nominal',
                            controller: _targetMasukNominal,
                          ),
                          const SizedBox(height: 12),
                          InputField(
                            label: 'Jumlah Orang',
                            controller: _targetMasukOrang,
                            inputMode: TextInputType.number,
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '7. Target Keluar',
                      children: [
                        CurrencyField(
                          label: 'Nominal',
                          controller: _targetKeluarNominal,
                        ),
                        const SizedBox(height: 12),
                        InputField(
                          label: 'Jumlah Orang',
                          controller: _targetKeluarOrang,
                          inputMode: TextInputType.number,
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '8. Total Target',
                      children: [
                        CurrencyField(
                          label: 'Nominal',
                          controller: _totalTargetNominal,
                        ),
                        const SizedBox(height: 12),
                        InputField(
                          label: 'Jumlah Orang',
                          controller: _totalTargetOrang,
                          inputMode: TextInputType.number,
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '9. Catatan',
                      children: [
                        TextareaField(label: 'Catatan', controller: _catatan),
                      ],
                    ),
                    const SizedBox(height: 14),
                    SectionCard(
                      title: '10. Info Otomatis',
                      children: [
                        InfoRow(
                          label: 'Marketing',
                          value: widget.auth?.user?.name ?? '-',
                        ),
                        InfoRow(
                          label: 'Kode Marketing',
                          value: widget.auth?.user?.marketing?.code ?? '-',
                        ),
                        InfoRow(
                          label: 'Resort',
                          value: widget.auth?.user?.marketing?.area ?? '-',
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),
                    PrimaryButton(
                      onPressed: _saving ? null : _save,
                      loading: _saving,
                      child: const Text('Simpan'),
                    ),
                    const SizedBox(height: 10),
                    GestureDetector(
                      onTap: widget.nav.goBack,
                      child: const Center(
                        child: Text(
                          'Batal',
                          style: TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    ),
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
