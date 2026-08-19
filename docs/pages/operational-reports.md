---
title: "Laporan Operasional Harian"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Laporan Operasional Harian

## Route

`GET /admin/operational-reports`

## Tujuan

Menampilkan data Input Setoran dari Flutter.

## Data yang ditampilkan

- Storting
- Asuransi
- Drop
- Tabungan Keluar
- Target nominal/orang
- Drop Baru/Lanjut
- Catatan
- Marketing
- Resort
- Waktu
- Sinkronisasi
- Foto Pencairan
- Foto Bukti Transfer

## Interaksi

- Filter
- Search
- Detail

## Tampilan mobile

- Card list dengan nominal utama
- Detail key-value sections

## Tampilan desktop

- Tabel horizontal lokal bila perlu
- Pagination

## Aturan bisnis

- Tidak ada nominal negatif
- Field sama persis Data Dictionary

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan tampilkan foto anggota, lokasi setoran, atau Lain-lain.
- Foto Pencairan dan Foto Bukti Transfer hanya tampil sebagai dua kategori
  lampiran pada laporan operasional.

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
