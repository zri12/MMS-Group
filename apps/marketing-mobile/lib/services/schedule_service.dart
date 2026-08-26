import '../api/api_client.dart';

/// Mirrors the real `Schedule` resource from `/api/v1/schedules*`
/// (see BACKEND_INTEGRATION_TASKS.md Phase 3 — field names verified against
/// the reference backend's `MarketingScheduleController`/`ScheduleResource`).
class Schedule {
  final int id;
  final int? prospectId;
  final String day;
  final String date;
  final String startTime;
  final String endTime;
  final String? consumerName;
  final String? agenda;
  final String area;
  final String resort;
  final String? destination;
  final String? note;
  final String status; // Belum Dikunjungi | Berlangsung | Selesai | Dibatalkan

  const Schedule({
    required this.id,
    this.prospectId,
    required this.day,
    required this.date,
    required this.startTime,
    required this.endTime,
    this.consumerName,
    this.agenda,
    required this.area,
    required this.resort,
    this.destination,
    this.note,
    required this.status,
  });

  factory Schedule.fromJson(Map<String, dynamic> json) {
    return Schedule(
      id: json['id'] as int,
      prospectId: json['prospect_id'] as int?,
      day: (json['day'] ?? '') as String,
      date: (json['date'] ?? '') as String,
      startTime: (json['start_time'] ?? '') as String,
      endTime: (json['end_time'] ?? '') as String,
      consumerName: json['consumer_name'] as String?,
      agenda: json['agenda'] as String?,
      area: (json['area'] ?? '') as String,
      resort: (json['resort'] ?? '') as String,
      destination: json['destination'] as String?,
      note: json['note'] as String?,
      status: (json['status'] ?? 'Belum Dikunjungi') as String,
    );
  }
}

/// Wraps `/api/v1/schedules*`.
class ScheduleService {
  ScheduleService([ApiClient? client]) : _client = client ?? ApiClient.instance;
  final ApiClient _client;

  Future<List<Schedule>> listToday() async {
    final data = await _client.get('/schedules/today');
    return (data as List).map((e) => Schedule.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<List<Schedule>> list({String? date, String? day, String? status}) async {
    final envelope = await _client.getEnvelope('/schedules', query: {
      if (date != null) 'date': date,
      if (day != null) 'day': day,
      if (status != null) 'status': status,
    });
    final data = envelope['data'] as List;
    return data.map((e) => Schedule.fromJson(e as Map<String, dynamic>)).toList();
  }

  Future<Schedule> detail(int id) async {
    final data = await _client.get('/schedules/$id');
    return Schedule.fromJson(data as Map<String, dynamic>);
  }

  static const Map<String, List<String>> allowedTransitions = {
    'Belum Dikunjungi': ['Berlangsung', 'Dibatalkan'],
    'Berlangsung': ['Selesai', 'Dibatalkan'],
    'Selesai': [],
    'Dibatalkan': [],
  };

  Future<Schedule> updateStatus(int id, String status) async {
    final data = await _client.patch('/schedules/$id/status', body: {'status': status});
    return Schedule.fromJson(data as Map<String, dynamic>);
  }
}
