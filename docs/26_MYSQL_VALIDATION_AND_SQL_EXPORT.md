---
title: "MySQL Validation and SQL Export"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Ringkasan

Validasi MySQL lokal dilakukan pada 2026-07-29 menggunakan database development lokal.

## Environment

| Item | Nilai |
|---|---|
| APP_ENV | `local` |
| DB_CONNECTION | `mysql` |
| DB_HOST | `127.0.0.1` |
| DB_PORT | `3306` |
| DB_DATABASE | `mms_monitoring_dev` |
| MySQL/MariaDB | `10.4.32-MariaDB` |
| PHP | `8.2.12` |
| Laravel | `12.64.0` |

Password database dan `APP_KEY` tidak didokumentasikan.

## Migration

Database `mms_monitoring_dev` dibuat lokal dengan charset `utf8mb4` dan awalnya kosong.

Hasil:

- `php artisan migrate` berhasil.
- Semua 15 migration berstatus `Ran`.
- Validasi rollback development berhasil.
- Migration ulang setelah rollback berhasil.

Catatan perbaikan MySQL:

- Kolom event time tracking memakai `dateTime` pada migration `tracking_sessions` dan `tracking_points` agar kompatibel dengan MariaDB 10.4.

## Seeder

`php artisan db:seed` berhasil pada database MySQL lokal.

Seeder ulang juga berhasil tanpa duplikasi.

| Metric | Count |
|---|---:|
| marketing_profiles | 13 |
| marketing_work_days | 78 |
| members | 39 |
| prospects | 3 |
| daily_operational_reports | 13 |
| tracking_sessions | 1 |
| tracking_points | 9 |
| operational_recaps | 6 |
| operational_recap_rows | 78 |

Status anggota:

| Status | Count |
|---|---:|
| Menunggu | 13 |
| Disetujui | 13 |
| Ditolak | 13 |

Tracking utama:

| Metric | Count |
|---|---:|
| visit_count | 3 |
| tracking_points | 9 |
| point_type Kunjungan | 3 |

Rekap 2026-07-20:

| Metric | Nilai |
|---|---:|
| rows | 13 |
| target_s total | 123500000 |
| drop_total | 91000000 |
| storting_total | 37700000 |
| percentage non-null | 0 |
| previous_circulation total | 0 |
| current_circulation total | 0 |

Validasi relasi:

- Herman Malik memiliki `source_prospect_id = null`.
- Tidak ada duplikasi `member_number`.
- Tidak ada duplikasi `loan_number`.
- Tidak ada duplikasi `marketing_profiles.code`.
- Tidak ada satu prospek yang digunakan oleh lebih dari satu anggota.
- Tidak ada orphan `members.marketing_profile_id`.

## SQL Export

Tool:

```text
D:\XAMPP\mysql\bin\mysqldump.exe
```

File yang dibuat:

| File | Ukuran | Fungsi |
|---|---:|---|
| `apps/web-admin-api/database/sql/mms_monitoring_schema.sql` | 25,592 bytes | Schema-only |
| `apps/web-admin-api/database/sql/mms_monitoring_development_data.sql` | 49,022 bytes | Data development-only |
| `apps/web-admin-api/database/sql/mms_monitoring_development_full.sql` | 74,640 bytes | Schema + data development |
| `apps/web-admin-api/database/sql/README.md` | - | Panduan import |

Static validation:

- Schema SQL memiliki 21 `CREATE TABLE` dan 0 `INSERT`.
- Development data SQL memiliki 0 `CREATE TABLE` dan 13 `INSERT`.
- Full SQL memiliki 21 `CREATE TABLE` dan 13 `INSERT`.
- Tidak ada `APP_KEY`.
- Tidak ada `DB_PASSWORD`.
- Tidak ada `DEFINER`.
- Tidak ada INSERT untuk `cache`, `cache_locks`, `sessions`, `jobs`, `job_batches`, `failed_jobs`, atau `personal_access_tokens`.
- Auto increment runtime value dinetralkan dari dump.

## Restore Validation

Restore full SQL berhasil pada database sementara:

```text
mms_monitoring_restore_test_20260729115801
```

Hasil restore:

| Metric | Count |
|---|---:|
| marketing_profiles | 13 |
| marketing_work_days | 78 |
| members | 39 |
| tracking_points | 9 |
| operational_recap_rows | 78 |

Rekap restore 2026-07-20:

| Metric | Nilai |
|---|---:|
| rows | 13 |
| target_s total | 123500000 |
| drop_total | 91000000 |
| storting_total | 37700000 |
| percentage non-null | 0 |
| current_circulation total | 0 |

Database sementara dihapus setelah validasi. Pemeriksaan akhir menunjukkan sisa database restore: 0.

## Pending

- SQL dump hanya untuk development. Production wajib memakai credential berbeda dan review deployment.
- Password hash development wajib direset sebelum deployment.
