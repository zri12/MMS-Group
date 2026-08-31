# Deploy cPanel

## Prasyarat Hosting

- PHP 8.2 atau lebih baru dengan ekstensi `ctype`, `curl`, `dom`, `fileinfo`,
  `mbstring`, `openssl`, `pdo_mysql`, `session`, `tokenizer`, dan `xml`.
- MySQL/MariaDB dan SSL aktif pada domain.
- File Manager cPanel diperlukan. Terminal bersifat opsional.
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
dalamnya. Generator juga membuat `deployment-package/mms-web-admin-cpanel-setup.txt`
yang berisi token privat sekali pakai untuk halaman setup web.

## Konfigurasi cPanel

1. Buat database MySQL dan user database melalui **MySQL Database Wizard**.
   Berikan seluruh hak akses user tersebut pada database.
2. Di **Domains**, arahkan document root domain atau subdomain ke:
   `/home/NAMA_AKUN/mms-web-admin/public`.
   Jangan arahkan domain ke root proyek Laravel.
3. Upload ZIP ke home directory cPanel, di luar `public_html`, lalu extract.
   Folder hasil extract bernama `mms-web-admin`.
4. Buka `mms-web-admin-cpanel-setup.txt` pada komputer Anda. Gunakan URL
`cpanel_keymigrate.php` yang tercantum di file tersebut untuk membuat `.env`.
Jangan membagikan token tersebut.
5. Melalui **File Manager**, edit `.env` lalu isi minimal nilai berikut:

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

6. Buka halaman berikut secara berurutan dengan token yang sama:

```text
https://domain-anda.tld/cpanel_keymigrate.php?token=TOKEN_ANDA
https://domain-anda.tld/cpanel_storage.php?token=TOKEN_ANDA
https://domain-anda.tld/cpanel_provision_admin.php?token=TOKEN_ANDA
https://domain-anda.tld/cpanel_optimize.php?token=TOKEN_ANDA
```

Di `cpanel_keymigrate.php`, pilih **Buat APP_KEY**, lalu **Jalankan migration**.
Di halaman admin awal, isi akun administrator dengan password kuat minimal 12
karakter. Jalankan storage link dan optimasi setelah migration berhasil.

Perintah administrator hanya berhasil bila belum ada admin. Jangan gunakan
password contoh pada sistem yang dapat diakses publik.

7. Buka `cpanel_clear.php` memakai token yang sama lalu pilih
**Nonaktifkan setup web**. Setelah itu hapus seluruh file `cpanel_*.php` dari
folder `public` menggunakan File Manager. Langkah ini wajib agar halaman setup
tidak tersisa di internet.

8. Pastikan folder berikut dapat ditulis oleh user web server. Gunakan izin
`775`; jangan menggunakan `777`:

```text
storage
bootstrap/cache
```

## Alternatif Terminal

Jika Terminal tersedia, Anda dapat menjalankan perintah Artisan biasa. Tetap
nonaktifkan dan hapus file `cpanel_*.php` setelah deployment selesai.

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

## Struktur public terpisah

Untuk struktur cPanel berikut:

```text
/home/fazriluk/PROJECT MMS WEB ADMIN
/home/fazriluk/public_html/demoprojectweb.net
```

buat ZIP publik khusus dari root repository:

```powershell
.\scripts\create-cpanel-public-package.ps1
```

Hasilnya `deployment-package/demoprojectweb.net-public.zip`. Upload ZIP ini ke
`/home/fazriluk/public_html`, lalu extract sehingga folder hasilnya menjadi
`/home/fazriluk/public_html/demoprojectweb.net`. ZIP tersebut hanya berisi file
yang boleh diakses web dan `index.php` di dalamnya sudah menunjuk ke
`/home/fazriluk/PROJECT MMS WEB ADMIN`.

Upload juga paket aplikasi Laravel ke `/home/fazriluk`, lalu pastikan nama
folder aplikasi adalah tepat `PROJECT MMS WEB ADMIN`. Jangan extract ZIP publik
ke dalam folder aplikasi Laravel.

## Layout Public Terpisah

Gunakan layout ini bila cPanel tidak dapat mengubah document root domain:

```text
/home/fazriluk/PROJECT MESS MONITORING
/home/fazriluk/public_html/demoprojectweb.net
```

Ekstrak `mms-web-admin-cpanel.zip`, lalu ubah nama folder hasil ekstrak menjadi
`PROJECT MESS MONITORING`. Setelah itu, dari komputer pengembangan jalankan:

```powershell
.\scripts\create-cpanel-public-package.ps1
```

Upload dan ekstrak `deployment-package/demoprojectweb.net-public.zip` ke
`/home/fazriluk/public_html/demoprojectweb.net`. ZIP ini memiliki `index.php`
dan halaman setup yang sudah diarahkan ke folder proyek di atas. Jangan
menggunakan ZIP public tersebut untuk domain atau nama folder proyek lain;
buat ulang paket dengan parameter `-ProjectPath` dan `-DocumentRoot` yang
sesuai.
