---
title: "Rencana Kerja"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Rencana Kerja

## Route

`GET /admin/daily?day=Senin`

## Tujuan

Pusat navigasi data berdasarkan hari.

## Data yang ditampilkan

- Selected day
- Jumlah prospek
- Jumlah anggota
- Jumlah laporan operasional
- Jumlah laporan kunjungan
- Tracking
- Rekap

## Interaksi

- Pilih hari
- Buka tiap modul

Shortcut hanya mengarah ke Tracking, Data Prospek, Data Anggota, Laporan
Operasional, Laporan Kunjungan, dan Rekap Operasional. Laporan Tunai, Rekap
Target, dan Ubah Password tidak menjadi shortcut.

## Tampilan mobile

- Shortcut dua kolom
- Counter ringkas

## Tampilan desktop

- Grid shortcut dan ringkasan

## Aturan bisnis

- Semua counter satu sumber database
- Hari dan tanggal konsisten

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan memakai data mock berbeda antar card

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
