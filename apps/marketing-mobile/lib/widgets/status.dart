import 'package:flutter/material.dart';
import '../theme/app_colors.dart';

/// Status -> (bg, text) colors, ported verbatim from BADGE_MAP in shared.tsx.
class _BadgeColors {
  final Color bg;
  final Color text;
  const _BadgeColors(this.bg, this.text);
}

const Map<String, _BadgeColors> _badgeMap = {
  'Baru': _BadgeColors(AppColors.badgeBlueBg, AppColors.badgeBlueText),
  'Tertarik': _BadgeColors(AppColors.badgeGoldBg, AppColors.badgeGoldText),
  'Perlu Follow Up': _BadgeColors(AppColors.badgeOrangeBg, AppColors.badgeOrangeText),
  'Tidak Tertarik': _BadgeColors(AppColors.badgeNeutralBg, AppColors.badgeNeutralText),
  'Selesai': _BadgeColors(AppColors.badgeGreenBg, AppColors.badgeGreenText),
  'Terkirim': _BadgeColors(AppColors.badgeGreenBg, AppColors.badgeGreenText),
  'Menunggu Sinkronisasi': _BadgeColors(AppColors.badgeAmberBg, AppColors.badgeAmberText),
  'Belum Dikunjungi': _BadgeColors(AppColors.badgeNeutralBg, AppColors.badgeNeutralText),
  'Berlangsung': _BadgeColors(AppColors.badgeGoldBg, AppColors.badgeGoldText),
  'Draft': _BadgeColors(AppColors.badgeNeutralBg, AppColors.badgeNeutralText),
  'Gagal': _BadgeColors(AppColors.badgeRedBg, AppColors.badgeRedText),
  'Aktif': _BadgeColors(AppColors.badgeGreenBg, AppColors.badgeGreenText),
  'Diizinkan': _BadgeColors(AppColors.badgeGreenBg, AppColors.badgeGreenText),
  'Berhasil Bertemu': _BadgeColors(AppColors.badgeGreenBg, AppColors.badgeGreenText),
  'Tidak Bertemu': _BadgeColors(AppColors.badgeRedBg, AppColors.badgeRedText),
  'Transaksi Selesai': _BadgeColors(AppColors.badgeGreenBg, AppColors.badgeGreenText),
  'Menunggu': _BadgeColors(AppColors.badgeAmberBg, AppColors.badgeAmberText),
  'Disetujui': _BadgeColors(AppColors.badgeGreenBg, AppColors.badgeGreenText),
  'Ditolak': _BadgeColors(AppColors.badgeRedBg, AppColors.badgeRedText),
};

class StatusBadge extends StatelessWidget {
  final String status;
  final bool sm;
  const StatusBadge({super.key, required this.status, this.sm = false});

  @override
  Widget build(BuildContext context) {
    final colors = _badgeMap[status] ??
        const _BadgeColors(AppColors.cardElevated, AppColors.mutedForeground);
    return Container(
      padding: EdgeInsets.symmetric(
        horizontal: sm ? 8 : 10,
        vertical: sm ? 3 : 5,
      ),
      decoration: BoxDecoration(
        color: colors.bg,
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        status,
        style: TextStyle(
          color: colors.text,
          fontSize: sm ? 11 : 12,
          fontWeight: FontWeight.w600,
        ),
      ),
    );
  }
}

enum StatusDotType { online, offline, warning }

class StatusDot extends StatelessWidget {
  final StatusDotType type;
  const StatusDot({super.key, this.type = StatusDotType.online});

  @override
  Widget build(BuildContext context) {
    final color = switch (type) {
      StatusDotType.online => AppColors.success,
      StatusDotType.offline => AppColors.destructive,
      StatusDotType.warning => AppColors.warning,
    };
    return Container(
      width: 8,
      height: 8,
      decoration: BoxDecoration(
        color: color,
        shape: BoxShape.circle,
        boxShadow: type == StatusDotType.online
            ? [BoxShadow(color: color.withValues(alpha: 0.4), blurRadius: 4, spreadRadius: 2)]
            : null,
      ),
    );
  }
}
