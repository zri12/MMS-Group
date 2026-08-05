---
title: "Prospect Module"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Prospect Module

## Web Admin Route

| Method | URL | Name |
|---|---|---|
| GET | `/admin/prospects` | `admin.prospects.index` |
| GET | `/admin/prospects/{prospect}` | `admin.prospects.show` |

## API Route

| Method | URL | Name |
|---|---|---|
| GET | `/api/v1/prospects` | `api.v1.prospects.index` |
| POST | `/api/v1/prospects` | `api.v1.prospects.store` |
| GET | `/api/v1/prospects/{prospect}` | `api.v1.prospects.show` |
| PUT | `/api/v1/prospects/{prospect}` | `api.v1.prospects.update` |

## Rules

- Marketing hanya melihat prospek miliknya.
- `local_uuid` membuat create idempotent.
- Duplicate `local_uuid` dari marketing lain menjadi 409.
- Latitude dan longitude divalidasi range.
- Admin web read-only untuk prospek.

## Test

- `ProspectAdminTest`
- `MarketingProspectTest`

## Non-Scope

Tidak ada hard delete prospek.
