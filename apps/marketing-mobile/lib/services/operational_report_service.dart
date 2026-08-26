import 'package:dio/dio.dart' show MultipartFile;
import 'package:image_picker/image_picker.dart' show XFile;

import '../api/api_client.dart';
import '../data/local/read_cache.dart';

/// Mirrors the real `OperationalReport` resource from
/// `/api/v1/operational-reports*`. Field names/shape verified against
/// BACKEND_INTEGRATION_TASKS.md Phase 3 — this replaces the reference app's
/// invented "Data Anggota"/"Laporan Tunai" sections inside `inputHarian`,
/// which have no backend equivalent (Phase 0 DEC-021/022): target values
/// live here, not on a separate rekap; Foto Nasabah/Foto Transfer become
/// the optional `attachments[]` (`disbursement`/`transfer_proof`) on this
/// same report.
/// One entry in an `OperationalReport`'s `attachments[]` (`disbursement` or
/// `transfer_proof`). `caption` is the only human-chosen label that
/// actually survives server-side -- the uploaded file's own name does not
/// (`CreateOperationalReportAction.php` stores it via `store()`, which
/// discards the original filename in favor of a random one).
class OperationalReportAttachment {
  final int id;
  final String type;
  final String photoUrl;
  final String? caption;

  const OperationalReportAttachment({
    required this.id,
    required this.type,
    required this.photoUrl,
    this.caption,
  });

  factory OperationalReportAttachment.fromJson(Map<String, dynamic> json) {
    return OperationalReportAttachment(
      id: json['id'] as int,
      type: (json['type'] ?? '') as String,
      photoUrl: (json['photo_url'] ?? '') as String,
      caption: json['caption'] as String?,
    );
  }
}

class OperationalReport {
  final int id;
  final String? localUuid;
  final String? date;
  final String? time;
  final String? day;
  final String? resort;
  final List<OperationalReportAttachment> attachments;
  final int storting;
  final int insuranceAmount;
  final int drop;
  final int withdrawalSaving;
  final int previousTargetAmount;
  final int previousTargetPeople;
  final int incomingTargetAmount;
  final int incomingTargetPeople;
  final int outgoingTargetAmount;
  final int outgoingTargetPeople;
  final int totalTargetAmount;
  final int totalTargetPeople;
  final int newDrop;
  final int continuedDrop;
  final String? notes;

  const OperationalReport({
    required this.id,
    this.localUuid,
    this.date,
    this.time,
    this.day,
    this.resort,
    this.attachments = const [],
    required this.storting,
    required this.insuranceAmount,
    required this.drop,
    required this.withdrawalSaving,
    required this.previousTargetAmount,
    required this.previousTargetPeople,
    required this.incomingTargetAmount,
    required this.incomingTargetPeople,
    required this.outgoingTargetAmount,
    required this.outgoingTargetPeople,
    required this.totalTargetAmount,
    required this.totalTargetPeople,
    required this.newDrop,
    required this.continuedDrop,
    this.notes,
  });

  factory OperationalReport.fromJson(Map<String, dynamic> json) {
    int i(String key) => (json[key] as num?)?.toInt() ?? 0;
    return OperationalReport(
      id: json['id'] as int,
      localUuid: json['local_uuid'] as String?,
      date: json['date'] as String?,
      time: json['time'] as String?,
      day: json['day'] as String?,
      resort: json['resort'] as String?,
      attachments:
          (json['attachments'] as List?)
              ?.cast<Map<String, dynamic>>()
              .map(OperationalReportAttachment.fromJson)
              .toList() ??
          const [],
      storting: i('storting'),
      insuranceAmount: i('insurance_amount'),
      drop: i('drop'),
      withdrawalSaving: i('withdrawal_saving'),
      previousTargetAmount: i('previous_target_amount'),
      previousTargetPeople: i('previous_target_people'),
      incomingTargetAmount: i('incoming_target_amount'),
      incomingTargetPeople: i('incoming_target_people'),
      outgoingTargetAmount: i('outgoing_target_amount'),
      outgoingTargetPeople: i('outgoing_target_people'),
      totalTargetAmount: i('total_target_amount'),
      totalTargetPeople: i('total_target_people'),
      newDrop: i('new_drop'),
      continuedDrop: i('continued_drop'),
      notes: json['notes'] as String?,
    );
  }
}

const _cacheType = 'operational_report';

/// Wraps `/api/v1/operational-reports*`.
class OperationalReportService {
  OperationalReportService([ApiClient? client, ReadCacheStore? cache])
    : _client = client ?? ApiClient.instance,
      _cache = cache ?? createDefaultReadCacheStore();
  final ApiClient _client;
  final ReadCacheStore _cache;

  /// See `ProspectService.list()` for the caching/fallback rationale —
  /// same pattern applied here. Not yet called from any screen (the
  /// "Rekap Operasional" own-history screen this would power is Phase 6,
  /// not built this pass), but ready.
  Future<List<OperationalReport>> list({
    String? dateFrom,
    String? dateTo,
    int perPage = 20,
  }) async {
    try {
      final envelope = await _client.getEnvelope(
        '/operational-reports',
        query: {
          if (dateFrom != null) 'date_from': dateFrom,
          if (dateTo != null) 'date_to': dateTo,
          'per_page': perPage,
        },
      );
      final data = (envelope['data'] as List).cast<Map<String, dynamic>>();
      await _cache.upsertAll(_cacheType, data);
      return data.map(OperationalReport.fromJson).toList();
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.listCached(_cacheType);
      if (cached.isEmpty) rethrow;
      return cached.map(OperationalReport.fromJson).toList();
    }
  }

  Future<OperationalReport> detail(int id) async {
    try {
      final data =
          await _client.get('/operational-reports/$id') as Map<String, dynamic>;
      await _cache.upsertAll(_cacheType, [data]);
      return OperationalReport.fromJson(data);
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.getCached(_cacheType, id.toString());
      if (cached == null) rethrow;
      return OperationalReport.fromJson(cached);
    }
  }

  Map<String, dynamic> buildCreatePayload({
    required String localUuid,
    required String date,
    required String time,
    required String day,
    required String resort,
    required int storting,
    required int insuranceAmount,
    required int drop,
    required int withdrawalSaving,
    required int previousTargetAmount,
    required int previousTargetPeople,
    required int incomingTargetAmount,
    required int incomingTargetPeople,
    required int outgoingTargetAmount,
    required int outgoingTargetPeople,
    required int totalTargetAmount,
    required int totalTargetPeople,
    required int newDrop,
    required int continuedDrop,
    String? notes,
  }) {
    return {
      'local_uuid': localUuid,
      'date': date,
      'time': time,
      'day': day,
      'resort': resort,
      'storting': storting,
      'insurance_amount': insuranceAmount,
      'drop': drop,
      'withdrawal_saving': withdrawalSaving,
      'previous_target_amount': previousTargetAmount,
      'previous_target_people': previousTargetPeople,
      'incoming_target_amount': incomingTargetAmount,
      'incoming_target_people': incomingTargetPeople,
      'outgoing_target_amount': outgoingTargetAmount,
      'outgoing_target_people': outgoingTargetPeople,
      'total_target_amount': totalTargetAmount,
      'total_target_people': totalTargetPeople,
      'new_drop': newDrop,
      'continued_drop': continuedDrop,
      if (notes != null && notes.isNotEmpty) 'notes': notes,
      // attachments[] intentionally omitted: real photo capture (image_picker)
      // isn't wired yet — see BACKEND_INTEGRATION_TASKS.md Phase 5.
    };
  }

  /// Posts an already-built payload map — used both by [create] and by the
  /// sync dispatcher replaying a queued offline record.
  Future<OperationalReport> createRaw(Map<String, dynamic> body) async {
    final data = await _client.post('/operational-reports', body: body);
    return OperationalReport.fromJson(data as Map<String, dynamic>);
  }

  /// Posts an already-built base-fields map plus already-resolved
  /// [attachments] (each `{'type': ..., 'photo': MultipartFile}`) — used
  /// both by [createWithAttachments] and by the sync dispatcher replaying a
  /// queued offline record (whose photo bytes were read back from a
  /// durably-persisted local file, not a live `XFile`; see
  /// `submitAttachmentWithOfflineFallback`).
  Future<OperationalReport> createRawAttachments(
    Map<String, dynamic> baseFields,
    List<Map<String, dynamic>> attachments,
  ) async {
    final fields = Map<String, dynamic>.from(baseFields);
    if (attachments.isNotEmpty) fields['attachments'] = attachments;
    final data = await _client.postMultipart(
      '/operational-reports',
      fields: fields,
    );
    return OperationalReport.fromJson(data as Map<String, dynamic>);
  }

  /// Multipart create used only when at least one attachment photo is
  /// provided. Offline resilience is handled at the call site via
  /// `submitAttachmentWithOfflineFallback` (see `daily_input_screen.dart`),
  /// which persists the photo bytes to a durable local file on a
  /// connection failure and replays via [createRawAttachments] later.
  Future<OperationalReport> createWithAttachments(
    Map<String, dynamic> baseFields, {
    XFile? disbursementPhoto,
    String? disbursementCaption,
    XFile? transferProofPhoto,
    String? transferProofCaption,
  }) async {
    final attachments = <Map<String, dynamic>>[];
    if (disbursementPhoto != null) {
      attachments.add({
        'type': 'disbursement',
        'photo': MultipartFile.fromBytes(
          await disbursementPhoto.readAsBytes(),
          filename: disbursementPhoto.name,
        ),
        if (disbursementCaption != null && disbursementCaption.isNotEmpty)
          'caption': disbursementCaption,
      });
    }
    if (transferProofPhoto != null) {
      attachments.add({
        'type': 'transfer_proof',
        'photo': MultipartFile.fromBytes(
          await transferProofPhoto.readAsBytes(),
          filename: transferProofPhoto.name,
        ),
        if (transferProofCaption != null && transferProofCaption.isNotEmpty)
          'caption': transferProofCaption,
      });
    }
    return createRawAttachments(baseFields, attachments);
  }
}
