import 'package:dio/dio.dart' show MultipartFile;
import 'package:image_picker/image_picker.dart' show XFile;

import '../api/api_client.dart';
import '../data/local/read_cache.dart';

/// Mirrors the real `Member` resource from `/api/v1/members*` — confirmed
/// separate from `Prospect` per Phase 0 DEC-021/022 (see
/// BACKEND_INTEGRATION_TASKS.md). A member always starts
/// `approvalStatus == 'Menunggu'`; approval is an admin-only web action, not
/// something the marketing app can do.
class Member {
  final int id;
  final String? localUuid;
  final int? sourceProspectId;
  final String? resort;
  final String? date;
  final String? time;
  final String name;
  final String memberNumber;
  final String? loanNumber;
  final String address;
  final String phone;
  final String? business;
  final int loanAmount;
  final int installmentAmount;
  final int? insuranceAmount;
  final String? collateral;
  final double? latitude;
  final double? longitude;
  final String? locationAddress;
  final String approvalStatus;
  final String? memberPhotoUrl;

  const Member({
    required this.id,
    this.localUuid,
    this.sourceProspectId,
    this.resort,
    this.date,
    this.time,
    required this.name,
    required this.memberNumber,
    this.loanNumber,
    required this.address,
    required this.phone,
    this.business,
    required this.loanAmount,
    required this.installmentAmount,
    this.insuranceAmount,
    this.collateral,
    this.latitude,
    this.longitude,
    this.locationAddress,
    required this.approvalStatus,
    this.memberPhotoUrl,
  });

  String get initials {
    final parts = name
        .trim()
        .split(RegExp(r'\s+'))
        .where((p) => p.isNotEmpty)
        .toList();
    if (parts.isEmpty) return '?';
    if (parts.length == 1) return parts.first.substring(0, 1).toUpperCase();
    return (parts.first.substring(0, 1) + parts.last.substring(0, 1))
        .toUpperCase();
  }

  factory Member.fromJson(Map<String, dynamic> json) {
    return Member(
      id: json['id'] as int,
      localUuid: json['local_uuid'] as String?,
      sourceProspectId: json['source_prospect_id'] as int?,
      resort: json['resort'] as String?,
      date: json['date'] as String?,
      time: json['time'] as String?,
      name: (json['name'] ?? '') as String,
      memberNumber: (json['member_number'] ?? '') as String,
      loanNumber: json['loan_number'] as String?,
      address: (json['address'] ?? '') as String,
      phone: (json['phone'] ?? '') as String,
      business: json['business'] as String?,
      loanAmount: (json['loan_amount'] as num?)?.toInt() ?? 0,
      installmentAmount: (json['installment_amount'] as num?)?.toInt() ?? 0,
      insuranceAmount: (json['insurance_amount'] as num?)?.toInt(),
      collateral: json['collateral'] as String?,
      latitude: (json['latitude'] as num?)?.toDouble(),
      longitude: (json['longitude'] as num?)?.toDouble(),
      locationAddress: json['location_address'] as String?,
      approvalStatus: (json['approval_status'] ?? 'Menunggu') as String,
      memberPhotoUrl:
          json['member_photo_url'] as String? ??
          json['member_photo'] as String?,
    );
  }
}

const _cacheType = 'member';

/// Wraps `/api/v1/members*`.
class MemberService {
  MemberService([ApiClient? client, ReadCacheStore? cache])
    : _client = client ?? ApiClient.instance,
      _cache = cache ?? createDefaultReadCacheStore();
  final ApiClient _client;
  final ReadCacheStore _cache;

  /// See `ProspectService.list()` for the caching/fallback rationale —
  /// same pattern applied here.
  Future<List<Member>> list({
    String? approvalStatus,
    String? search,
    int perPage = 20,
  }) async {
    try {
      final envelope = await _client.getEnvelope(
        '/members',
        query: {
          if (approvalStatus != null) 'approval_status': approvalStatus,
          if (search != null && search.isNotEmpty) 'search': search,
          'per_page': perPage,
        },
      );
      final data = (envelope['data'] as List).cast<Map<String, dynamic>>();
      await _cache.upsertAll(_cacheType, data);
      return data.map(Member.fromJson).toList();
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.listCached(_cacheType);
      if (cached.isEmpty) rethrow;
      return cached.map(Member.fromJson).toList();
    }
  }

  /// Uses pagination metadata so dashboard counts stay correct even when the
  /// member list itself is displayed one page at a time.
  Future<int> count() async {
    final envelope = await _client.getEnvelope(
      '/members',
      query: const {'per_page': 1},
    );
    final meta = envelope['meta'] as Map<String, dynamic>?;
    return (meta?['total'] as num?)?.toInt() ?? 0;
  }

  Future<Member> detail(int id) async {
    try {
      final data = await _client.get('/members/$id') as Map<String, dynamic>;
      await _cache.upsertAll(_cacheType, [data]);
      return Member.fromJson(data);
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.getCached(_cacheType, id.toString());
      if (cached == null) rethrow;
      return Member.fromJson(cached);
    }
  }

  Map<String, dynamic> buildCreatePayload({
    required String localUuid,
    int? sourceProspectId,
    String? resort,
    String? date,
    String? time,
    required String name,
    required String memberNumber,
    String? loanNumber,
    required String address,
    required String phone,
    String? business,
    required int loanAmount,
    required int installmentAmount,
    int? insuranceAmount,
    String? collateral,
    double? latitude,
    double? longitude,
    String? locationAddress,
  }) {
    return {
      'local_uuid': localUuid,
      if (sourceProspectId != null) 'source_prospect_id': sourceProspectId,
      if (resort != null) 'resort': resort,
      if (date != null) 'date': date,
      if (time != null) 'time': time,
      'name': name,
      'member_number': memberNumber,
      if (loanNumber != null && loanNumber.isNotEmpty)
        'loan_number': loanNumber,
      'address': address,
      'phone': phone,
      if (business != null && business.isNotEmpty) 'business': business,
      'loan_amount': loanAmount,
      'installment_amount': installmentAmount,
      if (insuranceAmount != null) 'insurance_amount': insuranceAmount,
      if (collateral != null && collateral.isNotEmpty) 'collateral': collateral,
      if (latitude != null) 'latitude': latitude,
      if (longitude != null) 'longitude': longitude,
      if (locationAddress != null) 'location_address': locationAddress,
    };
  }

  /// Posts an already-built fields map plus an already-resolved
  /// [memberPhoto] — used both by [create] and by the sync dispatcher
  /// replaying a queued offline record (whose photo bytes were read back
  /// from a durably-persisted local file, not a live `XFile`).
  Future<Member> createRawMultipart(
    Map<String, dynamic> fields, {
    MultipartFile? memberPhoto,
  }) async {
    final f = Map<String, dynamic>.from(fields);
    if (memberPhoto != null) f['member_photo'] = memberPhoto;
    final data = await _client.postMultipart('/members', fields: f);
    return Member.fromJson(data as Map<String, dynamic>);
  }

  /// Multipart create — `member_photo` is sent as a real file when [photo]
  /// is provided (Phase 5, `image_picker`), omitted otherwise.
  Future<Member> create({
    required String localUuid,
    int? sourceProspectId,
    String? resort,
    String? date,
    String? time,
    required String name,
    required String memberNumber,
    String? loanNumber,
    required String address,
    required String phone,
    String? business,
    required int loanAmount,
    required int installmentAmount,
    int? insuranceAmount,
    String? collateral,
    double? latitude,
    double? longitude,
    String? locationAddress,
    XFile? photo,
  }) async {
    final fields = buildCreatePayload(
      localUuid: localUuid,
      sourceProspectId: sourceProspectId,
      resort: resort,
      date: date,
      time: time,
      name: name,
      memberNumber: memberNumber,
      loanNumber: loanNumber,
      address: address,
      phone: phone,
      business: business,
      loanAmount: loanAmount,
      installmentAmount: installmentAmount,
      insuranceAmount: insuranceAmount,
      collateral: collateral,
      latitude: latitude,
      longitude: longitude,
      locationAddress: locationAddress,
    );
    MultipartFile? memberPhoto;
    if (photo != null) {
      memberPhoto = MultipartFile.fromBytes(
        await photo.readAsBytes(),
        filename: photo.name,
      );
    }
    return createRawMultipart(fields, memberPhoto: memberPhoto);
  }
}
