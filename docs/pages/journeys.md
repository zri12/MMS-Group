---
title: "Riwayat Perjalanan"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Riwayat Perjalanan

## Route

`GET /admin/journeys`

## Tujuan

Menampilkan sesi tracking historis.

## Data yang ditampilkan

- Marketing
- Hari/tanggal
- Mulai/selesai
- Durasi
- Jarak
- Kunjungan
- Map route
- Timeline

## Interaksi

- Filter
- Detail
- Lihat marketing
- Fullscreen map

## Tampilan mobile

- Map, summary, timeline sederhana

## Tampilan desktop

- Map dan detail/timeline

## Aturan bisnis

- Data mengikuti session ID
- Tanggal tidak hardcoded

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menampilkan riwayat prospek/marketing lain

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
