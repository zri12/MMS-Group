import 'dart:convert';

import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:sqflite/sqflite.dart';

import 'local_database.dart';

/// Storage seam for the offline read cache. Deliberately generic (raw JSON
/// maps keyed by `entity_type`/`entity_id`) rather than one hand-typed
/// table per entity — every entity's own `fromJson` factory already knows
/// how to turn a decoded map back into a model, so there's nothing gained
/// from a bespoke schema per entity, and one implementation covers all of
/// them (see BACKEND_INTEGRATION_TASKS.md Phase 2).
///
/// This is a *cache*, not a full offline data layer: it only ever holds
/// whatever was last successfully fetched from the server (write-through
/// on every successful `list()`/`detail()`), so a marketing user can still
/// browse previously-seen Prospects/Members/Reports while offline. It does
/// NOT merge in locally-created-but-not-yet-synced records from
/// `sync_queue` — an offline-created record only appears here after its
/// next successful sync + re-fetch.
abstract class ReadCacheStore {
  Future<void> upsertAll(String entityType, List<Map<String, dynamic>> records);
  Future<List<Map<String, dynamic>>> listCached(String entityType);
  Future<Map<String, dynamic>?> getCached(String entityType, String entityId);
}

/// Default store: real SQLite on Android/iOS/desktop, in-memory on web —
/// same platform split and memoized-singleton reasoning as
/// `createDefaultSyncQueueStore()` in sync_queue.dart.
ReadCacheStore? _defaultReadCacheStore;
ReadCacheStore createDefaultReadCacheStore() => _defaultReadCacheStore ??= (kIsWeb ? InMemoryReadCacheStore() : SqliteReadCacheStore());

class InMemoryReadCacheStore implements ReadCacheStore {
  final Map<String, Map<String, Map<String, dynamic>>> _byType = {};

  @override
  Future<void> upsertAll(String entityType, List<Map<String, dynamic>> records) async {
    final bucket = _byType.putIfAbsent(entityType, () => {});
    for (final r in records) {
      bucket[r['id'].toString()] = r;
    }
  }

  @override
  Future<List<Map<String, dynamic>>> listCached(String entityType) async {
    return (_byType[entityType]?.values ?? const <Map<String, dynamic>>[]).toList(growable: false);
  }

  @override
  Future<Map<String, dynamic>?> getCached(String entityType, String entityId) async {
    return _byType[entityType]?[entityId];
  }
}

class SqliteReadCacheStore implements ReadCacheStore {
  Future<Database> _open() => LocalDatabase.open();

  @override
  Future<void> upsertAll(String entityType, List<Map<String, dynamic>> records) async {
    if (records.isEmpty) return;
    final db = await _open();
    final now = DateTime.now().toIso8601String();
    final batch = db.batch();
    for (final r in records) {
      batch.insert(
        'read_cache',
        {'entity_type': entityType, 'entity_id': r['id'].toString(), 'payload_json': jsonEncode(r), 'cached_at': now},
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
    }
    await batch.commit(noResult: true);
  }

  @override
  Future<List<Map<String, dynamic>>> listCached(String entityType) async {
    final db = await _open();
    final rows = await db.query('read_cache', where: 'entity_type = ?', whereArgs: [entityType]);
    return rows.map((row) => jsonDecode(row['payload_json'] as String) as Map<String, dynamic>).toList();
  }

  @override
  Future<Map<String, dynamic>?> getCached(String entityType, String entityId) async {
    final db = await _open();
    final rows = await db.query('read_cache', where: 'entity_type = ? AND entity_id = ?', whereArgs: [entityType, entityId], limit: 1);
    if (rows.isEmpty) return null;
    return jsonDecode(rows.first['payload_json'] as String) as Map<String, dynamic>;
  }
}
