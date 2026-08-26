import 'dart:io';
import 'dart:typed_data';

import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:path/path.dart' as p;
import 'package:path_provider/path_provider.dart';
import 'package:uuid/uuid.dart';

/// Encodes which multipart field/attachment-type a queued photo belongs to
/// alongside its local path, for storage in [SyncQueueEntry.filePaths]
/// (e.g. a member has one `member_photo` slot, but an operational report
/// has two — `disbursement`/`transfer_proof`). `=>` is used as the
/// separator (not `:`) so a Windows-style local path containing its own
/// drive-letter colon never gets mis-split.
String encodeAttachmentPath(String field, String localPath) => '$field=>$localPath';

/// Splits an entry produced by [encodeAttachmentPath] back into its field
/// name and local path. Returns null if the entry isn't in that format.
({String field, String path})? decodeAttachmentPath(String entry) {
  final i = entry.indexOf('=>');
  if (i == -1) return null;
  return (field: entry.substring(0, i), path: entry.substring(i + 2));
}

/// Storage seam for persisting picked photo bytes to a durable local file
/// — used when a multipart create fails offline, so the bytes survive an
/// app restart until [SyncDispatcher] can replay the upload (the picked
/// `XFile`'s own path isn't guaranteed to survive: on some platforms it's
/// a transient cache/temp file). Mirrors `SyncQueueStore`'s in-memory/real
/// split so tests never touch the real `path_provider` platform channel
/// (unmocked under plain `flutter test`).
abstract class AttachmentStore {
  Future<String> persist(List<int> bytes, String originalFilename);
  Future<Uint8List> read(String path);
  Future<void> delete(String path);
}

/// Default store: real on-disk files on Android/iOS/desktop, in-memory on
/// web (same "web is a dev-preview convenience, not the shipped target"
/// tradeoff as `createDefaultSyncQueueStore`). Memoized for the same
/// reason: every call site must persist to / read from the same store.
AttachmentStore? _defaultAttachmentStore;
AttachmentStore createDefaultAttachmentStore() => _defaultAttachmentStore ??= (kIsWeb ? InMemoryAttachmentStore() : FileAttachmentStore());

class InMemoryAttachmentStore implements AttachmentStore {
  final Map<String, Uint8List> _files = {};
  int _next = 1;

  @override
  Future<String> persist(List<int> bytes, String originalFilename) async {
    final path = 'mem-attachment://${_next++}-$originalFilename';
    _files[path] = Uint8List.fromList(bytes);
    return path;
  }

  @override
  Future<Uint8List> read(String path) async {
    final bytes = _files[path];
    if (bytes == null) throw StateError('Attachment not found: $path');
    return bytes;
  }

  @override
  Future<void> delete(String path) async => _files.remove(path);
}

class FileAttachmentStore implements AttachmentStore {
  Future<Directory> _dir() async {
    final docs = await getApplicationDocumentsDirectory();
    final dir = Directory(p.join(docs.path, 'pending_attachments'));
    if (!await dir.exists()) await dir.create(recursive: true);
    return dir;
  }

  @override
  Future<String> persist(List<int> bytes, String originalFilename) async {
    final dir = await _dir();
    final ext = originalFilename.contains('.') ? originalFilename.split('.').last : 'jpg';
    final file = File(p.join(dir.path, '${const Uuid().v4()}.$ext'));
    await file.writeAsBytes(bytes);
    return file.path;
  }

  @override
  Future<Uint8List> read(String path) => File(path).readAsBytes();

  @override
  Future<void> delete(String path) async {
    final file = File(path);
    if (await file.exists()) await file.delete();
  }
}
