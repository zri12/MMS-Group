---
title: "Reporting Module"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Reporting Module

## Web Admin Route

| Method | URL | Name |
|---|---|---|
| GET | `/admin/operational-reports` | `admin.operational-reports.index` |
| GET | `/admin/operational-reports/{operational_report}` | `admin.operational-reports.show` |
| GET | `/admin/visit-reports` | `admin.visit-reports.index` |
| GET | `/admin/visit-reports/{visit_report}` | `admin.visit-reports.show` |

## API Route

| Method | URL | Name |
|---|---|---|
| GET | `/api/v1/operational-reports` | `api.v1.operational-reports.index` |
| POST | `/api/v1/operational-reports` | `api.v1.operational-reports.store` |
| GET | `/api/v1/operational-reports/{operationalReport}` | `api.v1.operational-reports.show` |
| GET | `/api/v1/visit-reports` | `api.v1.visit-reports.index` |
| POST | `/api/v1/visit-reports` | `api.v1.visit-reports.store` |
| GET | `/api/v1/visit-reports/{visitReport}` | `api.v1.visit-reports.show` |

## Rules

- Operational report memakai field final dari Data Dictionary.
- Nominal tidak boleh negatif.
- Visit report harus terhubung ke prospek milik marketing.
- Create visit report memperbarui status prospek dalam transaction.
- Upload foto kunjungan optional, divalidasi image dan ukuran.
- `local_uuid` create idempotent.

## Test

- `ReportAdminTest`
- `MarketingOperationalReportTest`
- `MarketingVisitReportTest`

## Non-Scope

Tidak ada bukti transfer, laporan tunai terpisah, atau foto pencairan.
