---
title: "Offline Sync and Tracking"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Tujuan

Aplikasi tetap dapat mencatat data ketika internet tidak stabil dan menyinkronkan tanpa duplikasi saat koneksi kembali.

# Local tables Flutter

Minimal:

- local_prospects
- local_members
- local_operational_reports
- local_visit_reports
- local_tracking_sessions
- local_tracking_points
- sync_queue

# Status lokal

- Draft
- Menunggu Sinkronisasi
- Sedang Dikirim
- Terkirim
- Gagal

`Sedang Dikirim` adalah state internal dan tidak perlu tampil sebagai status permanen.

# local_uuid

Setiap data create offline memiliki UUID v4.

Server menjadikan `local_uuid` unique per tabel. Retry request mengembalikan record yang sama.

# Sync queue

Kolom rekomendasi:

- id
- entity_type
- local_uuid
- operation
- payload_json atau reference lokal
- file_paths
- retry_count
- last_error
- next_retry_at
- status
- created_at
- updated_at

# Urutan sinkronisasi

1. Auth/profile
2. Master/jadwal pull
3. Prospek
4. Anggota
5. Laporan operasional
6. Laporan kunjungan
7. Tracking sessions
8. Tracking points

Relasi yang memakai local ID harus dipetakan ke server ID setelah parent berhasil.

# Retry

- Exponential backoff.
- Validation error tidak di-retry otomatis sampai user memperbaiki.
- 401 meminta login ulang.
- Timeout/network di-retry.
- 409 local_uuid dianggap perlu mengambil record server.
- Maksimum retry dan interval ditentukan config.

# Conflict strategy

Versi awal:

- Data lapangan create-only setelah terkirim.
- Edit prospek memakai last-write-wins atau server updated_at; keputusan detail perlu implementasi sederhana.
- Approval anggota selalu server/admin.
- Jadwal server menjadi sumber kebenaran.
- Tracking point append-only.

# Tracking flow

1. Marketing menekan mulai.
2. Flutter membuat local tracking session.
3. Foreground tracking aktif.
4. Titik disimpan SQLite.
5. Batch dikirim saat koneksi tersedia.
6. Marketing menekan selesai.
7. Session stop dikirim.
8. Server menghitung/menyimpan ringkasan bila diperlukan.
9. Web admin polling posisi terbaru.

# Frekuensi

Belum dikunci. Gunakan config, bukan hardcode.

Pertimbangan:

- interval terlalu cepat membebani baterai/server;
- interval terlalu lambat membuat rute kasar;
- shared hosting cPanel tidak cocok request per detik.

# Background

- Android harus menampilkan foreground service notification saat tracking aktif.
- Workmanager untuk retry/sync, bukan pengganti foreground GPS.
- Tracking tidak boleh berjalan diam-diam setelah sesi dihentikan.

# Batch API

- Maksimum awal 100 titik/request.
- Urut berdasarkan recorded_at.
- Server dedupe local_uuid.
- Server menyimpan received_at.
- Partial failure harus dilaporkan per item atau request atomic; pilih atomic versi awal agar sederhana.
- Implementasi Laravel versi ini memakai request atomic: bila satu point konflik, seluruh batch rollback.
- Duplicate `local_uuid` pada sesi yang sama dianggap retry dan tidak membuat row baru.
- Duplicate `local_uuid` pada sesi lain mengembalikan konflik.
- Hanya satu sesi `Aktif` per marketing.
- Stop session memakai status `Offline` karena enum final belum memiliki status `Selesai`.

# Web polling

- Default contoh 30 detik, configurable.
- Pause polling ketika tab tidak aktif bila sesuai.
- Refresh manual tersedia.
- Tampilkan waktu pembaruan.
