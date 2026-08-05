# MMS Database Implementation Matrix

## 1. Sumber yang Digunakan

- `AGENTS.md`
- `README.md`
- `PROJECT_STRUCTURE.md`
- `MANIFEST.md`
- `docs/INDEX.md`
- `docs/00_MASTER_SPEC.md`
- `docs/01_TECH_STACK.md`
- `docs/02_SCOPE_AND_MODULES.md`
- `docs/05_PAGE_AND_ROUTE_MAP.md`
- `docs/06_AUTH_AND_ROLES.md`
- `docs/07_DATABASE_SCHEMA.md`
- `docs/08_DATA_DICTIONARY.md`
- `docs/09_API_CONTRACT.md`
- `docs/10_BUSINESS_RULES.md`
- `docs/11_FILE_STORAGE.md`
- `docs/12_MIGRATION_PLAN.md`
- `docs/13_TESTING_ACCEPTANCE.md`
- `docs/15_DECISION_LOG.md`
- `docs/16_CHANGELOG.md`
- `docs/17_CODING_STANDARDS.md`
- `docs/18_ENVIRONMENT_AND_CONFIG.md`
- `docs/19_SECURITY_AND_PRIVACY.md`
- `docs/20_OFFLINE_SYNC_AND_TRACKING.md`
- `docs/21_SEED_DATA.md`
- `docs/22_OPEN_QUESTIONS.md`
- `docs/23_GLOSSARY.md`
- `docs/pages/*.md`
- `docs/diagrams/database-erd.md`
- `docs/diagrams/data-flow.md`
- `docs/diagrams/system-architecture.md`

## 2. Prinsip Implementasi

- Target production adalah MySQL/MariaDB dengan InnoDB dan `utf8mb4`.
- Test migration memakai SQLite in-memory dari `phpunit.xml`, bukan `.env` lokal.
- Status domain memakai PHP backed enum bila daftar status terdokumentasi.
- Kolom status di database disimpan sebagai string, bukan native database enum.
- Foreign key tidak memakai cascade kecuali child murni yang tidak bermakna tanpa parent.
- Kolom uang memakai BIGINT unsigned dalam satuan rupiah.
- Latitude dan longitude memakai `decimal(10,7)`, bukan float.
- File disimpan sebagai path relatif, bukan binary/base64.
- Rekap operasional menyimpan nilai eksplisit dan tidak memakai formula otomatis.
- Field offline create memakai `local_uuid` unique per tabel sesuai kontrak idempotency.
- Migration sudah divalidasi pada MySQL/MariaDB lokal dan test otomatis tetap memakai SQLite in-memory.

## 3. Matrix Per Tabel

### users

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key Laravel |
| name | varchar(150) | Tidak | - | - | - | - | string | `07_DATABASE_SCHEMA.md`, `08_DATA_DICTIONARY.md` | Nama pengguna |
| username | varchar(100) | Tidak | - | - | Ya | - | string | `07_DATABASE_SCHEMA.md`, `10_BUSINESS_RULES.md` | Login unik |
| email | varchar(150) | Ya | null | - | Ya | - | string/null | `07_DATABASE_SCHEMA.md`, `08_DATA_DICTIONARY.md` | Email tidak wajib |
| email_verified_at | timestamp | Ya | null | - | - | - | datetime | Laravel infrastructure | Dipertahankan dari Laravel auth |
| password | varchar(255) | Tidak | - | - | - | - | hashed | `06_AUTH_AND_ROLES.md` | Hash Laravel |
| role | varchar(30) | Tidak | - | Ya | - | - | `UserRole` | `06_AUTH_AND_ROLES.md`, `07_DATABASE_SCHEMA.md` | `admin`, `marketing`; tidak ada default admin |
| is_active | boolean | Tidak | - | Ya | - | - | boolean | `06_AUTH_AND_ROLES.md`, `07_DATABASE_SCHEMA.md` | Akun inactive tidak login; nilai diisi service/seeder |
| last_login_at | timestamp | Ya | null | - | - | - | datetime | `07_DATABASE_SCHEMA.md` | Login terakhir |
| remember_token | varchar(100) | Ya | null | - | - | - | string | Laravel infrastructure | Dipertahankan |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel infrastructure | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel infrastructure | Timestamp |
| deleted_at | timestamp | Ya | null | - | - | - | datetime | `07_DATABASE_SCHEMA.md`, `10_BUSINESS_RULES.md` | Soft delete untuk akun/master |

### marketing_profiles

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| user_id | bigint unsigned | Tidak | - | FK | Ya | `users.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | User 1-1 MarketingProfile |
| code | varchar(10) | Tidak | - | - | Ya | - | string | `07_DATABASE_SCHEMA.md`, `10_BUSINESS_RULES.md` | Format awal M01-M99 |
| phone | varchar(30) | Ya | null | Ya | - | - | string/null | `07_DATABASE_SCHEMA.md` | Nomor HP |
| area | varchar(100) | Tidak | - | Ya | - | - | string | `07_DATABASE_SCHEMA.md` | Area/resort utama |
| profile_photo_path | varchar(255) | Ya | null | - | - | - | string/null | `11_FILE_STORAGE.md` | Path relatif |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |

### marketing_work_days

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | composite | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Child hari kerja |
| day_name | varchar(20) | Tidak | - | Ya | composite | - | `DayName` | `07_DATABASE_SCHEMA.md`, `10_BUSINESS_RULES.md` | Senin-Sabtu |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |

### prospects

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| local_uuid | char(36) | Ya | null | - | Ya | - | string/null | `08_DATA_DICTIONARY.md`, `20_OFFLINE_SYNC_AND_TRACKING.md` | Idempotency offline |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | - | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Owner marketing |
| name | varchar(150) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Search |
| phone | varchar(30) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Search |
| address | text | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Alamat |
| business | varchar(150) | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Usaha/pekerjaan |
| status | varchar(30) | Tidak | - | Ya | - | - | `ProspectStatus` | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` | Status prospek |
| initial_visit_result | text | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Hasil awal |
| notes | text | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Catatan |
| resort | varchar(100) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md`, page specs | Filter resort |
| input_date | date | Tidak | - | Ya | - | - | date | `08_DATA_DICTIONARY.md` | Tanggal input |
| input_time | time | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Waktu input |
| latitude | decimal(10,7) | Ya | null | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md`, `08_DATA_DICTIONARY.md` | Koordinat |
| longitude | decimal(10,7) | Ya | null | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md`, `08_DATA_DICTIONARY.md` | Koordinat |
| location_address | varchar(255) | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Lokasi reverse geocode |
| sync_status | varchar(30) | Tidak | - | Ya | - | - | `SyncStatus` | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` | Status sinkronisasi |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| deleted_at | timestamp | Ya | null | - | - | - | datetime | `10_BUSINESS_RULES.md` | Soft delete |

### members

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| local_uuid | char(36) | Ya | null | - | Ya | - | string/null | `08_DATA_DICTIONARY.md`, `20_OFFLINE_SYNC_AND_TRACKING.md` | Idempotency offline |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | - | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Marketing penginput |
| source_prospect_id | bigint unsigned | Ya | null | Ya | Ya | `prospects.id` null on delete | integer/null | `07_DATABASE_SCHEMA.md`, ERD, DEC-014 | Relasi canonical ke prospek asal; satu prospek maksimal satu anggota |
| resort | varchar(100) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Filter resort |
| input_date | date | Tidak | - | Ya | - | - | date | `08_DATA_DICTIONARY.md` | Tanggal input |
| input_time | time | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Waktu input |
| name | varchar(150) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Search |
| member_number | varchar(50) | Tidak | - | - | Ya | - | string | `10_BUSINESS_RULES.md` | Nomor anggota unik |
| loan_number | varchar(50) | Tidak | - | - | Ya | - | string | `10_BUSINESS_RULES.md` | Nomor pinjaman unik menurut schema locked |
| address | text | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Alamat |
| phone | varchar(30) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Search |
| business | varchar(150) | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Jenis usaha |
| loan_amount | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| installment_amount | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| insurance_amount | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| collateral | varchar(255) | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Jaminan |
| approval_status | varchar(30) | Tidak | - | Ya | - | - | `MemberApprovalStatus` | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` | Status awal di service: Menunggu |
| member_photo_path | varchar(255) | Ya | null | - | - | - | string/null | `11_FILE_STORAGE.md`, `22_OPEN_QUESTIONS.md` | Schema mengizinkan null; kewajiban foto masih open question |
| latitude | decimal(10,7) | Ya | null | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md` | Koordinat |
| longitude | decimal(10,7) | Ya | null | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md` | Koordinat |
| location_address | varchar(255) | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Lokasi |
| approved_by | bigint unsigned | Ya | null | FK | - | `users.id` null on delete | integer/null | `06_AUTH_AND_ROLES.md` | Audit minimum |
| approved_at | timestamp | Ya | null | - | - | - | datetime | `06_AUTH_AND_ROLES.md` | Audit minimum |
| rejected_by | bigint unsigned | Ya | null | FK | - | `users.id` null on delete | integer/null | `06_AUTH_AND_ROLES.md` | Audit minimum |
| rejected_at | timestamp | Ya | null | - | - | - | datetime | `06_AUTH_AND_ROLES.md` | Audit minimum |
| rejection_reason | text | Ya | null | - | - | - | string/null | `07_DATABASE_SCHEMA.md`, `22_OPEN_QUESTIONS.md` | Alasan wajib masih open question |
| sync_status | varchar(30) | Tidak | - | Ya | - | - | `SyncStatus` | `08_DATA_DICTIONARY.md` | Sinkronisasi |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| deleted_at | timestamp | Ya | null | - | - | - | datetime | `10_BUSINESS_RULES.md` | Soft delete |

### marketing_schedules

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | - | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Jadwal marketing |
| prospect_id | bigint unsigned | Ya | null | FK | - | `prospects.id` null on delete | integer/null | `07_DATABASE_SCHEMA.md`, `22_OPEN_QUESTIONS.md` | Jadwal tanpa prospek diizinkan schema |
| day_name | varchar(20) | Tidak | - | Ya | - | - | `DayName` | `08_DATA_DICTIONARY.md` | Hari |
| schedule_date | date | Tidak | - | Ya | - | - | date | `08_DATA_DICTIONARY.md` | Tanggal |
| start_time | time | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Jam mulai |
| end_time | time | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Jam selesai |
| consumer_name_snapshot | varchar(150) | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Nama konsumen snapshot |
| agenda | varchar(255) | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Agenda |
| area | varchar(100) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Area |
| resort | varchar(100) | Ya | null | Ya | - | - | string/null | `08_DATA_DICTIONARY.md` | Resort opsional |
| destination | varchar(255) | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Tujuan |
| note | text | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Catatan |
| status | varchar(30) | Tidak | - | Ya | - | - | `ScheduleStatus` | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` | Status jadwal |
| created_by | bigint unsigned | Tidak | - | FK | - | `users.id` restrict | integer | `07_DATABASE_SCHEMA.md` | Admin pembuat |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| deleted_at | timestamp | Ya | null | - | - | - | datetime | `07_DATABASE_SCHEMA.md` | Soft delete |

### daily_operational_reports

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| local_uuid | char(36) | Ya | null | - | Ya | - | string/null | `20_OFFLINE_SYNC_AND_TRACKING.md` | Idempotency |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | - | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Owner |
| report_date | date | Tidak | - | Ya | - | - | date | `08_DATA_DICTIONARY.md` | Tanggal |
| report_time | time | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Waktu |
| day_name | varchar(20) | Tidak | - | Ya | - | - | `DayName` | `08_DATA_DICTIONARY.md` | Hari |
| resort | varchar(100) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Resort |
| storting | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| insurance_amount | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| drop_amount | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| withdrawal_saving | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| previous_target_amount | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| previous_target_people | unsigned smallint | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Orang |
| incoming_target_amount | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| incoming_target_people | unsigned smallint | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Orang |
| outgoing_target_amount | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| outgoing_target_people | unsigned smallint | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Orang |
| total_target_amount | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Dikirim eksplisit |
| total_target_people | unsigned smallint | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Orang |
| new_drop | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| continued_drop | bigint unsigned | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, DEC-011 | Rupiah |
| notes | text | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Catatan |
| sync_status | varchar(30) | Tidak | - | Ya | - | - | `SyncStatus` | `08_DATA_DICTIONARY.md` | Sinkronisasi |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |

### visit_reports

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| local_uuid | char(36) | Ya | null | - | Ya | - | string/null | `20_OFFLINE_SYNC_AND_TRACKING.md` | Idempotency |
| prospect_id | bigint unsigned | Tidak | - | FK | - | `prospects.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Wajib terkait prospek |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | - | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Marketing pembuat |
| visit_date | date | Tidak | - | Ya | - | - | date | `08_DATA_DICTIONARY.md` | Tanggal |
| visit_time | time | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Waktu |
| day_name | varchar(20) | Tidak | - | Ya | - | - | `DayName` | `08_DATA_DICTIONARY.md` | Hari |
| visit_purpose | varchar(255) | Tidak | - | - | - | - | string | `08_DATA_DICTIONARY.md` | Tujuan |
| visit_result | varchar(50) | Tidak | - | Ya | - | - | `VisitResult` | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` | Hasil |
| prospect_status | varchar(50) | Tidak | - | Ya | - | - | `ProspectStatus` | `08_DATA_DICTIONARY.md` | Status prospek setelah kunjungan |
| notes | text | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Catatan |
| follow_up_date | date | Ya | null | Ya | - | - | date/null | `08_DATA_DICTIONARY.md` | Follow-up opsional |
| photo_path | varchar(255) | Ya | null | - | - | - | string/null | `11_FILE_STORAGE.md`, `22_OPEN_QUESTIONS.md` | Satu foto; wajib masih open question |
| photo_caption | varchar(255) | Ya | null | - | - | - | string/null | `11_FILE_STORAGE.md` | Caption opsional |
| resort | varchar(100) | Tidak | - | Ya | - | - | string | `08_DATA_DICTIONARY.md` | Resort |
| latitude | decimal(10,7) | Ya | null | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md` | Koordinat |
| longitude | decimal(10,7) | Ya | null | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md` | Koordinat |
| location_address | varchar(255) | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Lokasi |
| sync_status | varchar(30) | Tidak | - | Ya | - | - | `SyncStatus` | `08_DATA_DICTIONARY.md` | Sinkronisasi |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |

### tracking_sessions

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| local_uuid | char(36) | Ya | null | - | Ya | - | string/null | `20_OFFLINE_SYNC_AND_TRACKING.md` | Idempotency |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | - | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Marketing |
| schedule_id | bigint unsigned | Ya | null | FK | - | `marketing_schedules.id` null on delete | integer/null | `07_DATABASE_SCHEMA.md`, ERD, `22_OPEN_QUESTIONS.md` | Tracking dapat tanpa jadwal belum final, schema nullable |
| session_date | date | Tidak | - | Ya | - | - | date | `07_DATABASE_SCHEMA.md` | Tanggal sesi |
| day_name | varchar(20) | Tidak | - | Ya | - | - | `DayName` | `07_DATABASE_SCHEMA.md` | Hari |
| started_at | timestamp | Tidak | - | Ya | - | - | datetime | `09_API_CONTRACT.md` | Mulai |
| ended_at | timestamp | Ya | null | - | - | - | datetime/null | `09_API_CONTRACT.md` | Selesai |
| status | varchar(30) | Tidak | - | Ya | - | - | `TrackingStatus` | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` | Status tracking |
| distance_meters | unsigned integer | Ya | null | - | - | - | integer/null | `08_DATA_DICTIONARY.md` | Jarak |
| visit_count | unsigned smallint | Tidak | - | - | - | - | integer | `08_DATA_DICTIONARY.md`, `09_API_CONTRACT.md` | Ringkasan stop |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |

### tracking_points

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| local_uuid | char(36) | Ya | null | - | Ya | - | string/null | `20_OFFLINE_SYNC_AND_TRACKING.md` | Idempotency |
| tracking_session_id | bigint unsigned | Tidak | - | FK | - | `tracking_sessions.id` cascade | integer | `07_DATABASE_SCHEMA.md`, ERD | Child murni session |
| latitude | decimal(10,7) | Tidak | - | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md` | Koordinat |
| longitude | decimal(10,7) | Tidak | - | - | - | - | decimal:7 | `07_DATABASE_SCHEMA.md` | Koordinat |
| accuracy_meters | decimal(8,2) | Ya | null | - | - | - | decimal:2 | `08_DATA_DICTIONARY.md` | Akurasi |
| speed_mps | decimal(8,2) | Ya | null | - | - | - | decimal:2 | `08_DATA_DICTIONARY.md` | Kecepatan |
| heading | decimal(8,2) | Ya | null | - | - | - | decimal:2 | `08_DATA_DICTIONARY.md` | Arah |
| altitude_meters | decimal(10,2) | Ya | null | - | - | - | decimal:2 | `08_DATA_DICTIONARY.md` | Ketinggian |
| address | varchar(255) | Ya | null | - | - | - | string/null | `08_DATA_DICTIONARY.md` | Alamat |
| point_type | varchar(30) | Tidak | - | Ya | - | - | `TrackingPointType` | `08_DATA_DICTIONARY.md` | Mulai/Perjalanan/Kunjungan/Selesai |
| recorded_at | timestamp | Tidak | - | Ya | - | - | datetime | `08_DATA_DICTIONARY.md`, `20_OFFLINE_SYNC_AND_TRACKING.md` | Waktu perangkat |
| received_at | timestamp | Tidak | - | - | - | - | datetime | `10_BUSINESS_RULES.md` | Waktu server menerima |
| created_at | timestamp | Tidak | Current timestamp | - | - | - | datetime | Laravel | Timestamp dibuat database, tanpa `updated_at` |

### operational_recaps

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| report_number | varchar(50) | Tidak | - | - | Ya | - | string | `07_DATABASE_SCHEMA.md` | Nomor laporan |
| recap_date | date | Tidak | - | Ya | Ya | - | date | `07_DATABASE_SCHEMA.md` | Satu header per tanggal |
| day_name | varchar(20) | Tidak | - | Ya | - | - | `DayName` | `07_DATABASE_SCHEMA.md` | Hari |
| status | varchar(30) | Tidak | - | Ya | - | - | string | `07_DATABASE_SCHEMA.md` | Nilai status belum punya enum terdokumentasi |
| created_by | bigint unsigned | Tidak | - | FK | - | `users.id` restrict | integer | `07_DATABASE_SCHEMA.md` | Admin pembuat |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |

### operational_recap_rows

| Column | Database Type | Nullable | Default | Index | Unique | Foreign Key | Enum/Cast | Source | Notes |
|---|---|---:|---|---|---|---|---|---|---|
| id | bigint unsigned | Tidak | auto | PK | - | - | integer | `07_DATABASE_SCHEMA.md` | Primary key |
| operational_recap_id | bigint unsigned | Tidak | - | FK | composite | `operational_recaps.id` cascade | integer | `07_DATABASE_SCHEMA.md`, ERD | Row child header |
| marketing_profile_id | bigint unsigned | Tidak | - | FK | composite | `marketing_profiles.id` restrict | integer | `07_DATABASE_SCHEMA.md`, ERD | Satu row per marketing |
| mg | varchar(20) | Tidak | - | - | - | - | string | `10_BUSINESS_RULES.md`, `22_OPEN_QUESTIONS.md` | Definisi pending, disimpan eksplisit |
| members_l | unsigned integer | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Nilai eksplisit |
| members_m | unsigned integer | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Nilai eksplisit |
| members_k | unsigned integer | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Nilai eksplisit |
| members_s | unsigned integer | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md` | Nilai eksplisit |
| target_previous | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| target_incoming | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| target_outgoing | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| target_s | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| drop_previous | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| drop_current | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| drop_total | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| storting_previous | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| storting_current | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| storting_total | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| percentage | decimal(7,2) | Ya | null | - | - | - | decimal:2 | `07_DATABASE_SCHEMA.md`, `22_OPEN_QUESTIONS.md` | Rumus pending; manual/null |
| previous_circulation | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| current_circulation | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| followed_by | varchar(150) | Tidak | - | - | - | - | string | `07_DATABASE_SCHEMA.md`, `22_OPEN_QUESTIONS.md` | Definisi pending, disimpan eksplisit |
| morning_cash | bigint unsigned | Tidak | - | - | - | - | integer | `07_DATABASE_SCHEMA.md`, DEC-011 | Rupiah |
| created_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |
| updated_at | timestamp | Tidak | Laravel | - | - | - | datetime | Laravel | Timestamp |

### Laravel Infrastructure

| Table | Source | Implementation |
|---|---|---|
| password_reset_tokens | Laravel infrastructure | Dipertahankan pada migration users |
| sessions | Laravel infrastructure | Dipertahankan, walaupun runtime test memakai session array |
| cache, cache_locks | Laravel infrastructure | Dipertahankan |
| jobs, job_batches, failed_jobs | Laravel infrastructure | Dipertahankan |
| personal_access_tokens | Sanctum | Dipertahankan sebagai tabel standar Sanctum |

## 4. Relationship Matrix

| Parent | Relationship | Child | Foreign Key | Delete Policy | Source |
|---|---|---|---|---|---|
| User | hasOne | MarketingProfile | `marketing_profiles.user_id` | restrict; user memakai soft delete | `07_DATABASE_SCHEMA.md`, ERD |
| MarketingProfile | hasMany | MarketingWorkDay | `marketing_work_days.marketing_profile_id` | restrict | `07_DATABASE_SCHEMA.md`, ERD |
| MarketingProfile | hasMany | Prospect | `prospects.marketing_profile_id` | restrict; prospect soft delete | `07_DATABASE_SCHEMA.md`, ERD |
| Prospect | hasMany | VisitReport | `visit_reports.prospect_id` | restrict | `07_DATABASE_SCHEMA.md`, `10_BUSINESS_RULES.md` |
| Prospect | hasOne | Member | `members.source_prospect_id` | null on delete | `07_DATABASE_SCHEMA.md`, ERD, DEC-014 |
| MarketingProfile | hasMany | Member | `members.marketing_profile_id` | restrict; member soft delete | `07_DATABASE_SCHEMA.md`, ERD |
| MarketingProfile | hasMany | MarketingSchedule | `marketing_schedules.marketing_profile_id` | restrict; schedule soft delete | `07_DATABASE_SCHEMA.md`, ERD |
| Prospect | hasMany | MarketingSchedule | `marketing_schedules.prospect_id` | null on delete | `07_DATABASE_SCHEMA.md` |
| User | hasMany | MarketingSchedule | `marketing_schedules.created_by` | restrict | `07_DATABASE_SCHEMA.md` |
| MarketingProfile | hasMany | DailyOperationalReport | `daily_operational_reports.marketing_profile_id` | restrict | `07_DATABASE_SCHEMA.md`, ERD |
| MarketingProfile | hasMany | VisitReport | `visit_reports.marketing_profile_id` | restrict | `07_DATABASE_SCHEMA.md`, ERD |
| MarketingProfile | hasMany | TrackingSession | `tracking_sessions.marketing_profile_id` | restrict | `07_DATABASE_SCHEMA.md`, ERD |
| MarketingSchedule | hasMany | TrackingSession | `tracking_sessions.schedule_id` | null on delete | `07_DATABASE_SCHEMA.md`, ERD |
| TrackingSession | hasMany | TrackingPoint | `tracking_points.tracking_session_id` | cascade | `07_DATABASE_SCHEMA.md`, ERD |
| OperationalRecap | hasMany | OperationalRecapRow | `operational_recap_rows.operational_recap_id` | cascade | `07_DATABASE_SCHEMA.md`, ERD |
| MarketingProfile | hasMany | OperationalRecapRow | `operational_recap_rows.marketing_profile_id` | restrict | `07_DATABASE_SCHEMA.md`, ERD |
| User | hasMany | OperationalRecap | `operational_recaps.created_by` | restrict | `07_DATABASE_SCHEMA.md` |
| User | hasMany | Member approvals | `members.approved_by`, `members.rejected_by` | null on delete | `06_AUTH_AND_ROLES.md` |

## 5. Unique Constraint Matrix

| Table | Column(s) | Constraint | Reason | Source |
|---|---|---|---|---|
| users | username | unique | Login unik | `07_DATABASE_SCHEMA.md`, `10_BUSINESS_RULES.md` |
| users | email | unique nullable | Email admin/marketing unik bila diisi | `07_DATABASE_SCHEMA.md` |
| marketing_profiles | user_id | unique | User 1-1 MarketingProfile | `07_DATABASE_SCHEMA.md`, ERD |
| marketing_profiles | code | unique | Kode marketing unik | `10_BUSINESS_RULES.md` |
| marketing_work_days | marketing_profile_id, day_name | unique | Satu hari kerja per marketing | `07_DATABASE_SCHEMA.md` |
| prospects | local_uuid | unique nullable | Idempotency offline per tabel | `20_OFFLINE_SYNC_AND_TRACKING.md` |
| members | local_uuid | unique nullable | Idempotency offline per tabel | `20_OFFLINE_SYNC_AND_TRACKING.md` |
| members | source_prospect_id | unique nullable | Satu prospek maksimal menjadi asal satu anggota | DEC-014 |
| members | member_number | unique | Nomor anggota unik | `10_BUSINESS_RULES.md` |
| members | loan_number | unique | Schema locked menyatakan unique | `07_DATABASE_SCHEMA.md` |
| marketing_schedules | - | none | Tidak ada unique terkonfirmasi | `22_OPEN_QUESTIONS.md` |
| daily_operational_reports | local_uuid | unique nullable | Idempotency offline per tabel | `20_OFFLINE_SYNC_AND_TRACKING.md` |
| daily_operational_reports | marketing_profile_id, report_date | not created | Aturan satu laporan per tanggal masih pending | `22_OPEN_QUESTIONS.md` |
| visit_reports | local_uuid | unique nullable | Idempotency offline per tabel | `20_OFFLINE_SYNC_AND_TRACKING.md` |
| tracking_sessions | local_uuid | unique nullable | Idempotency offline per tabel | `20_OFFLINE_SYNC_AND_TRACKING.md` |
| tracking_points | local_uuid | unique nullable | Idempotency offline per tabel | `20_OFFLINE_SYNC_AND_TRACKING.md` |
| operational_recaps | report_number | unique | Nomor laporan unik | `07_DATABASE_SCHEMA.md` |
| operational_recaps | recap_date | unique | Satu header rekap per tanggal | `07_DATABASE_SCHEMA.md` |
| operational_recap_rows | operational_recap_id, marketing_profile_id | unique | Satu row mewakili satu marketing | `07_DATABASE_SCHEMA.md`, `10_BUSINESS_RULES.md` |

## 6. Index Matrix

| Table | Column(s) | Index Name | Query Purpose |
|---|---|---|---|
| users | role | `idx_users_role` | Filter role |
| users | is_active | `idx_users_active` | Filter akun aktif |
| marketing_profiles | phone | `idx_marketing_profiles_phone` | Search nomor HP |
| marketing_profiles | area | `idx_marketing_profiles_area` | Filter area |
| marketing_work_days | day_name | `idx_work_days_day` | Filter hari |
| prospects | marketing_profile_id, input_date | `idx_prospects_marketing_date` | Filter owner/tanggal |
| prospects | status, resort | `idx_prospects_status_resort` | Filter admin |
| prospects | name | `idx_prospects_name` | Search |
| prospects | phone | `idx_prospects_phone` | Search |
| members | marketing_profile_id, input_date | `idx_members_marketing_date` | Filter owner/tanggal |
| members | approval_status, resort | `idx_members_approval_resort` | Filter admin |
| members | name | `idx_members_name` | Search |
| members | phone | `idx_members_phone` | Search |
| members | source_prospect_id | `uq_members_source_prospect` | Relasi prospek dan unique nullable |
| marketing_schedules | marketing_profile_id, schedule_date | `idx_schedules_marketing_date` | Jadwal marketing/tanggal |
| marketing_schedules | status, schedule_date | `idx_schedules_status_date` | Filter status/tanggal |
| marketing_schedules | day_name | `idx_schedules_day` | Filter hari |
| marketing_schedules | area | `idx_schedules_area` | Filter area |
| marketing_schedules | resort | `idx_schedules_resort` | Filter resort |
| daily_operational_reports | marketing_profile_id, report_date | `idx_reports_marketing_date` | Filter owner/tanggal |
| daily_operational_reports | report_date | `idx_reports_date` | Filter tanggal |
| daily_operational_reports | day_name | `idx_reports_day` | Filter hari |
| daily_operational_reports | resort | `idx_reports_resort` | Filter resort |
| daily_operational_reports | sync_status | `idx_reports_sync_status` | Menunggu sinkronisasi |
| visit_reports | prospect_id | `idx_visit_reports_prospect` | Riwayat prospek |
| visit_reports | marketing_profile_id, visit_date | `idx_visit_reports_marketing_date` | Filter owner/tanggal |
| visit_reports | visit_result, prospect_status | `idx_visit_reports_result_status` | Filter hasil/status |
| visit_reports | follow_up_date | `idx_visit_reports_follow_up` | Filter follow-up |
| visit_reports | resort | `idx_visit_reports_resort` | Filter resort |
| tracking_sessions | marketing_profile_id, session_date | `idx_tracking_sessions_marketing_date` | Riwayat marketing |
| tracking_sessions | schedule_id | `idx_tracking_sessions_schedule` | Relasi jadwal |
| tracking_sessions | status | `idx_tracking_sessions_status` | Status web |
| tracking_sessions | started_at | `idx_tracking_sessions_started` | Urut waktu |
| tracking_points | tracking_session_id, recorded_at | `idx_tracking_points_session_time` | Urutan titik |
| tracking_points | point_type | `idx_tracking_points_type` | Filter tipe titik |
| operational_recaps | day_name | `idx_operational_recaps_day` | Filter hari |
| operational_recaps | status | `idx_operational_recaps_status` | Filter status |
| operational_recap_rows | marketing_profile_id | `idx_recap_rows_marketing` | Relasi marketing |

## 7. Enum Matrix

| Enum Class | Database Column | Backing Values | Display Labels | Source |
|---|---|---|---|---|
| `UserRole` | `users.role` | `admin`, `marketing` | Administrator, Marketing | `06_AUTH_AND_ROLES.md`, `09_API_CONTRACT.md` |
| `DayName` | `marketing_work_days.day_name`, `marketing_schedules.day_name`, `daily_operational_reports.day_name`, `visit_reports.day_name`, `tracking_sessions.day_name`, `operational_recaps.day_name` | `Senin`, `Selasa`, `Rabu`, `Kamis`, `Jumat`, `Sabtu` | Sama dengan backing value | `08_DATA_DICTIONARY.md`, `21_SEED_DATA.md` |
| `MemberApprovalStatus` | `members.approval_status` | `Menunggu`, `Disetujui`, `Ditolak` | Menunggu, Disetujui, Ditolak | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` |
| `ProspectStatus` | `prospects.status`, `visit_reports.prospect_status` | `Baru`, `Tertarik`, `Perlu Follow Up`, `Tidak Tertarik`, `Selesai` | Sama dengan backing value | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` |
| `VisitResult` | `visit_reports.visit_result` | `Berhasil Bertemu`, `Tidak Bertemu`, `Transaksi Selesai` | Sama dengan backing value | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` |
| `ScheduleStatus` | `marketing_schedules.status` | `Belum Dikunjungi`, `Berlangsung`, `Selesai`, `Dibatalkan` | Sama dengan backing value | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md`, `09_API_CONTRACT.md` |
| `TrackingStatus` | `tracking_sessions.status` | `Aktif`, `Offline`, `Belum Mulai`, `GPS Tidak Aktif`, `Tidak Dijadwalkan` | Sama dengan backing value | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` |
| `TrackingPointType` | `tracking_points.point_type` | `Mulai`, `Perjalanan`, `Kunjungan`, `Selesai` | Sama dengan backing value | `08_DATA_DICTIONARY.md`, `20_OFFLINE_SYNC_AND_TRACKING.md` |
| `SyncStatus` | `prospects.sync_status`, `members.sync_status`, `daily_operational_reports.sync_status`, `visit_reports.sync_status` | `Tersinkronisasi`, `Menunggu Sinkronisasi`, `Gagal` | Sama dengan backing value | `08_DATA_DICTIONARY.md`, `10_BUSINESS_RULES.md` |

## 8. Open Questions

- Definisi dan rumus rekap: MG, L/M/K/S, Target S, persentase, Drop, Storting, Sirkulasi, Diikuti Oleh, dan Kas Pagi belum dikonfirmasi. Schema menyimpan nilai eksplisit tanpa formula.
- Satu marketing satu laporan operasional per tanggal belum dikonfirmasi. Composite unique tidak dibuat.
- Foto anggota dan foto laporan kunjungan wajib atau opsional masih open question. Schema mengikuti `07_DATABASE_SCHEMA.md` dengan path nullable.
- Definisi batas offline tracking dan interval titik belum dikonfirmasi. Schema hanya menyimpan session/points.
- Tracking wajib terkait jadwal atau boleh tanpa jadwal masih open question. Schema mengikuti `schedule_id` nullable.
- Nilai status `operational_recaps.status` belum memiliki daftar enum terkunci. Kolom dibuat string tanpa PHP enum.

## 9. Implementation Decision

- Karena project greenfield, `database/database.sqlite` tidak ada, dan tidak ada bukti migration telah dipakai pada database penting, migration `create_users_table` diperbarui langsung untuk schema users final.
- Relasi fisik Prospect-Member mengikuti DEC-014: hanya `members.source_prospect_id`; `prospects.linked_member_id` tidak dibuat dan hanya boleh menjadi field response turunan.
- `tracking_points` memakai cascade delete terhadap `tracking_sessions` karena point adalah child murni session.
- `operational_recap_rows` memakai cascade delete terhadap `operational_recaps` karena row tidak bermakna tanpa header rekap.
- Data historis marketing, prospek, anggota, jadwal, laporan, dan rekap tidak memakai cascade dari marketing/user.
- Enum backing value status domain mengikuti teks status pada Data Dictionary dan API Contract; khusus role memakai `admin` dan `marketing` sesuai contract login.
- Cast `DayName` diterapkan pada semua model yang memiliki kolom `day_name`.
- Factory jadwal dan tracking menjaga konsistensi `marketing_profile_id` terhadap prospek atau jadwal terkait.
- Factory rekap dapat membuat lebih dari satu record tanpa bentrok `recap_date` atau `report_number`.
- Development seeder menghasilkan 39 anggota, 13 laporan operasional Senin, 6 header rekap dengan 13 row per hari, dan 1 tracking session M01 berisi 9 titik.
- Agregat dashboard Senin, 20 Juli 2026, tervalidasi dari database: target 123.500.000, drop 91.000.000, storting 37.700.000.
- Tracking event time memakai `dateTime` pada migration MySQL untuk kompatibilitas MariaDB 10.4 terhadap beberapa kolom waktu eksplisit.
- SQL export development tersedia di `apps/web-admin-api/database/sql/` dan telah direstore pada database sementara lokal.
