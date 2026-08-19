---
title: "Architecture Decision Log"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.5.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# DEC-001 — Backend dan web admin satu project

- Status: Accepted
- Keputusan: Laravel menangani backend, REST API, dan web admin dalam satu project.
- Alasan: deployment lebih sederhana, session auth mudah, tidak ada duplikasi backend.

# DEC-002 — Flutter untuk aplikasi marketing

- Status: Accepted
- Keputusan: aplikasi marketing menggunakan Flutter.
- Alasan: satu codebase Android, UI telah disetujui, dukungan SQLite/GPS.

# DEC-003 — MySQL dan SQLite

- Status: Accepted
- Keputusan: MySQL server, SQLite perangkat.
- Alasan: cocok cPanel dan offline-first.
- Larangan: Flutter tidak mengakses MySQL langsung.

# DEC-004 — Laravel Sanctum

- Status: Accepted
- Keputusan: token mobile menggunakan Sanctum.
- Alasan: sederhana dan resmi untuk Laravel.

# DEC-005 — Inertia React

- Status: **Superseded oleh DEC-013**
- Keputusan lama: web admin memakai Inertia + React + TypeScript.
- Digantikan oleh: Blade + Livewire (lihat DEC-013).

# DEC-006 — Polling tracking

- Status: Accepted untuk versi awal
- Keputusan: web admin melakukan polling, bukan WebSocket.
- Alasan: kompatibel shared hosting cPanel.

# DEC-007 — OpenStreetMap

- Status: Accepted untuk versi awal
- Keputusan: Leaflet.js + OpenStreetMap untuk web admin; flutter_map + OpenStreetMap untuk aplikasi marketing.
- Catatan: penyedia tile production perlu mengikuti kebijakan penggunaan.

# DEC-008 — Bukti transaksi tidak digunakan

- Status: Superseded by DEC-021
- Keputusan lama: tidak ada Bukti Kunjungan lama, Bukti Transaksi, atau foto transfer.
- Digantikan oleh: dua kategori lampiran pada Laporan Operasional (lihat DEC-021).

# DEC-009 — Rekap sebagai tabel utuh

- Status: Accepted
- Keputusan: tabel rekap tetap satu table pada semua viewport.
- Alasan: harus mengikuti lembar referensi customer.

# DEC-010 — Rumus rekap ditunda

- Status: Accepted
- Keputusan: nilai rekap disimpan eksplisit sampai rumus dikonfirmasi.
- Larangan: tidak memakai rumus dummy.

# DEC-011 — Nominal sebagai BIGINT rupiah

- Status: Accepted
- Keputusan: uang disimpan integer rupiah.
- Alasan: menghindari floating point.

# DEC-012 — Data development melalui seeder

- Status: Accepted
- Keputusan: tidak ada mock array production.
- Alasan: konsistensi web/API/testing.

# DEC-013 — Web Admin menggunakan Blade dan Livewire

- Status: Accepted
- Keputusan: web admin dibangun menggunakan Laravel Blade sebagai template engine dan Laravel Livewire untuk interaksi dinamis.
- React dan Inertia.js tidak digunakan.
- TypeScript dan TSX hanya menjadi referensi visual, tidak disalin secara mekanis.
- Alpine.js digunakan melalui Livewire (sudah termasuk), tidak dipasang terpisah.
- Leaflet.js digunakan untuk peta, menggantikan React Leaflet.
- Alasan:
  - arsitektur lebih sederhana dan dominan PHP;
  - mudah dikelola oleh tim PHP;
  - deployment cPanel lebih sederhana tanpa Node.js server;
  - tidak membutuhkan SPA atau build TypeScript terpisah;
  - Livewire menyediakan interaksi dinamis tanpa kompleksitas React.
- Tanggal: 2026-07-28.

# DEC-014 - Relasi canonical Prospect dan Member

- Status: Accepted
- Keputusan: relasi fisik Prospect ke Member hanya menggunakan `members.source_prospect_id`.
- `prospects.linked_member_id` tidak dibuat sebagai kolom database.
- `members.source_prospect_id` nullable, unique, foreign key ke `prospects.id`, dan `nullOnDelete`.
- `Prospect::member()` adalah hasOne melalui `members.source_prospect_id`.
- `Member::sourceProspect()` adalah belongsTo ke `prospects.id`.
- `linked_member_id` boleh ditampilkan kemudian hanya sebagai field response turunan dari `prospect.member?->id`.
- Alasan:
  - menghindari circular foreign key;
  - menjaga satu sumber kebenaran relasi prospek menjadi anggota;
  - lebih mudah divalidasi dengan unique nullable constraint.
- Tanggal: 2026-07-29.

# DEC-015 - Web admin memakai session auth username-only

- Status: Accepted
- Keputusan: web admin memakai Laravel session auth guard `web` dengan login identifier `username`.
- Route admin dilindungi middleware `auth`, `active`, dan `admin`.
- Ubah password admin memakai route `PUT /admin/password` dengan name `admin.password.update`.
- Registrasi, forgot password, reset password, email verification, dan remember me tidak dibuat pada fase ini.
- Alasan:
  - akun admin dibuat melalui proses development/admin yang terkendali;
  - mencegah marketing login ke web admin;
  - menjaga scope auth web terpisah dari Sanctum mobile.
- Tanggal: 2026-07-29.

# DEC-016 - Marketing API auth memakai Sanctum ability khusus

- Status: Accepted
- Keputusan: API marketing memakai Sanctum personal access token dengan ability `marketing-mobile`.
- Login API memakai `username`, `password`, dan `device_name`.
- Token hanya dibuat untuk role `marketing` aktif yang memiliki `MarketingProfile`.
- Logout API hanya menghapus current access token.
- Alasan:
  - memisahkan session web admin dari token mobile;
  - membatasi token mobile pada kemampuan marketing;
  - mendukung multi-device tanpa refresh token atau logout semua perangkat pada fase ini.
- Tanggal: 2026-07-29.

# DEC-017 - Source ZIP memakai forward slash

- Status: Accepted
- Keputusan: script packaging memakai `ZipArchive` dan menormalisasi entry ZIP menjadi `/`.
- Alasan: ZIP source harus aman diekstrak pada Windows, Linux, dan macOS.
- Tanggal: 2026-07-29.

# DEC-018 - MMS Web Admin Uses a Reusable Blade Design System

- Status: Accepted
- Keputusan: Web Admin memakai Blade anonymous components, Tailwind CSS design tokens, Alpine.js local interactions, attribute forwarding yang Livewire-compatible, visual Modern Calm Black-Gold, dan component catalog khusus local/testing.
- Tidak memakai third-party UI library.
- Alasan:
  - menjaga konsistensi visual tanpa membawa dependency besar;
  - memudahkan reuse pada modul admin berikutnya;
  - memastikan form, feedback, table, modal, dan navigation punya accessibility dasar;
  - menjaga arsitektur Laravel Blade/Livewire tetap ringan.
- Tanggal: 2026-07-29.

# DEC-019 - Business Modules Use Action Classes and API Resources

- Status: Accepted
- Keputusan: Modul Data Marketing, Prospek, Anggota, Laporan, dan Tracking memindahkan operasi tulis sensitif ke Action class dan memakai API Resource untuk response mobile.
- Alasan:
  - menjaga controller tetap tipis;
  - memudahkan transaction, ownership check, dan idempotency;
  - mencegah model mentah keluar dari API.
- Tanggal: 2026-07-29.

# DEC-020 - Finished Tracking Session Uses Offline Status

- Status: Accepted
- Keputusan: Stop tracking mengubah status sesi menjadi `Offline`.
- Alasan: enum `TrackingStatus` belum memiliki nilai `Selesai`. Menambah enum baru tanpa perubahan dokumen bisnis tidak dilakukan pada fase ini.
- Catatan: stop ulang idempotent dan tidak mengubah nilai final.
- Tanggal: 2026-07-29.

# DEC-021 - Customer Revision for Prospect, Recap, and Operational Attachments

- Status: Accepted
- Keputusan:
  - Prospek/Konsumen dan Anggota memakai daftar, route, API resource, dan tabel
    yang berbeda; relasi opsional tetap melalui `members.source_prospect_id`.
  - Laporan Tunai dan Rekap Target tidak dibuat sebagai fitur atau endpoint
    marketing. Target tetap berada pada Laporan Operasional milik marketing;
    Rekap Operasional tetap merupakan halaman web admin.
  - Foto Pencairan dan Foto Bukti Transfer menjadi dua kategori lampiran opsional
    pada Laporan Operasional. Satu kategori hanya boleh ada satu kali pada satu
    laporan.
- Alasan: menerapkan revisi customer yang diterima pada 2026-08-15 tanpa
  menduplikasi data atau membuat modul keuangan baru.
- Tanggal: 2026-08-15.
