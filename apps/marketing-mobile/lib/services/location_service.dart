import 'package:geolocator/geolocator.dart';
import 'package:permission_handler/permission_handler.dart' as ph;

/// The four permission types this app's onboarding flow requests, mapped
/// 1:1 to `permLocation`/`permBg`/`permNotification`/`permCamera` in
/// `navigation/screen.dart`.
enum AppPermissionKind { location, locationAlways, notification, camera }

class LocationServiceException implements Exception {
  final String message;
  const LocationServiceException(this.message);
  @override
  String toString() => message;
}

/// Wraps `permission_handler` (onboarding permission screens) and
/// `geolocator` (actual GPS position reads) — see
/// BACKEND_INTEGRATION_TASKS.md Phase 4. Position reads are used by
/// `LocationCard`'s consuming screens once those screens are unblocked by
/// the Phase 0 Consumer/Prospect/Member decision.
class LocationService {
  Future<ph.PermissionStatus> requestPermission(AppPermissionKind kind) {
    final permission = switch (kind) {
      AppPermissionKind.location => ph.Permission.locationWhenInUse,
      AppPermissionKind.locationAlways => ph.Permission.locationAlways,
      AppPermissionKind.notification => ph.Permission.notification,
      AppPermissionKind.camera => ph.Permission.camera,
    };
    return permission.request();
  }

  /// Real current GPS position. Throws [LocationServiceException] with a
  /// user-facing Indonesian message on failure (GPS disabled, permission
  /// denied, timeout) rather than a raw platform exception.
  Future<Position> getCurrentPosition() async {
    final serviceEnabled = await Geolocator.isLocationServiceEnabled();
    if (!serviceEnabled) {
      throw const LocationServiceException('Layanan lokasi tidak aktif. Aktifkan GPS perangkat Anda.');
    }

    var permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.denied) {
      permission = await Geolocator.requestPermission();
    }
    if (permission == LocationPermission.denied || permission == LocationPermission.deniedForever) {
      throw const LocationServiceException('Izin lokasi ditolak. Aktifkan izin lokasi di pengaturan aplikasi.');
    }

    try {
      return await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(accuracy: LocationAccuracy.high, timeLimit: Duration(seconds: 15)),
      );
    } catch (_) {
      throw const LocationServiceException('Gagal mendapatkan lokasi. Coba lagi.');
    }
  }
}
