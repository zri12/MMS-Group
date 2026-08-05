---
title: "Tracking UI and Polling"
project: "MMS Marketing Monitoring System"
status: "active"
version: "1.0.0"
last_updated: "2026-08-02"
source_of_truth: false
---

# Tracking UI and Polling

## Layout

- Tracking index memakai Leaflet dan OpenStreetMap.
- Marker memakai foto marketing bila tersedia.
- Map height memakai `.mms-map-overview`.
- Detail tracking memakai `.mms-map-detail`.
- Detail journey memakai `.mms-journey-map`.
- Bottom sheet picker/filter memakai `.mms-sheet-panel` dan `.mms-sheet-backdrop`.

## Feed Polling

Route baru:

`GET /admin/tracking/feed`

Route ini berada di group admin sehingga tetap dilindungi:

- `auth`
- `active`
- `admin`

Payload hanya berisi data marker yang diperlukan UI:

- latitude;
- longitude;
- label marketing;
- status;
- update time;
- detail URL;
- foto marker.

JavaScript polling melakukan `fetch()` ke feed URL dan memperbarui marker layer tanpa `window.location.reload()`.

## Security

- Popup marker dibuat dengan DOM API, bukan HTML database mentah.
- URL foto di marker div-icon di-escape untuk attribute.
- Tidak memakai API key.
- Tidak memakai paid map service.
