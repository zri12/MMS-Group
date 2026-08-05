---
title: "Tracking Module"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Tracking Module

## Web Admin Route

| Method | URL | Name |
|---|---|---|
| GET | `/admin/tracking` | `admin.tracking.index` |
| GET | `/admin/tracking/feed` | `admin.tracking.feed` |
| GET | `/admin/tracking/{trackingSession}` | `admin.tracking.show` |
| GET | `/admin/journeys` | `admin.journeys.index` |
| GET | `/admin/journeys/{trackingSession}` | `admin.journeys.show` |

## API Route

| Method | URL | Name |
|---|---|---|
| GET | `/api/v1/tracking/sessions` | `api.v1.tracking.sessions.index` |
| GET | `/api/v1/tracking/sessions/current` | `api.v1.tracking.sessions.current` |
| POST | `/api/v1/tracking/sessions/start` | `api.v1.tracking.sessions.start` |
| GET | `/api/v1/tracking/sessions/{trackingSession}` | `api.v1.tracking.sessions.show` |
| POST | `/api/v1/tracking/sessions/{trackingSession}/points` | `api.v1.tracking.sessions.points.store` |
| POST | `/api/v1/tracking/sessions/{trackingSession}/points/batch` | `api.v1.tracking.sessions.points.batch` |
| POST | `/api/v1/tracking/sessions/{trackingSession}/stop` | `api.v1.tracking.sessions.stop` |

## Rules

- Marketing hanya mengakses sesi tracking miliknya.
- Satu marketing hanya boleh memiliki satu sesi `Aktif`.
- Start session idempotent berdasarkan `local_uuid`.
- Batch point maksimal mengikuti `config('mms.tracking.max_batch_points')`.
- Batch point atomic; konflik satu item menggagalkan batch.
- Duplicate point `local_uuid` pada sesi yang sama dianggap retry.
- Stop session idempotent; stop ulang tidak mengubah nilai final.
- Status selesai memakai `Offline` karena enum belum memiliki status `Selesai`.

## Map

Web admin memakai Leaflet.js dan OpenStreetMap tanpa key. Data marker dirender dengan `Js::from()`/`@js` dan popup dibangun memakai DOM text content, bukan HTML mentah.

Tracking index memakai feed polling admin untuk memperbarui marker tanpa full-page reload. Feed hanya mengembalikan data marker yang diperlukan UI dan tetap berada di middleware admin.

## Test

- `TrackingAdminTest`
- `MarketingTrackingTest`

## Non-Scope

Tidak ada WebSocket dan tidak ada background tracking Flutter pada fase ini.
