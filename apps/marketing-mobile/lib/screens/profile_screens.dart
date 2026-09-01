import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../state/auth_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/primitives.dart';
import '../widgets/status.dart';

String _initialsOf(String name) {
  final parts = name
      .trim()
      .split(RegExp(r'\s+'))
      .where((p) => p.isNotEmpty)
      .toList();
  if (parts.isEmpty) return '?';
  if (parts.length == 1) return parts.first.substring(0, 1).toUpperCase();
  return (parts.first.substring(0, 1) + parts.last.substring(0, 1))
      .toUpperCase();
}

class ProfileScreen extends StatelessWidget {
  final NavController nav;
  final AuthController auth;
  final Future<void> Function()? onLogout;
  const ProfileScreen({
    super.key,
    required this.nav,
    required this.auth,
    this.onLogout,
  });

  Widget _menuItem(
    BuildContext context,
    IconData icon,
    String label,
    VoidCallback onTap, {
    bool danger = false,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14, horizontal: 4),
        decoration: const BoxDecoration(
          border: Border(bottom: BorderSide(color: AppColors.border)),
        ),
        child: Row(
          children: [
            Icon(
              icon,
              size: 18,
              color: danger ? AppColors.destructive : AppColors.mutedForeground,
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                label,
                style: TextStyle(
                  color: danger ? AppColors.destructive : AppColors.foreground,
                  fontSize: 14,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
            if (!danger)
              const Icon(
                LucideIcons.chevronRight,
                size: 16,
                color: AppColors.disabled,
              ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final offline = nav.offline;
    final user = auth.user;
    final name = user?.name ?? '—';
    final code = user?.marketing?.code;
    final area = user?.marketing?.area;
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            const PageHeader(title: 'Profil'),
            Expanded(
              child: ListView(
                padding: const EdgeInsets.all(16),
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
                            color: AppColors.gold,
                            shape: BoxShape.circle,
                          ),
                          child: Center(
                            child: Text(
                              _initialsOf(name),
                              style: const TextStyle(
                                color: AppColors.primaryForeground,
                                fontSize: 20,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                        ),
                        const SizedBox(height: 10),
                        Text(
                          name,
                          style: const TextStyle(
                            color: AppColors.foreground,
                            fontSize: 16,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        Text(
                          'Marketing${area != null ? ' · $area' : ''}',
                          style: const TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 12,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            if (code != null)
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 10,
                                  vertical: 4,
                                ),
                                decoration: BoxDecoration(
                                  color: AppColors.cardElevated,
                                  borderRadius: BorderRadius.circular(999),
                                ),
                                child: Text(
                                  'Kode $code',
                                  style: const TextStyle(
                                    color: AppColors.goldSoft,
                                    fontSize: 11,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                            const SizedBox(width: 8),
                            StatusBadge(
                              status: offline
                                  ? 'Menunggu Sinkronisasi'
                                  : 'Aktif',
                              sm: true,
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        const Divider(color: AppColors.border, height: 1),
                        const SizedBox(height: 14),
                        Row(
                          children: const [
                            Expanded(
                              child: _ProfileStat(
                                value: '3',
                                label: 'Kunjungan Hari Ini',
                              ),
                            ),
                            Expanded(
                              child: _ProfileStat(
                                value: '2',
                                label: 'Laporan Terkirim',
                              ),
                            ),
                            Expanded(
                              child: _ProfileStat(
                                value: '8,4 km',
                                label: 'Perjalanan',
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: AppColors.card,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: Row(
                      children: const [
                        KspLogo(size: KspLogoSize.sm),
                        SizedBox(width: 10),
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'KSP Manunggal Makmur Sejahtera',
                                style: TextStyle(
                                  color: AppColors.foreground,
                                  fontSize: 12,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                              Text(
                                'Marketing Monitoring',
                                style: TextStyle(
                                  color: AppColors.mutedForeground,
                                  fontSize: 11,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 16),
                  _menuItem(
                    context,
                    LucideIcons.user,
                    'Informasi Akun',
                    () => nav.navigate(AppScreen.accountInfo),
                  ),
                  _menuItem(
                    context,
                    LucideIcons.bellRing,
                    'Status Izin Aplikasi',
                    () => nav.navigate(AppScreen.permissionStatus),
                  ),
                  _menuItem(
                    context,
                    LucideIcons.bookOpen,
                    'Panduan Penggunaan',
                    () => nav.navigate(AppScreen.usageGuide),
                  ),
                  _menuItem(
                    context,
                    LucideIcons.info,
                    'Tentang Aplikasi',
                    () => nav.navigate(AppScreen.about),
                  ),
                  _menuItem(
                    context,
                    LucideIcons.logOut,
                    'Keluar',
                    () => _confirmLogout(context),
                    danger: true,
                  ),
                  const SizedBox(height: 20),
                  const Center(
                    child: Text(
                      'Marketing KSP MMS v1.0.0',
                      style: TextStyle(color: AppColors.disabled, fontSize: 11),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  void _confirmLogout(BuildContext context) {
    showModalConfirm(
      context,
      title: 'Keluar dari aplikasi?',
      desc: 'Anda perlu masuk kembali menggunakan akun marketing Anda.',
      confirmLabel: 'Keluar',
      danger: true,
      onConfirm: () async {
        if (onLogout != null) {
          await onLogout!();
        } else {
          await auth.logout();
          nav.resetTo(AppScreen.login);
        }
      },
    );
  }
}

class _ProfileStat extends StatelessWidget {
  final String value;
  final String label;
  const _ProfileStat({required this.value, required this.label});

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Text(
          value,
          style: const TextStyle(
            color: AppColors.foreground,
            fontSize: 15,
            fontWeight: FontWeight.w700,
            fontFamily: 'monospace',
          ),
        ),
        Text(
          label,
          style: const TextStyle(
            color: AppColors.mutedForeground,
            fontSize: 10,
          ),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }
}

/// Per Phase 0 DEC-021/022: there is no marketing self-service password
/// change endpoint — password resets are admin-only, done from the web
/// dashboard. This screen no longer simulates a change-password form; it
/// tells the user to contact their admin instead.
class AccountInfoScreen extends StatelessWidget {
  final NavController nav;
  final AuthController auth;
  const AccountInfoScreen({super.key, required this.nav, required this.auth});

  @override
  Widget build(BuildContext context) {
    final user = auth.user;
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(title: 'Informasi Akun', onBack: nav.goBack),
            Expanded(
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(16),
                child: SectionCard(
                  children: [
                    InfoRow(label: 'Nama Marketing', value: user?.name ?? '—'),
                    InfoRow(label: 'Username', value: user?.username ?? '—'),
                    InfoRow(
                      label: 'Kode Marketing',
                      value: user?.marketing?.code ?? '—',
                    ),
                    InfoRow(label: 'Area', value: user?.marketing?.area ?? '—'),
                    InfoRow(
                      label: 'Status Akun',
                      value: (user?.isActive ?? false) ? 'Aktif' : 'Nonaktif',
                    ),
                    InfoRow(
                      label: 'Nomor Telepon',
                      value: user?.marketing?.phone ?? '—',
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

class PermissionStatusScreen extends StatelessWidget {
  final NavController nav;
  const PermissionStatusScreen({super.key, required this.nav});

  @override
  Widget build(BuildContext context) {
    final perms = [
      ('Akses Lokasi', 'Digunakan untuk mencatat titik kunjungan'),
      ('Lokasi Latar Belakang', 'Menjaga tracking tetap berjalan'),
      ('Notifikasi Tracking', 'Pengingat jadwal dan status sinkronisasi'),
      ('Kamera', 'Mengambil foto nasabah dan bukti transfer'),
      ('Penyimpanan Foto', 'Menyimpan foto sementara di perangkat'),
    ];
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(title: 'Status Izin Aplikasi', onBack: nav.goBack),
            Expanded(
              child: ListView.separated(
                padding: const EdgeInsets.all(16),
                itemCount: perms.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (context, i) {
                  final p = perms[i];
                  return Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: AppColors.card,
                      borderRadius: BorderRadius.circular(14),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                p.$1,
                                style: const TextStyle(
                                  color: AppColors.foreground,
                                  fontSize: 13,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                              const SizedBox(height: 2),
                              Text(
                                p.$2,
                                style: const TextStyle(
                                  color: AppColors.mutedForeground,
                                  fontSize: 11,
                                ),
                              ),
                            ],
                          ),
                        ),
                        const StatusBadge(status: 'Diizinkan', sm: true),
                      ],
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class UsageGuideScreen extends StatelessWidget {
  final NavController nav;
  const UsageGuideScreen({super.key, required this.nav});

  @override
  Widget build(BuildContext context) {
    final tips = [
      'Masuk menggunakan username dan password yang diberikan admin.',
      'Aktifkan izin lokasi agar tracking perjalanan berjalan otomatis.',
      'Tambahkan anggota dari menu Data Anggota.',
      'Buat laporan kunjungan setelah bertemu anggota atau calon anggota.',
      'Periksa status sinkronisasi secara berkala saat koneksi tersedia.',
      'Data tetap tersimpan di perangkat saat offline dan akan terkirim otomatis saat online.',
    ];
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(title: 'Panduan Penggunaan', onBack: nav.goBack),
            Expanded(
              child: ListView.separated(
                padding: const EdgeInsets.all(16),
                itemCount: tips.length,
                separatorBuilder: (_, __) => const SizedBox(height: 10),
                itemBuilder: (context, i) => Container(
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
                        width: 24,
                        height: 24,
                        decoration: const BoxDecoration(
                          color: AppColors.gold,
                          shape: BoxShape.circle,
                        ),
                        child: Center(
                          child: Text(
                            '${i + 1}',
                            style: const TextStyle(
                              color: AppColors.primaryForeground,
                              fontSize: 11,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          tips[i],
                          style: const TextStyle(
                            color: AppColors.foreground,
                            fontSize: 13,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class AboutScreen extends StatelessWidget {
  final NavController nav;
  const AboutScreen({super.key, required this.nav});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            PageHeader(title: 'Tentang Aplikasi', onBack: nav.goBack),
            Expanded(
              child: Center(
                child: Padding(
                  padding: const EdgeInsets.all(24),
                  child: Container(
                    padding: const EdgeInsets.all(24),
                    decoration: BoxDecoration(
                      color: AppColors.card,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: AppColors.border),
                    ),
                    child: Column(
                      children: [
                        const KspLogo(size: KspLogoSize.md),
                        const SizedBox(height: 14),
                        const Text(
                          'KSP Manunggal Makmur Sejahtera',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: AppColors.foreground,
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Marketing · Versi 1.0.0',
                          style: TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 12,
                          ),
                        ),
                        const SizedBox(height: 14),
                        const Text(
                          'Aplikasi pemantauan marketing lapangan untuk mendukung pencatatan kunjungan, tracking perjalanan, dan pelaporan data anggota KSP Manunggal Makmur Sejahtera.',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: AppColors.mutedForeground,
                            fontSize: 12,
                            height: 1.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
