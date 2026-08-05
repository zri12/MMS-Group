---
title: "UI/UX Guidelines"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.1.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Referensi yang disetujui

- Web Admin: https://ui-mms-group-web-admin.vercel.app/
- Aplikasi Marketing: https://mms-group-ui.vercel.app/

# Konsep visual

**Modern Calm Black–Gold**

Karakter:

- modern;
- sederhana;
- profesional;
- tenang;
- premium;
- mudah dipindai;
- tidak ramai;
- nyaman digunakan dalam durasi lama.

# Hierarki

1. Judul halaman dan konteks hari
2. Ringkasan utama
3. Aksi utama
4. Data/list
5. Informasi sekunder
6. Metadata teknis yang user-friendly

Semua elemen tidak boleh terlihat sama penting.

# Penggunaan gold

Gold digunakan untuk:

- tombol utama;
- menu aktif;
- selected state;
- focus ring;
- ikon aktif;
- aksen angka utama secara terbatas.

Gold tidak digunakan untuk:

- semua judul;
- semua border;
- semua ikon;
- semua card;
- semua badge;
- shadow/glow besar.

# Responsive

## Mobile

Target:

- 360×800
- 375×812
- 390×844
- 393×852
- 412×915
- 430×932

Aturan:

- padding horizontal 16 px;
- card ringkasan dua kolom;
- form satu kolom;
- bottom navigation lima item;
- filter lanjutan melalui bottom sheet;
- tabel rekap tetap tabel dan menggunakan horizontal scroll;
- halaman detail menyembunyikan bottom navigation bila diperlukan;
- tidak ada horizontal overflow pada body.

## Tablet

Target:

- 768×1024
- 820×1180

Layout tidak boleh sekadar mobile yang dilebarkan. Gunakan grid yang proporsional.

## Desktop

Target:

- 1280×720
- 1366×768
- 1440×900
- 1920×1080

Web admin dioptimalkan untuk laptop/PC. Sidebar tetap dan tabel lengkap tampil. Konten sangat lebar memakai max-width yang masuk akal.

# Header

- Satu header per halaman.
- Jangan menampilkan AppHeader dan page header ganda.
- Mobile: tombol menu, logo kecil, judul halaman, tanggal/hari, satu action.
- Desktop: breadcrumb/nama halaman, subtitle singkat, konteks hari, profil.

# Sidebar

Kelompok:

- Menu Utama
- Data
- Laporan
- Aktivitas
- Akun

Active state menggunakan gold-soft dan indikator kecil. Sidebar mobile harus memiliki backdrop dan body scroll lock.

# Bottom navigation

Menu:

- Dashboard
- Marketing
- Harian
- Tracking
- Profil

Ikon aktif berada dalam circle gold. Label tidak boleh dipampatkan dengan `scale-x`. Subhalaman Data Prospek, Anggota, Laporan, Jadwal, dan Rekap dipetakan ke Harian atau bottom nav disembunyikan pada detail.

# Card

- Primary card: ringkasan penting.
- Secondary card: list/data rutin.
- Flat section: detail informasi.

Jangan membungkus semua informasi dalam card.

# Form

- Label jelas.
- Input minimal 44 px.
- Focus state gold lembut.
- Error message dekat field.
- Nominal memakai keyboard numeric pada Flutter.
- Field panjang dikelompokkan berdasarkan section.

# Tables

- Desktop: semantic HTML table, sticky header jika stabil.
- Mobile: tabel rekap tetap tabel horizontal.
- Tabel daftar biasa boleh berubah menjadi card di mobile.
- Scroll horizontal hanya pada wrapper, bukan body.

# Maps

- Leaflet.js + OpenStreetMap sebagai basemap.
- Map normal tidak fixed/sticky.
- Fullscreen melalui Alpine.js atau Blade + Livewire event.
- Map tidak boleh tampil di atas sidebar/modal.
- Tombol fullscreen dan fit marker.
- Loading, empty, dan error state wajib.

# Animasi

- 160–220 ms ease-out.
- Tidak ada bounce, glow, looping, atau scaling besar.
- Animasi hanya untuk feedback.

# Accessibility

- Touch target minimal 44×44.
- Focus-visible jelas.
- Status tidak hanya dibedakan warna.
- Gambar memiliki alt.
- Dialog menggunakan aria-modal.
- Escape menutup modal/sheet.
- Tabel memakai caption dan scope.
