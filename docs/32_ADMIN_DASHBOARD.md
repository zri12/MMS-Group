---
title: "Admin Dashboard"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Admin Dashboard

## Route

| Method | URL | Name |
|---|---|---|
| GET | `/admin` | `admin.home` |
| GET | `/admin/dashboard` | `admin.dashboard` |

## Data

Dashboard memakai data database nyata:

- total anggota dan anggota disetujui;
- total target, drop, dan storting dari laporan operasional;
- prospek baru;
- kunjungan hari ini;
- status sinkronisasi;
- jadwal hari ini;
- status tracking marketing;
- laporan operasional terbaru;
- rekap ringkas dari tabel rekap, tanpa menghitung rumus baru.

## Filter

- tanggal;
- hari;
- marketing.

## Test

- `DashboardTest`

## Non-Scope

Dashboard tidak menghitung percentage atau rumus rekap yang belum dikonfirmasi.
