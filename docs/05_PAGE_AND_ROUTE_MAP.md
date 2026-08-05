---
title: "Page and Route Map"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.3.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Konvensi

- Semua route admin memakai prefix `/admin`.
- Semua route admin dilindungi middleware `auth` dan role `admin`.
- Route API memakai prefix `/api/v1`.
- Route model binding menggunakan parameter singular bahasa Inggris.
- Nama route Laravel menggunakan namespace `admin.*`.

# Authentication

| Halaman/Aksi | Method | URL | Route name |
|---|---|---|---|
| Login | GET | `/login` | `login` |
| Proses login | POST | `/login` | `login.store` |
| Logout | POST | `/logout` | `logout` |

# API Authentication

| Aksi | Method | URL | Route name |
|---|---|---|---|
| Login marketing | POST | `/api/v1/auth/login` | `api.v1.auth.login` |
| Profile marketing | GET | `/api/v1/auth/profile` | `api.v1.auth.profile` |
| Logout marketing | POST | `/api/v1/auth/logout` | `api.v1.auth.logout` |

# Dashboard dan Rencana Kerja

| Halaman | Method | URL | Route name |
|---|---|---|---|
| Site root | GET | `/` | `home` |
| Dashboard alias | GET | `/admin` | `admin.home` |
| Dashboard | GET | `/admin/dashboard` | `admin.dashboard` |
| Rencana Kerja | GET | `/admin/daily` | `admin.daily.index` |

Parameter query umum: `day=Senin`.

# Marketing

| Halaman/Aksi | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/admin/marketing` | `admin.marketing.index` |
| Tambah | GET | `/admin/marketing/create` | `admin.marketing.create` |
| Simpan | POST | `/admin/marketing` | `admin.marketing.store` |
| Detail | GET | `/admin/marketing/{marketing}` | `admin.marketing.show` |
| Edit | GET | `/admin/marketing/{marketing}/edit` | `admin.marketing.edit` |
| Update | PUT | `/admin/marketing/{marketing}` | `admin.marketing.update` |
| Reset password | PATCH | `/admin/marketing/{marketing}/reset-password` | `admin.marketing.reset-password` |
| Status akun | PATCH | `/admin/marketing/{marketing}/status` | `admin.marketing.status` |

# Prospek

| Halaman | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/admin/prospects` | `admin.prospects.index` |
| Detail | GET | `/admin/prospects/{prospect}` | `admin.prospects.show` |

Query: `day`, `marketing_id`, `status`, `resort`, `search`.

# Anggota

| Halaman/Aksi | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/admin/members` | `admin.members.index` |
| Detail | GET | `/admin/members/{member}` | `admin.members.show` |
| Persetujuan | PATCH | `/admin/members/{member}/approval` | `admin.members.approval` |

# Laporan Operasional

| Halaman | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/admin/operational-reports` | `admin.operational-reports.index` |
| Detail | GET | `/admin/operational-reports/{report}` | `admin.operational-reports.show` |

# Laporan Kunjungan

| Halaman | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/admin/visit-reports` | `admin.visit-reports.index` |
| Detail | GET | `/admin/visit-reports/{visitReport}` | `admin.visit-reports.show` |

# Jadwal Web Admin

| Halaman/Aksi | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/admin/schedules` | `admin.schedules.index` |
| Tambah | GET | `/admin/schedules/create` | `admin.schedules.create` |
| Simpan | POST | `/admin/schedules` | `admin.schedules.store` |
| Edit | GET | `/admin/schedules/{schedule}/edit` | `admin.schedules.edit` |
| Update | PUT/PATCH | `/admin/schedules/{schedule}` | `admin.schedules.update` |
| Hapus | DELETE | `/admin/schedules/{schedule}` | `admin.schedules.destroy` |

# Jadwal API

| Aksi API | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/api/v1/schedules` | `api.v1.schedules.index` |
| Hari ini | GET | `/api/v1/schedules/today` | `api.v1.schedules.today` |
| Detail | GET | `/api/v1/schedules/{schedule}` | `api.v1.schedules.show` |
| Status | PATCH | `/api/v1/schedules/{schedule}/status` | `api.v1.schedules.status` |

# Tracking dan Riwayat

| Halaman | Method | URL | Route name |
|---|---|---|---|
| Tracking map | GET | `/admin/tracking` | `admin.tracking.index` |
| Tracking feed | GET | `/admin/tracking/feed` | `admin.tracking.feed` |
| Detail tracking | GET | `/admin/tracking/{trackingSession}` | `admin.tracking.show` |
| Riwayat perjalanan | GET | `/admin/journeys` | `admin.journeys.index` |
| Detail riwayat perjalanan | GET | `/admin/journeys/{trackingSession}` | `admin.journeys.show` |

# Rekap

Dashboard menampilkan rekap ringkas dari tabel rekap tanpa menghitung rumus baru. Halaman rekap web admin menampilkan tabel kertas utuh.

| Halaman | Method | URL | Route name |
|---|---|---|---|
| Daftar | GET | `/admin/operational-recaps` | `admin.operational-recaps.index` |
| Detail | GET | `/admin/operational-recaps/{operationalRecap}` | `admin.operational-recaps.show` |

# Profil

| Halaman/Aksi | Method | URL | Route name |
|---|---|---|---|
| Profil | GET | `/admin/profile` | `admin.profile.edit` |
| Update profil | PUT | `/admin/profile` | `admin.profile.update` |
| Ubah password | PUT | `/admin/password` | `admin.password.update` |
| Katalog Design System | GET | `/admin/design-system` | `admin.design-system` |

`/admin/design-system` hanya didaftarkan pada environment `local` dan `testing`, dengan middleware `auth`, `active`, dan `admin`.

# Pemetaan bottom navigation

| Bottom nav | Route yang dianggap aktif |
|---|---|
| Beranda | Dashboard |
| Marketing | Marketing index/create/show/edit |
| Prospek | Prospects index/show |
| Anggota | Members index/show |
| Laporan | Operational reports dan visit reports |
| Lacak | Tracking index/show |
| Profil | Profile dan password |

Halaman detail boleh menyembunyikan bottom navigation dan menggunakan tombol kembali.
