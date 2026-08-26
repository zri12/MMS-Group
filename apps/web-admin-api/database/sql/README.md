# MMS Database SQL

`mms_monitoring_schema.sql` hanya berisi struktur database. Tidak ada akun,
data PDL, data nasabah, lokasi, laporan, maupun berkas operasional.

Untuk instalasi baru, gunakan migration Laravel agar skema selalu mengikuti
versi aplikasi:

```bash
php artisan migrate --force
php artisan mms:provision-admin --name="Administrator MMS" --username=admin --email=admin@domain-anda.tld --password="password-kuat"
```

Gunakan dump schema hanya bila server tidak menyediakan akses terminal.
Setelah import, buat administrator pertama dengan perintah di atas saat akses
terminal tersedia. Jangan impor dump ini ke database yang telah berisi data
operasional.
