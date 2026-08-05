---
title: "Data Prospek dan Detail Prospek"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Data Prospek dan Detail Prospek

## Route

`GET /admin/prospects · GET /admin/prospects/{prospect}`

## Tujuan

Memantau konsumen/calon anggota yang dicatat marketing.

## Data yang ditampilkan

- Nama
- HP
- Alamat
- Usaha
- Status
- Hasil awal
- Catatan
- Resort
- Lokasi
- Marketing
- Riwayat kunjungan
- Anggota terkait

## Interaksi

- Filter
- Search
- Detail
- Lihat marketing
- Lihat riwayat
- Lihat lokasi
- Lihat anggota

## Tampilan mobile

- Card list
- Bottom sheet filter
- Detail dalam flat sections

## Tampilan desktop

- Tabel daftar
- Detail dua kolom bila sesuai

## Aturan bisnis

- Admin read-only pada scope awal
- Riwayat hanya prospect terkait

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan tampilkan seluruh laporan hari ketika membuka riwayat satu prospek

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
