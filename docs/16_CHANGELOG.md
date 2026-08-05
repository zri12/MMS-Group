---
title: "Specification Changelog"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.8.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# 1.9.0 - 2026-07-29

## Added

- Technical hardening API JSON dan Blade Design System.
- Manual auth check web admin dan API marketing.
- Modul Data Marketing web admin.
- Dashboard Admin berbasis data database.
- REST API Jadwal Marketing.
- Modul Prospek admin dan API marketing.
- Modul Anggota, create API marketing, dan approval admin.
- Modul Laporan Operasional dan Laporan Kunjungan admin/API.
- Modul Tracking API, batch point, current session, stop session, dan peta admin Leaflet/OpenStreetMap.
- Dokumentasi modul `30` sampai `38`.
- Focused feature tests untuk seluruh modul bisnis.

## Changed

- Navigasi admin menampilkan Marketing, Prospek, Anggota, Laporan, dan Tracking.
- API protected route memakai envelope JSON konsisten tanpa bergantung pada header `Accept`.
- Tracking session stop memakai status `Offline` karena enum belum memiliki status selesai.
- Source frontend menambahkan dependency `leaflet`.

## Security

- Marketing API selalu scoped ke `marketing_profile_id` dari token.
- `local_uuid` dipakai untuk idempotency create dan batch tracking point.
- Upload foto anggota/kunjungan divalidasi MIME dan ukuran.
- Map popup dibangun dari text content, bukan HTML mentah.

## Not Changed

- Tidak ada migration baru.
- Tidak ada perubahan SQL dump.
- Tidak ada Flutter, background service, deployment, atau data production.

# 1.8.0 - 2026-07-29

## Added

- Blade design tokens `--mms-*`.
- Reusable Blade UI components.
- Responsive Admin shell.
- Desktop sidebar.
- Mobile navigation.
- Topbar.
- Form components.
- Feedback components.
- Data display components.
- Modal and confirmation dialog.
- Component catalog for local/testing.
- Design system tests.
- Design system documentation.

## Changed

- Web username normalization.
- Marketing API username normalization.
- Device name normalization.
- Inactive API current token revocation.
- Admin password form loading state.
- Password toggle accessibility.
- Existing Auth pages migrated to Design System components.
- API route comments updated.

## Security

- Inactive current Sanctum token revoked.
- Password remains unmodified during normalization.
- Component catalog blocked from production.
- UI components escape user content by default.

## Not Changed

- Tidak ada migration baru, SQL dump baru, seeder baru, CRUD bisnis, dashboard final, Flutter, REST API bisnis, atau deployment.

# 1.7.0 - 2026-07-29

## Added

- Marketing API login.
- Marketing API profile.
- Marketing API logout.
- Sanctum token ability `marketing-mobile`.
- Middleware `marketing`.
- API authentication resource.
- API authentication tests.
- Marketing API authentication documentation.

## Changed

- Web Admin rate limit configurable.
- Login and logout flash messages.
- Login loading state.
- Password visibility controls.
- `EnsureUserIsActive` supports JSON.
- Admin profile shows status and last login.
- Admin home shows status and last login.
- 403 page uses generic message and safe logout.
- Admin Form Request authorization hardened.
- Packaging uses cross-platform ZIP paths.

## Security

- Generic Marketing API login failures.
- Role and active account checks.
- MarketingProfile requirement.
- Current-token-only logout.
- Token ability enforcement.
- Tokens excluded from logs and documentation examples.

## Not Changed

- Tidak ada migration baru, SQL dump baru, seeder baru, CRUD bisnis, dashboard final, Flutter, atau deployment.

# 1.6.0 - 2026-07-29

## Added

- Manual web admin session authentication.
- Route `GET /login`, `POST /login`, dan `POST /logout`.
- Route admin protected `GET /admin`, `GET /admin/profile`, `PUT /admin/profile`, dan `PUT /admin/password`.
- Middleware `active` untuk menolak session user inactive.
- Halaman login admin, beranda admin sementara, profil admin, dan ubah password admin.
- Feature test auth, logout, access control, profile, password, dan rate limit.
- Dokumentasi `docs/27_WEB_ADMIN_AUTHENTICATION.md`.

## Changed

- Route admin kini memakai middleware `auth`, `active`, dan `admin`.
- Login web admin dikunci ke `username`, role `admin`, dan `is_active = true`.
- Route map profil disesuaikan dengan kontrak fase auth terbaru.

## Skipped

- Manual browser responsive check belum dilakukan pada fase otomatis ini.

## Not Changed

- Tidak ada perubahan migration, seeder, factory, SQL dump, REST API auth, Sanctum token flow, atau CRUD bisnis.


# 1.5.0 - 2026-07-29

## Added

- MySQL local database validation pada `mms_monitoring_dev`.
- MySQL schema SQL export.
- Development data SQL export.
- Full development SQL export.
- SQL import documentation pada `apps/web-admin-api/database/sql/README.md`.
- SQL restore validation pada database sementara.
- MySQL validation report pada `docs/26_MYSQL_VALIDATION_AND_SQL_EXPORT.md`.

## Changed

- Local `.env` database connection dari SQLite ke MySQL lokal.
- `.env.example` memakai contoh database `mms_monitoring_dev`.
- Operational recap circulation values memakai fixture eksplisit bernilai 0, bukan formula.
- `TrackingSessionFactory` menghapus state `completed()` yang menyesatkan.
- `OperationalRecapFactory` membuat `day_name` mengikuti `recap_date` dan tidak menghasilkan Minggu.
- Tracking event time migration memakai `dateTime` agar kompatibel dengan MariaDB 10.4.
- Development database tests diperluas untuk sirkulasi rekap, state offline tracking, dan kesesuaian hari rekap.
- DatabaseSeeder documentation diperbarui.

## Removed

- Runtime circulation formula assumptions pada development seeder.
- SQL dump lama digantikan oleh export final tahap ini.

# 1.4.0 - 2026-07-29

## Added

- Folder `REFERENSI UI APLIKASI MARKETING/` sebagai source referensi aplikasi marketing hasil ekstraksi whitelist dari ZIP eksternal.
- Test agregat dashboard untuk 39 anggota, total target 123.500.000, total drop 91.000.000, dan total storting 37.700.000.
- Test konsistensi factory lintas model dan kemampuan factory rekap membuat lebih dari satu record.
- Validasi model cast `DayName` untuk laporan operasional, laporan kunjungan, tracking session, dan operational recap.
- Script packaging timestamped dengan dry run, staging, validasi forbidden entries, dan validasi required entries.

## Changed

- Development seeder anggota menjadi 39 anggota deterministik, 3 anggota per marketing, dengan status Menunggu, Disetujui, dan Ditolak.
- Laporan operasional Senin dibuat eksplisit untuk 13 marketing agar aggregate dashboard berasal dari database.
- Rekap operasional memakai fixture eksplisit Senin-Sabtu dan `percentage` null sampai rumus dikonfirmasi customer.
- Tracking M01 memakai 9 titik dengan 3 titik Kunjungan.
- `minimum-stability` Composer dikunci ke `stable`.
- Dokumentasi referensi UI diperbarui untuk membedakan referensi web admin, referensi aplikasi marketing, dan placeholder Flutter.

## Removed

- Artefak workspace referensi web admin: `.codex-artifacts`, `.vercel`, `dist`, `.codex-vite-error.log`, dan `.codex-vite.log`.
- Formula seed rekap berbasis angka dasar/generate otomatis.

## Skipped

- Validasi MySQL lokal tetap belum dijalankan karena environment lokal aktif memakai SQLite dan belum ada koneksi MySQL kosong yang terbukti aman.

# 1.3.0 - 2026-07-29

## Added

- Enum `DayName` dan `TrackingPointType`.
- Factory domain untuk user, marketing, jadwal, prospek, anggota, laporan, tracking, dan rekap.
- Development seeder deterministik untuk dataset awal MMS.
- Test factory, seeder, enum, cast, relasi, unique constraint, cascade, dan timestamp tracking.
- Laporan cleanup referensi UI di `docs/25_REFERENCE_CLEANUP_REPORT.md`.
- Script packaging source bersih.

## Changed

- Relasi canonical Prospect-Member menggunakan `members.source_prospect_id`.
- `members.source_prospect_id` nullable, unique, dan `nullOnDelete`.
- `tracking_points.created_at` non-null dengan default current timestamp.
- Seeder development membaca kredensial dari `config('mms.seed.*')`.

## Removed

- Kolom fisik `prospects.linked_member_id`.
- `node_modules` dan nested `.git` dari folder referensi UI.

## Skipped

- Validasi MySQL lokal belum dijalankan karena environment lokal aktif masih memakai SQLite, bukan koneksi MySQL kosong yang aman.


# 1.2.0 - 2026-07-29

## Added

- Database implementation matrix.
- Migration domain MMS.
- PHP Enum domain.
- Model Eloquent domain.
- Relationship tests.
- Schema tests.

## Changed

- Middleware admin menggunakan `UserRole`.
- Testing memakai SQLite in-memory.
- Filesystem testing ditetapkan `public`.

# 1.1.1 - 2026-07-28

## Changed

- Fondasi Blade/Livewire difinalisasi agar tidak membutuhkan database untuk session, cache, dan queue.
- Default runtime fondasi menjadi `SESSION_DRIVER=file`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`, dan `FILESYSTEM_DISK=public`.
- Struktur layout ditambahkan: `base`, `app`, `admin`, dan `auth`.
- Struktur folder Livewire, Blade, dan JavaScript minimal dilengkapi sebagai placeholder per modul.
- Dokumentasi source bersih diperbarui agar `.env`, dependency lokal, build output, database SQLite, dan cache runtime tidak masuk paket source.
# 1.1.0 — 2026-07-28

## Changed

- Web Admin dari arsitektur Inertia + React + TypeScript menjadi Laravel Blade + Livewire (DEC-013).
- React Leaflet digantikan oleh Leaflet.js untuk peta web admin.
- Struktur frontend diperbarui: `resources/views/` menggantikan `resources/js/Pages/`.
- Livewire component menggantikan React component untuk interaksi dinamis.
- Alpine.js digunakan melalui Livewire, tidak dipasang terpisah.
- Dokumen arsitektur diperbarui: 01_TECH_STACK, 03_UI_UX_GUIDELINES, 04_DESIGN_TOKENS, 05_PAGE_AND_ROUTE_MAP, 12_MIGRATION_PLAN, 13_TESTING_ACCEPTANCE, 15_DECISION_LOG, 17_CODING_STANDARDS, 18_ENVIRONMENT_AND_CONFIG.

## Removed

- React / React DOM
- TypeScript / TSX
- Inertia.js
- React Router
- React Leaflet
- React Context
- Folder `resources/js/Pages/`, `Layouts/`, `Components/`, `Types/`, `Hooks/`, `Utils/`

---

# 1.0.0 — 2026-07-28

## Added

- Dokumentasi migrasi Laravel lengkap
- Data Prospek
- Laporan Kunjungan
- Laporan Operasional sesuai Input Setoran
- Tabel Rekap utuh
- Tracking dan riwayat
- Offline sync contract
- REST API v1 contract
- Database schema

## Changed

- Backend dan web admin disatukan dalam Laravel
- Web admin memakai Inertia React pada keputusan awal, kemudian digantikan oleh DEC-013.
- Aplikasi marketing memakai Flutter
- Database utama MySQL
- Status anggota: Menunggu, Disetujui, Ditolak
- Status jadwal: Belum Dikunjungi, Berlangsung, Selesai, Dibatalkan
- Status sinkronisasi distandarkan

## Removed

- Bukti Kunjungan versi lama
- Bukti Transaksi
- Bukti Transfer
- Foto Pencairan
- Laporan Tunai terpisah
- Field Lain-lain pada Laporan Operasional
- Foto anggota/bukti transfer/lokasi pada Laporan Operasional

## Pending confirmation

- Arti MG
- Arti L/M/K/S
- Rumus persentase dan sirkulasi
- Diikuti Oleh
- Kas Pagi
- Frekuensi tracking
- Batas offline
- Satu laporan operasional per marketing per hari
