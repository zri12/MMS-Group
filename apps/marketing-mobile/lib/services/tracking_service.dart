import '../api/api_client.dart';

/// One GPS sample within a tracking session. `pointType` matches the
/// server's enum (`Mulai`/`Perjalanan`/`Kunjungan`/`Selesai`).
class TrackingPoint {
  final String localUuid;
  final double latitude;
  final double longitude;
  final String pointType;
  final DateTime recordedAt;

  const TrackingPoint({
    required this.localUuid,
    required this.latitude,
    required this.longitude,
    required this.pointType,
    required this.recordedAt,
  });

  Map<String, dynamic> toJson() => {
    'local_uuid': localUuid,
    'latitude': latitude,
    'longitude': longitude,
    'point_type': pointType,
    'recorded_at': recordedAt.toIso8601String(),
  };

  factory TrackingPoint.fromJson(Map<String, dynamic> json) {
    return TrackingPoint(
      localUuid: json['local_uuid'] as String,
      latitude: (json['latitude'] as num).toDouble(),
      longitude: (json['longitude'] as num).toDouble(),
      pointType: (json['point_type'] ?? 'Perjalanan') as String,
      recordedAt: DateTime.parse(json['recorded_at'] as String),
    );
  }
}

/// Mirrors the real `TrackingSession` resource from
/// `/api/v1/tracking/sessions*`. `status` is server-driven — note the
/// backend uses `Offline` as the terminal "stopped" status, there is no
/// "Selesai" tracking status (see BACKEND_INTEGRATION_TASKS.md Phase 3).
class TrackingSession {
  final int id;
  final String? localUuid;
  final int? scheduleId;
  final DateTime? startedAt;
  final DateTime? endedAt;
  final String status;
  final int visitCount;
  final double distanceMeters;
  final List<TrackingPoint> points;

  const TrackingSession({
    required this.id,
    this.localUuid,
    this.scheduleId,
    this.startedAt,
    this.endedAt,
    required this.status,
    required this.visitCount,
    required this.distanceMeters,
    this.points = const [],
  });

  bool get isActive => endedAt == null;

  factory TrackingSession.fromJson(Map<String, dynamic> json) {
    DateTime? parseDate(dynamic v) => v is String ? DateTime.tryParse(v) : null;
    return TrackingSession(
      id: json['id'] as int,
      localUuid: json['local_uuid'] as String?,
      scheduleId: json['schedule_id'] as int?,
      startedAt: parseDate(json['started_at']),
      endedAt: parseDate(json['ended_at']),
      status: (json['status'] ?? 'Aktif') as String,
      visitCount: (json['visit_count'] as num?)?.toInt() ?? 0,
      distanceMeters: (json['distance_meters'] as num?)?.toDouble() ?? 0,
      points:
          (json['points'] as List?)
              ?.map((e) => TrackingPoint.fromJson(e as Map<String, dynamic>))
              .toList() ??
          const [],
    );
  }
}

/// Wraps `/api/v1/tracking/sessions*`.
class TrackingService {
  TrackingService([ApiClient? client]) : _client = client ?? ApiClient.instance;
  final ApiClient _client;

  static const maxBatchPoints = 100;

  /// Null if there's no in-progress session to resume.
  Future<TrackingSession?> current() async {
    final data = await _client.get('/tracking/sessions/current');
    if (data == null) return null;
    return TrackingSession.fromJson(data as Map<String, dynamic>);
  }

  Future<TrackingSession> start({
    required String localUuid,
    int? scheduleId,
    required DateTime startedAt,
  }) async {
    final data = await _client.post(
      '/tracking/sessions/start',
      body: {
        'local_uuid': localUuid,
        if (scheduleId != null) 'schedule_id': scheduleId,
        'started_at': startedAt.toIso8601String(),
      },
    );
    final payload = data as Map<String, dynamic>;
    return TrackingSession.fromJson(payload['session'] as Map<String, dynamic>);
  }

  /// Sorts by `recorded_at` and splits into `maxBatchPoints`-sized chunks
  /// (each batch is atomic server-side — one bad point fails the whole
  /// batch, so chunking also limits the blast radius of a bad point).
  Future<void> pointsBatch(int sessionId, List<TrackingPoint> points) async {
    if (points.isEmpty) return;
    final sorted = [...points]
      ..sort((a, b) => a.recordedAt.compareTo(b.recordedAt));
    for (var i = 0; i < sorted.length; i += maxBatchPoints) {
      final end = (i + maxBatchPoints < sorted.length)
          ? i + maxBatchPoints
          : sorted.length;
      final chunk = sorted.sublist(i, end);
      await _client.post(
        '/tracking/sessions/$sessionId/points/batch',
        body: {'points': chunk.map((p) => p.toJson()).toList()},
      );
    }
  }

  Future<TrackingSession> stop(
    int sessionId, {
    required DateTime endedAt,
    required int visitCount,
    required double distanceMeters,
  }) async {
    final data = await _client.post(
      '/tracking/sessions/$sessionId/stop',
      body: {
        'ended_at': endedAt.toIso8601String(),
        'visit_count': visitCount,
        'distance_meters': distanceMeters,
      },
    );
    return TrackingSession.fromJson(data as Map<String, dynamic>);
  }

  Future<List<TrackingSession>> history() async {
    final envelope = await _client.getEnvelope('/tracking/sessions');
    final data = envelope['data'] as List;
    return data
        .map((e) => TrackingSession.fromJson(e as Map<String, dynamic>))
        .toList();
  }

  Future<TrackingSession> detail(int id) async {
    final payload =
        await _client.get('/tracking/sessions/$id') as Map<String, dynamic>;
    final session = Map<String, dynamic>.from(
      payload['session'] as Map<String, dynamic>,
    );
    session['points'] = payload['points'] ?? const [];
    return TrackingSession.fromJson(session);
  }
}
