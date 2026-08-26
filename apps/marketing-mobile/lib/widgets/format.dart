import 'package:intl/intl.dart';

/// formatRupiah(n) -> "Rp" + n.toLocaleString("id-ID"), ported from shared.tsx.
String formatRupiah(int n) {
  final f = NumberFormat.decimalPattern('id_ID');
  return 'Rp${f.format(n)}';
}

/// mapsUrl(coords) -> google maps deep link, ported from shared.tsx.
String mapsUrl(String coords) {
  final stripped = coords.replaceAll(RegExp(r'\s+'), '');
  return 'https://www.google.com/maps?q=$stripped';
}

/// fileNameFromNama(nama) -> lowercase first word + ".png", ported from shared.tsx.
String fileNameFromNama(String nama) {
  final trimmed = nama.trim();
  if (trimmed.isEmpty) return 'foto.png';
  final first = trimmed.split(RegExp(r'\s+')).first;
  return '${first.toLowerCase()}.png';
}

/// formatDistanceKm(meters) -> "8,4 km", for real TrackingSession distances.
String formatDistanceKm(double meters) {
  final km = meters / 1000;
  return '${km.toStringAsFixed(1).replaceAll('.', ',')} km';
}

/// Formats a `time` field for the real API's `date_format:H:i:s` validation
/// rule (Prospect/Member/VisitReport/OperationalReport all require this
/// exact format) — found via live QA against the real backend: every
/// create screen previously sent `HH:mm` (no seconds), which the backend
/// rejected with a 422 on every single submission.
String apiTimeFormat(DateTime dt) {
  final h = dt.hour.toString().padLeft(2, '0');
  final m = dt.minute.toString().padLeft(2, '0');
  final s = dt.second.toString().padLeft(2, '0');
  return '$h:$m:$s';
}

/// initials("Deden") -> "D", initials("Ahmad Hidayat") -> "AH", for avatar
/// badges when there's no real photo to show.
String initials(String name) {
  final words = name.trim().split(RegExp(r'\s+')).where((w) => w.isNotEmpty).toList();
  if (words.isEmpty) return '?';
  final letters = words.take(2).map((w) => w[0].toUpperCase()).join();
  return letters;
}
