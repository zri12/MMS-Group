---
title: "Data Marketing"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Data Marketing

## Route

`GET /admin/marketing dan route CRUD terkait`

## Tujuan

Mengelola akun dan profil marketing.

## Data yang ditampilkan

- Kode
- Nama
- Username
- Nomor HP
- Area
- Foto
- Status akun
- Hari kerja
- Status tracking

## Interaksi

- Tambah
- Edit
- Detail
- Reset password
- Aktif/nonaktif

## Tampilan mobile

- Card list
- Search dan filter ringkas
- Action menu

## Tampilan desktop

- Tabel/list dengan pagination
- Action menu tiga titik

## Aturan bisnis

- Kode/username unik
- Inactive tidak login
- Data historis dipertahankan

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menghapus marketing beserta history
- Jangan menampilkan password

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
