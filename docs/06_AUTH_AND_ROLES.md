---
title: "Authentication and Roles"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.2.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Role

Sistem versi pertama memiliki dua role:

1. `admin`
2. `marketing`

# Admin

Akses melalui web admin.

## Hak akses

- melihat dashboard seluruh marketing;
- membuat dan mengubah akun marketing;
- menonaktifkan akun marketing;
- reset password marketing;
- melihat prospek;
- melihat anggota;
- menyetujui/menolak anggota;
- melihat laporan operasional;
- melihat laporan kunjungan;
- membuat/mengubah jadwal;
- melihat tracking seluruh marketing;
- melihat riwayat;
- melihat rekap;
- mengubah profil sendiri.

Admin tidak menginput laporan lapangan melalui web pada scope awal.

# Marketing

Akses melalui Flutter.

## Hak akses

- login;
- melihat profil sendiri;
- melihat jadwal sendiri;
- membuat dan mengubah prospek milik sendiri sesuai aturan;
- membuat anggota;
- melihat status anggota yang ia kirim;
- membuat laporan operasional sendiri;
- membuat laporan kunjungan sendiri;
- menjalankan tracking;
- melihat riwayat sendiri;
- sinkronisasi data offline.

Marketing tidak boleh:

- melihat data marketing lain;
- mengubah approval anggota;
- mengubah akun marketing;
- melihat rekap seluruh marketing;
- mengubah jadwal yang dibuat admin, selain update status yang diizinkan.

# Authentication

## Web admin

- Laravel session authentication.
- Login menggunakan username admin.
- Login web admin hanya untuk role `admin` dengan `is_active = true`.
- Session regeneration setelah login.
- `last_login_at` diperbarui setelah login berhasil.
- CSRF protection aktif.
- Route admin memakai middleware `auth`, `active`, dan `admin`.
- Logout hanya melalui `POST /logout`, lalu session di-invalidate dan CSRF token diregenerasi.
- Tidak ada register, forgot password, reset password, email verification, atau remember me pada fase web admin auth.

## Flutter

- Laravel Sanctum personal access token.
- Login melalui `/api/v1/auth/login`.
- Login API memakai `username`, `password`, dan `device_name`.
- Login API hanya untuk role `marketing` aktif yang memiliki `MarketingProfile`.
- Token memakai ability `marketing-mobile`.
- Endpoint profile/logout memakai middleware `auth:sanctum`, `active`, `marketing`, dan `abilities:marketing-mobile`.
- Token disimpan dalam Flutter Secure Storage.
- Logout menghapus token server yang sedang dipakai dan token lokal.
- Token tidak boleh dicetak dalam log produksi.

# Account status

User memiliki status:

- `active`
- `inactive`

Akun inactive tidak dapat login. Menonaktifkan marketing tidak menghapus data historis.

# Password

- Password disimpan menggunakan hash Laravel.
- Password awal dibuat admin dan harus dapat direset.
- Password minimal 8 karakter.
- Tidak pernah dikirim kembali melalui API.
- Respons reset hanya mengembalikan status keberhasilan; password sementara hanya ditampilkan bila flow bisnis memang meminta dan harus dikonfirmasi.

# Authorization

Gunakan Policy atau Gate untuk memastikan:

- marketing hanya mengakses data sendiri;
- admin mengakses seluruh data;
- endpoint approval hanya admin;
- endpoint tracking hanya marketing terautentikasi untuk dirinya sendiri;
- ID dari request tidak boleh mengalahkan identitas token.

# Audit minimum

Simpan:

- `approved_by`
- `approved_at`
- `rejected_by`
- `rejected_at`
- `updated_at`

Audit log umum bersifat opsional fase berikutnya.
