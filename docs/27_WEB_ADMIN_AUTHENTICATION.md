---
title: "Web Admin Authentication"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.2.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Ringkasan

Autentikasi web admin MMS menggunakan Laravel session authentication dalam project Laravel yang sama dengan web admin dan REST API.

Fase ini hanya mencakup:

- login web admin;
- logout web admin;
- proteksi route admin;
- halaman beranda admin sementara setelah login;
- profil admin;
- ubah password admin.

Fase ini tidak mencakup registrasi, forgot password, reset password, email verification, remember me, autentikasi API mobile, CRUD bisnis, dashboard bisnis final, migration, seeder, atau SQL dump baru.

# Guard dan Provider

| Item | Nilai |
|---|---|
| Guard | `web` |
| Driver guard | `session` |
| Provider | `users` |
| Model | `App\Models\User` |
| Login identifier | `username` |

# Syarat Login Web Admin

User hanya dapat login ke web admin bila semua syarat terpenuhi:

- `username` valid;
- password valid;
- `role` bernilai `admin`;
- `is_active` bernilai `true`;
- user tidak soft-deleted.

Marketing tidak dapat login ke web admin. Admin inactive tidak dapat login.

Pesan gagal login dibuat generik agar tidak membocorkan apakah username, password, role, atau status akun yang salah.

Sebelum validasi dan autentikasi:

- `username` di-trim;
- `username` di-lowercase karena username project diperlakukan case-insensitive;
- password tidak di-trim dan tidak diubah.

# Rate Limit

Login memakai rate limiting berbasis username dan IP address. Password tidak menjadi bagian dari key rate limit.

Throttle key memakai username yang sudah dinormalisasi.

Rate limiter dibersihkan setelah login berhasil.

Nilai konfigurasi:

- `MMS_ADMIN_LOGIN_MAX_ATTEMPTS`
- `MMS_ADMIN_LOGIN_DECAY_SECONDS`

# Session Security

Setelah login berhasil:

- session diregenerasi;
- `last_login_at` diperbarui;
- user diarahkan ke `admin.home`.

Saat logout:

- hanya menerima `POST /logout`;
- guard `web` logout;
- session di-invalidate;
- CSRF token diregenerasi;
- user diarahkan ke halaman login.

# Middleware Admin

Semua route admin menggunakan middleware:

```text
auth
active
admin
```

`active` memastikan user login masih aktif. Bila user inactive, session dihancurkan dan user diarahkan ke login.

`admin` memastikan hanya role admin yang dapat mengakses halaman admin.

# Route Contract

| Method | URL | Route name | Middleware |
|---|---|---|---|
| GET | `/login` | `login` | `guest` |
| POST | `/login` | `login.store` | `guest` |
| POST | `/logout` | `logout` | `auth` |
| GET | `/admin` | `admin.home` | `auth`, `active`, `admin` |
| GET | `/admin/profile` | `admin.profile.edit` | `auth`, `active`, `admin` |
| PUT | `/admin/profile` | `admin.profile.update` | `auth`, `active`, `admin` |
| PUT | `/admin/password` | `admin.password.update` | `auth`, `active`, `admin` |

Route berikut tidak dibuat pada fase ini:

- `GET /logout`;
- route register;
- route forgot password;
- route reset password;
- route email verification.

# Profil Admin

Halaman profil hanya mengizinkan update:

- `name`;
- `email`.

Field berikut tidak dapat diubah melalui form profil:

- `username`;
- `role`;
- `is_active`;
- `password`;
- `last_login_at`.

# Ubah Password Admin

Update password admin melalui `PUT /admin/password`.

Validasi:

- password saat ini wajib benar;
- password baru minimal 8 karakter;
- password baru wajib dikonfirmasi;
- password baru harus berbeda dari password saat ini.

Password disimpan menggunakan hash Laravel.

# UI

UI login, beranda admin sementara, dan profil memakai konsep Modern Calm Black-Gold.

Halaman beranda admin sementara tidak menampilkan data bisnis, angka dashboard, mock data, atau modul CRUD.

Finalisasi fase ini menambahkan:

- flash login;
- flash logout;
- show/hide password pada login;
- show/hide password pada form ubah password;
- loading dan disabled state saat submit login;
- loading dan disabled state saat submit form ubah password;
- status akun dan login terakhir pada home/profile;
- halaman 403 generik dengan logout POST aman.

Semua password visibility toggle memakai dynamic `aria-label`, `aria-pressed`, `type="button"`, dan icon dekoratif `aria-hidden`.

# Testing

Automated test memakai SQLite in-memory melalui `phpunit.xml`, bukan MySQL lokal.

Coverage fase auth:

- admin aktif dapat login;
- kredensial salah ditolak dengan pesan generik;
- marketing ditolak dari web admin;
- admin inactive ditolak;
- login rate limit aktif;
- login username dengan whitespace luar dan case variant;
- password tidak di-trim;
- throttle key memakai username normalized;
- logout hanya via POST;
- guest diarahkan ke login saat membuka admin;
- marketing mendapat 403 pada admin route;
- admin inactive dipaksa logout saat membuka admin route;
- profil dapat diupdate hanya untuk field yang diizinkan;
- password dapat diubah dengan validasi password saat ini, konfirmasi, panjang minimal, dan berbeda dari password lama;
- form ubah password memiliki loading, disabled state, dan password toggle accessible.

# Status

```text
SELESAI DENGAN PENDING MANUAL BROWSER CHECK
```

Automated check sudah memverifikasi routing, Blade compile, test, dan build. Manual browser responsive check tetap perlu dilakukan sebelum customer review.
