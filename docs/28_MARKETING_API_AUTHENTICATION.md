---
title: "Marketing API Authentication"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.1.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Ringkasan

Autentikasi aplikasi marketing memakai REST API `/api/v1` dan Laravel Sanctum personal access token.

Fase ini hanya mencakup:

- login marketing;
- profile marketing terautentikasi;
- logout token saat ini.

Fase ini tidak mencakup registrasi, ubah password marketing, refresh token, logout semua perangkat, daftar token perangkat, REST API bisnis, upload file, offline sync runtime, Flutter, atau deployment.

# Syarat Login

`POST /api/v1/auth/login` hanya berhasil bila:

- `username` valid;
- password valid;
- user memiliki role `marketing`;
- `is_active` bernilai `true`;
- user memiliki `MarketingProfile`;
- request mengirim `device_name`.

Admin tidak dapat login melalui endpoint marketing API.

Pesan gagal login dibuat generik agar tidak membocorkan apakah username, password, role, status akun, atau profile yang tidak valid.

Sebelum validasi dan autentikasi:

- `username` di-trim;
- `username` di-lowercase karena username project diperlakukan case-insensitive;
- `device_name` di-trim;
- whitespace berlebih pada `device_name` disederhanakan;
- password tidak di-trim dan tidak diubah.

# Rate Limit

Rate limit login API memakai key:

```text
username|ip
```

Password dan `device_name` tidak masuk throttle key.

Throttle key memakai username yang sudah dinormalisasi.

Nilai konfigurasi:

- `MMS_MARKETING_API_LOGIN_MAX_ATTEMPTS`
- `MMS_MARKETING_API_LOGIN_DECAY_SECONDS`

Rate limiter dibersihkan setelah login berhasil.

# Token Sanctum

Token dibuat dengan:

- token type: `Bearer`;
- ability: `marketing-mobile`;
- token name: prefix `mms-marketing` + `device_name`.

Ability dan prefix dapat dikonfigurasi melalui:

- `MMS_MARKETING_API_TOKEN_ABILITY`
- `MMS_MARKETING_API_TOKEN_PREFIX`

Client hanya menerima plain text token satu kali pada response login. Database menyimpan hash token Sanctum, bukan plain text token.

Token name memakai `device_name` yang sudah dinormalisasi.

# Protected Route

Route profile dan logout memakai middleware:

```text
auth:sanctum
active
marketing
abilities:marketing-mobile
```

`active` menolak akun inactive. Untuk request JSON dengan Sanctum token, middleware menghapus hanya current access token bila token tersebut adalah `PersonalAccessToken`. Token perangkat lain tidak dihapus.

`marketing` menolak role selain marketing dan menolak user marketing tanpa `MarketingProfile`.

`abilities` memastikan token memiliki ability mobile yang benar.

# Route Contract

| Method | URL | Route name | Middleware |
|---|---|---|---|
| POST | `/api/v1/auth/login` | `api.v1.auth.login` | public |
| GET | `/api/v1/auth/profile` | `api.v1.auth.profile` | `auth:sanctum`, `active`, `marketing`, `abilities:marketing-mobile` |
| POST | `/api/v1/auth/logout` | `api.v1.auth.logout` | `auth:sanctum`, `active`, `marketing`, `abilities:marketing-mobile` |

Tidak ada route ubah password marketing. Reset password merupakan tindakan admin
pada web admin dan tidak dapat dilakukan menggunakan token marketing.

# Response Login

Contoh dengan token placeholder:

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token_type": "Bearer",
    "token": "<plain-text-token-hanya-pada-login>",
    "user": {
      "id": 2,
      "name": "Deden",
      "username": "m01.deden",
      "email": "deden@example.test",
      "role": "marketing",
      "is_active": true,
      "last_login_at": "2026-07-29T15:30:00+07:00",
      "marketing": {
        "id": 1,
        "code": "M01",
        "phone": "081200000001",
        "area": "Gedebage",
        "profile_photo_url": null,
        "work_days": ["Senin", "Selasa"]
      }
    }
  }
}
```

Response tidak mengembalikan password, hash password, remember token, atau token lain.

# Logout

`POST /api/v1/auth/logout` hanya menghapus token yang sedang dipakai.

Token perangkat lain milik user yang sama tetap aktif.

# Testing

Automated test memakai SQLite in-memory.

Coverage:

- login marketing aktif dengan profile;
- `device_name` wajib;
- admin ditolak;
- marketing inactive ditolak;
- marketing tanpa profile ditolak;
- password salah generik;
- profile membutuhkan token;
- profile membutuhkan akun aktif;
- profile membutuhkan role marketing;
- profile membutuhkan ability `marketing-mobile`;
- logout menghapus current token saja;
- token lama tidak dapat dipakai setelah logout;
- token akun inactive dicabut hanya untuk current token;
- token perangkat lain tetap tersimpan;
- username dan device name normalization;
- password tidak di-trim;
- rate limit API memakai username normalized dan IP.
