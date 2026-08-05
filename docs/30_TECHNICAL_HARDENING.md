---
title: "Technical Hardening"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-29"
source_of_truth: true
---

# Technical Hardening

## Scope

Hardening ini mencakup error JSON untuk seluruh route `/api/*`, middleware role/status, validasi auth terhadap input non-scalar, dan peningkatan Blade Design System.

## API JSON

Deteksi JSON memakai helper `App\Support\ApiRequest`:

- request path `api/*`;
- atau request `expectsJson()`.

Exception API ditangani dalam `bootstrap/app.php` untuk 401, 403, 404, 405, 422, 429, dan HTTP exception lain dengan envelope standar.

## Middleware

- `EnsureUserIsActive`
- `EnsureUserIsAdmin`
- `EnsureUserIsMarketing`

Ketiganya mengembalikan JSON untuk request API tanpa bergantung hanya pada header `Accept`.

## Design System

Perbaikan:

- checkbox/radio/switch meneruskan `wire:*`, `x-*`, `aria-*`, `data-*`, `disabled`, dan `required` ke elemen input;
- field/input/help/error memakai `aria-describedby` dan `aria-invalid`;
- error memakai `role="alert"`;
- confirmation dialog memakai slot `trigger`, `confirm`, dan `cancel`;
- modal memiliki focus trap, Escape close, overlay close opsional, scroll lock, dan focus return;
- tabs memakai role ARIA dan keyboard Arrow/Home/End;
- pagination MMS berada di `resources/views/vendor/pagination/mms.blade.php`.

## Test

- `ApiJsonResponseHardeningTest`
- `BladeComponentRenderTest`
- `AdminLoginTest`
- `MarketingLoginTest`

## Non-Scope

Tidak ada migration, seeder, atau perubahan database pada hardening ini.
