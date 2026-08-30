import 'dart:async';
import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../navigation/nav_controller.dart';
import '../navigation/screen.dart';
import '../services/location_service.dart';
import '../state/auth_controller.dart';
import '../state/tracking_controller.dart';
import '../theme/app_colors.dart';
import '../widgets/primitives.dart';
import '../widgets/buttons.dart';
import '../widgets/fields.dart';

class SplashScreen extends StatefulWidget {
  final NavController nav;
  final AuthController auth;
  const SplashScreen({super.key, required this.nav, required this.auth});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();
    Timer(const Duration(milliseconds: 2800), () {
      if (!mounted) return;
      // A stored token was already re-validated against /auth/profile by
      // AuthController.bootstrap() (called at app startup) — if it's still
      // valid, skip straight past login/permissions to the dashboard.
      widget.nav.resetTo(widget.auth.isAuthenticated ? AppScreen.dashboard : AppScreen.login);
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Column(
          children: [
            Container(height: 2, margin: const EdgeInsets.only(top: 32), width: 48, color: AppColors.gold),
            const Spacer(flex: 2),
            const KspLogo(size: KspLogoSize.lg),
            const SizedBox(height: 20),
            const Text(
              'KSP Manunggal Makmur Sejahtera',
              textAlign: TextAlign.center,
              style: TextStyle(color: AppColors.foreground, fontSize: 18, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 6),
            const Text('Marketing Monitoring', style: TextStyle(color: AppColors.mutedForeground, fontSize: 13)),
            const SizedBox(height: 28),
            Container(width: 32, height: 1, color: AppColors.border),
            const Spacer(),
            const _BouncingDots(),
            const SizedBox(height: 12),
            const SizedBox(
              width: 152,
              child: ClipRRect(
                borderRadius: BorderRadius.all(Radius.circular(999)),
                child: LinearProgressIndicator(
                  minHeight: 3,
                  color: AppColors.gold,
                  backgroundColor: AppColors.border,
                ),
              ),
            ),
            const SizedBox(height: 24),
            const Text('v1.0.0', style: TextStyle(color: AppColors.disabled, fontSize: 11)),
            const SizedBox(height: 24),
          ],
        ),
      ),
    );
  }
}

class _BouncingDots extends StatefulWidget {
  const _BouncingDots();
  @override
  State<_BouncingDots> createState() => _BouncingDotsState();
}

class _BouncingDotsState extends State<_BouncingDots> with SingleTickerProviderStateMixin {
  late final AnimationController _c = AnimationController(vsync: this, duration: const Duration(milliseconds: 900))..repeat();

  @override
  void dispose() {
    _c.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _c,
      builder: (context, _) {
        return Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: List.generate(3, (i) {
            final t = (_c.value - i * 0.2) % 1.0;
            final dy = -6 * (t < 0.5 ? (t * 2) : (2 - t * 2));
            return Padding(
              padding: const EdgeInsets.symmetric(horizontal: 4),
              child: Transform.translate(
                offset: Offset(0, dy),
                child: Container(width: 7, height: 7, decoration: const BoxDecoration(color: AppColors.gold, shape: BoxShape.circle)),
              ),
            );
          }),
        );
      },
    );
  }
}

class LoginScreen extends StatefulWidget {
  final NavController nav;
  final AuthController auth;
  final LocationService locationService;
  const LoginScreen({super.key, required this.nav, required this.auth, required this.locationService});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _username = TextEditingController();
  final _password = TextEditingController();
  bool _obscure = true;
  bool _remember = true;

  @override
  void dispose() {
    _username.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (_username.text.trim().isEmpty || _password.text.trim().isEmpty) return;
    final ok = await widget.auth.login(_username.text.trim(), _password.text);
    if (!mounted) return;
    if (ok) {
      final needsOnboarding = await widget.locationService.requiresLocationOnboarding();
      if (!mounted) return;
      widget.nav.resetTo(needsOnboarding ? AppScreen.permLocation : AppScreen.dashboard);
    } else {
      setState(() {}); // surface widget.auth.errorMessage below
    }
  }

  @override
  Widget build(BuildContext context) {
    final canSubmit = _username.text.trim().isNotEmpty && _password.text.trim().isNotEmpty;
    final loading = widget.auth.busy;
    final error = widget.auth.errorMessage;
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(24, 48, 24, 24),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Center(child: KspLogo(size: KspLogoSize.lg)),
              const SizedBox(height: 20),
              const Text('Selamat Datang', textAlign: TextAlign.center, style: TextStyle(color: AppColors.foreground, fontSize: 22, fontWeight: FontWeight.w700)),
              const SizedBox(height: 6),
              const Text('Masuk menggunakan akun marketing Anda', textAlign: TextAlign.center, style: TextStyle(color: AppColors.mutedForeground, fontSize: 13)),
              const SizedBox(height: 32),
              if (error != null) ...[
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: AppColors.destructive.withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: AppColors.destructive.withValues(alpha: 0.4)),
                  ),
                  child: Text(error, style: const TextStyle(color: AppColors.destructive, fontSize: 12)),
                ),
                const SizedBox(height: 16),
              ],
              InputField(label: 'Username', controller: _username, onChanged: (_) => setState(() {}), required: true),
              const SizedBox(height: 16),
              InputField(
                label: 'Password',
                controller: _password,
                obscureText: _obscure,
                required: true,
                onChanged: (_) => setState(() {}),
                suffix: IconButton(
                  icon: Icon(_obscure ? LucideIcons.eye : LucideIcons.eyeOff, size: 18, color: AppColors.mutedForeground),
                  onPressed: () => setState(() => _obscure = !_obscure),
                ),
              ),
              const SizedBox(height: 14),
              Row(
                children: [
                  GestureDetector(
                    onTap: () => setState(() => _remember = !_remember),
                    child: Container(
                      width: 20,
                      height: 20,
                      decoration: BoxDecoration(
                        color: _remember ? AppColors.gold : Colors.transparent,
                        border: Border.all(color: AppColors.borderGold),
                        borderRadius: BorderRadius.circular(5),
                      ),
                      child: _remember ? const Icon(LucideIcons.check, size: 14, color: AppColors.primaryForeground) : null,
                    ),
                  ),
                  const SizedBox(width: 8),
                  const Text('Ingat saya', style: TextStyle(color: AppColors.foreground, fontSize: 13)),
                ],
              ),
              const SizedBox(height: 24),
              PrimaryButton(
                onPressed: canSubmit ? _submit : null,
                loading: loading,
                child: const Text('Masuk'),
              ),
              const SizedBox(height: 16),
              const Text(
                'Lupa akun atau password? Hubungi admin jika perlu bantuan',
                textAlign: TextAlign.center,
                style: TextStyle(color: AppColors.mutedForeground, fontSize: 12),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _PermConfig {
  final IconData icon;
  final String title;
  final String desc;
  final String note;
  final AppScreen next;
  final AppPermissionKind kind;
  const _PermConfig({required this.icon, required this.title, required this.desc, required this.note, required this.next, required this.kind});
}

final Map<AppScreen, _PermConfig> _permConfigs = {
  AppScreen.permLocation: const _PermConfig(
    icon: LucideIcons.mapPin,
    title: 'Izin Lokasi',
    desc: 'Aplikasi memerlukan akses lokasi untuk mencatat titik kunjungan dan tracking perjalanan marketing.',
    note: 'Lokasi hanya digunakan selama jam kerja dan tidak dibagikan ke pihak ketiga.',
    next: AppScreen.permBg,
    kind: AppPermissionKind.location,
  ),
  AppScreen.permBg: const _PermConfig(
    icon: LucideIcons.navigation,
    title: 'Izin Lokasi Latar Belakang',
    desc: 'Agar tracking tetap berjalan meski aplikasi tidak dibuka, izinkan akses lokasi latar belakang.',
    note: 'Anda dapat menonaktifkan izin ini kapan saja lewat pengaturan perangkat.',
    next: AppScreen.permNotification,
    kind: AppPermissionKind.locationAlways,
  ),
  AppScreen.permNotification: const _PermConfig(
    icon: LucideIcons.bellRing,
    title: 'Izin Notifikasi',
    desc: 'Izinkan notifikasi untuk mendapat pengingat jadwal kunjungan dan status sinkronisasi data.',
    note: 'Notifikasi tidak akan mengganggu di luar jam kerja.',
    next: AppScreen.permCamera,
    kind: AppPermissionKind.notification,
  ),
  AppScreen.permCamera: const _PermConfig(
    icon: LucideIcons.camera,
    title: 'Izin Kamera',
    desc: 'Kamera digunakan untuk mengambil foto nasabah dan bukti transfer langsung dari aplikasi.',
    note: 'Foto disimpan di perangkat sebelum disinkronkan ke server.',
    next: AppScreen.dashboard,
    kind: AppPermissionKind.camera,
  ),
};

class PermissionScreen extends StatefulWidget {
  final NavController nav;
  final AppScreen screen;
  final LocationService? service;
  final TrackingController? tracking;
  const PermissionScreen({super.key, required this.nav, required this.screen, this.service, this.tracking});

  @override
  State<PermissionScreen> createState() => _PermissionScreenState();
}

class _PermissionScreenState extends State<PermissionScreen> {
  late final LocationService _service = widget.service ?? LocationService();
  bool _requesting = false;

  Future<void> _requestAndProceed(_PermConfig cfg) async {
    setState(() => _requesting = true);
    // Real OS permission dialog. The app proceeds through onboarding
    // regardless of the outcome (matching the original UX — permissions can
    // still be granted later from PermissionStatusScreen), it just no
    // longer *pretends* to ask.
    try {
      await _service.requestPermission(cfg.kind);
    } catch (_) {
      // Ignore — proceed to the next onboarding step either way.
    }
    if (!mounted) return;
    setState(() => _requesting = false);
    if (cfg.next == AppScreen.dashboard) await widget.tracking?.ensureStarted();
    if (!mounted) return;
    widget.nav.resetTo(cfg.next);
  }

  @override
  Widget build(BuildContext context) {
    final cfg = _permConfigs[widget.screen]!;
    return Scaffold(
      backgroundColor: AppColors.background,
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            children: [
              const Spacer(flex: 2),
              Container(
                width: 96,
                height: 96,
                decoration: BoxDecoration(color: AppColors.cardElevated, shape: BoxShape.circle, border: Border.all(color: AppColors.borderGold)),
                child: Icon(cfg.icon, color: AppColors.gold, size: 40),
              ),
              const SizedBox(height: 24),
              Text(cfg.title, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.foreground, fontSize: 20, fontWeight: FontWeight.w700)),
              const SizedBox(height: 10),
              Text(cfg.desc, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.mutedForeground, fontSize: 13, height: 1.5)),
              const SizedBox(height: 14),
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(color: AppColors.cardElevated, borderRadius: BorderRadius.circular(12)),
                child: Text(cfg.note, textAlign: TextAlign.center, style: const TextStyle(color: AppColors.mutedForeground, fontSize: 12)),
              ),
              const Spacer(flex: 3),
              PrimaryButton(
                onPressed: _requesting ? null : () => _requestAndProceed(cfg),
                loading: _requesting,
                child: const Text('Izinkan'),
              ),
              const SizedBox(height: 12),
              TextButton(
                onPressed: _requesting
                    ? null
                    : () {
                        if (widget.screen == AppScreen.permLocation) {
                          _showTrackingSheet(context, cfg);
                        } else {
                          widget.nav.resetTo(cfg.next);
                        }
                      },
                child: const Text('Nanti Saja', style: TextStyle(color: AppColors.mutedForeground, fontSize: 13)),
              ),
              const SizedBox(height: 8),
            ],
          ),
        ),
      ),
    );
  }

  void _showTrackingSheet(BuildContext context, _PermConfig cfg) {
    showModalBottomSheet(
      context: context,
      backgroundColor: AppColors.popover,
      shape: const RoundedRectangleBorder(borderRadius: BorderRadius.vertical(top: Radius.circular(24))),
      builder: (ctx) => Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text('Tracking belum aktif', style: TextStyle(color: AppColors.foreground, fontSize: 16, fontWeight: FontWeight.w700)),
            const SizedBox(height: 8),
            const Text('Tanpa izin lokasi, perjalanan Anda tidak akan tercatat otomatis.', style: TextStyle(color: AppColors.mutedForeground, fontSize: 13)),
            const SizedBox(height: 20),
            PrimaryButton(
              onPressed: () {
                Navigator.of(ctx).pop();
                widget.nav.resetTo(cfg.next);
              },
              child: const Text('Izinkan Sekarang'),
            ),
            const SizedBox(height: 10),
            SecondaryButton(
              onPressed: () {
                Navigator.of(ctx).pop();
                widget.nav.resetTo(AppScreen.dashboard, const NavParams(trackingDisabled: true));
              },
              child: const Text('Lanjutkan Tanpa Tracking'),
            ),
          ],
        ),
      ),
    );
  }
}
