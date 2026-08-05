---
title: "Scope and Modules"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Scope global

Sistem hanya mencakup monitoring aktivitas marketing KSP MMS. Fitur harus mengikuti UI yang telah disetujui dan tidak berkembang menjadi sistem akuntansi, payroll, atau CRM kompleks tanpa perubahan scope tertulis.

# 1. Dashboard

## In scope

- Total Anggota
- Total Target
- Total Drop
- Total Storting
- Filter/pilihan hari Senin–Sabtu
- Shortcut Tracking
- Shortcut Data Anggota
- Shortcut Laporan Operasional
- Shortcut Rekap
- Aktivitas pemasaran: Prospek Baru, Kunjungan Hari Ini, Menunggu Sinkronisasi
- Status marketing
- Rekap ringkas

## Out of scope

- Grafik kompleks
- Prediksi AI
- Widget finansial di luar field yang disepakati
- Foto bukti transfer

# 2. Data Marketing

- Daftar marketing
- Pencarian dan filter
- Tambah marketing
- Edit marketing
- Foto profil
- Username
- Kode marketing
- Nomor HP
- Area/resort
- Hari kerja
- Aktif/nonaktif akun
- Reset password
- Detail marketing
- Tab Jadwal, Tracking, Prospek, Anggota, Setoran, Kunjungan, Riwayat

# 3. Data Harian

- Pilihan hari
- Jumlah data per hari
- Akses ke Tracking
- Akses Data Prospek
- Akses Data Anggota
- Akses Laporan Operasional
- Akses Laporan Kunjungan
- Akses Rekap Operasional

# 4. Data Prospek

- Daftar dan detail
- Filter hari, marketing, status, resort
- Pencarian
- Lokasi
- Marketing penanggung jawab
- Riwayat kunjungan
- Relasi ke anggota apabila telah dikonversi

Admin tidak menambah prospek melalui web pada scope awal. Prospek berasal dari Flutter.

# 5. Data Anggota

- Daftar dan detail
- Foto anggota
- Informasi pinjaman
- Lokasi input
- Marketing penginput
- Status Menunggu, Disetujui, Ditolak
- Admin dapat memperbarui status persetujuan
- Riwayat perubahan status secara minimal melalui kolom waktu dan admin pemroses

Admin tidak mengubah data identitas anggota pada scope awal, kecuali kebutuhan koreksi disetujui kemudian.

# 6. Laporan Operasional Harian

Field final:

- Storting
- Asuransi
- Drop
- Tabungan Keluar
- Target Lama: nominal dan jumlah orang
- Target Masuk: nominal dan jumlah orang
- Target Keluar: nominal dan jumlah orang
- Jumlah Target: nominal dan jumlah orang
- Drop Baru
- Drop Lanjut
- Catatan
- Marketing
- Resort
- Hari, tanggal, waktu
- Status sinkronisasi

Tidak mencakup foto anggota, bukti transfer, lokasi setoran, atau field Lain-lain.

# 7. Laporan Kunjungan

- Prospek
- Marketing
- Tujuan kunjungan
- Hasil kunjungan
- Status prospek setelah kunjungan
- Catatan
- Tanggal follow-up opsional
- Satu foto kunjungan
- Caption foto
- Lokasi
- Tanggal, waktu
- Status sinkronisasi

# 8. Jadwal Marketing

- Admin membuat dan mengedit jadwal
- Marketing melihat jadwal sendiri
- Hari, tanggal, jam mulai/selesai
- Prospek/konsumen
- Agenda
- Area/resort
- Status: Belum Dikunjungi, Berlangsung, Selesai, Dibatalkan

# 9. Tracking dan Riwayat

- Start/stop sesi tracking dari Flutter
- Penyimpanan titik lokasi
- Posisi terakhir marketing
- Status Aktif, Offline, Belum Mulai, GPS Tidak Aktif, Tidak Dijadwalkan
- Map web admin
- Filter
- Fullscreen
- Riwayat perjalanan
- Timeline titik
- Jarak dan jumlah kunjungan bila tersedia

# 10. Rekap Operasional

- Satu tabel utuh
- Header bertingkat sesuai referensi
- No, Hari, Tanggal
- MG
- Anggota L/M/K/S
- Target Lalu/MSK/KLR/S
- Drop Lalu/Kini/Total
- Storting Lalu/Kini/Total
- Persentase
- Sirkulasi Lalu
- Sirkulasi Sekarang
- Diikuti Oleh
- Kas Pagi
- Horizontal scroll mobile
- Fullscreen table

Rumus belum termasuk scope sampai customer mengonfirmasi.

# 11. Profil Admin

- Foto/avatar
- Nama
- Username/email
- Role
- Ubah password
- Logout

# Out of scope global

- Bukti transfer
- Payment gateway
- Payroll
- Akuntansi
- Notifikasi WhatsApp
- Ekspor/cetak final
- Multi tenant/multi KSP
- Realtime WebSocket
- AI analytics
- Integrasi pihak ketiga
