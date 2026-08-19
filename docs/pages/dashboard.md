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

- Drop
- Storting
- Sirkulasi
- Target Masuk
- Target Keluar
- Total Target
- Anggota Masuk
- Anggota Keluar
- Total Anggota
- Foto Pencairan
- Foto Bukti Transfer
- Kartu PDL

## Interaksi

- Buka detail PDL
- Buka Tracking PDL
- Buka Laporan Operasional

## Tampilan mobile

- Metrik bertumpuk agar label dan nilai tetap terbaca
- Panel foto dua kategori

## Tampilan desktop

- Metrik tiga kolom
- Panel foto dua kategori
- Kartu PDL dan aksi Tracking PDL

## Aturan bisnis

- Data metrik mengikuti tanggal dan hari terpilih.
- Anggota Masuk dan Anggota Keluar menampilkan `Belum tersedia` bila data
  operasional belum mengirim nilainya; tidak ada rumus turunan.
- Laporan Tunai tidak ditampilkan di dashboard.

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menambah kartu utama tanpa persetujuan.
- Jangan hardcode total di komponen web.
- Jangan membuat panel foto sebagai modul atau route mandiri.

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
