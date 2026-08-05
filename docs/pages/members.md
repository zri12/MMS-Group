---
title: "Data Anggota dan Detail Anggota"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Data Anggota dan Detail Anggota

## Route

`GET /admin/members · GET /admin/members/{member} · PATCH approval`

## Tujuan

Melihat data anggota dan memproses persetujuan.

## Data yang ditampilkan

- Identitas
- Nomor anggota/pinjaman
- Pinjaman
- Angsuran
- Asuransi
- Jaminan
- Foto
- Lokasi
- Marketing
- Status

## Interaksi

- Filter
- Detail
- Disetujui
- Ditolak
- Lihat marketing
- Lihat lokasi

## Tampilan mobile

- Card list
- Section detail
- Sticky primary action bila perlu

## Tampilan desktop

- Tabel list
- Foto dan informasi terstruktur

## Aturan bisnis

- Status awal Menunggu
- Approval hanya admin
- Alasan penolakan mengikuti keputusan final

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan tampilkan bukti transfer
- Jangan gunakan istilah ACC/Belum Diproses

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
