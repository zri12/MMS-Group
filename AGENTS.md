---
title: "AI and Developer Working Agreement"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Instruksi wajib untuk Codex, AI coding assistant, dan developer

File ini wajib dibaca sebelum melakukan perubahan pada project.

## Prinsip utama

1. Dokumentasi dalam folder `docs/` adalah sumber kebenaran.
2. Jangan mengubah desain, nama menu, field, status, route, atau struktur tabel rekap tanpa dokumen perubahan.
3. Jangan membuat asumsi bisnis untuk kolom yang belum dikonfirmasi.
4. Jangan menghidupkan kembali modul yang telah dihapus, terutama:
   - Bukti Kunjungan versi lama
   - Bukti Transaksi
   - Foto Bukti Transfer
   - Laporan Tunai
   - Foto Pencairan
5. Jangan menggunakan mock data di dalam komponen setelah migrasi Laravel. Data simulasi hanya berasal dari seeder.
6. Jangan membuat backend terpisah dari web admin. Backend, web admin, dan REST API berada dalam satu project Laravel.
7. Flutter adalah project terpisah dan hanya mengakses server melalui REST API.
8. Flutter tidak boleh terhubung langsung ke MySQL.

## Stack final project aktif

Project Laravel aktif menggunakan:

- Laravel 12;
- Blade;
- Livewire;
- Tailwind CSS;
- Alpine.js bawaan Livewire;
- Vite;
- JavaScript biasa untuk integrasi browser;
- Leaflet.js + OpenStreetMap untuk peta;
- Laravel REST API;
- Sanctum;
- MySQL;
- Flutter sebagai project terpisah.

Aturan stack:

1. Jangan membuat React.
2. Jangan membuat TypeScript.
3. Jangan membuat TSX.
4. Jangan memasang Inertia.
5. Jangan membuat React Router.
6. Jangan membuat React Context.
7. Jangan menggunakan React Leaflet.
8. Jangan memindahkan source referensi UI secara langsung.
9. Bangun ulang tampilan menggunakan Blade.
10. Gunakan Livewire untuk interaksi server-driven.
11. Gunakan Blade Components untuk elemen reusable.
12. Gunakan Alpine hanya untuk interaksi browser ringan.
13. Gunakan JavaScript biasa untuk Leaflet.
14. Gunakan controller Web Admin untuk halaman Blade.
15. Gunakan API Controller untuk Flutter.
16. Gunakan Services dan Actions untuk logika bersama.
17. Folder referensi UI bersifat read-only.
18. Dokumentasi menjadi sumber kebenaran.
19. Jangan menebak aturan dalam Open Questions.
20. Jalankan test setelah perubahan.

## Checklist sebelum coding

- [ ] Baca `00_MASTER_SPEC.md`
- [ ] Baca spesifikasi halaman terkait di `docs/pages/`
- [ ] Baca `08_DATA_DICTIONARY.md`
- [ ] Baca `10_BUSINESS_RULES.md`
- [ ] Baca `09_API_CONTRACT.md` bila mengubah API
- [ ] Baca `07_DATABASE_SCHEMA.md` bila mengubah database
- [ ] Pastikan tidak ada open question yang dilanggar

## Aturan saat menemukan konflik

Bila source UI lama berbeda dengan dokumentasi:

- Ikuti dokumentasi.
- Jangan menyalin field lama yang sudah tidak digunakan.
- Jangan menghapus kebutuhan yang tertulis di dokumentasi.
- Catat konflik pada laporan pengerjaan.

Bila dua dokumen bertentangan:

- Ikuti urutan prioritas pada `README.md`.
- Jangan memilih sendiri tanpa mencatat konflik.
- Tambahkan pertanyaan pada `22_OPEN_QUESTIONS.md`.

## Aturan perubahan UI

- Pertahankan konsep **Modern Calm Black–Gold**.
- Jangan menambah gradient, glow, animasi, grafik, atau card dekoratif tanpa kebutuhan.
- Gold hanya untuk aksi utama, elemen aktif, selected state, dan focus state.
- Tidak boleh ada horizontal overflow pada body.
- Tabel rekap harus tetap satu tabel utuh pada mobile dan desktop.
- Peta tidak boleh menembus sidebar, modal, bottom sheet, atau fullscreen overlay.

## Aturan perubahan database

- Migration harus reversible.
- Foreign key dan index harus ditentukan.
- Nama kolom menggunakan `snake_case`.
- Nominal uang disimpan sebagai integer satuan rupiah atau decimal yang konsisten. Project ini menggunakan **BIGINT unsigned dalam rupiah**.
- Latitude dan longitude menggunakan decimal presisi.
- Status disimpan sebagai string dan divalidasi melalui PHP Enum/Form Request, bukan MySQL ENUM.
- File gambar tidak disimpan sebagai base64 atau binary di MySQL.

## Aturan API

- Semua endpoint Flutter berada di `/api/v1`.
- Autentikasi menggunakan Laravel Sanctum.
- Semua respons menggunakan envelope standar.
- Validasi wajib melalui Form Request.
- Output menggunakan API Resource.
- Endpoint create dari mode offline harus menerima `local_uuid` untuk mencegah duplikasi.
- Tracking point dikirim secara batch untuk mengurangi beban server.

## Aturan laporan pengerjaan

Setelah menyelesaikan tugas, laporkan:

1. Dokumen yang dibaca.
2. File yang dibuat/diubah/dihapus.
3. Migration, route, controller, request, resource, dan test yang dibuat.
4. Perubahan UI.
5. Hasil test, typecheck, dan build.
6. Bagian yang belum selesai.
7. Pertanyaan bisnis yang masih terbuka.

## Larangan

- Jangan menampilkan istilah `mock`, `dummy`, `API`, `backend`, atau `APK` di UI pengguna.
- Jangan membuat button tanpa aksi.
- Jangan membuat route yang tidak dapat diakses.
- Jangan mengulang model/type yang sama di banyak file.
- Jangan membuat query Eloquent di file Blade atau komponen Livewire yang seharusnya ditangani controller/service/action.
- Jangan memuat seluruh data tanpa pagination pada halaman daftar produksi.
- Jangan mengubah rumus tabel rekap sebelum customer mengonfirmasi.
