---
title: "Tracking Lokasi"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Tracking Lokasi

## Route

`GET /admin/tracking`

## Tujuan

Melihat posisi terakhir dan kondisi tracking marketing.

## Data yang ditampilkan

- Map
- Marker
- Marketing
- Status
- Lokasi terakhir
- Waktu update
- Hari
- Area

## Interaksi

- Filter
- Refresh
- Pilih marketing
- Fullscreen
- Fit marker
- Detail tracking

## Tampilan mobile

- Filter ringkas
- Peta 300–345 px
- Bottom sheet
- List status

## Tampilan desktop

- Peta besar
- Filter lengkap
- List berdampingan

## Aturan bisnis

- Polling configurable
- Map tidak menembus overlay
- Tidak dijadwalkan dibedakan

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan membuat map normal fixed/sticky
- Jangan menggunakan WebSocket versi awal

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
