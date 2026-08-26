import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart' show XFile;
import 'package:intl/intl.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../api/api_client.dart';
import '../data/local/attachment_store.dart';
import '../data/local/offline_submit.dart';
import '../data/local/sync_queue.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/location_service.dart';
import '../services/member_service.dart';
import '../services/photo_service.dart';
import '../services/prospect_service.dart';
import '../state/auth_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';
import '../widgets/fields.dart';
import '../widgets/location_card.dart';

const _accStatusFilters = ['Semua', 'Menunggu', 'Disetujui', 'Ditolak'];

/// NOTE: MembersScreen/MemberDetailScreen/AddMemberScreen are kept fully
/// built and routed (per "hide, don't delete") but are not currently linked
/// from the bottom nav -- the live "Data Anggota" tab points at
/// ConsumersScreen (real `Prospect` data) instead. Un-orphaning these into a
/// Prospek/Anggota segmented view is tracked separately (Phase 0's
/// implementation note in BACKEND_INTEGRATION_TASKS.md). Now wired to the
/// real `/api/v1/members*` endpoints (Phase 3).
class MembersScreen extends StatefulWidget {
  final NavController nav;
  final MemberService? service;
  const MembersScreen({super.key, required this.nav, this.service});

  @override
  State<MembersScreen> createState() => _MembersScreenState();
}

class _MembersScreenState extends State<MembersScreen> {
  late final MemberService _service = widget.service ?? MemberService();
  final _search = TextEditingController();
  String _filter = 'Semua';

  bool _loading = true;
  String? _error;
  List<Member> _members = [];

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
      final members = await _service.list(
        approvalStatus: _filter == 'Semua' ? null : _filter,
        search: _search.text.trim().isEmpty ? null : _search.text.trim(),
      );
      if (!mounted) return;
      setState(() {
        _members = members;
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
                      const Text('Anggota', style: TextStyle(color: AppColors.foreground, fontSize: 20, fontWeight: FontWeight.w700)),
                      GestureDetector(
                        onTap: () => widget.nav.navigate(AppScreen.addMember),
                        child: Container(
                          width: 40,
                          height: 40,
                          decoration: const BoxDecoration(color: AppColors.gold, shape: BoxShape.circle),
                          child: const Icon(LucideIcons.plus, color: AppColors.primaryForeground, size: 20),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _search,
                    onSubmitted: (_) => _load(),
                    style: const TextStyle(color: AppColors.foreground, fontSize: 14),
                    decoration: InputDecoration(
                      hintText: 'Cari nama atau no. anggota...',
                      hintStyle: const TextStyle(color: AppColors.disabled, fontSize: 13),
                      prefixIcon: const Icon(LucideIcons.search, size: 18, color: AppColors.mutedForeground),
                      suffixIcon: IconButton(icon: const Icon(LucideIcons.search, size: 16, color: AppColors.gold), onPressed: _load),
                      filled: true,
                      fillColor: AppColors.inputBackground,
                      border: OutlineInputBorder(borderRadius: BorderRadius.circular(12), borderSide: const BorderSide(color: AppColors.inputBorder)),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: _accStatusFilters
                        .map((f) => Padding(
                              padding: const EdgeInsets.only(right: 8),
                              child: AppFilterChip(
                                label: f,
                                active: f == _filter,
                                onTap: () {
                                  setState(() => _filter = f);
                                  _load();
                                },
                              ),
                            ))
                        .toList(),
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
    if (_members.isEmpty) {
      return const EmptyState(icon: LucideIcons.users, title: 'Belum Ada Anggota', desc: 'Data anggota yang cocok tidak ditemukan.');
    }
    return RefreshIndicator(
      onRefresh: _load,
      color: AppColors.gold,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _members.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, i) {
          final m = _members[i];
          return GestureDetector(
            onTap: () => widget.nav.navigate(AppScreen.memberDetail, NavParams(memberId: m.id.toString())),
            child: Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(color: AppColors.card, borderRadius: BorderRadius.circular(14), border: Border.all(color: AppColors.border)),
              child: Row(
                children: [
                  Container(
                    width: 44,
                    height: 44,
                    decoration: const BoxDecoration(color: AppColors.cardElevated, shape: BoxShape.circle),
                    child: Center(child: Text(m.initials, style: const TextStyle(color: AppColors.gold, fontWeight: FontWeight.w700))),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(m.name, style: const TextStyle(color: AppColors.foreground, fontSize: 14, fontWeight: FontWeight.w700)),
                        Text('No. Anggota ${m.memberNumber} · ${m.resort ?? '-'}', style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
                      ],
                    ),
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      StatusBadge(status: m.approvalStatus, sm: true),
                      const SizedBox(height: 6),
                      Text(formatRupiah(m.loanAmount), style: const TextStyle(color: AppColors.mutedForeground, fontSize: 11, fontFamily: 'monospace')),
                    ],
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

class MemberDetailScreen extends StatefulWidget {
  final NavController nav;
  final MemberService? service;
  const MemberDetailScreen({super.key, required this.nav, this.service});

  @override
  State<MemberDetailScreen> createState() => _MemberDetailScreenState();
}

class _MemberDetailScreenState extends State<MemberDetailScreen> {
  late final MemberService _service = widget.service ?? MemberService();
  bool _loading = true;
  String? _error;
  Member? _member;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final id = int.tryParse(widget.nav.current.params?.memberId ?? '');
    if (id == null) {
      setState(() {
        _loading = false;
        _error = 'Anggota tidak ditemukan.';
      });
      return;
    }
    try {
      final member = await _service.detail(id);
      if (!mounted) return;
      setState(() {
        _member = member;
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
            PageHeader(title: _member?.name ?? 'Detail Anggota', subtitle: _member != null ? 'No. Anggota ${_member!.memberNumber}' : null, onBack: widget.nav.goBack),
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
    if (_error != null || _member == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(_error ?? 'Anggota tidak ditemukan.', textAlign: TextAlign.center, style: const TextStyle(color: AppColors.mutedForeground, fontSize: 13)),
              const SizedBox(height: 16),
              SecondaryButton(onPressed: _load, child: const Text('Coba Lagi')),
            ],
          ),
        ),
      );
    }
    final member = _member!;
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(color: AppColors.card, borderRadius: BorderRadius.circular(16), border: Border.all(color: AppColors.border)),
            child: Column(
              children: [
                Container(
                  width: 64,
                  height: 64,
                  decoration: const BoxDecoration(color: AppColors.cardElevated, shape: BoxShape.circle),
                  child: Center(child: Text(member.initials, style: const TextStyle(color: AppColors.gold, fontWeight: FontWeight.w700, fontSize: 20))),
                ),
                const SizedBox(height: 10),
                Text(member.name, style: const TextStyle(color: AppColors.foreground, fontSize: 16, fontWeight: FontWeight.w700)),
                Text('No. Anggota ${member.memberNumber}', style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
                const SizedBox(height: 8),
                StatusBadge(status: member.approvalStatus),
                const SizedBox(height: 12),
                InfoRow(label: 'Nomor Pinjaman', value: member.loanNumber ?? '-'),
                InfoRow(label: 'Nomor HP', value: member.phone),
                InfoRow(label: 'Alamat', value: member.address),
                InfoRow(label: 'Usaha', value: member.business ?? '-'),
                InfoRow(label: 'Tanggal Input', value: member.date ?? '-'),
              ],
            ),
          ),
          const SizedBox(height: 16),
          SectionCard(title: 'Informasi Pinjaman', children: [
            InfoRow(label: 'Pinjaman', value: formatRupiah(member.loanAmount)),
            InfoRow(label: 'Angsuran', value: formatRupiah(member.installmentAmount)),
            InfoRow(label: 'Asuransi', value: formatRupiah(member.insuranceAmount ?? 0)),
            InfoRow(label: 'Jaminan', value: member.collateral ?? '-'),
            InfoRow(label: 'Status ACC', value: member.approvalStatus),
          ]),
          const SizedBox(height: 16),
          SectionCard(title: 'Foto Anggota', children: [
            member.memberPhotoUrl != null
                ? Container(
                    height: 140,
                    decoration: BoxDecoration(color: AppColors.cardElevated, borderRadius: BorderRadius.circular(12)),
                    child: const Center(child: Icon(LucideIcons.image, color: AppColors.disabled, size: 28)),
                  )
                : const Text('Foto belum tersedia', style: TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
          ]),
          if (member.locationAddress != null) ...[
            const SizedBox(height: 16),
            LocationCard(
              state: LocationState.success,
              address: member.locationAddress!,
              coords: member.latitude != null ? '${member.latitude}, ${member.longitude}' : '-',
              updatedAt: member.date ?? '-',
              onRefresh: () {},
            ),
          ],
        ],
      ),
    );
  }
}

class AddMemberScreen extends StatefulWidget {
  final NavController nav;
  final AuthController? auth;
  final MemberService? service;
  final LocationService? locationService;
  final PhotoService? photoService;
  final SyncQueueStore? syncStore;
  final AttachmentStore? attachmentStore;
  final ProspectService? prospectService;
  const AddMemberScreen({super.key, required this.nav, this.auth, this.service, this.locationService, this.photoService, this.syncStore, this.attachmentStore, this.prospectService});

  @override
  State<AddMemberScreen> createState() => _AddMemberScreenState();
}

class _AddMemberScreenState extends State<AddMemberScreen> {
  late final MemberService _service = widget.service ?? MemberService();
  late final LocationService _locationService = widget.locationService ?? LocationService();
  late final PhotoService _photoService = widget.photoService ?? PhotoService();
  late final SyncQueueStore _syncStore = widget.syncStore ?? createDefaultSyncQueueStore();
  late final AttachmentStore _attachmentStore = widget.attachmentStore ?? createDefaultAttachmentStore();
  late final ProspectService _prospectService = widget.prospectService ?? ProspectService();
  int? _sourceProspectId;
  final _nama = TextEditingController();
  final _nomorAnggota = TextEditingController();
  final _nomorPinjaman = TextEditingController();
  final _alamat = TextEditingController();
  final _noHp = TextEditingController();
  final _usaha = TextEditingController();
  final _pinjaman = TextEditingController();
  final _angsuran = TextEditingController();
  final _asuransi = TextEditingController();
  final _jaminan = TextEditingController();
  XFile? _photo;
  bool _photoLoading = false;
  String? _photoError;
  bool _saving = false;
  bool _saved = false;
  bool _queued = false;
  String? _submitError;

  LocationState _locState = LocationState.idle;
  double? _lat;
  double? _lng;

  @override
  void initState() {
    super.initState();
    _refreshLocation();
    _prefillFromProspect();
    _prefillFromFotoNasabah();
  }

  /// If reached via "Foto Nasabah"'s "Lanjutkan ke Pendaftaran Anggota"
  /// handoff (per the 2026-08-25 "gabung dengan alur Daftarkan Anggota"
  /// decision), the nama/jumlah pinjaman collected there and the
  /// already-watermarked photo prefill this form instead of that photo
  /// becoming its own separate disbursement record.
  void _prefillFromFotoNasabah() {
    final params = widget.nav.current.params;
    final name = params?.prefillName;
    final loanAmount = params?.prefillLoanAmount;
    final photoBytes = params?.prefillPhotoBytes;
    if (name == null && loanAmount == null && photoBytes == null) return;
    setState(() {
      if (name != null) _nama.text = name;
      if (loanAmount != null) _pinjaman.text = NumberFormat.decimalPattern('id_ID').format(loanAmount);
      if (photoBytes != null) {
        _photo = XFile.fromData(photoBytes, name: 'foto-anggota-${DateTime.now().millisecondsSinceEpoch}.png', mimeType: 'image/png');
      }
    });
  }

  /// If reached via "Daftarkan sebagai Anggota" (ConsumerDetailScreen),
  /// `consumerId` carries the source Prospect's id — prefill the fields it
  /// actually has in common and remember it as `sourceProspectId` for
  /// `_submit()`. A no-op (blank form) when opened from the dashboard's
  /// "Anggota Resmi" shortcut or the Anggota Resmi tab's "+" button,
  /// neither of which sets this param.
  Future<void> _prefillFromProspect() async {
    final id = int.tryParse(widget.nav.current.params?.consumerId ?? '');
    if (id == null) return;
    try {
      final prospect = await _prospectService.detail(id);
      if (!mounted) return;
      setState(() {
        _sourceProspectId = prospect.id;
        _nama.text = prospect.name;
        _noHp.text = prospect.phone;
        _alamat.text = prospect.address;
        _usaha.text = prospect.business ?? '';
      });
    } on ApiException {
      // Prospect lookup failed — leave the form blank rather than blocking
      // registration; sourceProspectId simply stays unset.
    }
  }

  Future<void> _pickPhoto(bool fromCamera) async {
    setState(() {
      _photoLoading = true;
      _photoError = null;
    });
    try {
      final file = fromCamera ? await _photoService.pickFromCamera() : await _photoService.pickFromGallery();
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

  Future<void> _refreshLocation() async {
    setState(() => _locState = LocationState.loading);
    try {
      final pos = await _locationService.getCurrentPosition();
      if (!mounted) return;
      setState(() {
        _lat = pos.latitude;
        _lng = pos.longitude;
        _locState = LocationState.success;
      });
    } on LocationServiceException {
      if (!mounted) return;
      setState(() => _locState = LocationState.failed);
    }
  }

  Future<void> _submit() async {
    if (_nomorPinjaman.text.trim().isEmpty || _jaminan.text.trim().isEmpty) {
      setState(() => _submitError = 'Nomor pinjaman dan jaminan wajib diisi');
      return;
    }
    setState(() {
      _saving = true;
      _submitError = null;
    });
    final now = DateTime.now();
    final localUuid = newLocalUuid();
    final loanAmount = int.tryParse(_pinjaman.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
    final installmentAmount = int.tryParse(_angsuran.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
    final insuranceAmount = int.tryParse(_asuransi.text.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
    final resort = widget.auth?.user?.marketing?.area ?? '-';
    final payload = _service.buildCreatePayload(
      localUuid: localUuid,
      sourceProspectId: _sourceProspectId,
      resort: resort,
      date: '${now.year}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}',
      time: apiTimeFormat(now),
      name: _nama.text.trim(),
      memberNumber: _nomorAnggota.text.trim(),
      loanNumber: _nomorPinjaman.text.trim(),
      address: _alamat.text.trim(),
      phone: _noHp.text.trim(),
      business: _usaha.text.trim(),
      loanAmount: loanAmount,
      installmentAmount: installmentAmount,
      insuranceAmount: insuranceAmount,
      collateral: _jaminan.text.trim(),
      latitude: _lat,
      longitude: _lng,
    );
    try {
      await submitAttachmentWithOfflineFallback(
        store: _syncStore,
        attachmentStore: _attachmentStore,
        entityType: 'member',
        localUuid: localUuid,
        payload: payload,
        photos: {'member_photo': _photo},
        send: () => _service.create(
          localUuid: localUuid,
          sourceProspectId: _sourceProspectId,
          resort: resort,
          date: '${now.year}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}',
          time: apiTimeFormat(now),
          name: _nama.text.trim(),
          memberNumber: _nomorAnggota.text.trim(),
          loanNumber: _nomorPinjaman.text.trim(),
          address: _alamat.text.trim(),
          phone: _noHp.text.trim(),
          business: _usaha.text.trim(),
          loanAmount: loanAmount,
          installmentAmount: installmentAmount,
          insuranceAmount: insuranceAmount,
          collateral: _jaminan.text.trim(),
          latitude: _lat,
          longitude: _lng,
          photo: _photo,
        ),
      );
      if (!mounted) return;
      setState(() {
        _saving = false;
        _saved = true;
      });
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) widget.nav.resetTo(AppScreen.members);
      });
    } on OfflineQueuedException {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _saved = true;
        _queued = true;
      });
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) widget.nav.resetTo(AppScreen.members);
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
              const Icon(LucideIcons.checkCircle, color: AppColors.success, size: 48),
              const SizedBox(height: 12),
              Text(_queued ? 'Anggota Disimpan (Menunggu Sinkronisasi)' : 'Anggota Disimpan', style: const TextStyle(color: AppColors.foreground, fontSize: 16, fontWeight: FontWeight.w700)),
              const SizedBox(height: 4),
              const Text('Status ACC: Menunggu persetujuan admin', style: TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
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
            PageHeader(title: 'Tambah Anggota', onBack: widget.nav.goBack),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    if (_submitError != null) ...[
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(color: AppColors.badgeRedBg, borderRadius: BorderRadius.circular(12)),
                        child: Text(_submitError!, style: const TextStyle(color: AppColors.badgeRedText, fontSize: 12)),
                      ),
                      const SizedBox(height: 14),
                    ],
                    SectionCard(title: 'A. Informasi Utama', children: [
                      SelectField(label: 'Resort', value: widget.auth?.user?.marketing?.area ?? '-', options: [widget.auth?.user?.marketing?.area ?? '-'], onChanged: (_) {}, enabled: false),
                      const SizedBox(height: 12),
                      InputField(label: 'Nama', controller: _nama, required: true),
                      const SizedBox(height: 12),
                      InputField(label: 'Nomor Anggota', controller: _nomorAnggota, required: true),
                      const SizedBox(height: 12),
                      InputField(label: 'Nomor Pinjaman', controller: _nomorPinjaman, required: true),
                    ]),
                    const SizedBox(height: 14),
                    SectionCard(title: 'B. Kontak dan Usaha', children: [
                      TextareaField(label: 'Alamat', controller: _alamat, required: true, rows: 2),
                      const SizedBox(height: 12),
                      InputField(label: 'Nomor HP', controller: _noHp, required: true, inputMode: TextInputType.phone),
                      const SizedBox(height: 12),
                      InputField(label: 'Jenis Usaha', controller: _usaha, required: true),
                    ]),
                    const SizedBox(height: 14),
                    SectionCard(title: 'C. Informasi Pinjaman', children: [
                      CurrencyField(label: 'Pinjaman', controller: _pinjaman, required: true),
                      const SizedBox(height: 12),
                      CurrencyField(label: 'Angsuran', controller: _angsuran, required: true),
                      const SizedBox(height: 12),
                      CurrencyField(label: 'Asuransi', controller: _asuransi),
                      const SizedBox(height: 12),
                      InputField(label: 'Jaminan', controller: _jaminan, required: true),
                      const SizedBox(height: 12),
                      const InfoRow(label: 'Status ACC', value: 'Menunggu'),
                    ]),
                    const SizedBox(height: 14),
                    SectionCard(title: 'D. Foto Anggota', children: [
                      PhotoPickerField(
                        label: 'Foto Anggota',
                        hasPhoto: _photo != null,
                        loading: _photoLoading,
                        onCamera: () => _pickPhoto(true),
                        onGallery: () => _pickPhoto(false),
                        onRemove: () => setState(() => _photo = null),
                      ),
                      if (_photoError != null) ...[
                        const SizedBox(height: 6),
                        Text(_photoError!, style: const TextStyle(color: AppColors.badgeRedText, fontSize: 11)),
                      ],
                    ]),
                    const SizedBox(height: 14),
                    LocationCard(
                      state: _locState,
                      address: _lat != null ? 'Koordinat GPS perangkat' : '-',
                      coords: _lat != null ? '${_lat!.toStringAsFixed(4)}, ${_lng!.toStringAsFixed(4)}' : '-',
                      updatedAt: _locState == LocationState.success ? 'Baru saja' : '-',
                      onRefresh: _refreshLocation,
                    ),
                    const SizedBox(height: 14),
                    SectionCard(title: 'Info Otomatis', children: [
                      InfoRow(label: 'Marketing', value: widget.auth?.user?.name ?? '-'),
                      InfoRow(label: 'Kode Marketing', value: widget.auth?.user?.marketing?.code ?? '-'),
                    ]),
                    const SizedBox(height: 20),
                    PrimaryButton(onPressed: _saving ? null : _submit, loading: _saving, child: const Text('Simpan Anggota')),
                    const SizedBox(height: 10),
                    SecondaryButton(onPressed: widget.nav.goBack, child: const Text('Batal')),
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
