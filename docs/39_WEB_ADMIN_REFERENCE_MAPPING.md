---
title: "Web Admin Reference Mapping"
project: "MMS Marketing Monitoring System"
status: "active"
version: "1.0.0"
last_updated: "2026-08-02"
source_of_truth: false
---

# Web Admin Reference Mapping

Dokumen ini mencatat mapping reference React/TSX ke Laravel Blade. Source of truth bisnis tetap dokumen utama pada folder `docs/`.

| Reference | Laravel | Catatan |
|---|---|---|
| `DashboardPage` | `admin.dashboard` | Data berasal dari database dan filter tanggal/hari/marketing. |
| `MarketingDataPages` list | `admin.marketing.index` | Card grid memakai foto fallback dari `public/profiles`. |
| `MarketingDetailPage` | `admin.marketing.show` | Delapan tab: Ringkasan, Jadwal, Tracking, Prospek, Anggota, Setoran, Kunjungan, Riwayat. |
| `DailyHub` | `admin.daily.index` | Label user-facing menjadi `Rencana Kerja`; route tetap `/admin/daily`. |
| `SchedulePage` | `admin.schedules.*` | CRUD Laravel tetap dipakai. |
| `TrackingMap` | `admin.tracking.index/show` | Leaflet + OpenStreetMap, avatar marker, fit marker, fullscreen. |
| `JourneyPages` | `admin.journeys.index/show` | Detail perjalanan memakai route sendiri. |
| `PaperRecapTable` | `admin.operational-recaps.show` | Struktur tabel dikunci dan tidak menambah rumus. |

# Keputusan

- Reference TSX hanya dipakai sebagai acuan visual.
- Tidak ada React, TypeScript, Inertia, atau mock data di Laravel.
- Label menu diambil dari `config/mms.php`.
- Foto reference disalin sebagai source asset, bukan sebagai data upload runtime.
