---
title: "Web Admin Security Parity"
project: "MMS Marketing Monitoring System"
status: "active"
version: "1.0.0"
last_updated: "2026-08-02"
source_of_truth: false
---

# Web Admin Security Parity

## Auth dan Role

Semua route admin tetap berada di middleware:

- `auth`
- `active`
- `admin`

Route Flutter tetap berada di `/api/v1` dan menggunakan Sanctum sesuai kontrak.

## Data Scope

- Admin web dapat melihat data global.
- Marketing API tetap dibatasi pada data milik marketing terautentikasi.
- Perubahan UI tidak membuka route baru tanpa middleware admin.

## Secret Handling

- Password, hash, token, APP_KEY, dan konfigurasi database tidak dicetak dalam dokumentasi atau UI.
- Reset password marketing tetap melalui form admin yang sudah tervalidasi.

## Catatan

Policy class eksplisit belum ditambahkan karena middleware dan controller existing sudah menjaga akses pada scope saat ini. Penambahan policy dapat dilakukan bila kebutuhan authorization makin granular.
