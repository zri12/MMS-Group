import 'package:dio/dio.dart' show MultipartFile;
import 'package:image_picker/image_picker.dart' show XFile;

import '../api/api_client.dart';
import '../data/local/read_cache.dart';

/// Real visit-result enum accepted by `POST /api/v1/visit-reports` — NOT the
/// same list the reference app's mock UI used (`Tertarik`/`Perlu Follow Up`
/// were prospect *statuses*, not visit *results*, and aren't valid here).
const visitResultOptions = ['Berhasil Bertemu', 'Tidak Bertemu', 'Transaksi Selesai'];

/// Mirrors the real `VisitReport` resource from `/api/v1/visit-reports*`.
/// Always references a `Prospect` (`prospectId`), per Phase 0 DEC-021/022 —
/// submitting one also updates the linked prospect's status server-side in
/// the same transaction.
class VisitReport {
  final int id;
  final String? localUuid;
  final int prospectId;
  final String? prospectName;
  final String? visitPurpose;
  final String visitResult;
  final String? prospectStatus;
  final String? notes;
  final String? followUpDate;
  final String? photoUrl;
  final String? photoCaption;
  final String? resort;
  final String? day;
  final String? date;
  final String? time;
  final String? locationAddress;

  const VisitReport({
    required this.id,
    this.localUuid,
    required this.prospectId,
    this.prospectName,
    this.visitPurpose,
    required this.visitResult,
    this.prospectStatus,
    this.notes,
    this.followUpDate,
    this.photoUrl,
    this.photoCaption,
    this.resort,
    this.day,
    this.date,
    this.time,
    this.locationAddress,
  });

  factory VisitReport.fromJson(Map<String, dynamic> json) {
    return VisitReport(
      id: json['id'] as int,
      localUuid: json['local_uuid'] as String?,
      prospectId: (json['prospect_id'] as num).toInt(),
      prospectName: json['prospect_name'] as String?,
      visitPurpose: json['visit_purpose'] as String?,
      visitResult: (json['visit_result'] ?? '') as String,
      prospectStatus: json['prospect_status'] as String?,
      notes: json['notes'] as String?,
      followUpDate: json['follow_up_date'] as String?,
      photoUrl: json['photo_url'] as String? ?? json['photo'] as String?,
      photoCaption: json['photo_caption'] as String?,
      resort: json['resort'] as String?,
      day: json['day'] as String?,
      date: json['date'] as String?,
      time: json['time'] as String?,
      locationAddress: json['location_address'] as String?,
    );
  }
}

const _cacheType = 'visit_report';

/// Wraps `/api/v1/visit-reports*`.
class VisitReportService {
  VisitReportService([ApiClient? client, ReadCacheStore? cache])
      : _client = client ?? ApiClient.instance,
        _cache = cache ?? createDefaultReadCacheStore();
  final ApiClient _client;
  final ReadCacheStore _cache;

  /// See `ProspectService.list()` for the caching/fallback rationale —
  /// same pattern applied here. Filters (`prospectId`/`result`/date range)
  /// are ignored by the cache fallback, same caveat as elsewhere.
  Future<List<VisitReport>> list({int? prospectId, String? result, String? dateFrom, String? dateTo}) async {
    try {
      final envelope = await _client.getEnvelope('/visit-reports', query: {
        if (prospectId != null) 'prospect_id': prospectId,
        if (result != null) 'result': result,
        if (dateFrom != null) 'date_from': dateFrom,
        if (dateTo != null) 'date_to': dateTo,
      });
      final data = (envelope['data'] as List).cast<Map<String, dynamic>>();
      await _cache.upsertAll(_cacheType, data);
      return data.map(VisitReport.fromJson).toList();
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.listCached(_cacheType);
      if (cached.isEmpty) rethrow;
      return cached.map(VisitReport.fromJson).toList();
    }
  }

  Future<VisitReport> detail(int id) async {
    try {
      final data = await _client.get('/visit-reports/$id') as Map<String, dynamic>;
      await _cache.upsertAll(_cacheType, [data]);
      return VisitReport.fromJson(data);
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.getCached(_cacheType, id.toString());
      if (cached == null) rethrow;
      return VisitReport.fromJson(cached);
    }
  }

  Map<String, dynamic> buildCreatePayload({
    required String localUuid,
    required int prospectId,
    String? visitPurpose,
    required String visitResult,
    String? prospectStatus,
    String? notes,
    String? followUpDate,
    String? photoCaption,
    String? resort,
    String? day,
    String? date,
    String? time,
    double? latitude,
    double? longitude,
    String? locationAddress,
  }) {
    return {
      'local_uuid': localUuid,
      'prospect_id': prospectId,
      if (visitPurpose != null && visitPurpose.isNotEmpty) 'visit_purpose': visitPurpose,
      'visit_result': visitResult,
      if (prospectStatus != null && prospectStatus.isNotEmpty) 'prospect_status': prospectStatus,
      if (notes != null && notes.isNotEmpty) 'notes': notes,
      if (followUpDate != null) 'follow_up_date': followUpDate,
      if (photoCaption != null && photoCaption.isNotEmpty) 'photo_caption': photoCaption,
      if (resort != null) 'resort': resort,
      if (day != null) 'day': day,
      if (date != null) 'date': date,
      if (time != null) 'time': time,
      if (latitude != null) 'latitude': latitude,
      if (longitude != null) 'longitude': longitude,
      if (locationAddress != null) 'location_address': locationAddress,
    };
  }

  /// Posts an already-built fields map plus an already-resolved [photo] —
  /// used both by [create] and by the sync dispatcher replaying a queued
  /// offline record (whose photo bytes were read back from a
  /// durably-persisted local file, not a live `XFile`).
  Future<VisitReport> createRawMultipart(Map<String, dynamic> fields, {MultipartFile? photo}) async {
    final f = Map<String, dynamic>.from(fields);
    if (photo != null) f['photo'] = photo;
    final data = await _client.postMultipart('/visit-reports', fields: f);
    return VisitReport.fromJson(data as Map<String, dynamic>);
  }

  /// Multipart create — `photo` is sent as a real file when [photo] is
  /// provided (Phase 5, `image_picker`), omitted otherwise.
  Future<VisitReport> create({
    required String localUuid,
    required int prospectId,
    String? visitPurpose,
    required String visitResult,
    String? prospectStatus,
    String? notes,
    String? followUpDate,
    String? photoCaption,
    String? resort,
    String? day,
    String? date,
    String? time,
    double? latitude,
    double? longitude,
    String? locationAddress,
    XFile? photo,
  }) async {
    final fields = buildCreatePayload(
      localUuid: localUuid,
      prospectId: prospectId,
      visitPurpose: visitPurpose,
      visitResult: visitResult,
      prospectStatus: prospectStatus,
      notes: notes,
      followUpDate: followUpDate,
      photoCaption: photoCaption,
      resort: resort,
      day: day,
      date: date,
      time: time,
      latitude: latitude,
      longitude: longitude,
      locationAddress: locationAddress,
    );
    MultipartFile? mf;
    if (photo != null) {
      mf = MultipartFile.fromBytes(await photo.readAsBytes(), filename: photo.name);
    }
    return createRawMultipart(fields, photo: mf);
  }
}
