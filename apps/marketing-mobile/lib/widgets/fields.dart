import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:lucide_icons/lucide_icons.dart';
import '../theme/app_colors.dart';

InputDecoration _fieldDecoration({String? hint, bool error = false, Widget? suffix}) {
  return InputDecoration(
    hintText: hint,
    hintStyle: const TextStyle(color: AppColors.disabled, fontSize: 14),
    filled: true,
    fillColor: error ? AppColors.badgeRedBg.withValues(alpha: 0.3) : AppColors.inputBackground,
    contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 13),
    suffixIcon: suffix,
    border: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: BorderSide(color: error ? AppColors.destructive : AppColors.inputBorder),
    ),
    enabledBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: BorderSide(color: error ? AppColors.destructive : AppColors.inputBorder),
    ),
    focusedBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: BorderSide(color: error ? AppColors.destructive : AppColors.gold, width: 1.5),
    ),
    disabledBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(12),
      borderSide: const BorderSide(color: AppColors.inputBorder),
    ),
  );
}

class _FieldLabel extends StatelessWidget {
  final String label;
  final bool required;
  const _FieldLabel(this.label, {this.required = false});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 6),
      child: RichText(
        text: TextSpan(
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: AppColors.goldSoft),
          children: [
            TextSpan(text: label),
            if (required) const TextSpan(text: ' *', style: TextStyle(color: AppColors.destructive)),
          ],
        ),
      ),
    );
  }
}

class InputField extends StatelessWidget {
  final String label;
  final String? hint;
  final bool required;
  final TextEditingController? controller;
  final String? errorText;
  final TextInputType? inputMode;
  final bool obscureText;
  final Widget? suffix;
  final ValueChanged<String>? onChanged;
  final bool enabled;

  const InputField({
    super.key,
    required this.label,
    this.hint,
    this.required = false,
    this.controller,
    this.errorText,
    this.inputMode,
    this.obscureText = false,
    this.suffix,
    this.onChanged,
    this.enabled = true,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _FieldLabel(label, required: required),
        TextField(
          controller: controller,
          keyboardType: inputMode,
          obscureText: obscureText,
          enabled: enabled,
          onChanged: onChanged,
          style: const TextStyle(color: AppColors.foreground, fontSize: 14),
          decoration: _fieldDecoration(hint: hint, error: errorText != null, suffix: suffix),
        ),
        if (errorText != null)
          Padding(
            padding: const EdgeInsets.only(top: 4),
            child: Text(errorText!, style: const TextStyle(color: AppColors.destructive, fontSize: 12)),
          ),
      ],
    );
  }
}

class _ThousandsFormatter extends TextInputFormatter {
  final _fmt = NumberFormat.decimalPattern('id_ID');

  @override
  TextEditingValue formatEditUpdate(TextEditingValue oldValue, TextEditingValue newValue) {
    final digits = newValue.text.replaceAll(RegExp(r'[^0-9]'), '');
    if (digits.isEmpty) return newValue.copyWith(text: '');
    final n = int.parse(digits);
    final formatted = _fmt.format(n);
    return TextEditingValue(
      text: formatted,
      selection: TextSelection.collapsed(offset: formatted.length),
    );
  }
}

class CurrencyField extends StatelessWidget {
  final String label;
  final bool required;
  final TextEditingController controller;
  final String? errorText;
  final ValueChanged<int>? onChanged;

  const CurrencyField({
    super.key,
    required this.label,
    this.required = false,
    required this.controller,
    this.errorText,
    this.onChanged,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _FieldLabel(label, required: required),
        TextField(
          controller: controller,
          keyboardType: TextInputType.number,
          inputFormatters: [_ThousandsFormatter()],
          style: const TextStyle(color: AppColors.foreground, fontSize: 14, fontFamily: 'monospace'),
          onChanged: (v) {
            final n = int.tryParse(v.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
            onChanged?.call(n);
          },
          decoration: _fieldDecoration(
            hint: '0',
            error: errorText != null,
            suffix: null,
          ).copyWith(
            prefixText: 'Rp',
            prefixStyle: const TextStyle(color: AppColors.mutedForeground, fontWeight: FontWeight.w600),
          ),
        ),
        if (errorText != null)
          Padding(
            padding: const EdgeInsets.only(top: 4),
            child: Text(errorText!, style: const TextStyle(color: AppColors.destructive, fontSize: 12)),
          ),
      ],
    );
  }
}

class TextareaField extends StatelessWidget {
  final String label;
  final bool required;
  final TextEditingController? controller;
  final int rows;
  final String? hint;

  const TextareaField({
    super.key,
    required this.label,
    this.required = false,
    this.controller,
    this.rows = 3,
    this.hint,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _FieldLabel(label, required: required),
        TextField(
          controller: controller,
          maxLines: rows,
          style: const TextStyle(color: AppColors.foreground, fontSize: 14),
          decoration: _fieldDecoration(hint: hint),
        ),
      ],
    );
  }
}

class SelectField extends StatelessWidget {
  final String label;
  final bool required;
  final String? value;
  final List<String> options;
  final ValueChanged<String?> onChanged;
  final bool enabled;

  const SelectField({
    super.key,
    required this.label,
    this.required = false,
    required this.value,
    required this.options,
    required this.onChanged,
    this.enabled = true,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _FieldLabel(label, required: required),
        Container(
          decoration: BoxDecoration(
            color: AppColors.inputBackground,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: AppColors.inputBorder),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 14),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<String>(
              value: value,
              isExpanded: true,
              hint: const Text('Pilih...', style: TextStyle(color: AppColors.disabled, fontSize: 14)),
              icon: const Icon(LucideIcons.chevronDown, color: AppColors.mutedForeground, size: 18),
              dropdownColor: AppColors.popover,
              style: const TextStyle(color: AppColors.foreground, fontSize: 14),
              onChanged: enabled ? onChanged : null,
              items: options
                  .map((o) => DropdownMenuItem(value: o, child: Text(o)))
                  .toList(),
            ),
          ),
        ),
      ],
    );
  }
}

class PhotoPickerField extends StatelessWidget {
  final String label;
  final bool required;
  final bool hasPhoto;
  final bool loading;
  final VoidCallback onCamera;
  final VoidCallback onGallery;
  final VoidCallback onRemove;

  const PhotoPickerField({
    super.key,
    required this.label,
    this.required = false,
    required this.hasPhoto,
    this.loading = false,
    required this.onCamera,
    required this.onGallery,
    required this.onRemove,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _FieldLabel(label, required: required),
        AspectRatio(
          aspectRatio: 3 / 4,
          child: ConstrainedBox(
            constraints: const BoxConstraints(maxWidth: 180),
            child: Container(
              decoration: BoxDecoration(
                color: AppColors.inputBackground,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: AppColors.border),
              ),
              child: loading
                  ? const Center(child: CircularProgressIndicator(color: AppColors.gold, strokeWidth: 2))
                  : hasPhoto
                      ? Stack(
                          children: [
                            const Center(
                              child: Icon(LucideIcons.image, color: AppColors.mutedForeground, size: 32),
                            ),
                            const Positioned(
                              left: 8,
                              right: 8,
                              bottom: 8,
                              child: Text('Pratinjau Foto', style: TextStyle(fontSize: 11, color: AppColors.mutedForeground)),
                            ),
                            Positioned(
                              top: 6,
                              right: 6,
                              child: GestureDetector(
                                onTap: onRemove,
                                child: Container(
                                  padding: const EdgeInsets.all(4),
                                  decoration: const BoxDecoration(color: AppColors.destructive, shape: BoxShape.circle),
                                  child: const Icon(LucideIcons.x, size: 12, color: Colors.white),
                                ),
                              ),
                            ),
                          ],
                        )
                      : const Center(
                          child: Icon(LucideIcons.camera, color: AppColors.disabled, size: 32),
                        ),
            ),
          ),
        ),
        const SizedBox(height: 10),
        Row(
          children: [
            Expanded(
              child: OutlinedButton.icon(
                onPressed: onCamera,
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: AppColors.borderGold),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                icon: const Icon(LucideIcons.camera, size: 16, color: AppColors.foreground),
                label: Text(hasPhoto ? 'Ganti' : 'Ambil Foto', style: const TextStyle(color: AppColors.foreground, fontSize: 13)),
              ),
            ),
            const SizedBox(width: 8),
            Expanded(
              child: OutlinedButton.icon(
                onPressed: onGallery,
                style: OutlinedButton.styleFrom(
                  side: const BorderSide(color: AppColors.borderGold),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
                icon: const Icon(LucideIcons.image, size: 16, color: AppColors.foreground),
                label: const Text('Galeri', style: TextStyle(color: AppColors.foreground, fontSize: 13)),
              ),
            ),
          ],
        ),
        if (hasPhoto)
          Padding(
            padding: const EdgeInsets.only(top: 6),
            child: GestureDetector(
              onTap: onRemove,
              child: const Text('Hapus Foto', style: TextStyle(color: AppColors.destructive, fontSize: 12, fontWeight: FontWeight.w600)),
            ),
          ),
      ],
    );
  }
}
