---
title: "Rekap Operasional"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Rekap Operasional

## Route

`GET /admin/operational-recaps?day=Senin`

## Tujuan

Menampilkan format tabel rekap customer.

## Data yang ditampilkan

- No
- Hari
- Tanggal
- MG
- Anggota L/M/K/S
- Target
- Drop
- Storting
- %
- Sirkulasi
- Diikuti Oleh
- Kas Pagi

## Interaksi

- Pilih hari
- Horizontal scroll
- Perbesar tabel
- Tutup fullscreen

## Tampilan mobile

- Tabel yang sama, horizontal scroll, indikator geser

## Tampilan desktop

- Satu tabel utuh, header bertingkat, meta tiga kolom

## Aturan bisnis

- Tidak ada card/tab pengganti
- Tidak ada rumus sebelum konfirmasi
- M01–M13 satu row per marketing

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menambah kolom Marketing/Kode/Aksi pada format kertas
- Jangan rumus dummy

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
