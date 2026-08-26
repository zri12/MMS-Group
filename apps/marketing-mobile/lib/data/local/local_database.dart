import 'package:path/path.dart' as p;
import 'package:sqflite/sqflite.dart';

/// Single shared local SQLite database file for this app. Both the offline
/// write queue (`sync_queue`) and the offline read cache (`read_cache`)
/// live here — sqflite's `openDatabase` treats the same path as a single
/// instance, so if two classes each called `openDatabase(path, onCreate:
/// ...)` with different callbacks, only whichever opened first would have
/// its schema actually created; the second table would silently never
/// exist. Centralizing here avoids that trap.
class LocalDatabase {
  LocalDatabase._();
  static Database? _db;

  static Future<Database> open() async {
    if (_db != null) return _db!;
    final dbPath = await getDatabasesPath();
    final path = p.join(dbPath, 'mms_marketing_local.db');
    _db = await openDatabase(
      path,
      version: 2,
      onCreate: (db, version) async {
        await db.execute(_syncQueueSchema);
        await db.execute(_readCacheSchema);
      },
      onUpgrade: (db, oldVersion, newVersion) async {
        if (oldVersion < 2) {
          await db.execute(_readCacheSchema);
        }
      },
    );
    return _db!;
  }

  static const _syncQueueSchema = '''
    CREATE TABLE sync_queue (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      entity_type TEXT NOT NULL,
      local_uuid TEXT NOT NULL,
      operation TEXT NOT NULL,
      payload_json TEXT NOT NULL,
      file_paths TEXT,
      retry_count INTEGER NOT NULL DEFAULT 0,
      last_error TEXT,
      next_retry_at TEXT,
      status TEXT NOT NULL,
      created_at TEXT NOT NULL,
      updated_at TEXT NOT NULL
    )
  ''';

  static const _readCacheSchema = '''
    CREATE TABLE read_cache (
      entity_type TEXT NOT NULL,
      entity_id TEXT NOT NULL,
      payload_json TEXT NOT NULL,
      cached_at TEXT NOT NULL,
      PRIMARY KEY (entity_type, entity_id)
    )
  ''';
}
