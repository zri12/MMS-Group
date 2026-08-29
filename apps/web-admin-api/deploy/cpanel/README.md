# Deploy cPanel

## Prasyarat Hosting

- PHP 8.2 atau lebih baru dengan ekstensi `ctype`, `curl`, `dom`, `fileinfo`,
  `mbstring`, `openssl`, `pdo_mysql`, `session`, `tokenizer`, dan `xml`.
- MySQL/MariaDB dan SSL aktif pada domain.
- Akses cPanel Terminal diperlukan untuk migration, storage link, dan cache.
- `upload_max_filesize` minimal `3M`, `post_max_size` minimal `8M`, dan
  `memory_limit` minimal `256M`.

## Buat Paket Unggah

Jalankan dari root repository pada komputer pengembangan:

```powershell
.\scripts\create-cpanel-package.ps1
```

Hasilnya berada di `deployment-package/mms-web-admin-cpanel.zip`. ZIP berisi
dependensi PHP produksi dan aset Vite yang sudah dibangun. Tidak ada `.env`,
data database, file upload, log, cache runtime, test, atau `node_modules` di
dalamnya.

## Konfigurasi cPanel

1. Buat database MySQL dan user database melalui **MySQL Database Wizard**.
   Berikan seluruh hak akses user tersebut pada database.
2. Di **Domains**, arahkan document root domain atau subdomain ke:
   `/home/NAMA_AKUN/mms-web-admin/public`.
   Jangan arahkan domain ke root proyek Laravel.
3. Upload ZIP ke home directory cPanel, di luar `public_html`, lalu extract.
   Folder hasil extract bernama `mms-web-admin`.
4. Salin `.env.example` menjadi `.env`, lalu isi minimal nilai berikut:

```dotenv
APP_KEY=
APP_URL=https://domain-anda.tld
DB_DATABASE=prefix_nama_database
DB_USERNAME=prefix_user_database
DB_PASSWORD=password_database_anda
```

Biarkan `APP_ENV=production`, `APP_DEBUG=false`, dan
`SESSION_SECURE_COOKIE=true`. Gunakan nama database serta user lengkap dengan
prefix cPanel.

5. Buka **Terminal** cPanel, lalu jalankan dari folder `mms-web-admin`:

```bash
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan mms:provision-admin --name="Administrator MMS" --username=admin --email=admin@domain-anda.tld --password="GantiDenganPasswordKuat"
php artisan optimize
```

Perintah administrator hanya berhasil bila belum ada admin. Jangan gunakan
password contoh pada sistem yang dapat diakses publik.

6. Pastikan folder berikut dapat ditulis oleh user web server. Gunakan izin
`775`; jangan menggunakan `777`:

```text
storage
bootstrap/cache
```

## Verifikasi

- Buka `https://domain-anda.tld/up`; respons harus `200`.
- Buka `https://domain-anda.tld/api/v1/health`; respons harus JSON `200`.
- Login melalui `https://domain-anda.tld/login`.
- Upload satu foto dari aplikasi marketing dan pastikan foto dapat dibuka di
  web admin. Ini juga memverifikasi `public/storage`.

## Aplikasi Marketing

Build aplikasi marketing menggunakan URL HTTPS domain yang sudah aktif:

```powershell
flutter build apk --release --dart-define=API_BASE_URL=https://domain-anda.tld/api/v1
```

Jika hosting tidak mengizinkan document root mengarah ke folder `public`,
gunakan subdomain/addon domain yang mengizinkannya atau minta provider hosting
mengubah document root. Jangan menyalin `public/index.php` ke root proyek
Laravel karena berisiko mengekspos file konfigurasi.
