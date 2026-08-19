---
title: "Database Schema"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.1.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Konvensi database

- Engine: InnoDB
- Charset: utf8mb4
- Primary key: BIGINT unsigned
- Timestamp: UTC di database, ditampilkan Asia/Jakarta; atau konsisten Asia/Jakarta jika hosting membatasi. Keputusan implementasi harus satu.
- Nominal: BIGINT unsigned dalam satuan rupiah
- Latitude/longitude: DECIMAL(10,7)
- Status: VARCHAR + PHP Enum/Form Request, bukan MySQL ENUM
- Soft delete digunakan pada data master yang perlu riwayat, terutama marketing
- Nama tabel plural snake_case

# 1. users

| Kolom | Tipe | Null | Index | Keterangan |
|---|---|---:|---|---|
| id | bigint unsigned | Tidak | PK | ID |
| name | varchar(150) | Tidak | | Nama |
| username | varchar(100) | Tidak | Unique | Username login |
| email | varchar(150) | Ya | Unique | Email admin/marketing |
| password | varchar(255) | Tidak | | Hash |
| role | varchar(30) | Tidak | Index | admin/marketing |
| is_active | boolean | Tidak | Index | Status akun |
| last_login_at | timestamp | Ya | | Login terakhir |
| created_at | timestamp | Tidak | | |
| updated_at | timestamp | Tidak | | |
| deleted_at | timestamp | Ya | | Soft delete opsional |

# 2. marketing_profiles

| Kolom | Tipe | Null | Index | Keterangan |
|---|---|---:|---|---|
| id | bigint unsigned | Tidak | PK | |
| user_id | bigint unsigned | Tidak | Unique/FK | users |
| code | varchar(10) | Tidak | Unique | M01–M13 |
| phone | varchar(30) | Ya | Index | |
| area | varchar(100) | Tidak | Index | Area/resort utama |
| profile_photo_path | varchar(255) | Ya | | |
| created_at | timestamp | Tidak | | |
| updated_at | timestamp | Tidak | | |

Relasi: User hasOne MarketingProfile.

# 3. marketing_work_days

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| marketing_profile_id | bigint unsigned | Tidak | FK/index |
| day_name | varchar(20) | Tidak | Index |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |

Unique: `(marketing_profile_id, day_name)`.

# 4. marketing_schedules

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| marketing_profile_id | bigint unsigned | Tidak | FK/index |
| prospect_id | bigint unsigned | Ya | FK/index |
| day_name | varchar(20) | Tidak | Index |
| schedule_date | date | Tidak | Index |
| start_time | time | Tidak | |
| end_time | time | Ya | |
| consumer_name_snapshot | varchar(150) | Ya | |
| agenda | varchar(255) | Tidak | |
| area | varchar(100) | Tidak | Index |
| resort | varchar(100) | Ya | Index |
| destination | varchar(255) | Ya | |
| note | text | Ya | |
| status | varchar(30) | Tidak | Index |
| created_by | bigint unsigned | Tidak | FK |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |
| deleted_at | timestamp | Ya | |

# 5. prospects

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| local_uuid | char(36) | Ya | Unique |
| marketing_profile_id | bigint unsigned | Tidak | FK/index |
| name | varchar(150) | Tidak | Index |
| phone | varchar(30) | Tidak | Index |
| address | text | Tidak | |
| business | varchar(150) | Tidak | |
| status | varchar(30) | Tidak | Index |
| initial_visit_result | text | Tidak | |
| notes | text | Ya | |
| resort | varchar(100) | Tidak | Index |
| input_date | date | Tidak | Index |
| input_time | time | Tidak | |
| latitude | decimal(10,7) | Ya | |
| longitude | decimal(10,7) | Ya | |
| location_address | varchar(255) | Ya | |
| sync_status | varchar(30) | Tidak | Index |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |
| deleted_at | timestamp | Ya | |

Catatan relasi: prospek tidak menyimpan `linked_member_id` secara fisik. Anggota terkait diturunkan dari `members.source_prospect_id`.

# 6. members

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| local_uuid | char(36) | Ya | Unique |
| marketing_profile_id | bigint unsigned | Tidak | FK/index |
| source_prospect_id | bigint unsigned | Ya | Unique/FK |
| resort | varchar(100) | Tidak | Index |
| input_date | date | Tidak | Index |
| input_time | time | Tidak | |
| name | varchar(150) | Tidak | Index |
| member_number | varchar(50) | Tidak | Unique |
| loan_number | varchar(50) | Tidak | Unique |
| address | text | Tidak | |
| phone | varchar(30) | Tidak | Index |
| business | varchar(150) | Tidak | |
| loan_amount | bigint unsigned | Tidak | |
| installment_amount | bigint unsigned | Tidak | |
| insurance_amount | bigint unsigned | Tidak | |
| collateral | varchar(255) | Tidak | |
| approval_status | varchar(30) | Tidak | Index |
| member_photo_path | varchar(255) | Ya | |
| latitude | decimal(10,7) | Ya | |
| longitude | decimal(10,7) | Ya | |
| location_address | varchar(255) | Ya | |
| approved_by | bigint unsigned | Ya | FK |
| approved_at | timestamp | Ya | |
| rejected_by | bigint unsigned | Ya | FK |
| rejected_at | timestamp | Ya | |
| rejection_reason | text | Ya | |
| sync_status | varchar(30) | Tidak | Index |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |
| deleted_at | timestamp | Ya | |

`source_prospect_id` nullable dan unik. Satu prospek maksimal menjadi asal satu anggota. Bila prospek dihapus permanen, nilai ini menjadi null.

# 7. daily_operational_reports

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| local_uuid | char(36) | Ya | Unique |
| marketing_profile_id | bigint unsigned | Tidak | FK/index |
| report_date | date | Tidak | Index |
| report_time | time | Tidak | |
| day_name | varchar(20) | Tidak | Index |
| resort | varchar(100) | Tidak | Index |
| storting | bigint unsigned | Tidak | |
| insurance_amount | bigint unsigned | Tidak | |
| drop_amount | bigint unsigned | Tidak | |
| withdrawal_saving | bigint unsigned | Tidak | |
| previous_target_amount | bigint unsigned | Tidak | |
| previous_target_people | unsigned smallint | Tidak | |
| incoming_target_amount | bigint unsigned | Tidak | |
| incoming_target_people | unsigned smallint | Tidak | |
| outgoing_target_amount | bigint unsigned | Tidak | |
| outgoing_target_people | unsigned smallint | Tidak | |
| total_target_amount | bigint unsigned | Tidak | |
| total_target_people | unsigned smallint | Tidak | |
| new_drop | bigint unsigned | Tidak | |
| continued_drop | bigint unsigned | Tidak | |
| notes | text | Ya | |
| sync_status | varchar(30) | Tidak | Index |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |

Keputusan apakah satu marketing hanya boleh satu laporan per tanggal masih pending. Jangan buat unique constraint sebelum dikonfirmasi.

## 7A. operational_report_attachments

| Kolom | Tipe | Null | Index | Keterangan |
|---|---|---:|---|---|
| id | bigint unsigned | Tidak | PK | |
| daily_operational_report_id | bigint unsigned | Tidak | FK/index | Laporan operasional pemilik lampiran |
| type | varchar(30) | Tidak | Index | `disbursement` atau `transfer_proof` |
| photo_path | varchar(255) | Tidak | | Path relatif pada disk public |
| caption | varchar(255) | Ya | | |
| uploaded_at | timestamp | Ya | Index gabungan | |
| created_at | timestamp | Tidak | | |
| updated_at | timestamp | Tidak | | |

Foreign key `daily_operational_report_id` memakai cascade delete. Unique
`(daily_operational_report_id, type)` memastikan satu laporan paling banyak
memiliki satu foto untuk masing-masing kategori.

# 8. visit_reports

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| local_uuid | char(36) | Ya | Unique |
| prospect_id | bigint unsigned | Tidak | FK/index |
| marketing_profile_id | bigint unsigned | Tidak | FK/index |
| visit_date | date | Tidak | Index |
| visit_time | time | Tidak | |
| day_name | varchar(20) | Tidak | Index |
| visit_purpose | varchar(255) | Tidak | |
| visit_result | varchar(50) | Tidak | Index |
| prospect_status | varchar(50) | Tidak | Index |
| notes | text | Ya | |
| follow_up_date | date | Ya | Index |
| photo_path | varchar(255) | Ya | |
| photo_caption | varchar(255) | Ya | |
| resort | varchar(100) | Tidak | Index |
| latitude | decimal(10,7) | Ya | |
| longitude | decimal(10,7) | Ya | |
| location_address | varchar(255) | Ya | |
| sync_status | varchar(30) | Tidak | Index |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |

# 9. tracking_sessions

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| local_uuid | char(36) | Ya | Unique |
| marketing_profile_id | bigint unsigned | Tidak | FK/index |
| schedule_id | bigint unsigned | Ya | FK/index |
| session_date | date | Tidak | Index |
| day_name | varchar(20) | Tidak | Index |
| started_at | timestamp | Tidak | Index |
| ended_at | timestamp | Ya | |
| status | varchar(30) | Tidak | Index |
| distance_meters | unsigned integer | Ya | |
| visit_count | unsigned smallint | Tidak | |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |

Aturan satu sesi aktif per marketing ditegakkan pada service/transaction.

# 10. tracking_points

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| local_uuid | char(36) | Ya | Unique |
| tracking_session_id | bigint unsigned | Tidak | FK/index |
| latitude | decimal(10,7) | Tidak | |
| longitude | decimal(10,7) | Tidak | |
| accuracy_meters | decimal(8,2) | Ya | |
| speed_mps | decimal(8,2) | Ya | |
| heading | decimal(8,2) | Ya | |
| altitude_meters | decimal(10,2) | Ya | |
| address | varchar(255) | Ya | |
| point_type | varchar(30) | Tidak | Index |
| recorded_at | timestamp | Tidak | Index |
| received_at | timestamp | Tidak | |
| created_at | timestamp | Tidak | Default current timestamp |

Index gabungan: `(tracking_session_id, recorded_at)`.

# 11. operational_recaps

| Kolom | Tipe | Null | Index |
|---|---|---:|---|
| id | bigint unsigned | Tidak | PK |
| report_number | varchar(50) | Tidak | Unique |
| recap_date | date | Tidak | Unique/index |
| day_name | varchar(20) | Tidak | Index |
| status | varchar(30) | Tidak | Index |
| created_by | bigint unsigned | Tidak | FK |
| created_at | timestamp | Tidak | |
| updated_at | timestamp | Tidak | |

# 12. operational_recap_rows

| Kolom | Tipe | Null |
|---|---|---:|
| id | bigint unsigned | Tidak |
| operational_recap_id | bigint unsigned | Tidak |
| marketing_profile_id | bigint unsigned | Tidak |
| mg | varchar(20) | Tidak |
| members_l | integer unsigned | Tidak |
| members_m | integer unsigned | Tidak |
| members_k | integer unsigned | Tidak |
| members_s | integer unsigned | Tidak |
| target_previous | bigint unsigned | Tidak |
| target_incoming | bigint unsigned | Tidak |
| target_outgoing | bigint unsigned | Tidak |
| target_s | bigint unsigned | Tidak |
| drop_previous | bigint unsigned | Tidak |
| drop_current | bigint unsigned | Tidak |
| drop_total | bigint unsigned | Tidak |
| storting_previous | bigint unsigned | Tidak |
| storting_current | bigint unsigned | Tidak |
| storting_total | bigint unsigned | Tidak |
| percentage | decimal(7,2) | Ya |
| previous_circulation | bigint unsigned | Tidak |
| current_circulation | bigint unsigned | Tidak |
| followed_by | varchar(150) | Tidak |
| morning_cash | bigint unsigned | Tidak |
| created_at | timestamp | Tidak |
| updated_at | timestamp | Tidak |

Unique: `(operational_recap_id, marketing_profile_id)`.

Semua nilai disimpan eksplisit sampai rumus bisnis dikonfirmasi.

# 13. personal_access_tokens

Gunakan tabel standar Laravel Sanctum.

# Relasi utama

- User 1–1 MarketingProfile
- MarketingProfile 1–N WorkDays
- MarketingProfile 1–N Schedules
- MarketingProfile 1–N Prospects
- Prospect 1–N VisitReports
- Prospect 0–1 Member melalui `members.source_prospect_id`
- MarketingProfile 1–N Members
- MarketingProfile 1–N DailyOperationalReports
- MarketingProfile 1–N TrackingSessions
- TrackingSession 1–N TrackingPoints
- OperationalRecap 1–N OperationalRecapRows
- MarketingProfile 1–N OperationalRecapRows

- DailyOperationalReport memiliki banyak OperationalReportAttachments

# Delete policy

- Marketing: soft delete atau inactive, tidak cascade history.
- Prospect: soft delete.
- Prospect force delete membuat `members.source_prospect_id` menjadi null.
- Member: soft delete.
- Schedule: soft delete.
- Tracking point: tidak dihapus saat marketing inactive.
- File orphan dibersihkan dengan maintenance job setelah record benar-benar dihapus.
