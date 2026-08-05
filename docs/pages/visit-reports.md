---
title: "Laporan Kunjungan"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Laporan Kunjungan

## Route

`GET /admin/visit-reports`

## Tujuan

Melihat hasil kunjungan marketing kepada prospek.

## Data yang ditampilkan

- Prospek
- Marketing
- Tujuan
- Hasil
- Status prospek
- Catatan
- Follow-up
- Foto
- Lokasi
- Waktu
- Sinkronisasi

## Interaksi

- Filter
- Detail
- Lihat prospek
- Lihat marketing
- Lihat lokasi
- Perbesar foto

## Tampilan mobile

- Thumbnail dan card list
- Modal foto

## Tampilan desktop

- Tabel dengan thumbnail
- Detail section

## Aturan bisnis

- Terkait satu prospek
- Status prospect konsisten

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menggabungkan dengan bukti transfer atau laporan setoran

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
