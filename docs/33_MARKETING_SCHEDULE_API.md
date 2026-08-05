---
title: "Marketing Schedule API"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Marketing Schedule API

## Route

| Method | URL | Name |
|---|---|---|
| GET | `/api/v1/schedules` | `api.v1.schedules.index` |
| GET | `/api/v1/schedules/today` | `api.v1.schedules.today` |
| GET | `/api/v1/schedules/{schedule}` | `api.v1.schedules.show` |
| PATCH | `/api/v1/schedules/{schedule}/status` | `api.v1.schedules.status` |

Middleware: `auth:sanctum`, `active`, `marketing`, dan ability `marketing-mobile`.

## Ownership

Marketing hanya dapat melihat dan mengubah status jadwal miliknya.

## Transisi Status

- `Belum Dikunjungi` ke `Berlangsung` atau `Dibatalkan`.
- `Berlangsung` ke `Selesai` atau `Dibatalkan`.
- Status final tidak dapat diubah.

## Test

- `MarketingScheduleTest`

## Non-Scope

CRUD jadwal admin tersedia melalui route web admin `/admin/schedules`. Endpoint API tetap hanya untuk aplikasi marketing melihat jadwal sendiri dan memperbarui status yang diizinkan.
