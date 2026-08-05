---
title: "REST API Contract"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.3.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Konvensi

- Base URL: `/api/v1`
- Format: JSON, kecuali upload memakai multipart/form-data.
- Auth: `Authorization: Bearer <token>`
- Accept: `application/json`
- Tanggal: `YYYY-MM-DD`
- Timestamp: ISO 8601
- Pagination: `page`, `per_page`
- Filter menggunakan query parameter snake_case.

# Envelope success

```json
{
  "success": true,
  "message": "Data berhasil dimuat.",
  "data": {}
}
```

# Envelope collection

```json
{
  "success": true,
  "message": "Data berhasil dimuat.",
  "data": [],
  "meta": {
    "current_page": 1,
    "last_page": 3,
    "per_page": 20,
    "total": 42
  },
  "links": {
    "first": "...",
    "last": "...",
    "prev": null,
    "next": "..."
  }
}
```

# Validation error — 422

```json
{
  "success": false,
  "message": "Data yang diberikan tidak valid.",
  "errors": {
    "name": ["Nama wajib diisi."]
  }
}
```

# Error umum

- 401: belum login/token tidak valid
- 403: tidak memiliki akses
- 404: data tidak ditemukan
- 409: konflik/duplikasi/idempotency
- 422: validasi
- 429: rate limit
- 500: kesalahan server tanpa detail sensitif

# 1. Authentication

## POST `/auth/login`

Request:

```json
{
  "username": "m01.deden",
  "password": "<password>",
  "device_name": "Android Device"
}
```

Normalisasi server:

- `username` di-trim dan di-lowercase;
- `device_name` di-trim dan whitespace berlebih disederhanakan;
- `password` tidak di-trim dan tidak diubah.

Response:

```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "token_type": "Bearer",
    "token": "<plain-text-token-only-once>",
    "user": {
      "id": 2,
      "name": "Deden",
      "username": "m01.deden",
      "email": null,
      "role": "marketing",
      "is_active": true,
      "last_login_at": "2026-07-29T15:30:00+07:00",
      "marketing": {
        "id": 1,
        "code": "M01",
        "phone": "081200000001",
        "area": "Gedebage",
        "profile_photo_url": null,
        "work_days": ["Senin", "Selasa", "Rabu", "Kamis", "Jumat"]
      }
    }
  }
}
```

## GET `/auth/profile`

Mengembalikan user dan profile marketing aktif.

Middleware:

- `auth:sanctum`
- `active`
- `marketing`
- `abilities:marketing-mobile`

## POST `/auth/logout`

Menghapus token saat ini. Token perangkat lain tidak dihapus.

Jika akun yang sedang memakai token menjadi inactive, request protected mengembalikan 403 generik dan current token dicabut. Token perangkat lain tidak ikut dicabut.

# 2. Jadwal

## GET `/schedules`

Query:

- `date`
- `day`
- `status`
- `page`
- `per_page`

Marketing hanya menerima jadwal miliknya.

## GET `/schedules/today`

Menggunakan tanggal server Asia/Jakarta.

## GET `/schedules/{id}`

## PATCH `/schedules/{id}/status`

Request:

```json
{
  "status": "Berlangsung"
}
```

Marketing hanya dapat melakukan transisi yang diizinkan.

# 3. Prospek

## GET `/prospects`

Query: `status`, `search`, `page`, `per_page`.

## POST `/prospects`

```json
{
  "local_uuid": "f1247c48-1b2d-4aec-98b1-7b3038d8033c",
  "name": "Ahmad Hidayat",
  "phone": "081211112201",
  "address": "Jl. Gedebage Selatan No. 21",
  "business": "Toko Kelontong",
  "status": "Tertarik",
  "initial_visit_result": "Bersedia menerima presentasi produk",
  "notes": "Tertarik produk tabungan usaha.",
  "resort": "Gedebage",
  "input_date": "2026-07-20",
  "input_time": "08:30:00",
  "latitude": -6.9388,
  "longitude": 107.7079,
  "location_address": "Gedebage, Kota Bandung"
}
```

`marketing_id` tidak diterima dari client; server mengambil dari token.
`linked_member_id` tidak diterima pada request dan bukan kolom tabel `prospects`; field ini hanya boleh muncul sebagai response turunan dari `prospect.member?->id`.

## GET `/prospects/{id}`

## PUT `/prospects/{id}`

Hanya prospek milik marketing dan perubahan mengikuti business rule.

# 4. Anggota

## GET `/members`

Query: `approval_status`, `search`, `page`, `per_page`.

## POST `/members`

Multipart:

- `local_uuid`
- `source_prospect_id` opsional
- `resort`
- `date`
- `time`
- `name`
- `member_number`
- `loan_number`
- `address`
- `phone`
- `business`
- `loan_amount`
- `installment_amount`
- `insurance_amount`
- `collateral`
- `latitude`
- `longitude`
- `location_address`
- `member_photo`

Status server selalu `Menunggu`.

## GET `/members/{id}`

Marketing hanya melihat anggota miliknya.

# 5. Laporan Operasional

## GET `/operational-reports`

Query: `date_from`, `date_to`, `page`, `per_page`.

## POST `/operational-reports`

```json
{
  "local_uuid": "2464590b-e959-4fb1-bb7d-317a26fc9c9b",
  "date": "2026-07-20",
  "time": "16:00:00",
  "day": "Senin",
  "resort": "Gedebage",
  "storting": 2300000,
  "insurance_amount": 150000,
  "drop": 5500000,
  "withdrawal_saving": 200000,
  "previous_target_amount": 6500000,
  "previous_target_people": 3,
  "incoming_target_amount": 2000000,
  "incoming_target_people": 2,
  "outgoing_target_amount": 500000,
  "outgoing_target_people": 1,
  "total_target_amount": 8000000,
  "total_target_people": 4,
  "new_drop": 2000000,
  "continued_drop": 3500000,
  "notes": "Operasional berjalan normal."
}
```

## GET `/operational-reports/{id}`

# 6. Laporan Kunjungan

## GET `/visit-reports`

Query: `prospect_id`, `result`, `date_from`, `date_to`, pagination.

## POST `/visit-reports`

Multipart:

- `local_uuid`
- `prospect_id`
- `visit_purpose`
- `visit_result`
- `prospect_status`
- `notes`
- `follow_up_date` opsional
- `photo` opsional/sesuai form final
- `photo_caption`
- `resort`
- `day`
- `date`
- `time`
- `latitude`
- `longitude`
- `location_address`

Server:

- memastikan prospect milik marketing;
- menyimpan laporan;
- memperbarui status prospect dalam transaction;
- menyimpan file setelah validasi.

## GET `/visit-reports/{id}`

# 7. Tracking

## POST `/tracking/sessions/start`

```json
{
  "local_uuid": "972faf62-7280-43b4-a4af-466478d4230e",
  "schedule_id": 10,
  "started_at": "2026-07-20T08:05:00+07:00"
}
```

Response mengembalikan `session_id`.

## POST `/tracking/sessions/{session}/points/batch`

```json
{
  "points": [
    {
      "local_uuid": "uuid-1",
      "latitude": -6.9388,
      "longitude": 107.7079,
      "accuracy_meters": 8.5,
      "speed_mps": 1.2,
      "heading": 120,
      "altitude_meters": 710,
      "point_type": "Perjalanan",
      "recorded_at": "2026-07-20T08:06:00+07:00"
    }
  ]
}
```

Maksimum batch awal yang disarankan: 100 titik.

## POST `/tracking/sessions/{session}/points`

Payload single point memakai field yang sama dengan satu item pada `points/batch`.

## POST `/tracking/sessions/{session}/stop`

```json
{
  "ended_at": "2026-07-20T15:30:00+07:00",
  "visit_count": 3,
  "distance_meters": 8400
}
```

## GET `/tracking/sessions`

Riwayat marketing sendiri.

## GET `/tracking/sessions/current`

Mengembalikan sesi `Aktif` marketing sendiri atau `null` bila tidak ada sesi aktif.

## GET `/tracking/sessions/{session}`

Mengembalikan detail sesi dan titik tracking milik marketing sendiri.

# 8. Sinkronisasi

Create endpoint wajib idempotent melalui `local_uuid`.

Ketika request dengan `local_uuid` yang sama dikirim ulang:

- server mengembalikan record yang sama;
- tidak membuat duplikasi;
- status response tetap success atau 200/201 yang konsisten.

# 9. Admin JSON internal

Web admin menggunakan Blade dan Livewire melalui route web/session. Endpoint JSON internal dapat dibuat hanya untuk polling tracking atau kebutuhan browser ringan seperti integrasi Leaflet.

Contoh:

- GET `/admin/tracking/latest-data`
- GET `/admin/tracking/{marketing}/journey-data`

Route tersebut tetap memakai session auth dan role admin.

# 10. Versioning

Breaking change membuat versi baru `/api/v2`. Penambahan field optional tidak selalu breaking, tetapi harus dicatat di changelog.
