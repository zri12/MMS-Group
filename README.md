# MMS Marketing Monitoring

Satu repository untuk sistem monitoring marketing KSP Manunggal Makmur Sejahtera.

## Struktur

- `apps/web-admin-api`: Laravel 12 untuk web admin dan layanan REST.
- `apps/marketing-mobile`: aplikasi Flutter untuk marketing.

## Menjalankan Lokal

Web admin memakai MySQL lokal. Atur kredensial database pada
`apps/web-admin-api/.env`, lalu jalankan:

```powershell
cd apps/web-admin-api
composer install
npm ci
php artisan migrate --force
npm run build
php artisan serve
```

Untuk aplikasi Flutter:

```powershell
cd apps/marketing-mobile
flutter pub get
flutter run --dart-define=API_BASE_URL=http://10.0.2.2:8000/api/v1
```

`10.0.2.2` dipakai oleh Android Emulator. Untuk perangkat fisik, gunakan
alamat IP LAN komputer yang menjalankan Laravel.

## Deploy cPanel

1. Arahkan document root domain ke `apps/web-admin-api/public`.
2. Buat database dan user MySQL cPanel, lalu isi `apps/web-admin-api/.env`
   berdasarkan `.env.example`; gunakan `APP_ENV=production`,
   `APP_DEBUG=false`, dan URL HTTPS domain asli.
3. Jalankan `composer install --no-dev --optimize-autoloader`,
   `php artisan migrate --force`, `php artisan storage:link`,
   `php artisan optimize`, serta `npm ci && npm run build`.
4. Pastikan `storage` dan `bootstrap/cache` dapat ditulis oleh user web server.
5. Buat APK rilis dengan domain HTTPS:

```powershell
flutter build apk --release --dart-define=API_BASE_URL=https://domain-anda/api/v1
```

Kunci penandatanganan Android disimpan lokal melalui
`apps/marketing-mobile/android/key.properties`; contoh format tersedia di
`key.properties.example` dan file rahasia tersebut tidak masuk Git.
