import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../theme/app_colors.dart';

enum KspLogoSize { sm, md, lg }

class KspLogo extends StatelessWidget {
  final KspLogoSize size;
  const KspLogo({super.key, this.size = KspLogoSize.md});

  @override
  Widget build(BuildContext context) {
    final px = switch (size) { KspLogoSize.sm => 36.0, KspLogoSize.md => 48.0, KspLogoSize.lg => 64.0 };
    return Container(
      width: px,
      height: px,
      padding: EdgeInsets.all(px * 0.08),
      decoration: BoxDecoration(
        color: const Color(0xFFFDF8EC),
        shape: BoxShape.circle,
        border: Border.all(color: AppColors.gold, width: 1.5),
      ),
      child: ClipOval(
        child: Image.asset(
          'assets/images/logo-ksp.jpg',
          fit: BoxFit.cover,
          errorBuilder: (_, __, ___) => const Icon(LucideIcons.landmark, color: AppColors.gold),
        ),
      ),
    );
  }
}

class OfflineBanner extends StatelessWidget {
  final bool offline;
  const OfflineBanner({super.key, required this.offline});

  @override
  Widget build(BuildContext context) {
    if (!offline) return const SizedBox.shrink();
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
      color: AppColors.badgeAmberBg,
      child: Row(
        children: const [
          Icon(LucideIcons.wifiOff, size: 16, color: AppColors.warning),
          SizedBox(width: 8),
          Expanded(
            child: Text(
              'Tidak ada koneksi internet. Data tetap disimpan di perangkat.',
              style: TextStyle(color: AppColors.warning, fontSize: 12, fontWeight: FontWeight.w600),
            ),
          ),
        ],
      ),
    );
  }
}

class PageHeader extends StatelessWidget {
  final String title;
  final String? subtitle;
  final VoidCallback? onBack;
  final Widget? action;

  const PageHeader({super.key, required this.title, this.subtitle, this.onBack, this.action});

  @override
  Widget build(BuildContext context) {
    return Container(
      color: AppColors.backgroundSecondary,
      padding: const EdgeInsets.fromLTRB(8, 12, 16, 16),
      child: Row(
        children: [
          if (onBack != null)
            IconButton(
              onPressed: onBack,
              icon: const Icon(LucideIcons.chevronLeft, color: AppColors.foreground),
            )
          else
            const SizedBox(width: 44),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppColors.foreground)),
                if (subtitle != null)
                  Padding(
                    padding: const EdgeInsets.only(top: 2),
                    child: Text(subtitle!, style: const TextStyle(fontSize: 12, color: AppColors.mutedForeground)),
                  ),
              ],
            ),
          ),
          if (action != null) action!,
        ],
      ),
    );
  }
}

class EmptyState extends StatelessWidget {
  final IconData icon;
  final String title;
  final String desc;
  const EmptyState({super.key, required this.icon, required this.title, required this.desc});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 48, horizontal: 24),
      child: Column(
        children: [
          Container(
            width: 64,
            height: 64,
            decoration: BoxDecoration(color: AppColors.cardElevated, borderRadius: BorderRadius.circular(16)),
            child: Icon(icon, color: AppColors.disabled, size: 28),
          ),
          const SizedBox(height: 16),
          Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.foreground)),
          const SizedBox(height: 6),
          Text(desc, textAlign: TextAlign.center, style: const TextStyle(fontSize: 13, color: AppColors.mutedForeground)),
        ],
      ),
    );
  }
}

class SectionCard extends StatelessWidget {
  final String? title;
  final List<Widget> children;
  const SectionCard({super.key, this.title, required this.children});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.card,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (title != null) ...[
            Text(title!, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15, color: AppColors.foreground)),
            const SizedBox(height: 12),
          ],
          ...children,
        ],
      ),
    );
  }
}

class InfoRow extends StatelessWidget {
  final String label;
  final String value;
  const InfoRow({super.key, required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 10),
      decoration: const BoxDecoration(border: Border(bottom: BorderSide(color: AppColors.border))),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(label, style: const TextStyle(fontSize: 13, color: AppColors.mutedForeground)),
          const SizedBox(width: 12),
          Flexible(
            child: Text(value, textAlign: TextAlign.right, style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.foreground)),
          ),
        ],
      ),
    );
  }
}

class AppFilterChip extends StatelessWidget {
  final String label;
  final bool active;
  final VoidCallback onTap;
  const AppFilterChip({super.key, required this.label, required this.active, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
        decoration: BoxDecoration(
          color: active ? AppColors.gold : AppColors.card,
          borderRadius: BorderRadius.circular(999),
          border: Border.all(color: active ? AppColors.gold : AppColors.border),
        ),
        child: Text(
          label,
          style: TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: active ? AppColors.primaryForeground : AppColors.mutedForeground,
          ),
        ),
      ),
    );
  }
}

Future<void> showModalConfirm(
  BuildContext context, {
  required String title,
  required String desc,
  required String confirmLabel,
  required VoidCallback onConfirm,
  String cancelLabel = 'Batal',
  bool danger = false,
}) {
  return showDialog(
    context: context,
    barrierColor: Colors.black.withValues(alpha: 0.6),
    builder: (ctx) {
      return Dialog(
        backgroundColor: AppColors.popover,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(18)),
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16, color: AppColors.foreground)),
              const SizedBox(height: 8),
              Text(desc, style: const TextStyle(fontSize: 13, color: AppColors.mutedForeground)),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                height: 44,
                child: ElevatedButton(
                  style: ElevatedButton.styleFrom(
                    backgroundColor: danger ? AppColors.destructive : AppColors.gold,
                    foregroundColor: danger ? AppColors.destructiveForeground : AppColors.primaryForeground,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () {
                    Navigator.of(ctx).pop();
                    onConfirm();
                  },
                  child: Text(confirmLabel, style: const TextStyle(fontWeight: FontWeight.w600)),
                ),
              ),
              const SizedBox(height: 8),
              SizedBox(
                width: double.infinity,
                height: 44,
                child: OutlinedButton(
                  style: OutlinedButton.styleFrom(
                    side: const BorderSide(color: AppColors.borderGold),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  ),
                  onPressed: () => Navigator.of(ctx).pop(),
                  child: Text(cancelLabel, style: const TextStyle(color: AppColors.foreground, fontWeight: FontWeight.w600)),
                ),
              ),
            ],
          ),
        ),
      );
    },
  );
}
