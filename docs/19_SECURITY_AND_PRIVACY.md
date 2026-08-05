---
title: "Security and Privacy"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.3.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Data sensitif sistem

- akun dan password;
- nomor HP;
- alamat;
- data pinjaman;
- foto anggota;
- lokasi marketing;
- riwayat perjalanan;
- data prospek.

Data hanya digunakan untuk operasional sistem.

# Authentication security

- Hash password Laravel.
- Rate limit login.
- Username login dinormalisasi sebelum throttle.
- Password tidak di-trim atau diubah selama normalisasi.
- Session regeneration.
- Login web admin memakai pesan gagal generik.
- Login web admin dibatasi untuk role `admin` yang aktif.
- Logout web admin meng-invalidate session dan regenerate CSRF token.
- Sanctum token revocation.
- Marketing API login memakai kegagalan generik.
- Marketing API token wajib ability `marketing-mobile`.
- Logout API hanya menghapus current access token.
- Protected API request dari akun inactive menghapus hanya current access token.
- Akun inactive ditolak.
- Token disimpan Secure Storage.
- Tidak ada token dalam URL.

# Authorization

- Semua resource memakai Policy.
- Marketing hanya data sendiri.
- Admin seluruh data.
- Server mengambil marketing dari token, bukan request.
- Approval hanya admin.

# Validation

- Server-side wajib.
- Nominal non-negative.
- File MIME/size.
- Koordinat range valid.
- Status whitelist.
- Search/filter di-sanitize melalui query builder, tidak raw SQL.

# Location privacy

- Tracking hanya saat sesi aktif.
- UI harus menampilkan status tracking.
- Jangan merekam di luar kebutuhan.
- Retention data lokasi perlu dikonfirmasi.
- Akses tracking hanya admin yang sah dan marketing pemilik riwayat.
- HTTPS wajib.

# File security

- Random UUID filename.
- No SVG user upload.
- No base64 DB.
- Path traversal dicegah oleh Storage.
- EXIF dapat dihapus saat kompresi bila diperlukan.
- Foto tidak ditampilkan publik tanpa authorization bila storage private dipilih.

# Logging

Jangan log:

- password;
- token;
- full photo;
- payload pinjaman lengkap tanpa masking;
- tracking batch lengkap pada production kecuali debugging terbatas.

UI component tidak boleh memakai raw output untuk data user. Gunakan escaped Blade output dan props allowlist untuk variant/class.

Log:

- user ID;
- endpoint;
- status;
- error code;
- request ID;
- local UUID bila diperlukan.

# Database

- User DB hanya memiliki privilege database aplikasi.
- Backup terenkripsi/akses terbatas.
- Production tidak memakai akun root.
- Index untuk menghindari query berat.

# cPanel

- `.env` di luar public.
- Document root ke `/public`.
- Directory listing off.
- `APP_DEBUG=false`.
- SSL aktif.
- Permission storage/cache sesuai.

# Incident

Bila token bocor:

1. revoke token;
2. reset password;
3. cek log;
4. nonaktifkan akun bila perlu.

Bila perangkat hilang:

- revoke semua token user;
- logout paksa;
- pertimbangkan remote session invalidation.
