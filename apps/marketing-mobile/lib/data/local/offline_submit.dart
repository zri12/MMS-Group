import 'dart:convert';

import 'package:image_picker/image_picker.dart' show XFile;

import '../../api/api_client.dart';
import 'attachment_store.dart';
import 'sync_queue.dart';

/// Thrown by [submitWithOfflineFallback] instead of returning a value when
/// the record was queued locally rather than sent — callers should treat
/// this as a (different) success, not an error.
class OfflineQueuedException implements Exception {
  const OfflineQueuedException();
}

/// Runs [send]; if it fails because the server couldn't be reached at all
/// (a connection error/timeout — [ApiException.statusCode] is null, since
/// no HTTP response was ever received), the record is queued into [store]
/// for later retry instead of surfacing a hard failure. Server-returned
/// errors (422 validation, 409 conflict, etc. — anything with a real status
/// code) are NOT swallowed here; those need the user's attention now, not a
/// silent queue. See BACKEND_INTEGRATION_TASKS.md Phase 2.
Future<T> submitWithOfflineFallback<T>({
  required SyncQueueStore store,
  required String entityType,
  required String localUuid,
  required Map<String, dynamic> payload,
  required Future<T> Function() send,
}) async {
  try {
    return await send();
  } on ApiException catch (e) {
    if (e.statusCode != null) rethrow;
    final now = DateTime.now();
    await store.enqueue(
      SyncQueueEntry(
        entityType: entityType,
        localUuid: localUuid,
        operation: 'create',
        payloadJson: jsonEncode(payload),
        status: SyncQueueStatus.menungguSinkronisasi,
        createdAt: now,
        updatedAt: now,
      ),
    );
    throw const OfflineQueuedException();
  }
}

/// Same offline-fallback behavior as [submitWithOfflineFallback], but for
/// multipart creates that include one or more photos. On a connection
/// failure, each non-null [photos] entry's bytes are copied via
/// [attachmentStore] to a durable local file (the picked `XFile`'s own
/// path isn't guaranteed to survive an app restart) and recorded in
/// [SyncQueueEntry.filePaths] (as `field=>localPath`, see
/// [encodeAttachmentPath]) so [SyncDispatcher] can replay the exact same
/// upload later. [photos] keys are the field/attachment-type name the
/// dispatcher will need to reconstruct the multipart request (e.g.
/// `'member_photo'`, `'photo'`, `'disbursement'`).
Future<T> submitAttachmentWithOfflineFallback<T>({
  required SyncQueueStore store,
  required AttachmentStore attachmentStore,
  required String entityType,
  required String localUuid,
  required Map<String, dynamic> payload,
  required Map<String, XFile?> photos,
  Map<String, String?> attachmentCaptions = const {},
  required Future<T> Function() send,
}) async {
  try {
    return await send();
  } on ApiException catch (e) {
    if (e.statusCode != null) rethrow;
    final now = DateTime.now();
    final filePaths = <String>[];
    for (final entry in photos.entries) {
      final file = entry.value;
      if (file == null) continue;
      final bytes = await file.readAsBytes();
      final localPath = await attachmentStore.persist(bytes, file.name);
      filePaths.add(encodeAttachmentPath(entry.key, localPath));
    }
    final queuedPayload = Map<String, dynamic>.from(payload);
    final captions = attachmentCaptions.map(
      (type, caption) => MapEntry(type, caption?.trim() ?? ''),
    )..removeWhere((_, caption) => caption.isEmpty);
    if (captions.isNotEmpty) queuedPayload['_attachment_captions'] = captions;
    await store.enqueue(
      SyncQueueEntry(
        entityType: entityType,
        localUuid: localUuid,
        operation: 'create',
        payloadJson: jsonEncode(queuedPayload),
        filePaths: filePaths,
        status: SyncQueueStatus.menungguSinkronisasi,
        createdAt: now,
        updatedAt: now,
      ),
    );
    throw const OfflineQueuedException();
  }
}
