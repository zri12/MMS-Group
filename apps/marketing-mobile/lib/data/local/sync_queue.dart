import 'dart:async';

import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:sqflite/sqflite.dart' show Database;
import 'package:uuid/uuid.dart';

import 'local_database.dart';

/// Generates a client-side UUID v4 — required as `local_uuid` on every
/// offline-created record (prospects, members, reports, tracking sessions
/// and points) so the server can dedupe retried creates. See
/// BACKEND_INTEGRATION_TASKS.md Phase 2.
String newLocalUuid() => const Uuid().v4();

/// Local status, distinct from the server's `sync_status` field (mapped in
/// BACKEND_INTEGRATION_TASKS.md Phase 2): `Draft` never reaches this queue
/// at all (it lives only in an entity's own local table, not yet built —
/// depends on the Phase 0 Consumer/Prospect/Member decision). `SyncQueueEntry`
/// only exists once a record is ready to be sent.
enum SyncQueueStatus { menungguSinkronisasi, sedangDikirim, terkirim, gagal }

extension SyncQueueStatusLabel on SyncQueueStatus {
  /// Matches the exact strings StatusBadge's badge-color map expects
  /// (lib/widgets/status.dart) — 'Sedang Dikirim' has no dedicated color
  /// and falls back to the neutral default, which is fine for a transient
  /// state.
  String get label => switch (this) {
        SyncQueueStatus.menungguSinkronisasi => 'Menunggu Sinkronisasi',
        SyncQueueStatus.sedangDikirim => 'Sedang Dikirim',
        SyncQueueStatus.terkirim => 'Terkirim',
        SyncQueueStatus.gagal => 'Gagal',
      };

  static SyncQueueStatus fromDb(String value) => SyncQueueStatus.values.firstWhere(
        (s) => s.name == value,
        orElse: () => SyncQueueStatus.menungguSinkronisasi,
      );
}

/// One row of the offline sync queue — an entity record (prospect, member,
/// operational report, visit report, tracking session/point) waiting to be
/// POSTed to the real API. `entityType` values are decided once the
/// corresponding Phase 3 entity work starts (e.g. 'prospect', 'member',
/// 'visit_report', 'tracking_point').
class SyncQueueEntry {
  final int? id;
  final String entityType;
  final String localUuid;
  final String operation; // 'create' | 'update'
  final String payloadJson;
  final List<String> filePaths;
  final int retryCount;
  final String? lastError;
  final DateTime? nextRetryAt;
  final SyncQueueStatus status;
  final DateTime createdAt;
  final DateTime updatedAt;

  const SyncQueueEntry({
    this.id,
    required this.entityType,
    required this.localUuid,
    required this.operation,
    required this.payloadJson,
    this.filePaths = const [],
    this.retryCount = 0,
    this.lastError,
    this.nextRetryAt,
    this.status = SyncQueueStatus.menungguSinkronisasi,
    required this.createdAt,
    required this.updatedAt,
  });

  SyncQueueEntry copyWith({
    int? id,
    SyncQueueStatus? status,
    int? retryCount,
    String? lastError,
    DateTime? nextRetryAt,
    DateTime? updatedAt,
  }) {
    return SyncQueueEntry(
      id: id ?? this.id,
      entityType: entityType,
      localUuid: localUuid,
      operation: operation,
      payloadJson: payloadJson,
      filePaths: filePaths,
      retryCount: retryCount ?? this.retryCount,
      lastError: lastError ?? this.lastError,
      nextRetryAt: nextRetryAt ?? this.nextRetryAt,
      status: status ?? this.status,
      createdAt: createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }

  Map<String, Object?> toDb() => {
        if (id != null) 'id': id,
        'entity_type': entityType,
        'local_uuid': localUuid,
        'operation': operation,
        'payload_json': payloadJson,
        'file_paths': filePaths.join('|'),
        'retry_count': retryCount,
        'last_error': lastError,
        'next_retry_at': nextRetryAt?.toIso8601String(),
        'status': status.name,
        'created_at': createdAt.toIso8601String(),
        'updated_at': updatedAt.toIso8601String(),
      };

  static SyncQueueEntry fromDb(Map<String, Object?> row) => SyncQueueEntry(
        id: row['id'] as int?,
        entityType: row['entity_type'] as String,
        localUuid: row['local_uuid'] as String,
        operation: row['operation'] as String,
        payloadJson: row['payload_json'] as String,
        filePaths: ((row['file_paths'] as String?) ?? '').split('|').where((s) => s.isNotEmpty).toList(),
        retryCount: row['retry_count'] as int,
        lastError: row['last_error'] as String?,
        nextRetryAt: (row['next_retry_at'] as String?) != null ? DateTime.parse(row['next_retry_at'] as String) : null,
        status: SyncQueueStatusLabel.fromDb(row['status'] as String),
        createdAt: DateTime.parse(row['created_at'] as String),
        updatedAt: DateTime.parse(row['updated_at'] as String),
      );
}

/// Storage seam for the sync queue — lets tests (and the web build, which
/// plain `sqflite` doesn't support without the separate `sqflite_common_ffi_web`
/// package) use an in-memory fallback instead of a real on-device database.
abstract class SyncQueueStore {
  Future<SyncQueueEntry> enqueue(SyncQueueEntry entry);
  Future<List<SyncQueueEntry>> listAll();
  Future<List<SyncQueueEntry>> listByStatus(SyncQueueStatus status);
  Future<void> updateStatus(int id, SyncQueueStatus status, {String? lastError});
  /// Schedules an automatic retry: resets status to
  /// [SyncQueueStatus.menungguSinkronisasi] (still pending, not a terminal
  /// failure) with an incremented [retryCount] and a [nextRetryAt] the
  /// dispatcher won't attempt before. Distinct from [updateStatus] with
  /// `gagal`, which is a terminal failure the dispatcher won't retry
  /// automatically at all.
  Future<void> scheduleRetry(int id, {required int retryCount, required DateTime nextRetryAt, String? lastError});
  Future<void> remove(int id);
  Future<Map<String, int>> countsByEntityType({SyncQueueStatus? status});
}

/// Default store: real SQLite on Android/iOS/desktop, in-memory on web.
/// Mobile is this app's actual shipped target — web here is a dev-preview
/// convenience (`flutter run -d chrome`), not a real deployment target, so
/// an in-memory fallback (queue resets on reload) is an acceptable tradeoff
/// rather than adding `sqflite_common_ffi_web` for full web persistence.
///
/// Memoized to a single shared instance: every call site (screens' create
/// flows, `SyncStatusScreen`, main.dart's periodic dispatcher) must see the
/// same queue. `SqliteSyncQueueStore` already shares one underlying
/// database via a static field regardless of instance count, but
/// `InMemorySyncQueueStore` does not — without memoizing here, each web
/// call site would silently get its own isolated in-memory queue.
SyncQueueStore? _defaultSyncQueueStore;
SyncQueueStore createDefaultSyncQueueStore() => _defaultSyncQueueStore ??= (kIsWeb ? InMemorySyncQueueStore() : SqliteSyncQueueStore());

class InMemorySyncQueueStore implements SyncQueueStore {
  final List<SyncQueueEntry> _rows = [];
  int _nextId = 1;

  @override
  Future<SyncQueueEntry> enqueue(SyncQueueEntry entry) async {
    final saved = entry.copyWith(id: _nextId++);
    _rows.add(saved);
    return saved;
  }

  @override
  Future<List<SyncQueueEntry>> listAll() async => List.unmodifiable(_rows);

  @override
  Future<List<SyncQueueEntry>> listByStatus(SyncQueueStatus status) async =>
      _rows.where((r) => r.status == status).toList(growable: false);

  @override
  Future<void> updateStatus(int id, SyncQueueStatus status, {String? lastError}) async {
    final i = _rows.indexWhere((r) => r.id == id);
    if (i == -1) return;
    _rows[i] = _rows[i].copyWith(status: status, lastError: lastError, updatedAt: DateTime.now());
  }

  @override
  Future<void> scheduleRetry(int id, {required int retryCount, required DateTime nextRetryAt, String? lastError}) async {
    final i = _rows.indexWhere((r) => r.id == id);
    if (i == -1) return;
    _rows[i] = _rows[i].copyWith(status: SyncQueueStatus.menungguSinkronisasi, retryCount: retryCount, nextRetryAt: nextRetryAt, lastError: lastError, updatedAt: DateTime.now());
  }

  @override
  Future<void> remove(int id) async => _rows.removeWhere((r) => r.id == id);

  @override
  Future<Map<String, int>> countsByEntityType({SyncQueueStatus? status}) async {
    final counts = <String, int>{};
    for (final r in _rows) {
      if (status != null && r.status != status) continue;
      counts[r.entityType] = (counts[r.entityType] ?? 0) + 1;
    }
    return counts;
  }
}

class SqliteSyncQueueStore implements SyncQueueStore {
  Future<Database> _open() => LocalDatabase.open();

  @override
  Future<SyncQueueEntry> enqueue(SyncQueueEntry entry) async {
    final db = await _open();
    final id = await db.insert('sync_queue', entry.toDb()..remove('id'));
    return entry.copyWith(id: id);
  }

  @override
  Future<List<SyncQueueEntry>> listAll() async {
    final db = await _open();
    final rows = await db.query('sync_queue', orderBy: 'created_at ASC');
    return rows.map(SyncQueueEntry.fromDb).toList();
  }

  @override
  Future<List<SyncQueueEntry>> listByStatus(SyncQueueStatus status) async {
    final db = await _open();
    final rows = await db.query('sync_queue', where: 'status = ?', whereArgs: [status.name], orderBy: 'created_at ASC');
    return rows.map(SyncQueueEntry.fromDb).toList();
  }

  @override
  Future<void> updateStatus(int id, SyncQueueStatus status, {String? lastError}) async {
    final db = await _open();
    await db.update(
      'sync_queue',
      {'status': status.name, 'last_error': lastError, 'updated_at': DateTime.now().toIso8601String()},
      where: 'id = ?',
      whereArgs: [id],
    );
  }

  @override
  Future<void> scheduleRetry(int id, {required int retryCount, required DateTime nextRetryAt, String? lastError}) async {
    final db = await _open();
    await db.update(
      'sync_queue',
      {
        'status': SyncQueueStatus.menungguSinkronisasi.name,
        'retry_count': retryCount,
        'next_retry_at': nextRetryAt.toIso8601String(),
        'last_error': lastError,
        'updated_at': DateTime.now().toIso8601String(),
      },
      where: 'id = ?',
      whereArgs: [id],
    );
  }

  @override
  Future<void> remove(int id) async {
    final db = await _open();
    await db.delete('sync_queue', where: 'id = ?', whereArgs: [id]);
  }

  @override
  Future<Map<String, int>> countsByEntityType({SyncQueueStatus? status}) async {
    final db = await _open();
    final rows = status == null
        ? await db.rawQuery('SELECT entity_type, COUNT(*) AS c FROM sync_queue GROUP BY entity_type')
        : await db.rawQuery(
            'SELECT entity_type, COUNT(*) AS c FROM sync_queue WHERE status = ? GROUP BY entity_type',
            [status.name],
          );
    return {for (final r in rows) r['entity_type'] as String: r['c'] as int};
  }
}
