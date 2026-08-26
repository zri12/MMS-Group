import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'app_colors.dart';

/// App-wide ThemeData. Ported from the Manrope/DM Mono, black & gold
/// design tokens in src/styles/theme.css of the reference React app.
class AppTheme {
  AppTheme._();

  static TextStyle mono({
    double fontSize = 14,
    FontWeight fontWeight = FontWeight.w500,
    Color color = AppColors.foreground,
  }) {
    return GoogleFonts.dmMono(
      fontSize: fontSize,
      fontWeight: fontWeight,
      color: color,
    );
  }

  static ThemeData get dark {
    final base = ThemeData.dark(useMaterial3: true);
    final textTheme = GoogleFonts.manropeTextTheme(base.textTheme).apply(
      bodyColor: AppColors.foreground,
      displayColor: AppColors.foreground,
    );

    return base.copyWith(
      scaffoldBackgroundColor: AppColors.background,
      primaryColor: AppColors.gold,
      textTheme: textTheme,
      colorScheme: base.colorScheme.copyWith(
        surface: AppColors.background,
        primary: AppColors.gold,
        secondary: AppColors.goldLight,
        error: AppColors.destructive,
      ),
      dividerColor: AppColors.border,
      splashFactory: NoSplash.splashFactory,
      highlightColor: Colors.transparent,
      appBarTheme: const AppBarTheme(
        backgroundColor: AppColors.backgroundSecondary,
        elevation: 0,
        centerTitle: false,
      ),
    );
  }
}
