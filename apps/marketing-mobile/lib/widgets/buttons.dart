import 'package:flutter/material.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../theme/app_colors.dart';

class PrimaryButton extends StatelessWidget {
  final Widget child;
  final VoidCallback? onPressed;
  final bool danger;
  final bool loading;

  const PrimaryButton({
    super.key,
    required this.child,
    required this.onPressed,
    this.danger = false,
    this.loading = false,
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 48,
      child: ElevatedButton(
        onPressed: loading ? null : onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: danger ? AppColors.destructive : AppColors.gold,
          foregroundColor: danger ? AppColors.destructiveForeground : AppColors.primaryForeground,
          disabledBackgroundColor: (danger ? AppColors.destructive : AppColors.gold).withValues(alpha: 0.5),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          elevation: 0,
        ),
        child: loading
            ? Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                      strokeWidth: 2,
                      valueColor: AlwaysStoppedAnimation(danger ? AppColors.destructiveForeground : AppColors.primaryForeground),
                    ),
                  ),
                  const SizedBox(width: 10),
                  const Text('Memproses...', style: TextStyle(fontWeight: FontWeight.w600)),
                ],
              )
            : DefaultTextStyle.merge(
                style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15),
                child: child,
              ),
      ),
    );
  }
}

class SecondaryButton extends StatelessWidget {
  final Widget child;
  final VoidCallback? onPressed;
  const SecondaryButton({super.key, required this.child, required this.onPressed});

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: double.infinity,
      height: 48,
      child: OutlinedButton(
        onPressed: onPressed,
        style: OutlinedButton.styleFrom(
          side: const BorderSide(color: AppColors.borderGold),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          backgroundColor: Colors.transparent,
        ),
        child: DefaultTextStyle.merge(
          style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 15, color: AppColors.foreground),
          child: child,
        ),
      ),
    );
  }
}

class SpinningIcon extends StatefulWidget {
  final IconData icon;
  final Color color;
  final double size;
  const SpinningIcon({super.key, this.icon = LucideIcons.refreshCw, this.color = AppColors.foreground, this.size = 16});

  @override
  State<SpinningIcon> createState() => _SpinningIconState();
}

class _SpinningIconState extends State<SpinningIcon> with SingleTickerProviderStateMixin {
  late final AnimationController _controller =
      AnimationController(vsync: this, duration: const Duration(seconds: 1))..repeat();

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return RotationTransition(
      turns: _controller,
      child: Icon(widget.icon, color: widget.color, size: widget.size),
    );
  }
}
