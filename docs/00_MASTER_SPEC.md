---
title: "Master Specification"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# 1. Identitas project

- Nama: **MMS Marketing Monitoring System**
- Organisasi: **KSP Manunggal Makmur Sejahtera**
- Jenis sistem: Web admin monitoring dan aplikasi marketing lapangan
- Zona waktu: `Asia/Jakarta`
- Bahasa UI: Bahasa Indonesia
- Referensi Web Admin: https://ui-mms-group-web-admin.vercel.app/
- Referensi Aplikasi Marketing: https://mms-group-ui.vercel.app/

# 2. Tujuan

Sistem membantu admin memantau dan mengelola:

- akun marketing;
- jadwal marketing;
- data prospek;
- data anggota;
- persetujuan anggota;
- laporan operasional harian;
- laporan kunjungan;
- lokasi marketing;
- sesi dan riwayat perjalanan;
- rekap operasional.

Aplikasi Flutter digunakan marketing untuk memasukkan data dan menjalankan aktivitas lapangan. Web admin digunakan administrator untuk monitoring, pengelolaan akun, persetujuan, dan rekap.

# 3. Arsitektur final

## 3.1 Project Laravel

Satu project Laravel menangani:

- backend;
- database access;
- autentikasi web admin;
- REST API Flutter;
- web admin Blade dan Livewire;
- upload dan penyajian file;
- validasi dan business logic;
- scheduler dan maintenance task.

## 3.2 Project Flutter

Satu project Flutter menangani:

- login marketing;
- jadwal;
- data prospek;
- data anggota;
- input laporan operasional;
- laporan kunjungan;
- GPS dan tracking;
- penyimpanan offline SQLite;
- sinkronisasi ke REST API.

## 3.3 Database

- MySQL sebagai database utama di server.
- SQLite melalui `sqflite` sebagai penyimpanan offline di perangkat Flutter.
- Flutter tidak boleh terhubung langsung ke MySQL.

# 4. Modul final web admin

1. Login Admin
2. Dashboard
3. Data Marketing
4. Detail Marketing
5. Tambah/Edit Marketing
6. Data Harian
7. Data Prospek
8. Detail Prospek
9. Data Anggota
10. Detail Anggota
11. Persetujuan Anggota
12. Laporan Operasional Harian
13. Detail Laporan Operasional
14. Laporan Kunjungan
15. Detail Laporan Kunjungan
16. Jadwal Marketing
17. Tambah/Edit Jadwal
18. Tracking Lokasi
19. Detail Tracking
20. Riwayat Perjalanan
21. Detail Riwayat
22. Rekap Operasional
23. Profil Admin
24. Ubah Password
25. Logout

# 5. Modul final aplikasi marketing

1. Login Marketing
2. Dashboard Marketing
3. Profil
4. Jadwal
5. Data Prospek/Konsumen
6. Tambah/Edit Prospek
7. Detail Prospek
8. Data Anggota
9. Tambah Anggota
10. Detail Anggota
11. Input Setoran/Laporan Operasional
12. Riwayat Laporan Operasional
13. Laporan Kunjungan
14. Riwayat Laporan Kunjungan
15. Tracking
16. Riwayat Perjalanan
17. Status Sinkronisasi
18. Logout

# 6. Modul yang tidak digunakan

Modul berikut tidak termasuk scope aktif:

- Bukti Kunjungan versi lama yang berisi foto anggota;
- Bukti Transaksi;
- Laporan Tunai terpisah;
- payment gateway;
- payroll;
- akuntansi lengkap;
- multi perusahaan;
- notifikasi WhatsApp;
- WebSocket realtime pada versi awal;
- grafik dekoratif yang tidak diperlukan;
- export Excel/PDF otomatis kecuali disetujui kemudian.

Foto Pencairan dan Foto Bukti Transfer bukan modul atau laporan terpisah. Keduanya
merupakan lampiran opsional dengan kategori berbeda pada Laporan Operasional Harian.

# 7. Prinsip konsistensi web dan Flutter

Setiap form Flutter harus memiliki halaman tujuan di web admin:

| Flutter | Web Admin |
|---|---|
| Tambah Prospek/Konsumen | Data Prospek |
| Tambah Anggota | Data Anggota |
| Input Setoran | Laporan Operasional Harian |
| Laporan Kunjungan | Laporan Kunjungan |
| GPS Tracking | Tracking Lokasi dan Riwayat |
| Jadwal dari server | Jadwal Marketing |
| Status anggota | Persetujuan di Detail Anggota |

Nama field, tipe data, status, dan contoh data harus mengikuti `08_DATA_DICTIONARY.md`.

# 8. Data marketing awal

Marketing awal terdiri dari M01–M13:

- M01 Deden
- M02 Angil
- M03 Ari
- M04 Feri
- M05 Sukma
- M06 Sandi
- M07 Vikri
- M08 Farhad
- M09 Doni
- M10 Faiz
- M11 Agung
- M12 Faisal
- M13 Agnes

Data ini masuk melalui seeder dan dapat diedit admin.

# 9. Hari operasional

UI dan simulasi menggunakan:

- Senin
- Selasa
- Rabu
- Kamis
- Jumat
- Sabtu

Minggu tidak ditampilkan sebagai hari operasional pada versi pertama.

# 10. Total referensi dashboard

Angka referensi UI yang telah disetujui:

- Total Anggota: 39
- Total Target: Rp123.500.000
- Total Drop: Rp91.000.000
- Total Storting: Rp37.700.000

Pada implementasi produksi, nilai berasal dari database. Seeder development dapat dibuat agar menghasilkan angka referensi ini.

# 11. Aturan perubahan

- Nama menu tidak boleh diubah tanpa persetujuan.
- Field form tidak boleh ditambah/dihapus tanpa memperbarui Data Dictionary, Database Schema, API Contract, dan Changelog.
- Struktur tabel rekap tidak boleh diubah.
- Rumus rekap belum boleh diimplementasikan sebelum dikonfirmasi.
- Tema black–gold dan hierarki UI harus dipertahankan.
- Data development harus berasal dari seeder, bukan hardcode di komponen web.
- Semua route admin harus dilindungi autentikasi dan role.
- Semua endpoint Flutter harus dilindungi Sanctum kecuali login.

# 12. Definition of success

Project dinyatakan berhasil apabila:

- web admin memakai data MySQL, bukan mock array;
- Flutter dapat melakukan login dan sinkronisasi melalui API;
- admin dapat melihat data yang dikirim marketing;
- status persetujuan anggota dapat diperbarui admin dan dibaca Flutter;
- tracking dan riwayat dapat ditampilkan;
- tabel rekap sesuai referensi customer;
- responsive tidak rusak;
- deployment berjalan di cPanel melalui HTTPS;
- test, typecheck, dan build berhasil.
