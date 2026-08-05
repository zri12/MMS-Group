---
title: "Dashboard"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Dashboard

## Route

`GET /admin/dashboard?day=Senin`

## Tujuan

Memberikan ringkasan operasional dan akses cepat.

## Data yang ditampilkan

- Total Anggota
- Total Target
- Total Drop
- Total Storting
- Selected day
- Prospek Baru
- Kunjungan Hari Ini
- Menunggu Sinkronisasi
- Status Marketing
- Rekap ringkas

## Interaksi

- Pilih hari
- Buka Tracking
- Buka Data Anggota
- Buka Laporan Operasional
- Buka Rekap
- Buka Prospek/Kunjungan

## Tampilan mobile

- Empat summary card dua kolom
- Hari dua kolom
- Status marketing horizontal carousel

## Tampilan desktop

- Empat summary card sejajar
- Sidebar
- Rekap ringkas

## Aturan bisnis

- Data harian mengikuti day query
- Empat total utama tetap
- Tidak ada Foto Bukti Transfer

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menambah kartu utama tanpa persetujuan
- Jangan hardcode total di komponen web

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
