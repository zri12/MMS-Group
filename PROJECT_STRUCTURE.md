# MMS Marketing Monitoring - Project Structure

## Gambaran Arsitektur

MMS Marketing Monitoring System terdiri dari dua project utama:

1. Web Admin + Backend + REST API: satu project Laravel 12 yang menangani web admin Blade/Livewire, backend, database access, session auth, REST API untuk Flutter, upload file, validasi, dan business logic.
2. Aplikasi Marketing Flutter: project terpisah yang mengakses server hanya melalui REST API `/api/v1`. Flutter tidak terhubung langsung ke MySQL.

Database utama menggunakan MySQL/MariaDB di server. SQLite digunakan Flutter untuk penyimpanan offline di perangkat.

## Struktur Root

```text
mms-monitoring/
|-- apps/
|   |-- web-admin-api/        # Project Laravel 12 aktif
|   `-- marketing-mobile/     # Placeholder project Flutter
|-- docs/                     # Dokumentasi sumber kebenaran
|   |-- pages/                # Spesifikasi per halaman
|   |-- diagrams/             # Diagram sistem
|   `-- references/           # Referensi visual terdokumentasi
|-- REFERENSI UI WEB ADMIN MONITORING MARKETING/
|-- REFERENSI UI APLIKASI MARKETING/
|-- scripts/
|-- AGENTS.md
|-- MANIFEST.md
|-- README.md
|-- PROJECT_STRUCTURE.md
`-- .gitignore
```

## apps/web-admin-api - Project Laravel 12

Satu project Laravel menangani:

- Web Admin: halaman Blade, interaksi Livewire, navigasi named route Laravel.
- Backend: model, migration, business logic, validasi.
- REST API: endpoint Flutter dengan prefix `/api/v1`.
- Database access: Eloquent ORM ke MySQL/MariaDB.
- Authentication: session untuk web admin, Sanctum token untuk Flutter.
- File storage: foto marketing, anggota, dan laporan kunjungan.
- Business logic: Actions dan Services agar dapat dipakai web admin dan REST API.

## Struktur Laravel Utama

```text
apps/web-admin-api/
|-- app/
|   |-- Actions/
|   |-- Enums/
|   |-- Http/
|   |   |-- Controllers/
|   |   |   |-- Admin/
|   |   |   `-- Api/V1/
|   |   |-- Middleware/
|   |   |-- Requests/
|   |   |   |-- Admin/
|   |   |   `-- Api/V1/
|   |   `-- Resources/Api/V1/
|   |-- Livewire/
|   |   `-- Admin/
|   |-- Models/
|   |-- Policies/
|   |-- Services/
|   `-- Support/
|-- bootstrap/
|-- config/
|-- database/
|   |-- factories/
|   |-- migrations/
|   `-- seeders/
|-- resources/
|   |-- css/
|   |-- js/
|   `-- views/
|       |-- components/
|       |   |-- ui/
|       |   |-- form/
|       |   |-- data/
|       |   |-- navigation/
|       |   `-- feedback/
|       |-- layouts/
|       |-- auth/
|       |-- admin/
|       `-- errors/
|-- routes/
|   |-- web.php
|   |-- admin.php
|   |-- api.php
|   `-- api/v1.php
|-- storage/
|-- tests/
|   |-- Feature/
|   `-- Unit/
|-- composer.json
|-- package.json
`-- vite.config.js
```

## Web Admin Structure

- Blade template berada di `resources/views/`.
- Layout utama berada di `resources/views/layouts/`.
- Blade components reusable berada di `resources/views/components/`.
- Blade Design System berada di `resources/views/components/ui`, `form`, `data`, `navigation`, dan `feedback`.
- Livewire class berada di `app/Livewire/`.
- View Livewire berada di `resources/views/livewire/`.
- JavaScript minimal berada di `resources/js/`.
- JavaScript browser helper disiapkan di `resources/js/components/` dan `resources/js/utilities/`.
- Peta tracking memakai `resources/js/maps/` dengan Leaflet.js dan OpenStreetMap.

Web Admin tidak menggunakan React, Inertia.js, TypeScript, TSX, React Router, React Context, atau React Leaflet. Source TSX dari folder referensi tidak dipindahkan ke Laravel aktif.

## Routing

| Prefix | File | Fungsi |
|---|---|---|
| `/` | `routes/web.php` | Route web Laravel + halaman fondasi |
| `/admin/*` | `routes/admin.php` | Halaman web admin Blade/Livewire |
| `/api/*` | `routes/api.php` | Entry point API |
| `/api/v1/*` | `routes/api/v1.php` | REST API versi 1 |

Route Web Admin menggunakan session auth dan middleware admin setelah autentikasi dibuat. Route API Flutter menggunakan Sanctum pada endpoint bisnis. Tidak ada React Router; navigasi memakai named route Laravel.

## Endpoint Aktif

| Method | URL | Fungsi |
|---|---|---|
| GET | `/login` | Halaman login web admin |
| POST | `/login` | Proses login web admin |
| POST | `/logout` | Logout web admin |
| GET | `/admin` | Beranda admin sementara setelah login |
| GET | `/admin/profile` | Profil admin |
| PUT | `/admin/profile` | Update profil admin |
| PUT | `/admin/password` | Update password admin |
| GET | `/admin/design-system` | Katalog komponen local/testing saja |
| GET | `/admin/marketing` | Data Marketing |
| GET | `/admin/prospects` | Data Prospek |
| GET | `/admin/members` | Data Anggota |
| GET | `/admin/operational-reports` | Laporan Operasional |
| GET | `/admin/visit-reports` | Laporan Kunjungan |
| GET | `/admin/tracking` | Tracking Lokasi |
| GET | `/api/v1/health` | Health check API |
| POST | `/api/v1/auth/login` | Login API marketing |
| GET | `/api/v1/auth/profile` | Profil API marketing |
| POST | `/api/v1/auth/logout` | Logout current token API marketing |
| GET | `/api/v1/schedules` | Jadwal marketing |
| GET | `/api/v1/prospects` | Prospek marketing |
| GET | `/api/v1/members` | Anggota marketing |
| GET | `/api/v1/operational-reports` | Laporan operasional marketing |
| GET | `/api/v1/visit-reports` | Laporan kunjungan marketing |
| GET | `/api/v1/tracking/sessions` | Riwayat tracking marketing |

## Environment Fondasi

Pada tahap fondasi final, project tidak membutuhkan database untuk session, cache, atau queue.

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
```

Database runtime lokal memakai MySQL/MariaDB. Migration domain MMS, enum, model, factory, dan development seeder sudah tersedia. Test otomatis memakai SQLite in-memory melalui `phpunit.xml`.

## Struktur Storage Upload

| Folder | Fungsi |
|---|---|
| `marketing-profiles/` | Foto profil marketing per ID |
| `members/` | Foto anggota per ID |
| `visit-reports/` | Foto laporan kunjungan per ID |

## apps/marketing-mobile - Project Flutter

Placeholder. Project Flutter belum dibuat pada tahap ini.

Flutter nantinya akan:

- mengakses REST API pada `/api/v1`;
- autentikasi menggunakan Sanctum token;
- menyimpan data offline dengan SQLite;
- tracking GPS menggunakan geolocator;
- maps menggunakan flutter_map + OpenStreetMap.

## Folder Referensi UI

Folder `REFERENSI UI WEB ADMIN MONITORING MARKETING/` berisi project React/Vite/TSX lama yang sudah disetujui customer sebagai referensi visual web admin.

Folder `REFERENSI UI APLIKASI MARKETING/` berisi source React/Vite/TSX aplikasi marketing hasil ekstraksi whitelist dari ZIP eksternal. Folder ini hanya referensi visual dan bukan project Flutter aktif.

Kedua folder referensi bersifat read-only dan hanya digunakan untuk:

- struktur halaman dan menu;
- desain, warna, layout;
- komponen dan alur navigasi;
- responsive behavior.

Folder referensi tidak berisi `node_modules`, nested `.git`, `dist`, `build`, `.vercel`, `.codex-artifacts`, atau `DESIGN APLIKASI.zip`. Desain dibangun ulang menggunakan Blade/Livewire untuk web admin dan Flutter untuk aplikasi marketing.

## Batas Scope Tahap Database dan Source Baseline

Tahap finalisasi database dan development data menghasilkan:

- migration domain MMS;
- PHP Enum domain;
- model dan relasi Eloquent;
- cast enum `DayName` pada semua model yang memiliki `day_name`;
- factory domain dengan konsistensi relasi;
- development seeder deterministik;
- 39 anggota, 13 laporan operasional Senin, 6 rekap operasional, dan tracking M01 9 titik;
- test schema, enum, cast, relasi, factory, seeder, dan aggregate dashboard;
- validasi migration, seeder, rollback, dan restore SQL pada MySQL lokal;
- SQL export schema-only, data development-only, dan full development;
- cleanup folder referensi dari dependency lokal, build output, tool output, dan nested Git;
- ekstraksi source referensi aplikasi marketing ke folder terpisah;
- script packaging source bersih.

Tahap database dan source baseline tidak mencakup:

- CRUD;
- REST API bisnis;
- Flutter;
- deployment.

Tahap auth final dan Blade Design System sudah menambahkan autentikasi web admin, autentikasi marketing mobile, komponen Blade reusable, layout admin responsif, dan katalog komponen local/testing. Tahap tersebut tidak membuat CRUD bisnis, dashboard bisnis final, REST API bisnis, Flutter, migration, SQL dump, atau seeder baru.

## Status Pengerjaan

| Tahap | Status |
|---|---|
| Analisis kebutuhan | Selesai |
| Alur sistem | Selesai |
| Rancangan database | Selesai dalam dokumentasi |
| UI/UX referensi | Selesai dan disetujui |
| Referensi UI web admin | Selesai dan dibersihkan |
| Referensi UI aplikasi marketing | Selesai dan dipisahkan |
| Dokumentasi awal | Selesai |
| Fondasi Laravel | Selesai |
| Transisi Blade/Livewire | Selesai |
| Finalisasi fondasi Blade/Livewire | Selesai |
| Database implementation matrix | Selesai |
| Migration domain | Selesai |
| PHP Enum domain | Selesai |
| Model dan relasi | Selesai |
| Factory dan seeder | Selesai |
| SQLite automated test | Selesai |
| MySQL local migration | Selesai |
| MySQL local seeder | Selesai |
| MySQL rollback validation | Selesai |
| SQL schema export | Selesai |
| SQL development export | Selesai |
| SQL restore validation | Selesai |
| Source packaging | Selesai |
| Autentikasi web admin | Selesai dan manual auth check berhasil |
| Autentikasi API mobile | Selesai dan manual auth check berhasil |
| Auth final corrections | Selesai |
| Blade Design System | Selesai |
| Technical Hardening | Selesai |
| Manual Auth Check | Selesai |
| Data Marketing | Selesai |
| Dashboard Admin | Selesai |
| Schedule API | Selesai |
| Prospect Module | Selesai |
| Member Approval | Selesai |
| Reporting Module | Selesai |
| Tracking Module | Selesai |
| Modul Web Admin | Selesai untuk Data Marketing, Prospek, Anggota, Laporan, dan Tracking |
| REST API bisnis | Selesai untuk Jadwal, Prospek, Anggota, Laporan, dan Tracking |
| Flutter | Belum |
| Offline Sync Flutter | Belum |
| Deployment | Belum |

## Source Bersih

File dan folder berikut tidak boleh dimasukkan ke ZIP source bersih:

- `.env`;
- `vendor/`;
- `node_modules/`;
- `public/build/`;
- `database/database.sqlite`;
- `storage/logs/`;
- `storage/framework/views/`;
- `bootstrap/cache/*.php`;
- `.phpunit.result.cache`;
- file ZIP di dalam project;
- artefak tool/cache;
- nested repository Git yang tidak diperlukan.

Dependency dan asset dapat dibuat ulang dengan:

```bash
composer install
npm install
npm run build
```

Source ZIP bersih dapat dibuat dari root project dengan:

```powershell
.\scripts\create-source-zip.ps1 -DryRun
.\scripts\create-source-zip.ps1
```

SQL development berada di:

```text
apps/web-admin-api/database/sql/
```

## Tahap Berikutnya

1. Flutter application foundation.
2. Flutter authentication.
3. Flutter schedule, prospect, member, report, dan tracking client.
4. Offline SQLite queue.
5. Customer UAT.
6. Deployment cPanel.
