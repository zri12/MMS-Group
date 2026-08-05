---
title: "Documentation Index"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.5.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# MMS Marketing Monitoring System

Dokumentasi ini menjadi acuan utama pembangunan Web Admin Monitoring Marketing KSP Manunggal Makmur Sejahtera dengan Laravel 12, Blade, Livewire, dan Tailwind CSS, sekaligus menjadi kontrak integrasi untuk aplikasi marketing berbasis Flutter.

## Referensi UI yang Telah Disetujui

- Web Admin: https://ui-mms-group-web-admin.vercel.app/
- Aplikasi Marketing: https://mms-group-ui.vercel.app/

UI referensi dipakai untuk menjaga tampilan, susunan halaman, istilah, field form, dan alur navigasi. Source UI lama bukan sumber kebenaran untuk aturan bisnis apabila bertentangan dengan dokumentasi ini.

Source UI lama dibuat dengan React/TSX dan hanya menjadi referensi visual. Project Laravel aktif tidak menggunakan React, Inertia.js, TypeScript, TSX, React Router, React Context, atau React Leaflet.

## Struktur Project

```text
mms-monitoring/
|-- apps/
|   |-- web-admin-api/        # Laravel 12: web admin, backend, REST API
|   `-- marketing-mobile/     # Placeholder Flutter
|-- docs/                     # Sumber kebenaran project
|-- REFERENSI UI WEB ADMIN MONITORING MARKETING/
|-- REFERENSI UI APLIKASI MARKETING/
|-- AGENTS.md
|-- MANIFEST.md
|-- PROJECT_STRUCTURE.md
`-- README.md
```

`REFERENSI UI APLIKASI MARKETING/` adalah referensi visual React/TSX hasil ekstraksi source. Folder ini berbeda dari `apps/marketing-mobile/`, yang tetap menjadi placeholder project Flutter.

## Stack Final Project Aktif

- Web Admin: Laravel Blade, Livewire, Tailwind CSS, Alpine.js bawaan Livewire, Vite, JavaScript biasa.
- Peta Web Admin: Leaflet.js + OpenStreetMap.
- Backend dan REST API: Laravel 12, PHP 8.2+, Eloquent, Form Request, API Resource, Service/Action.
- Autentikasi: Laravel session untuk Web Admin, Laravel Sanctum untuk Flutter.
- Database utama: MySQL/MariaDB.
- Aplikasi marketing: Flutter, SQLite offline, REST API `/api/v1`.

## Development Laravel

```bash
cd apps/web-admin-api
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan optimize:clear
npm run build
php artisan test
php artisan serve
```

Migration domain MMS, enum, model, factory, dan development seeder sudah tersedia. Jalankan migration/seeder hanya pada database MySQL lokal kosong yang aman dan bukan production.

Sebelum menjalankan development seeder, isi `MMS_SEED_DEFAULT_PASSWORD` pada `.env` lokal. Jangan commit atau dokumentasikan nilai password tersebut.

Dataset development saat ini mencakup:

- 13 marketing M01-M13.
- 39 anggota, 3 anggota per marketing.
- 13 laporan operasional Senin, 20 Juli 2026.
- 6 rekap operasional Senin-Sabtu, masing-masing 13 row.
- Tracking M01 dengan 9 titik dan 3 kunjungan.
- Aggregate dashboard dari database: target 123.500.000, drop 91.000.000, storting 37.700.000.

Folder `REFERENSI UI WEB ADMIN MONITORING MARKETING/` dan `REFERENSI UI APLIKASI MARKETING/` bersifat read-only. Jangan install dependency, build, refactor, atau memindahkan source TSX dari folder tersebut ke Laravel aktif.

## Status Database dan Runtime

Runtime lokal Laravel memakai MySQL/MariaDB untuk data aplikasi:

- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=mms_monitoring_dev`

Database harus dibuat kosong sebelum migration. Jangan gunakan database production, cPanel, customer, atau database yang memiliki data penting.

Fondasi Laravel tidak memakai database untuk session, cache, atau queue:

- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`
- `FILESYSTEM_DISK=public`

Test otomatis memakai SQLite in-memory melalui `phpunit.xml`, sehingga test tidak bergantung pada `.env` lokal. `database/database.sqlite` tidak digunakan pada fondasi ini.

Relasi fisik Prospect-Member bersifat satu arah melalui `members.source_prospect_id`. Field `linked_member_id` hanya boleh menjadi response turunan bila dibutuhkan API, bukan kolom tabel `prospects`.

## Web Admin Authentication

Autentikasi web admin memakai Laravel session guard `web`.

Route aktif:

- `GET /login`
- `POST /login`
- `POST /logout`
- `GET /admin`
- `GET /admin/profile`
- `PUT /admin/profile`
- `PUT /admin/password`

Login web admin memakai `username` dan hanya menerima user role `admin` dengan `is_active = true`. Marketing tidak dapat login ke web admin. Detail lengkap ada di `docs/27_WEB_ADMIN_AUTHENTICATION.md`.

Username web admin dinormalisasi dengan trim dan lowercase sebelum validasi, autentikasi, dan throttle key. Password tidak di-trim atau diubah.

## Marketing API Authentication

Autentikasi aplikasi marketing memakai Laravel Sanctum token pada `/api/v1`.

Route aktif:

- `POST /api/v1/auth/login`
- `GET /api/v1/auth/profile`
- `POST /api/v1/auth/logout`

Login API marketing memakai `username`, `password`, dan `device_name`. Token hanya dibuat untuk user role `marketing` aktif yang memiliki `MarketingProfile`, dengan ability `marketing-mobile`. Detail lengkap ada di `docs/28_MARKETING_API_AUTHENTICATION.md`.

Username marketing dinormalisasi dengan trim dan lowercase. `device_name` di-trim dan whitespace berlebih disederhanakan sebelum menjadi token name. Password tidak di-trim. Bila akun inactive memakai token aktif, hanya current token yang dicabut.

## Blade Design System

Web Admin memakai Blade Design System reusable pada `resources/views/components/` dengan token CSS `--mms-*`, layout admin responsif, sidebar desktop, bottom navigation mobile, topbar, komponen form, feedback, data display, modal, dropdown, tabs, dan katalog local/testing.

Route katalog:

- `GET /admin/design-system`
- hanya environment `local` dan `testing`
- middleware `auth`, `active`, `admin`

Detail lengkap ada di `docs/29_BLADE_DESIGN_SYSTEM.md`.

## Source ZIP Bersih

Jangan masukkan file/folder berikut ke source ZIP:

- `.env`
- `vendor/`
- `node_modules/`
- `public/build/`
- `database/database.sqlite`
- `storage/logs/`
- `storage/framework/views/`
- `bootstrap/cache/*.php`
- `.phpunit.result.cache`
- file ZIP di dalam project
- artefak tool/cache
- nested repository Git yang tidak diperlukan

Dependency dan asset dapat dibuat ulang dengan `composer install`, `npm install` atau `npm ci`, dan `npm run build`.

Script `scripts/create-source-zip.ps1` tersedia untuk membuat ZIP source bersih. Gunakan opsi `-DryRun` untuk memeriksa daftar file sebelum membuat paket.

```powershell
.\scripts\create-source-zip.ps1 -DryRun
.\scripts\create-source-zip.ps1
```

SQL dump development tersedia di `apps/web-admin-api/database/sql/`:

- `mms_monitoring_schema.sql`
- `mms_monitoring_development_data.sql`
- `mms_monitoring_development_full.sql`
- `README.md`

SQL dump hanya untuk development. Cara utama tetap `php artisan migrate` dan `php artisan db:seed`.

## Cara Menggunakan Dokumentasi

Sebelum membuat atau mengubah kode:

1. Baca `AGENTS.md`.
2. Baca `docs/00_MASTER_SPEC.md`.
3. Baca dokumen modul yang sedang dikerjakan.
4. Periksa `docs/08_DATA_DICTIONARY.md`.
5. Periksa `docs/10_BUSINESS_RULES.md`.
6. Untuk endpoint Flutter, periksa `docs/09_API_CONTRACT.md`.
7. Untuk halaman web, periksa `docs/pages/`.
8. Bila ada ketidakjelasan, jangan membuat asumsi bisnis. Catat pada `docs/22_OPEN_QUESTIONS.md`.

## Urutan Prioritas Sumber Kebenaran

Apabila ada konflik, gunakan urutan berikut:

1. `docs/00_MASTER_SPEC.md`
2. `docs/10_BUSINESS_RULES.md`
3. `docs/08_DATA_DICTIONARY.md`
4. `docs/09_API_CONTRACT.md`
5. `docs/07_DATABASE_SCHEMA.md`
6. `docs/03_UI_UX_GUIDELINES.md`
7. `docs/pages/*.md`
8. `docs/15_DECISION_LOG.md`
9. Source UI React lama sebagai referensi visual
10. Data simulasi lama

Dokumen dengan prioritas lebih tinggi harus diikuti. Konflik tidak boleh diselesaikan diam-diam.

## Daftar Dokumentasi

| File | Fungsi |
|---|---|
| `00_MASTER_SPEC.md` | Spesifikasi induk dan batasan sistem |
| `01_TECH_STACK.md` | Teknologi final |
| `02_SCOPE_AND_MODULES.md` | Scope setiap modul |
| `03_UI_UX_GUIDELINES.md` | Aturan visual dan responsive |
| `04_DESIGN_TOKENS.md` | Warna, typography, radius, spacing |
| `05_PAGE_AND_ROUTE_MAP.md` | Peta halaman dan route Laravel |
| `06_AUTH_AND_ROLES.md` | Hak akses admin dan marketing |
| `07_DATABASE_SCHEMA.md` | Struktur tabel dan relasi |
| `08_DATA_DICTIONARY.md` | Pemetaan label UI, API, dan database |
| `09_API_CONTRACT.md` | Kontrak REST API Flutter |
| `10_BUSINESS_RULES.md` | Aturan bisnis dan transisi status |
| `11_FILE_STORAGE.md` | Aturan file dan foto |
| `12_MIGRATION_PLAN.md` | Urutan migrasi ke Laravel |
| `13_TESTING_ACCEPTANCE.md` | Acceptance criteria dan pengujian |
| `14_DEPLOYMENT_CPANEL.md` | Deployment Laravel ke cPanel |
| `15_DECISION_LOG.md` | Keputusan arsitektur |
| `16_CHANGELOG.md` | Riwayat perubahan spesifikasi |
| `17_CODING_STANDARDS.md` | Aturan implementasi kode |
| `18_ENVIRONMENT_AND_CONFIG.md` | Variabel environment dan konfigurasi |
| `19_SECURITY_AND_PRIVACY.md` | Keamanan akun, lokasi, dan foto |
| `20_OFFLINE_SYNC_AND_TRACKING.md` | Sinkronisasi Flutter dan tracking |
| `21_SEED_DATA.md` | Data awal M01-M13 dan contoh simulasi |
| `22_OPEN_QUESTIONS.md` | Hal yang belum dikonfirmasi customer |
| `23_GLOSSARY.md` | Istilah yang digunakan |
| `24_DATABASE_IMPLEMENTATION_MATRIX.md` | Matrix implementasi database domain |
| `25_REFERENCE_CLEANUP_REPORT.md` | Laporan cleanup source referensi |
| `26_MYSQL_VALIDATION_AND_SQL_EXPORT.md` | Laporan validasi MySQL dan SQL export |
| `27_WEB_ADMIN_AUTHENTICATION.md` | Kontrak autentikasi Web Admin |
| `28_MARKETING_API_AUTHENTICATION.md` | Kontrak autentikasi API Marketing |
| `29_BLADE_DESIGN_SYSTEM.md` | Fondasi Blade Design System Web Admin |
| `30_TECHNICAL_HARDENING.md` | Hardening JSON API dan komponen Blade |
| `31_MARKETING_MANAGEMENT_MODULE.md` | Modul Data Marketing |
| `32_ADMIN_DASHBOARD.md` | Dashboard Admin berbasis data nyata |
| `33_MARKETING_SCHEDULE_API.md` | REST API Jadwal Marketing |
| `34_PROSPECT_MODULE.md` | Modul Prospek admin dan API |
| `35_MEMBER_APPROVAL_MODULE.md` | Modul Anggota dan Approval |
| `36_REPORTING_MODULE.md` | Modul Laporan Operasional dan Kunjungan |
| `37_TRACKING_MODULE.md` | Modul Tracking API dan peta admin |
| `38_BUSINESS_MODULE_TESTING.md` | Ringkasan test modul bisnis |
| `pages/` | Spesifikasi rinci setiap halaman |
| `diagrams/` | Diagram sistem, database, dan alur data |

## Status Dokumentasi

- Locked: tidak boleh diubah tanpa persetujuan pemilik project.
- Draft: masih dapat diperbarui.
- Pending confirmation: belum boleh diterapkan sebagai rumus atau aturan bisnis final.

## Aturan Pembaruan

Setiap perubahan yang memengaruhi field, route, status, database, API, atau UI wajib:

1. Memperbarui dokumen terkait.
2. Menambahkan entri pada `16_CHANGELOG.md`.
3. Bila keputusan arsitektur berubah, menambahkan keputusan pada `15_DECISION_LOG.md`.
4. Menjalankan typecheck, test, dan build.
5. Memastikan UI tetap sesuai referensi yang telah disetujui.
