---
title: "Marketing Management Module"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Marketing Management Module

## Route

| Method | URL | Name |
|---|---|---|
| GET | `/admin/marketing` | `admin.marketing.index` |
| GET | `/admin/marketing/create` | `admin.marketing.create` |
| POST | `/admin/marketing` | `admin.marketing.store` |
| GET | `/admin/marketing/{marketing}` | `admin.marketing.show` |
| GET | `/admin/marketing/{marketing}/edit` | `admin.marketing.edit` |
| PUT | `/admin/marketing/{marketing}` | `admin.marketing.update` |
| PATCH | `/admin/marketing/{marketing}/status` | `admin.marketing.status` |
| PATCH | `/admin/marketing/{marketing}/reset-password` | `admin.marketing.reset-password` |

Semua route memakai middleware `auth`, `active`, dan `admin`.

## Flow

Admin dapat membuat, mengubah, mengaktifkan, menonaktifkan, dan reset password marketing. Data historis tidak dihapus. Disable dan reset password mencabut seluruh token marketing terkait.

## Validasi

- `code`, `username`, dan `email` unik.
- Work days hanya `Senin` sampai `Sabtu`.
- Password minimal 8 karakter.
- `is_active` tidak diterima dari API marketing.

## File Utama

- `MarketingController`
- `StoreMarketingRequest`
- `UpdateMarketingRequest`
- `UpdateMarketingStatusRequest`
- `ResetMarketingPasswordRequest`
- `CreateMarketingAction`
- `UpdateMarketingAction`
- `UpdateMarketingStatusAction`
- `ResetMarketingPasswordAction`

## Test

- `MarketingManagementTest`

## Non-Scope

Tidak ada hard delete marketing dan tidak ada registrasi publik.
