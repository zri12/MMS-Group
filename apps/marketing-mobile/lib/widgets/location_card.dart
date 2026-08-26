import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../theme/app_colors.dart';
import 'primitives.dart';
import 'status.dart';
import 'buttons.dart';

enum LocationState { idle, loading, success, failed }

class LocationCard extends StatelessWidget {
  final LocationState state;
  final String address;
  final String coords;
  final String updatedAt;
  final VoidCallback onRefresh;

  const LocationCard({
    super.key,
    required this.state,
    required this.address,
    required this.coords,
    required this.updatedAt,
    required this.onRefresh,
  });

  @override
  Widget build(BuildContext context) {
    final statusText = switch (state) {
      LocationState.loading => 'Mendapatkan lokasi...',
      LocationState.success => 'Lokasi berhasil didapatkan',
      LocationState.failed => 'Lokasi belum berhasil didapatkan',
      LocationState.idle => 'Lokasi berhasil didapatkan',
    };
    final dotType = switch (state) {
      LocationState.failed => StatusDotType.offline,
      LocationState.loading => StatusDotType.warning,
      _ => StatusDotType.online,
    };

    return SectionCard(
      title: 'Lokasi Pengambilan Data',
      children: [
        Row(
          children: [
            StatusDot(type: dotType),
            const SizedBox(width: 8),
            Text(statusText, style: const TextStyle(fontSize: 13, color: AppColors.mutedForeground)),
          ],
        ),
        const SizedBox(height: 8),
        InfoRow(label: 'Alamat', value: address),
        InfoRow(label: 'Koordinat', value: coords),
        InfoRow(label: 'Diperbarui', value: updatedAt),
        const SizedBox(height: 12),
        SecondaryButton(
          onPressed: onRefresh,
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if (state == LocationState.loading)
                const SpinningIcon(size: 16, color: AppColors.foreground)
              else
                const Icon(LucideIcons.refreshCw, size: 16, color: AppColors.foreground),
              const SizedBox(width: 8),
              const Text('Perbarui Lokasi'),
            ],
          ),
        ),
      ],
    );
  }
}
