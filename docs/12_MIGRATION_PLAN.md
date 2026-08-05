---
title: "Migration Plan"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.1.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Tujuan

Membangun ulang web admin menggunakan Laravel Blade + Livewire berdasarkan referensi UI/UX yang telah disetujui, tanpa membawa kode mock/duplikasi lama.

# Fase 0 — Freeze prototype

- Simpan ZIP/source UI final.
- Simpan link Vercel.
- Simpan screenshot halaman utama.
- Jangan edit source referensi setelah migrasi dimulai.
- Tandai elemen yang memang belum final melalui Open Questions.

# Fase 1 — Fondasi Laravel

1. Buat Laravel 12.
2. Pasang Livewire dan konfigurasi Blade.
3. Pasang Sanctum.
4. Konfigurasi MySQL.
5. Buat layout folder dan struktur views.
6. Buat session auth admin.
7. Buat role middleware/policy.
8. Salin design tokens, logo, dan aset yang benar-benar digunakan.
9. Tambahkan test/build.

# Fase 2 — Database

Urutan migration:

1. users
2. marketing_profiles
3. marketing_work_days
4. prospects
5. members
6. marketing_schedules
7. daily_operational_reports
8. visit_reports
9. tracking_sessions
10. tracking_points
11. operational_recaps
12. operational_recap_rows
13. Sanctum tokens

Lalu:

- model;
- relation;
- enum PHP;
- factory;
- seeder;
- policy.

# Fase 3 — Modul web admin per vertikal

Pola setiap modul:

1. Migration/model sudah ada.
2. Form Request.
3. Service/action bila perlu.
4. Controller (Blade) atau full-page Livewire component.
5. Blade view + Livewire component.
6. Filter dan pagination (Livewire).
7. Authorization (Policy/Gate).
8. Feature test.
9. Responsive test.

Urutan:

1. Login dan layout
2. Data Marketing
3. Dashboard dan Data Harian
4. Prospek
5. Anggota dan approval
6. Laporan Operasional
7. Laporan Kunjungan
8. Jadwal
9. Tracking dan Riwayat
10. Rekap
11. Profil

# Fase 4 — REST API Flutter

Urutan:

1. Auth/profile
2. Schedules
3. Prospects
4. Members
5. Operational reports
6. Visit reports
7. Tracking sessions/points
8. Sync/error handling

Setiap endpoint harus diuji sebelum Flutter terhubung.

# Fase 5 — Flutter per modul

Pola:

1. Model sesuai API Contract.
2. Dio service.
3. Repository.
4. SQLite entity.
5. Provider.
6. UI dari prototype.
7. Offline queue.
8. Integration test manual.
9. Sinkronisasi ke admin.

# Fase 6 — Integrasi

Skenario end-to-end:

- Admin membuat akun M01.
- M01 login Flutter.
- Admin membuat jadwal.
- Jadwal muncul di Flutter.
- M01 membuat prospek.
- Prospek muncul di admin.
- M01 membuat anggota.
- Admin menyetujui.
- Status terlihat di Flutter.
- M01 mengirim laporan.
- Laporan muncul admin.
- Tracking muncul dan memiliki riwayat.
- Rekap tampil sesuai hari.

# Fase 7 — Deployment cPanel

- Hosting check
- Database production
- Build production
- Upload
- Migration
- Storage link
- SSL
- Cron
- Smoke test
- Backup baseline

# Larangan migrasi

- Jangan menyalin AdminApplication.tsx ke Laravel secara mekanis.
- Jangan membawa mock array ke komponen production.
- Jangan membawa node_modules, dist, .git, dan artefak build.
- Jangan membuat route Blade yang tidak terdaftar di routes/admin.php.
- Jangan mencampurkan controller admin dan API dalam satu controller besar.
- Jangan menggunakan React, TypeScript, atau Inertia.js.

# Exit criteria

Setiap fase selesai bila acceptance criteria pada `13_TESTING_ACCEPTANCE.md` terpenuhi.
