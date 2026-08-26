import 'dart:convert';

import 'package:dio/dio.dart' show MultipartFile;

import '../../api/api_client.dart';
import '../../services/member_service.dart';
import '../../services/operational_report_service.dart';
import '../../services/prospect_service.dart';
import '../../services/tracking_service.dart';
import '../../services/visit_report_service.dart';
import 'attachment_store.dart';
import 'sync_queue.dart';

/// Replays queued offline records against the real API.
///
/// `prospect`/`operational_report` carry a plain JSON body. `member`/
/// `visit_report`/`operational_report_attachment` carry one or more photos
/// — their bytes were persisted to a durable local file by
/// `submitAttachmentWithOfflineFallback` at queue time (see
/// `offline_submit.dart`/`attachment_store.dart`); `entry.filePaths` holds
/// `field=>localPath` entries this dispatcher reads back and re-wraps as
/// `MultipartFile`s before replaying. `tracking_points` carries a GPS
/// points batch that failed to flush live (see `TrackingController`).
///
/// Failure handling is status-code-aware rather than treating every error
/// the same:
/// - Transient errors (no response at all — connection dropped mid-dispatch
///   even though the queue entry was created while offline earlier —
///   `429` rate-limited, or a `5xx` server error) get an exponential-backoff
///   retry via [SyncQueueStore.scheduleRetry], up to [_maxRetries] attempts.
/// - Permanent errors (`401`/`403`/`404`/`409`/`422`/other `4xx`) are marked
///   `Gagal` immediately and are NOT retried automatically — resending the
///   identical payload would just fail identically again, and the user
///   needs to see and act on it (`SyncStatusScreen`), not have it silently
///   retried forever.
/// - A `401` specifically also stops the rest of the dispatch run early:
///   the session is invalid (`ApiClient.onUnauthorized` already triggers a
///   force-logout via `AuthController`), so attempting the remaining queue
///   entries against a dead token would just fail every one of them.
class SyncDispatcher {
  SyncDispatcher({
    ProspectService? prospectService,
    OperationalReportService? operationalReportService,
    MemberService? memberService,
    VisitReportService? visitReportService,
    TrackingService? trackingService,
    AttachmentStore? attachmentStore,
  }) : _prospectService = prospectService ?? ProspectService(),
       _operationalReportService =
           operationalReportService ?? OperationalReportService(),
       _memberService = memberService ?? MemberService(),
       _visitReportService = visitReportService ?? VisitReportService(),
       _trackingService = trackingService ?? TrackingService(),
       _attachmentStore = attachmentStore ?? createDefaultAttachmentStore();

  final ProspectService _prospectService;
  final OperationalReportService _operationalReportService;
  final MemberService _memberService;
  final VisitReportService _visitReportService;
  final TrackingService _trackingService;
  final AttachmentStore _attachmentStore;

  static const _maxRetries = 8;
  static const _baseBackoff = Duration(seconds: 10);
  static const _maxBackoff = Duration(minutes: 10);

  Future<void> dispatchAll(SyncQueueStore store) async {
    final pending = await store.listAll();
    final now = DateTime.now();
    for (final entry in pending) {
      if (entry.status == SyncQueueStatus.terkirim ||
          entry.status == SyncQueueStatus.gagal) {
        continue;
      }
      if (entry.nextRetryAt != null && entry.nextRetryAt!.isAfter(now)) {
        continue; // backoff not elapsed yet
      }
      final stop = await _dispatchOne(store, entry);
      if (stop) {
        break;
      }
    }
  }

  /// Returns true if the rest of the run should stop (session invalid).
  Future<bool> _dispatchOne(SyncQueueStore store, SyncQueueEntry entry) async {
    if (entry.id == null) return false;
    await store.updateStatus(entry.id!, SyncQueueStatus.sedangDikirim);
    try {
      final body = jsonDecode(entry.payloadJson) as Map<String, dynamic>;
      switch (entry.entityType) {
        case 'prospect':
          await _prospectService.createRaw(body);
          break;
        case 'operational_report':
          await _operationalReportService.createRaw(body);
          break;
        case 'member':
          await _memberService.createRawMultipart(
            body,
            memberPhoto: await _resolveOne(entry.filePaths, 'member_photo'),
          );
          await _cleanupAttachments(entry.filePaths);
          break;
        case 'visit_report':
          await _visitReportService.createRawMultipart(
            body,
            photo: await _resolveOne(entry.filePaths, 'photo'),
          );
          await _cleanupAttachments(entry.filePaths);
          break;
        case 'operational_report_attachment':
          final captions = Map<String, dynamic>.from(
            body.remove('_attachment_captions') as Map? ?? const {},
          );
          await _operationalReportService.createRawAttachments(
            body,
            await _resolveAttachmentList(entry.filePaths, captions),
          );
          await _cleanupAttachments(entry.filePaths);
          break;
        case 'tracking_points':
          final sessionId = body['session_id'] as int;
          final points = (body['points'] as List)
              .map((e) => TrackingPoint.fromJson(e as Map<String, dynamic>))
              .toList();
          await _trackingService.pointsBatch(sessionId, points);
          break;
        default:
          throw ApiException('Tipe data tidak dikenal: ${entry.entityType}');
      }
      await store.updateStatus(entry.id!, SyncQueueStatus.terkirim);
      return false;
    } on ApiException catch (e) {
      if (_isRetryable(e) && entry.retryCount < _maxRetries) {
        final retryCount = entry.retryCount + 1;
        final delay = _backoffFor(retryCount);
        await store.scheduleRetry(
          entry.id!,
          retryCount: retryCount,
          nextRetryAt: DateTime.now().add(delay),
          lastError: e.message,
        );
        return false;
      }
      await store.updateStatus(
        entry.id!,
        SyncQueueStatus.gagal,
        lastError: e.message,
      );
      return e.isUnauthorized;
    }
  }

  /// Reads the single file queued under [field] (if any) back into a fresh
  /// `MultipartFile` — used for entity types with exactly one photo slot.
  Future<MultipartFile?> _resolveOne(
    List<String> filePaths,
    String field,
  ) async {
    for (final raw in filePaths) {
      final decoded = decodeAttachmentPath(raw);
      if (decoded == null || decoded.field != field) continue;
      final bytes = await _attachmentStore.read(decoded.path);
      return MultipartFile.fromBytes(
        bytes,
        filename: decoded.path.split(RegExp(r'[\\/]')).last,
      );
    }
    return null;
  }

  /// Reads every queued file back into the `{'type', 'photo'}` attachment
  /// list shape `OperationalReportService.createRawAttachments` expects —
  /// used for the operational-report entity type, which can carry up to
  /// two differently-typed photos (`disbursement`/`transfer_proof`).
  Future<List<Map<String, dynamic>>> _resolveAttachmentList(
    List<String> filePaths,
    Map<String, dynamic> captions,
  ) async {
    final result = <Map<String, dynamic>>[];
    for (final raw in filePaths) {
      final decoded = decodeAttachmentPath(raw);
      if (decoded == null) continue;
      final bytes = await _attachmentStore.read(decoded.path);
      result.add({
        'type': decoded.field,
        'photo': MultipartFile.fromBytes(
          bytes,
          filename: decoded.path.split(RegExp(r'[\\/]')).last,
        ),
        if (captions[decoded.field] is String &&
            (captions[decoded.field] as String).isNotEmpty)
          'caption': captions[decoded.field],
      });
    }
    return result;
  }

  /// Best-effort local-file cleanup after a successful replay — deletion
  /// failures are swallowed since they only leave a harmless orphaned temp
  /// file behind, not a data-correctness issue, and shouldn't cause an
  /// otherwise-successful upload to be reported as failed.
  Future<void> _cleanupAttachments(List<String> filePaths) async {
    for (final raw in filePaths) {
      final decoded = decodeAttachmentPath(raw);
      if (decoded == null) continue;
      try {
        await _attachmentStore.delete(decoded.path);
      } catch (_) {
        // Orphaned local file — not worth surfacing, see doc comment above.
      }
    }
  }

  bool _isRetryable(ApiException e) {
    final status = e.statusCode;
    return status == null || status == 429 || status >= 500;
  }

  Duration _backoffFor(int retryCount) {
    final seconds =
        _baseBackoff.inSeconds * (1 << (retryCount - 1).clamp(0, 20));
    return Duration(seconds: seconds) > _maxBackoff
        ? _maxBackoff
        : Duration(seconds: seconds);
  }
}
