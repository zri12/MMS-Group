---
title: "Glossary"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Istilah

**Admin**  
Pengguna web admin yang mengelola dan memantau seluruh marketing.

**Marketing**  
Petugas lapangan pengguna aplikasi Flutter.

**Prospek/Konsumen**  
Calon anggota atau konsumen yang dicatat marketing dan belum menjadi anggota.
Prospek tampil pada daftar terpisah dari Data Anggota.

**Anggota**  
Data anggota/pengajuan yang memiliki nomor anggota dan data pinjaman.

**Laporan Operasional Harian / Input Setoran**  
Laporan nominal operasional marketing: storting, asuransi, drop, tabungan keluar,
target, dan rincian drop. Laporan ini dapat memiliki lampiran Foto Pencairan dan
Foto Bukti Transfer.

**Foto Pencairan / Foto Bukti Transfer**

Dua kategori lampiran yang berbeda pada satu Laporan Operasional Harian. Keduanya
bukan menu, laporan tunai, atau endpoint marketing yang berdiri sendiri.

**Laporan Kunjungan**  
Laporan aktivitas kunjungan kepada prospek, berisi tujuan, hasil, status, foto, dan lokasi.

**Jadwal Marketing**  
Agenda kunjungan yang dibuat admin dan dilihat marketing.

**Tracking Session**  
Satu rentang tracking dari mulai sampai selesai.

**Tracking Point**  
Satu titik koordinat dalam tracking session.

**Riwayat Perjalanan**  
Ringkasan tracking session beserta route dan timeline.

**Sinkronisasi**  
Proses mengirim data lokal Flutter ke server.

**local_uuid**  
ID unik dari perangkat untuk mencegah duplikasi saat retry.

**Storting**  
Istilah nominal operasional sesuai form customer. Definisi bisnis rinci mengikuti customer.

**Drop**  
Istilah nominal sesuai form customer.

**Rekap Operasional**  
Tabel rekap harian dengan format customer.

**MG, L/M/K/S, Sirkulasi, Kas Pagi**  
Istilah pada tabel rekap yang maknanya masih pending confirmation.

**Blade**  
Template server-side Laravel untuk web admin.

**Livewire**  
Komponen interaktif Laravel untuk web admin tanpa SPA terpisah.

**Inertia.js**  
Keputusan lama yang sudah digantikan. Tidak digunakan pada project aktif.

**Sanctum**  
Autentikasi token Laravel untuk Flutter.

**Polling**  
Web meminta update lokasi secara berkala.

**Foreground Service**  
Layanan Android yang berjalan dengan notifikasi saat tracking GPS aktif.
