---
title: "Login Admin"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---

# Login Admin

## Route

`GET /login · POST /login`

## Tujuan

Mengautentikasi administrator ke web admin.

## Data yang ditampilkan

- Logo KSP
- Nama sistem
- Username/email
- Password
- Pesan validasi

## Interaksi

- Masuk
- Tampilkan/sembunyikan password

## Tampilan mobile

- Form satu kolom
- Logo dan nama perusahaan jelas
- Tidak ada teks akun marketing

## Tampilan desktop

- Centered card atau split layout sederhana
- Tidak ada dekorasi berlebihan

## Aturan bisnis

- Hanya admin aktif dapat masuk
- Session regeneration
- Rate limit login

## State wajib

- Loading
- Empty
- Error
- Success/feedback tindakan
- Unauthorized bila relevan

## Jangan diubah

- Jangan menampilkan kredensial demo di production
- Jangan menyebut APK atau marketing pada instruksi login

## Acceptance criteria

- Halaman mengikuti design tokens.
- Tidak ada horizontal overflow body.
- Route dan tombol bekerja.
- Data berasal dari database melalui controller, view model, atau Livewire.
- Filter dapat direset.
- Responsive diuji pada 390 px dan 1366 px.
- Tidak ada istilah teknis seperti mock/API/backend pada UI.
