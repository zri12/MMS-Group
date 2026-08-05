---
title: "Web Admin Asset Usage"
project: "MMS Marketing Monitoring System"
status: "active"
version: "1.0.0"
last_updated: "2026-08-02"
source_of_truth: false
---

# Web Admin Asset Usage

## Logo

Logo aplikasi web admin menggunakan file:

`apps/web-admin-api/storage/app/public/LOGO-KSP.jpeg`

File tersebut disajikan melalui public storage link sebagai `storage/LOGO-KSP.jpeg`.

## Foto Reference

Foto marketing dan member dari reference disalin ke:

`apps/web-admin-api/public/profiles`

Pemakaian:

- Marketing `M01` sampai `M13` memakai `profiles/marketing-01.jpg` sampai `profiles/marketing-13.jpg` bila `profile_photo_path` kosong.
- Member memakai `profiles/member-01.jpg` sampai `profiles/member-16.jpg` sebagai fallback visual bila `member_photo_path` kosong.

## Aturan

- File upload user tetap memakai disk `public`.
- Gambar tidak disimpan sebagai base64 atau binary di database.
- Fallback visual tidak mengubah isi tabel.
