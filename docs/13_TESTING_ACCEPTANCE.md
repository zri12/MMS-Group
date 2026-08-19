---
title: "Testing and Acceptance Criteria"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.3.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Quality gates

Sebelum merge/deploy:

- PHP test berhasil.
- Frontend build berhasil.
- Tidak ada error browser console.
- Tidak ada horizontal overflow body.
- Tidak ada broken image.
- Tidak ada tombol tanpa respons.
- Authorization diuji.

# Test layer

## Backend

- Unit test untuk helper/perhitungan yang sudah dikonfirmasi.
- Feature test untuk auth, CRUD, filter, approval, API, upload.
- Policy test untuk isolation data marketing.
- Database test untuk unique dan foreign key.

## Frontend web

- Blade view dapat dikompilasi.
- Livewire interaction untuk modal/filter penting.
- Manual responsive.
- Data view/controller/Livewire valid.
- Maps error/loading/fullscreen.

## Flutter

- Unit test model/repository.
- SQLite test.
- Offline queue test.
- Dio error mapping.
- Widget test form kritis.
- Manual GPS/background test pada perangkat fisik.

# Acceptance global

- Semua route admin membutuhkan login.
- Marketing tidak dapat login ke web admin.
- Admin tidak memakai token Flutter.
- Marketing tidak dapat mengambil data marketing lain.
- Response API tidak membocorkan password/path internal.
- Pagination bekerja.
- Filter dapat dihapus/reset.
- Empty/loading/error state tersedia.
- Bahasa UI konsisten.
- Blade Design System component render test tersedia.
- Component catalog hanya tersedia pada local/testing dan dilindungi admin.

# Login

- Admin valid masuk Dashboard.
- Kredensial salah menampilkan error.
- Akun inactive ditolak.
- Logout menghancurkan session.
- Rate limit login aktif.
- Username login web di-trim dan lowercase.
- Password login web tidak di-trim.
- Marketing tidak dapat login ke web admin.
- `GET /logout` tidak tersedia.
- Route admin memakai middleware `auth`, `active`, dan `admin`.
- Profil admin hanya mengubah `name` dan `email`.
- Ubah password admin melalui `PUT /admin/password`.
- API login marketing memakai `username`, `password`, dan `device_name`.
- Username API di-trim dan lowercase.
- `device_name` di-trim dan whitespace berlebih disederhanakan.
- Password API tidak di-trim.
- Admin tidak dapat login ke API marketing.
- Marketing inactive dan marketing tanpa profile tidak mendapat token.
- API profile/logout membutuhkan Sanctum token dengan ability `marketing-mobile`.
- Logout API hanya menghapus token saat ini.
- Protected request dari akun inactive mencabut current token saja.

# Marketing

- M01–M13 dari seeder tampil.
- Kode dan username unik.
- Tambah/edit bekerja.
- Nonaktif mencegah login Flutter.
- Reset password memberi feedback.
- Riwayat data tidak hilang saat akun inactive.

# Prospek

- Data Flutter muncul pada admin.
- Filter hari/marketing/status/resort/search bekerja.
- Detail sesuai ID.
- Riwayat hanya milik prospek tersebut.
- Marketing lain tidak dapat mengakses.
- `local_uuid` mencegah duplikasi.

# Anggota

- Status awal Menunggu.
- Foto tersimpan dan tampil.
- Admin dapat Disetujui/Ditolak.
- Marketing tidak dapat mengubah status.
- Nomor anggota/pinjaman unik.
- Filter dan detail benar.
- Status baru terlihat pada Flutter setelah refresh/sync.

# Laporan Operasional

- Semua field final tersimpan.
- Tidak ada field Lain-lain, foto anggota, atau lokasi setoran.
- Lampiran Foto Pencairan dan Foto Bukti Transfer dapat diunggah dan tampil di
  detail laporan; kategori tidak boleh diduplikasi dalam satu laporan.
- Nominal tidak negatif.
- Target orang integer.
- Filter tanggal/marketing/resort bekerja.
- Detail sesuai data input.

# Laporan Kunjungan

- Terkait prospect dan marketing.
- Status prospect diperbarui atomically.
- Foto/caption/lokasi tampil.
- Follow-up optional.
- Filter hasil/status bekerja.

# Jadwal

- Admin dapat create/edit/cancel.
- Marketing hanya melihat jadwal sendiri.
- Transisi status valid.
- Hari dan tanggal konsisten.
- Jadwal muncul di Dashboard/Detail Marketing/Flutter.

# Tracking

- Satu sesi aktif per marketing.
- Batch points idempotent.
- Map menampilkan marker.
- Fullscreen dan fit marker bekerja.
- Sidebar menutupi map.
- Riwayat menampilkan timeline.
- Offline points tersinkron tanpa duplikasi.
- Endpoint menolak session milik marketing lain.

# Rekap

- Satu tabel utuh.
- Meta No/Hari/Tanggal.
- Header dua tingkat.
- rowSpan/colSpan benar.
- MG dan M01–M13 tampil.
- Mobile horizontal scroll hanya di wrapper.
- Fullscreen bekerja.
- Data hari berubah saat filter hari berubah.
- Tidak ada rumus dummy.
- Rumus pending tidak dihitung otomatis.

# Responsive matrix

Uji minimal:

- 360×800
- 390×844
- 430×932
- 768×1024
- 1366×768
- 1440×900

Halaman:

- Login
- Dashboard
- Marketing
- Data Harian
- Prospek
- Anggota
- Laporan Operasional
- Laporan Kunjungan
- Jadwal
- Tracking
- Riwayat
- Rekap
- Profil

# Deployment smoke test

- `/login`
- `/admin/dashboard`
- API login
- API profile
- upload foto
- storage URL
- database write
- polling tracking
- scheduler cron
- HTTPS
