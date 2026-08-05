---
title: "Profil Admin"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Profil Admin

## Route

`GET/PUT /admin/profile`

## Tujuan

Menampilkan dan memperbarui profil admin.

## Data yang ditampilkan

- Avatar
- Nama
- Username/email
- Role

## Interaksi

- Edit profil
- Ubah password
- Logout

## Tampilan mobile

- Profile header dan action list

## Tampilan desktop

- Form sederhana, tidak banyak card

## Aturan bisnis

- Password lama diverifikasi
- Session tetap aman

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menampilkan token atau password

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
