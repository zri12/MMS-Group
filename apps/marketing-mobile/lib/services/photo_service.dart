import 'package:image_picker/image_picker.dart';

class PhotoServiceException implements Exception {
  final String message;
  const PhotoServiceException(this.message);
  @override
  String toString() => message;
}

/// Wraps `image_picker` for camera/gallery photo capture across
/// Member/Visit-Report/Operational-Report attachment forms (see
/// BACKEND_INTEGRATION_TASKS.md Phase 5). Compression is done via
/// `image_picker`'s own `maxWidth`/`imageQuality` params on pick (the
/// idiomatic approach — no separate compression package needed) rather
/// than a hardcoded server-side KB cap, since that cap isn't finalized per
/// the backend's own open questions.
class PhotoService {
  PhotoService([ImagePicker? picker]) : _picker = picker ?? ImagePicker();
  final ImagePicker _picker;

  static const _acceptedExtensions = {'jpg', 'jpeg', 'png', 'webp'};
  static const _maxWidth = 1600.0;
  static const _imageQuality = 85;

  Future<XFile?> pickFromCamera() => _pick(ImageSource.camera);
  Future<XFile?> pickFromGallery() => _pick(ImageSource.gallery);

  Future<XFile?> _pick(ImageSource source) async {
    XFile? file;
    try {
      file = await _picker.pickImage(source: source, maxWidth: _maxWidth, imageQuality: _imageQuality);
    } catch (_) {
      throw const PhotoServiceException('Gagal mengambil foto. Coba lagi.');
    }
    if (file == null) return null;
    final ext = file.name.split('.').last.toLowerCase();
    if (!_acceptedExtensions.contains(ext)) {
      throw const PhotoServiceException('Format file tidak didukung. Gunakan JPG, PNG, atau WEBP.');
    }
    return file;
  }
}
