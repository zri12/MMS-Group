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

Gunakan MySQL lokal dan isi variabel `DB_*` pada `.env`. Setelah migration,
buat administrator pertama secara eksplisit:

```powershell
php artisan mms:provision-admin --name="Administrator MMS" --username=admin --email=admin@local.test --password="ganti-dengan-password-kuat"
```

## Produksi cPanel

Panduan dan generator paket unggah ada pada
[`deploy/cpanel/README.md`](deploy/cpanel/README.md). Paket yang dihasilkan
sudah mencakup aset produksi dan dependensi PHP tanpa `.env`.

## Verifikasi

```bash
php artisan test
composer validate --strict
npm run build
```
