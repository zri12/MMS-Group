---
title: "Deployment to cPanel"
project: "MMS Marketing Monitoring System"
status: "locked"
version: "1.0.0"
last_updated: "2026-07-28"
source_of_truth: true
---


# Persyaratan hosting

- PHP 8.2 atau lebih baru
- MySQL/MariaDB
- Composer atau Terminal/SSH
- SSL
- Cron Jobs
- Document root dapat diarahkan ke `public`
- PHP extensions Laravel: mbstring, openssl, pdo_mysql, tokenizer, xml, ctype, json, bcmath, fileinfo
- Ruang storage yang cukup untuk foto
- Batas upload dapat diubah

# Struktur domain

Rekomendasi sederhana:

- Web admin: `https://domain.com/admin`
- API: `https://domain.com/api/v1`

Login web tetap dapat berada di `/login`.

# Persiapan lokal

```bash
composer install
npm ci
npm run build
php artisan test
```

Jangan upload:

- node_modules
- .git
- .env lokal
- screenshot testing
- ZIP prototype
- log Codex

# Konfigurasi .env production

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain.com`
- Database production
- Mail bila digunakan
- Filesystem public
- Session secure
- Sanctum domain sesuai kebutuhan

# Deployment

1. Backup versi lama.
2. Upload source.
3. Atur document root ke `/public`.
4. Buat database/user MySQL.
5. Salin `.env`.
6. Generate key jika deployment baru.
7. Install Composer production.
8. Jalankan migration.
9. Jalankan seeder hanya pada initial setup yang disetujui.
10. Build asset lokal atau di server.
11. Storage link.
12. Cache config/route/view.
13. Set permission storage dan cache.
14. Aktifkan SSL.
15. Smoke test.

Commands:

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

# Build frontend

Bila cPanel tidak memiliki Node:

1. Jalankan `npm ci && npm run build` di lokal.
2. Upload hasil build Vite bersama source.
3. Pastikan manifest tersedia.

# Cron

Tambahkan:

```cron
* * * * * cd /home/USER/project && php artisan schedule:run >> /dev/null 2>&1
```

Queue worker permanen tidak diasumsikan tersedia. Versi awal menghindari kebutuhan worker 24 jam. Pekerjaan ringan dapat memakai sync/database queue dengan cron sesuai kemampuan hosting.

# PHP INI

Rekomendasi awal:

```ini
upload_max_filesize=5M
post_max_size=8M
memory_limit=256M
max_execution_time=120
```

Sesuaikan hosting dan pengujian.

# Shared hosting limitations

Hindari:

- WebSocket/Reverb;
- proses daemon permanen;
- tracking request tiap detik;
- upload foto besar;
- query tanpa index;
- tabel tracking tanpa retention/archiving plan.

# Backup

Minimal:

- database harian;
- storage berkala;
- source release;
- file `.env` tersimpan aman.

# Rollback

- Simpan release sebelumnya.
- Backup database sebelum migration.
- Migration harus diuji.
- Jangan rollback migration production bila dapat menghapus data tanpa backup.
