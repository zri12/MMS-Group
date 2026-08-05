---
title: "Environment and Configuration"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.3.0"
last_updated: "2026-07-29"
source_of_truth: true
---


# Environment Laravel

Contoh variabel:

```env
APP_NAME="MMS Marketing Monitoring"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mms_monitoring_dev
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public

SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1

MMS_SEED_ADMIN_USERNAME=admin
MMS_SEED_ADMIN_EMAIL=admin@mms.local
MMS_SEED_DEFAULT_PASSWORD=
```

Untuk production:

- `APP_DEBUG=false`
- HTTPS
- cookie secure
- key unik
- kredensial database production

# Config aplikasi

Buat `config/mms.php` untuk nilai yang bukan rahasia:

```php
return [
    'timezone' => 'Asia/Jakarta',
    'operational_days' => [
        'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu',
    ],
    'tracking' => [
        'polling_seconds' => env('MMS_TRACKING_POLLING_SECONDS', 30),
        'offline_threshold_minutes' => env('MMS_OFFLINE_THRESHOLD_MINUTES', 5),
        'max_batch_points' => env('MMS_MAX_TRACKING_BATCH_POINTS', 100),
    ],
    'uploads' => [
        'max_image_kb' => env('MMS_MAX_IMAGE_KB', 2048),
    ],
    'seed' => [
        'admin_username' => env('MMS_SEED_ADMIN_USERNAME', 'admin'),
        'admin_email' => env('MMS_SEED_ADMIN_EMAIL', 'admin@mms.local'),
        'default_password' => env('MMS_SEED_DEFAULT_PASSWORD'),
    ],
];
```

Nilai tracking masih pending confirmation; environment memudahkan perubahan.
Nilai password seed wajib diisi hanya pada `.env` lokal development dan tidak boleh didokumentasikan.

# Local MySQL Development

Runtime lokal Laravel memakai MySQL/MariaDB:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mms_monitoring_dev
DB_USERNAME=root
DB_PASSWORD=
```

Aturan:

- Database harus lokal, yaitu `127.0.0.1` atau `localhost`.
- Database harus kosong sebelum migration pertama.
- Jangan menjalankan migration pada database production, cPanel, customer, atau database yang memiliki data penting.
- Jangan commit `.env`.
- Jangan menulis password database pada dokumentasi.
- `MMS_SEED_DEFAULT_PASSWORD` wajib diisi di `.env` lokal bila menjalankan development seeder.
- Reset password development sebelum deployment.

Command development:

```bash
php artisan optimize:clear
php artisan migrate
php artisan db:seed
php artisan migrate:rollback
php artisan migrate
php artisan db:seed
```

Automated test tetap memakai SQLite in-memory melalui `phpunit.xml`:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

# SQL Export

SQL dump development berada di:

```text
apps/web-admin-api/database/sql/
```

File:

- `mms_monitoring_schema.sql`
- `mms_monitoring_development_data.sql`
- `mms_monitoring_development_full.sql`
- `README.md`

SQL dump hanya untuk local development dan restore development. Migration dan seeder Laravel tetap menjadi sumber implementasi utama.

# Flutter flavors

Minimal:

- development
- production

Config:

- base URL
- connect/read timeout
- logging enabled hanya development
- polling/sync interval
- map tile URL

Jangan hardcode base URL di widget.

# Secrets

Tidak boleh masuk Git:

- `.env`
- database password
- Sanctum token
- signing key Android
- keystore
- service credentials

Sediakan `.env.example`.

# Web Admin Auth Runtime

Autentikasi web admin memakai session Laravel:

```env
AUTH_GUARD=web
AUTH_PASSWORD_BROKER=users
SESSION_DRIVER=file
```

Login web admin membaca user dari tabel `users` dengan identifier `username`.

Identifier login web dan marketing dinormalisasi dengan trim dan lowercase sebelum autentikasi dan throttle key. Password tidak dinormalisasi.

Test otomatis tetap memakai SQLite in-memory dari `phpunit.xml`. Jangan menjalankan migration atau seeder hanya untuk fase auth web admin.

Konfigurasi rate limit dan token auth:

```env
MMS_ADMIN_LOGIN_MAX_ATTEMPTS=5
MMS_ADMIN_LOGIN_DECAY_SECONDS=60
MMS_MARKETING_API_LOGIN_MAX_ATTEMPTS=5
MMS_MARKETING_API_LOGIN_DECAY_SECONDS=60
MMS_MARKETING_API_TOKEN_ABILITY=marketing-mobile
MMS_MARKETING_API_TOKEN_PREFIX=mms-marketing
```

Blade Design System tidak membutuhkan environment tambahan. Component catalog hanya didaftarkan pada environment `local` dan `testing`.

# Timezone

- Server menggunakan Asia/Jakarta untuk domain bisnis.
- API timestamp selalu menyertakan offset atau UTC.
- Flutter menyimpan waktu kejadian dan waktu sync.
