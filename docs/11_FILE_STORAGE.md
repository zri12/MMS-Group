---
title: "File Storage"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Prinsip

- File disimpan melalui Laravel Storage.
- Database hanya menyimpan path relatif.
- Tidak menyimpan base64 atau binary di MySQL.
- URL publik dibuat server.
- Nama file tidak memakai nama asli pengguna secara langsung.
- Validasi MIME dan ukuran wajib.

# Disk

Versi awal menggunakan disk `public`.

```env
FILESYSTEM_DISK=public
```

Jalankan:

```bash
php artisan storage:link
```

# Struktur folder

```text
marketing-profiles/{marketing_id}/profile-{uuid}.webp
members/{member_id}/member-{uuid}.webp
visit-reports/{visit_report_id}/visit-{uuid}.webp
```

# Ketentuan foto

## Foto profil marketing

- jpg, jpeg, png, webp
- maksimal 2 MB
- rekomendasi 800×800
- crop square
- fallback inisial

## Foto anggota

- jpg, jpeg, png, webp
- maksimal 2 MB setelah kompresi
- rekomendasi sisi panjang 1600 px
- wajib sesuai form final
- tidak digunakan sebagai bukti transaksi

## Foto laporan kunjungan

- satu foto
- jpg, jpeg, png, webp
- maksimal 2 MB
- caption optional
- dapat diperbesar di web admin

# Proses upload

1. Request divalidasi.
2. Record dibuat dalam transaction bila sesuai.
3. File diberi nama UUID.
4. File disimpan.
5. Path disimpan.
6. Bila transaction gagal, file sementara dibersihkan.
7. Bila foto diganti, file lama dihapus setelah update berhasil.

# Flutter

- Foto dikompresi sebelum upload.
- Upload multipart melalui Dio.
- Data offline menyimpan path lokal.
- Setelah sinkronisasi berhasil, simpan server URL/ID.
- File lokal boleh dibersihkan setelah kebijakan cache terpenuhi.

# Security

- Jangan menerima SVG dari user.
- Verifikasi MIME server-side.
- Jangan percaya ekstensi.
- Jangan mengekspos absolute server path.
- Gunakan URL HTTPS.
- Batasi akses berdasarkan authorization untuk file privat bila dibutuhkan.

# Backup

Backup harus mencakup:

- database;
- `storage/app/public`;
- `.env` secara aman, tidak dalam repository.

# Cleanup

Buat maintenance command opsional:

- mencari file orphan;
- membersihkan upload sementara;
- tidak menghapus file record aktif.
