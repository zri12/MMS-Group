---
title: "Jadwal Marketing"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Jadwal Marketing

## Route

`GET/POST/PUT/DELETE /admin/schedules`

## Tujuan

Mengelola agenda kunjungan marketing.

## Data yang ditampilkan

- Marketing
- Prospek/konsumen
- Hari
- Tanggal
- Jam
- Agenda
- Area/resort
- Tujuan
- Catatan
- Status

## Interaksi

- Tambah
- Edit
- Batalkan/Hapus sesuai kebijakan
- Filter

## Tampilan mobile

- Timeline/card list
- Form satu kolom

## Tampilan desktop

- Tabel/list dan form terstruktur

## Aturan bisnis

- Status terpusat
- Marketing Flutter hanya melihat miliknya

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan campur status jadwal dengan status tracking

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
