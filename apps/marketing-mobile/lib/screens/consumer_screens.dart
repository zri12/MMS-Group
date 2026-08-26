import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../api/api_client.dart';
import '../data/local/offline_submit.dart';
import '../data/local/sync_queue.dart';
import '../data/form_options.dart' show consumerStatusOptions;
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/member_service.dart';
import '../services/prospect_service.dart';
import '../state/auth_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/format.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';
import '../widgets/buttons.dart';
import '../widgets/fields.dart';

enum _ConsumersTab { prospek, anggotaResmi }

/// Wired to the real `/api/v1/prospects*` and `/api/v1/members*` endpoints
/// (BACKEND_INTEGRATION_TASKS.md Phase 3). Prospek and Anggota Resmi are
/// confirmed separate entities/resources per Phase 0 DEC-021/022, but that
/// decision also states the bottom nav should keep a single "Data Anggota"
/// tab with a Prospek/Anggota Resmi segmented switch here rather than a 6th
/// bottom-nav tab — implemented below. `MembersScreen` (member_screens.dart)
/// still exists as its own route too (reached via the dashboard's "Anggota
/// Resmi" shortcut), same dual-access pattern as "Data Prospek".
class ConsumersScreen extends StatefulWidget {
  final NavController nav;
  final ProspectService? service;
  final MemberService? memberService;
  const ConsumersScreen({
    super.key,
    required this.nav,
    this.service,
    this.memberService,
  });

  @override
  State<ConsumersScreen> createState() => _ConsumersScreenState();
}

class _ConsumersScreenState extends State<ConsumersScreen> {
  late final ProspectService _service = widget.service ?? ProspectService();
  late final MemberService _memberService =
      widget.memberService ?? MemberService();
  final _search = TextEditingController();

  _ConsumersTab _tab = _ConsumersTab.prospek;

  bool _loading = true;
  String? _error;
  List<Prospect> _prospects = [];

  bool _membersLoaded = false;
  bool _memberLoading = false;
  String? _memberError;
  List<Member> _members = [];

  @override
  void initState() {
    super.initState();
    _load();
  }

  void _switchTab(_ConsumersTab tab) {
    setState(() => _tab = tab);
    if (tab == _ConsumersTab.anggotaResmi && !_membersLoaded) _loadMembers();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final prospects = await _service.list(
        search: _search.text.trim().isEmpty ? null : _search.text.trim(),
      );
      if (!mounted) return;
      setState(() {
        _prospects = prospects;
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

  Future<void> _loadMembers() async {
    setState(() {
      _memberLoading = true;
      _memberError = null;
    });
    try {
      final members = await _memberService.list(
        search: _search.text.trim().isEmpty ? null : _search.text.trim(),
      );
      if (!mounted) return;
      setState(() {
        _members = members;
        _membersLoaded = true;
        _memberLoading = false;
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _memberError = e.message;
        _memberLoading = false;
      });
    }
  }

  void _onSearchSubmitted() =>
      _tab == _ConsumersTab.prospek ? _load() : _loadMembers();

  @override
  Widget build(BuildContext context) {
    final isMember = _tab == _ConsumersTab.anggotaResmi;
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
                            'DATA LAPANGAN',
                            style: TextStyle(
                              color: AppColors.goldSoft,
                              fontSize: 11,
                              fontWeight: FontWeight.w700,
                              letterSpacing: 1,
                            ),
                          ),
                          SizedBox(height: 2),
                          Text(
                            'Anggota',
                            style: TextStyle(
                              color: AppColors.foreground,
                              fontSize: 20,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ],
                      ),
                      GestureDetector(
                        onTap: () => widget.nav.navigate(
                          isMember
                              ? AppScreen.addMember
                              : AppScreen.addConsumer,
                        ),
                        child: Container(
                          width: 40,
                          height: 40,
                          decoration: const BoxDecoration(
                            color: AppColors.gold,
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            LucideIcons.plus,
                            color: AppColors.primaryForeground,
                            size: 20,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    children: [
                      AppFilterChip(
                        label: 'Prospek',
                        active: !isMember,
                        onTap: () => _switchTab(_ConsumersTab.prospek),
                      ),
                      const SizedBox(width: 8),
                      AppFilterChip(
                        label: 'Anggota Resmi',
                        active: isMember,
                        onTap: () => _switchTab(_ConsumersTab.anggotaResmi),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  TextField(
                    controller: _search,
                    onSubmitted: (_) => _onSearchSubmitted(),
                    style: const TextStyle(
                      color: AppColors.foreground,
                      fontSize: 14,
                    ),
                    decoration: InputDecoration(
                      hintText: isMember
                          ? 'Cari nama atau no. anggota...'
                          : 'Cari nama atau alamat...',
                      hintStyle: const TextStyle(
                        color: AppColors.disabled,
                        fontSize: 13,
                      ),
                      prefixIcon: const Icon(
                        LucideIcons.search,
                        size: 18,
                        color: AppColors.mutedForeground,
                      ),
                      suffixIcon: IconButton(
                        icon: const Icon(
                          LucideIcons.search,
                          size: 16,
                          color: AppColors.gold,
                        ),
                        onPressed: _onSearchSubmitted,
                      ),
                      filled: true,
                      fillColor: AppColors.inputBackground,
                      contentPadding: const EdgeInsets.symmetric(vertical: 12),
                      border: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(
                          color: AppColors.inputBorder,
                        ),
                      ),
                      enabledBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(
                          color: AppColors.inputBorder,
                        ),
                      ),
                      focusedBorder: OutlineInputBorder(
                        borderRadius: BorderRadius.circular(12),
                        borderSide: const BorderSide(color: AppColors.gold),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            Expanded(child: isMember ? _buildMemberBody() : _buildBody()),
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
    if (_prospects.isEmpty) {
      return const EmptyState(
        icon: LucideIcons.users,
        title: 'Belum Ada Prospek',
        desc: 'Data prospek yang cocok tidak ditemukan.',
      );
    }
    return RefreshIndicator(
      onRefresh: _load,
      color: AppColors.gold,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _prospects.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, i) => _ProspectCard(
          prospect: _prospects[i],
          onTap: () => widget.nav.navigate(
            AppScreen.consumerDetail,
            NavParams(consumerId: _prospects[i].id.toString()),
          ),
        ),
      ),
    );
  }

  Widget _buildMemberBody() {
    if (_memberLoading) {
      return const Center(
        child: CircularProgressIndicator(color: AppColors.gold),
      );
    }
    if (_memberError != null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _memberError!,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: AppColors.mutedForeground,
                  fontSize: 13,
                ),
              ),
              const SizedBox(height: 16),
              SecondaryButton(
                onPressed: _loadMembers,
                child: const Text('Coba Lagi'),
              ),
            ],
          ),
        ),
      );
    }
    if (_members.isEmpty) {
      return const EmptyState(
        icon: LucideIcons.users,
        title: 'Belum Ada Anggota Resmi',
        desc: 'Data anggota resmi yang cocok tidak ditemukan.',
      );
    }
    return RefreshIndicator(
      onRefresh: _loadMembers,
      color: AppColors.gold,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _members.length,
        separatorBuilder: (_, __) => const SizedBox(height: 10),
        itemBuilder: (context, i) => _MemberCard(
          member: _members[i],
          onTap: () => widget.nav.navigate(
            AppScreen.memberDetail,
            NavParams(memberId: _members[i].id.toString()),
          ),
        ),
      ),
    );
  }
}

class _MemberCard extends StatelessWidget {
  final Member member;
  final VoidCallback onTap;
  const _MemberCard({required this.member, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
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
              width: 44,
              height: 44,
              decoration: const BoxDecoration(
                color: AppColors.cardElevated,
                shape: BoxShape.circle,
              ),
              child: Center(
                child: Text(
                  member.initials,
                  style: const TextStyle(
                    color: AppColors.gold,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    member.name,
                    style: const TextStyle(
                      color: AppColors.foreground,
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  Text(
                    'No. Anggota ${member.memberNumber} · ${member.resort ?? '-'}',
                    style: const TextStyle(
                      color: AppColors.mutedForeground,
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ),
            Column(
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                StatusBadge(status: member.approvalStatus, sm: true),
                const SizedBox(height: 6),
                Text(
                  formatRupiah(member.loanAmount),
                  style: const TextStyle(
                    color: AppColors.mutedForeground,
                    fontSize: 11,
                    fontFamily: 'monospace',
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _ProspectCard extends StatelessWidget {
  final Prospect prospect;
  final VoidCallback onTap;
  const _ProspectCard({required this.prospect, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
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
              width: 44,
              height: 44,
              decoration: const BoxDecoration(
                color: AppColors.cardElevated,
                shape: BoxShape.circle,
              ),
              child: Center(
                child: Text(
                  prospect.initials,
                  style: const TextStyle(
                    color: AppColors.gold,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    prospect.name,
                    style: const TextStyle(
                      color: AppColors.foreground,
                      fontSize: 14,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Row(
                    children: [
                      const Icon(
                        LucideIcons.mapPin,
                        size: 12,
                        color: AppColors.mutedForeground,
                      ),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          prospect.address,
                          style: const TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 12,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(width: 8),
            StatusBadge(status: prospect.status, sm: true),
          ],
        ),
      ),
    );
  }
}

class ConsumerDetailScreen extends StatefulWidget {
  final NavController nav;
  final ProspectService? service;
  const ConsumerDetailScreen({super.key, required this.nav, this.service});

  @override
  State<ConsumerDetailScreen> createState() => _ConsumerDetailScreenState();
}

class _ConsumerDetailScreenState extends State<ConsumerDetailScreen> {
  late final ProspectService _service = widget.service ?? ProspectService();
  bool _loading = true;
  String? _error;
  Prospect? _prospect;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final id = int.tryParse(widget.nav.current.params?.consumerId ?? '');
    if (id == null) {
      setState(() {
        _loading = false;
        _error = 'Anggota tidak ditemukan.';
      });
      return;
    }
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final prospect = await _service.detail(id);
      if (!mounted) return;
      setState(() {
        _prospect = prospect;
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
              title: _prospect?.name ?? 'Detail Anggota',
              subtitle: _prospect?.business,
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
    if (_error != null || _prospect == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _error ?? 'Anggota tidak ditemukan.',
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
    final prospect = _prospect!;
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.card,
              borderRadius: BorderRadius.circular(16),
              border: Border.all(color: AppColors.border),
            ),
            child: Column(
              children: [
                Container(
                  width: 64,
                  height: 64,
                  decoration: const BoxDecoration(
                    color: AppColors.cardElevated,
                    shape: BoxShape.circle,
                  ),
                  child: Center(
                    child: Text(
                      prospect.initials,
                      style: const TextStyle(
                        color: AppColors.gold,
                        fontWeight: FontWeight.w700,
                        fontSize: 20,
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 10),
                Text(
                  prospect.name,
                  style: const TextStyle(
                    color: AppColors.foreground,
                    fontSize: 16,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  prospect.business ?? '-',
                  style: const TextStyle(
                    color: AppColors.mutedForeground,
                    fontSize: 12,
                  ),
                ),
                const SizedBox(height: 8),
                StatusBadge(status: prospect.status),
                const SizedBox(height: 12),
                InfoRow(label: 'Nomor Telepon', value: prospect.phone),
                InfoRow(label: 'Alamat', value: prospect.address),
                if (prospect.inputDate != null)
                  InfoRow(label: 'Tanggal Input', value: prospect.inputDate!),
              ],
            ),
          ),
          const SizedBox(height: 16),
          SectionCard(
            title: 'Catatan',
            children: [
              Text(
                prospect.notes?.isNotEmpty == true
                    ? prospect.notes!
                    : 'Belum ada catatan.',
                style: const TextStyle(
                  color: AppColors.mutedForeground,
                  fontSize: 13,
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          PrimaryButton(
            onPressed: () => widget.nav.navigate(
              AppScreen.newReport,
              NavParams(consumerId: prospect.id.toString()),
            ),
            child: const Text('Buat Laporan'),
          ),
          const SizedBox(height: 10),
          // Manual-only action — the backend explicitly forbids auto-converting
          // a prospect just because of its status (docs/10_BUSINESS_RULES.md:
          // "Konversi ke anggota tidak boleh terjadi otomatis hanya karena
          // status Selesai."), so this button is always available regardless
          // of `prospect.status`, never triggered automatically.
          SecondaryButton(
            onPressed: () => widget.nav.navigate(
              AppScreen.addMember,
              NavParams(consumerId: prospect.id.toString()),
            ),
            child: const Text('Daftarkan sebagai Anggota'),
          ),
        ],
      ),
    );
  }
}

class AddConsumerScreen extends StatefulWidget {
  final NavController nav;
  final AuthController? auth;
  final ProspectService? service;
  final SyncQueueStore? syncStore;
  const AddConsumerScreen({
    super.key,
    required this.nav,
    this.auth,
    this.service,
    this.syncStore,
  });

  @override
  State<AddConsumerScreen> createState() => _AddConsumerScreenState();
}

class _AddConsumerScreenState extends State<AddConsumerScreen> {
  late final ProspectService _service = widget.service ?? ProspectService();
  late final SyncQueueStore _syncStore =
      widget.syncStore ?? createDefaultSyncQueueStore();
  final _nama = TextEditingController();
  final _telepon = TextEditingController();
  final _alamat = TextEditingController();
  final _usaha = TextEditingController();
  final _hasil = TextEditingController();
  final _catatan = TextEditingController();
  String? _status;
  bool _saving = false;
  bool _saved = false;
  bool _queued = false;
  String? _submitError;
  final Map<String, String> _errors = {};

  Future<void> _submit() async {
    final errs = <String, String>{};
    if (_nama.text.trim().isEmpty) errs['nama'] = 'Nama wajib diisi';
    if (_telepon.text.trim().isEmpty) {
      errs['telepon'] = 'Nomor telepon wajib diisi';
    }
    if (_alamat.text.trim().isEmpty) errs['alamat'] = 'Alamat wajib diisi';
    if (_usaha.text.trim().isEmpty) errs['usaha'] = 'Jenis usaha wajib diisi';
    if (_hasil.text.trim().isEmpty) {
      errs['hasil'] = 'Hasil kunjungan awal wajib diisi';
    }
    if (_status == null) errs['status'] = 'Status wajib dipilih';
    setState(
      () => _errors
        ..clear()
        ..addAll(errs),
    );
    if (errs.isNotEmpty) return;

    setState(() {
      _saving = true;
      _submitError = null;
    });
    final localUuid = newLocalUuid();
    final now = DateTime.now();
    final payload = _service.buildCreatePayload(
      localUuid: localUuid,
      name: _nama.text.trim(),
      phone: _telepon.text.trim(),
      address: _alamat.text.trim(),
      business: _usaha.text.trim(),
      status: _status!,
      initialVisitResult: _hasil.text.trim(),
      notes: _catatan.text.trim(),
      resort: 'Resort 3',
      inputDate:
          '${now.year}-${now.month.toString().padLeft(2, '0')}-${now.day.toString().padLeft(2, '0')}',
      inputTime: apiTimeFormat(now),
    );
    try {
      await submitWithOfflineFallback(
        store: _syncStore,
        entityType: 'prospect',
        localUuid: localUuid,
        payload: payload,
        send: () => _service.createRaw(payload),
      );
      if (!mounted) return;
      setState(() {
        _saving = false;
        _saved = true;
      });
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) widget.nav.goBack();
      });
    } on OfflineQueuedException {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _saved = true;
        _queued = true;
      });
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) widget.nav.goBack();
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() {
        _saving = false;
        _submitError = e.message;
        if (e.errors != null) {
          for (final entry in e.errors!.entries) {
            _errors[entry.key] = entry.value.first;
          }
        }
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
                    ? 'Anggota Disimpan (Menunggu Sinkronisasi)'
                    : 'Anggota Disimpan',
                style: const TextStyle(
                  color: AppColors.foreground,
                  fontSize: 16,
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
                    SectionCard(
                      title: 'Informasi Dasar',
                      children: [
                        InputField(
                          label: 'Nama Anggota',
                          controller: _nama,
                          required: true,
                          errorText: _errors['nama'],
                        ),
                        const SizedBox(height: 12),
                        InputField(
                          label: 'Nomor Telepon',
                          controller: _telepon,
                          required: true,
                          errorText: _errors['telepon'],
                          inputMode: TextInputType.phone,
                        ),
                        const SizedBox(height: 12),
                        InputField(
                          label: 'Alamat',
                          controller: _alamat,
                          required: true,
                          errorText: _errors['alamat'],
                        ),
                        const SizedBox(height: 12),
                        InputField(
                          label: 'Nama Usaha/Pekerjaan',
                          controller: _usaha,
                          required: true,
                          errorText: _errors['usaha'],
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    SectionCard(
                      title: 'Status & Hasil Kunjungan',
                      children: [
                        SelectField(
                          label: 'Status Anggota',
                          required: true,
                          value: _status,
                          options: consumerStatusOptions,
                          onChanged: (v) => setState(() => _status = v),
                        ),
                        if (_errors['status'] != null) ...[
                          const SizedBox(height: 4),
                          Text(
                            _errors['status']!,
                            style: const TextStyle(
                              color: AppColors.badgeRedText,
                              fontSize: 11,
                            ),
                          ),
                        ],
                        const SizedBox(height: 12),
                        TextareaField(
                          label: 'Hasil Awal Kunjungan',
                          controller: _hasil,
                          required: true,
                        ),
                        if (_errors['hasil'] != null) ...[
                          const SizedBox(height: 4),
                          Text(
                            _errors['hasil']!,
                            style: const TextStyle(
                              color: AppColors.badgeRedText,
                              fontSize: 11,
                            ),
                          ),
                        ],
                        const SizedBox(height: 12),
                        TextareaField(
                          label: 'Catatan Tambahan',
                          controller: _catatan,
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    SectionCard(
                      title: 'Info Otomatis',
                      children: [
                        InfoRow(
                          label: 'Marketing',
                          value: widget.auth?.user?.name ?? '-',
                        ),
                        InfoRow(
                          label: 'Resort',
                          value: widget.auth?.user?.marketing?.area ?? '-',
                        ),
                      ],
                    ),
                    const SizedBox(height: 24),
                    PrimaryButton(
                      onPressed: _saving ? null : _submit,
                      loading: _saving,
                      child: const Text('Simpan Konsumen'),
                    ),
                    const SizedBox(height: 10),
                    SecondaryButton(
                      onPressed: widget.nav.goBack,
                      child: const Text('Batal'),
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

class EditConsumerScreen extends StatefulWidget {
  final NavController nav;
  final ProspectService? service;
  const EditConsumerScreen({super.key, required this.nav, this.service});

  @override
  State<EditConsumerScreen> createState() => _EditConsumerScreenState();
}

class _EditConsumerScreenState extends State<EditConsumerScreen> {
  late final ProspectService _service = widget.service ?? ProspectService();
  final _nama = TextEditingController();
  final _alamat = TextEditingController();
  final _usaha = TextEditingController();
  final _catatan = TextEditingController();
  String? _status;

  bool _loading = true;
  bool _saving = false;
  bool _saved = false;
  String? _error;
  Prospect? _prospect;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    final id = int.tryParse(widget.nav.current.params?.consumerId ?? '');
    if (id == null) {
      setState(() {
        _loading = false;
        _error = 'Anggota tidak ditemukan.';
      });
      return;
    }
    try {
      final prospect = await _service.detail(id);
      if (!mounted) return;
      _nama.text = prospect.name;
      _alamat.text = prospect.address;
      _usaha.text = prospect.business ?? '';
      _catatan.text = prospect.notes ?? '';
      setState(() {
        _prospect = prospect;
        _status = prospect.status;
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

  Future<void> _submit() async {
    if (_prospect == null) return;
    setState(() => _saving = true);
    try {
      await _service.update(_prospect!.id, {
        'name': _nama.text.trim(),
        'address': _alamat.text.trim(),
        'business': _usaha.text.trim(),
        'status': _status,
        'notes': _catatan.text.trim(),
      });
      if (!mounted) return;
      setState(() {
        _saving = false;
        _saved = true;
      });
      Future.delayed(const Duration(milliseconds: 1500), () {
        if (mounted) widget.nav.goBack();
      });
    } on ApiException catch (e) {
      if (!mounted) return;
      setState(() => _saving = false);
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text(e.message)));
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
            children: const [
              Icon(LucideIcons.checkCircle, color: AppColors.success, size: 48),
              SizedBox(height: 12),
              Text(
                'Perubahan Disimpan',
                style: TextStyle(
                  color: AppColors.foreground,
                  fontSize: 16,
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
            PageHeader(title: 'Edit Anggota', onBack: widget.nav.goBack),
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
    if (_error != null || _prospect == null) {
      return Center(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                _error ?? 'Anggota tidak ditemukan.',
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
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          SectionCard(
            title: 'Informasi Dasar',
            children: [
              InputField(
                label: 'Nama Anggota',
                controller: _nama,
                required: true,
              ),
              const SizedBox(height: 12),
              InputField(label: 'Alamat', controller: _alamat, required: true),
              const SizedBox(height: 12),
              InputField(label: 'Nama Usaha/Pekerjaan', controller: _usaha),
            ],
          ),
          const SizedBox(height: 16),
          SectionCard(
            title: 'Status & Catatan',
            children: [
              SelectField(
                label: 'Status Anggota',
                required: true,
                value: _status,
                options: consumerStatusOptions,
                onChanged: (v) => setState(() => _status = v),
              ),
              const SizedBox(height: 12),
              TextareaField(label: 'Catatan', controller: _catatan),
            ],
          ),
          const SizedBox(height: 24),
          PrimaryButton(
            onPressed: _saving ? null : _submit,
            loading: _saving,
            child: const Text('Simpan Perubahan'),
          ),
          const SizedBox(height: 10),
          SecondaryButton(
            onPressed: widget.nav.goBack,
            child: const Text('Batal'),
          ),
        ],
      ),
    );
  }
}
