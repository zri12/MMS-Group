import '../api/api_client.dart';
import '../data/local/read_cache.dart';

/// Mirrors the real `Prospect` resource from `/api/v1/prospects*` — the
/// entity confirmed separate from `Member` per Phase 0 DEC-021/022 (see
/// BACKEND_INTEGRATION_TASKS.md). This is what the reference app's
/// `Consumer` model/`consumers*` screens map onto.
class Prospect {
  final int id;
  final String? localUuid;
  final String name;
  final String phone;
  final String address;
  final String? business;
  final String status;
  final String? initialVisitResult;
  final String? notes;
  final String? resort;
  final String? inputDate;
  final String? inputTime;
  final double? latitude;
  final double? longitude;
  final String? locationAddress;

  const Prospect({
    required this.id,
    this.localUuid,
    required this.name,
    required this.phone,
    required this.address,
    this.business,
    required this.status,
    this.initialVisitResult,
    this.notes,
    this.resort,
    this.inputDate,
    this.inputTime,
    this.latitude,
    this.longitude,
    this.locationAddress,
  });

  String get initials {
    final parts = name.trim().split(RegExp(r'\s+')).where((p) => p.isNotEmpty).toList();
    if (parts.isEmpty) return '?';
    if (parts.length == 1) return parts.first.substring(0, 1).toUpperCase();
    return (parts.first.substring(0, 1) + parts.last.substring(0, 1)).toUpperCase();
  }

  factory Prospect.fromJson(Map<String, dynamic> json) {
    return Prospect(
      id: json['id'] as int,
      localUuid: json['local_uuid'] as String?,
      name: (json['name'] ?? '') as String,
      phone: (json['phone'] ?? '') as String,
      address: (json['address'] ?? '') as String,
      business: json['business'] as String?,
      status: (json['status'] ?? '') as String,
      initialVisitResult: json['initial_visit_result'] as String?,
      notes: json['notes'] as String?,
      resort: json['resort'] as String?,
      inputDate: json['input_date'] as String?,
      inputTime: json['input_time'] as String?,
      latitude: (json['latitude'] as num?)?.toDouble(),
      longitude: (json['longitude'] as num?)?.toDouble(),
      locationAddress: json['location_address'] as String?,
    );
  }
}

const _cacheType = 'prospect';

/// Wraps `/api/v1/prospects*`.
class ProspectService {
  ProspectService([ApiClient? client, ReadCacheStore? cache])
      : _client = client ?? ApiClient.instance,
        _cache = cache ?? createDefaultReadCacheStore();
  final ApiClient _client;
  final ReadCacheStore _cache;

  /// On success, write-through caches the result for offline browsing. On
  /// a connection failure specifically (no HTTP response at all — the
  /// device is genuinely offline, not a real server error), falls back to
  /// whatever was last cached rather than showing a hard error, since the
  /// whole point of the cache is to keep the list usable while offline.
  /// Filters (`status`/`search`) are ignored by the cache fallback — the
  /// cache holds "whatever was last successfully fetched," not a result
  /// per filter combination (see BACKEND_INTEGRATION_TASKS.md Phase 2).
  Future<List<Prospect>> list({String? status, String? search}) async {
    try {
      final envelope = await _client.getEnvelope('/prospects', query: {
        if (status != null) 'status': status,
        if (search != null && search.isNotEmpty) 'search': search,
      });
      final data = (envelope['data'] as List).cast<Map<String, dynamic>>();
      await _cache.upsertAll(_cacheType, data);
      return data.map(Prospect.fromJson).toList();
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.listCached(_cacheType);
      if (cached.isEmpty) rethrow;
      return cached.map(Prospect.fromJson).toList();
    }
  }

  Future<Prospect> detail(int id) async {
    try {
      final data = await _client.get('/prospects/$id') as Map<String, dynamic>;
      await _cache.upsertAll(_cacheType, [data]);
      return Prospect.fromJson(data);
    } on ApiException catch (e) {
      if (e.statusCode != null) rethrow;
      final cached = await _cache.getCached(_cacheType, id.toString());
      if (cached == null) rethrow;
      return Prospect.fromJson(cached);
    }
  }

  Map<String, dynamic> buildCreatePayload({
    required String localUuid,
    required String name,
    required String phone,
    required String address,
    String? business,
    required String status,
    String? initialVisitResult,
    String? notes,
    String? resort,
    String? inputDate,
    String? inputTime,
    double? latitude,
    double? longitude,
    String? locationAddress,
  }) {
    return {
      'local_uuid': localUuid,
      'name': name,
      'phone': phone,
      'address': address,
      if (business != null && business.isNotEmpty) 'business': business,
      'status': status,
      if (initialVisitResult != null && initialVisitResult.isNotEmpty) 'initial_visit_result': initialVisitResult,
      if (notes != null && notes.isNotEmpty) 'notes': notes,
      if (resort != null) 'resort': resort,
      if (inputDate != null) 'input_date': inputDate,
      if (inputTime != null) 'input_time': inputTime,
      if (latitude != null) 'latitude': latitude,
      if (longitude != null) 'longitude': longitude,
      if (locationAddress != null) 'location_address': locationAddress,
    };
  }

  /// Posts an already-built payload map — used both by [create] and by the
  /// sync dispatcher replaying a queued offline record.
  Future<Prospect> createRaw(Map<String, dynamic> body) async {
    final data = await _client.post('/prospects', body: body);
    return Prospect.fromJson(data as Map<String, dynamic>);
  }

  Future<Prospect> create({
    required String localUuid,
    required String name,
    required String phone,
    required String address,
    String? business,
    required String status,
    String? initialVisitResult,
    String? notes,
    String? resort,
    String? inputDate,
    String? inputTime,
    double? latitude,
    double? longitude,
    String? locationAddress,
  }) {
    return createRaw(buildCreatePayload(
      localUuid: localUuid,
      name: name,
      phone: phone,
      address: address,
      business: business,
      status: status,
      initialVisitResult: initialVisitResult,
      notes: notes,
      resort: resort,
      inputDate: inputDate,
      inputTime: inputTime,
      latitude: latitude,
      longitude: longitude,
      locationAddress: locationAddress,
    ));
  }

  Future<Prospect> update(int id, Map<String, dynamic> fields) async {
    final data = await _client.put('/prospects/$id', body: fields);
    return Prospect.fromJson(data as Map<String, dynamic>);
  }
}
