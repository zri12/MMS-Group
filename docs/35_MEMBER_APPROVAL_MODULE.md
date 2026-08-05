---
title: "Member Approval Module"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Member Approval Module

## Web Admin Route

| Method | URL | Name |
|---|---|---|
| GET | `/admin/members` | `admin.members.index` |
| GET | `/admin/members/{member}` | `admin.members.show` |
| PATCH | `/admin/members/{member}/approval` | `admin.members.approval` |

## API Route

| Method | URL | Name |
|---|---|---|
| GET | `/api/v1/members` | `api.v1.members.index` |
| POST | `/api/v1/members` | `api.v1.members.store` |
| GET | `/api/v1/members/{member}` | `api.v1.members.show` |

## Rules

- Member yang dibuat marketing selalu berstatus `Menunggu`.
- Admin dapat mengubah `Menunggu` ke `Disetujui` atau `Ditolak`.
- Status final tidak dapat diubah ulang.
- `approved_by`, `approved_at`, `rejected_by`, dan `rejected_at` berasal dari server.
- `source_prospect_id` harus milik marketing yang sama.
- `local_uuid` create idempotent.

## Test

- `MemberAdminTest`
- `MarketingMemberTest`

## Open Question

Alasan penolakan saat ini nullable karena belum dikunci sebagai field wajib pada dokumen bisnis.
