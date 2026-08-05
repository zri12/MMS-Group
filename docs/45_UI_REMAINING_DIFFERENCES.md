---
title: "UI Remaining Differences"
project: "MMS Marketing Monitoring System"
status: "active"
version: "1.0.0"
last_updated: "2026-08-02"
source_of_truth: false
---

# UI Remaining Differences

Perbedaan yang masih tersisa secara jujur:

- Browser screenshot comparison belum tersedia dari sesi ini.
- Detail Marketing belum menyimpan tab aktif pada query string.
- Ringkasan Detail Marketing belum memiliki mini map khusus.
- Tab Tracking pada Detail Marketing masih berupa daftar sesi, belum map penuh per tab.
- Rencana Kerja masih berfokus pada counters dan shortcuts, belum activity feed kaya seperti reference.
- Jadwal, Prospek, Anggota, Laporan Operasional, dan Laporan Kunjungan masih memakai pola list/card standar yang perlu polish lanjutan.
- Rekap Operasional masih memakai alur index lalu detail, bukan selector hari langsung pada satu pengalaman utama.
- Profil Admin masih mengikuti form standar design system.

Yang sudah diperbaiki:

- Clean npm install dan Vite build berhasil.
- Class map/sheet/card kritis dipindah ke semantic CSS.
- Data Marketing memakai Target, Drop, Storting sebagai mini-stat utama.
- Upload foto marketing berfungsi.
- Tracking polling tidak lagi memakai full-page reload.
- Feed tracking terlindungi middleware admin.
