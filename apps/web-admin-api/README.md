# MMS Web Admin API

Project ini adalah Laravel 12 aktif untuk MMS Marketing Monitoring System.

## Stack

- Laravel 12
- PHP 8.2+
- Blade
- Livewire
- Tailwind CSS
- Alpine.js bawaan Livewire
- Vite
- Laravel Sanctum
- REST API v1
- MySQL/MariaDB direncanakan untuk production

Web Admin dibangun dengan Blade/Livewire. Project aktif tidak menggunakan React, Inertia.js, TypeScript, TSX, React Router, React Context, atau React Leaflet.

## Setup Lokal

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
php artisan optimize:clear
```

Jangan menjalankan migration domain MMS sampai tahap database selesai dan disetujui.

## Development

```bash
php artisan serve
npm run dev
```

## Build dan Test

```bash
npm run build
php artisan test
composer validate
```

## Route Fondasi

- `GET /`
- `GET /api/v1/health`

Endpoint bisnis, autentikasi admin, migration domain, model domain, CRUD, dan Flutter belum dibuat pada tahap fondasi ini.

## Runtime Fondasi

Fondasi ini tidak membutuhkan tabel database untuk session, cache, atau queue.

```env
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
```

Database contoh tetap MySQL/MariaDB untuk tahap domain berikutnya. Jangan menjalankan `php artisan migrate` sampai migration domain selesai direview.

## Source Bersih

Jangan masukkan `.env`, `vendor/`, `node_modules/`, `public/build/`, `database/database.sqlite`, `storage/logs/`, `storage/framework/views/`, `bootstrap/cache/*.php`, `.phpunit.result.cache`, file ZIP, atau artefak tool/cache ke paket source. Dependency dapat dibuat ulang dengan `composer install`, `npm install` atau `npm ci`, dan `npm run build`.
