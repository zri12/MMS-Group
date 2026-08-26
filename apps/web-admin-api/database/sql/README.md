# MMS Database SQL

Dump ini dibuat dari database `mms_monitoring` setelah seluruh 20 migration
Laravel diterapkan dan seeder development resmi dijalankan.

## File

- `mms_monitoring_schema.sql`: struktur tabel, index, foreign key, serta tabel
  `migrations` tanpa data simulasi.
- `mms_monitoring_development_data.sql`: data simulasi lengkap untuk schema
  yang sudah tersedia.
- `mms_monitoring_development_full.sql`: schema dan data simulasi lengkap
  dalam satu file untuk database MySQL kosong.

Dataset mencakup akun admin dan marketing, profil/wilayah kerja, hari kerja,
prospek, anggota dengan setiap status, jadwal, laporan operasional dan
lampirannya, laporan kunjungan, sesi tracking/titik lokasi, serta rekap
operasional.

## Import Lokal

Pilih salah satu cara berikut pada database development kosong.

```bash
# Schema dan data terpisah
mysql -h 127.0.0.1 -P 3306 -u root nama_database < database/sql/mms_monitoring_schema.sql
mysql -h 127.0.0.1 -P 3306 -u root nama_database < database/sql/mms_monitoring_development_data.sql

# Atau satu file lengkap
mysql -h 127.0.0.1 -P 3306 -u root nama_database < database/sql/mms_monitoring_development_full.sql
```

Untuk menghasilkan ulang data melalui Laravel pada environment lokal:

```bash
MMS_SEED_DEFAULT_PASSWORD=ubah-password-demo php artisan db:seed --force
php artisan db:seed --class=Database\\Seeders\\DemoDataSeeder --force
```

## Penting

- SQL ini hanya untuk localhost/development dan tidak boleh diimpor ke database
  production yang berisi data nyata.
- Dump mencatat 20 migration sebagai sudah dijalankan. Setelah import tidak
  perlu menjalankan `php artisan migrate` lagi kecuali ada migration baru.
- Password akun data simulasi harus diganti sebelum penggunaan selain demo.
- Token login, session, cache, job, dan file foto fisik tidak disertakan.
