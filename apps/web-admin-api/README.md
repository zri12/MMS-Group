# MMS Web Admin

Laravel 12 untuk web admin MMS dan endpoint `/api/v1` yang digunakan aplikasi
marketing Flutter.

## Konfigurasi Lokal

```powershell
composer install
npm ci
copy .env.example .env
php artisan key:generate
php artisan migrate --force
npm run build
php artisan serve
```

Gunakan MySQL lokal dan isi variabel `DB_*` pada `.env`. Database operasional
utama menggunakan `mms_monitoring`; data simulasi tidak digunakan pada
environment production.

## Produksi cPanel

1. Salin `.env.example` menjadi `.env`, lalu isi URL HTTPS, kredensial MySQL,
   konfigurasi email, dan `APP_KEY` yang unik.
2. Arahkan document root domain ke folder `public` aplikasi ini.
3. Jalankan perintah berikut dari root aplikasi Laravel:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

4. Berikan izin tulis pada `storage` dan `bootstrap/cache`.

## Verifikasi

```bash
php artisan test
composer validate --strict
npm run build
```
