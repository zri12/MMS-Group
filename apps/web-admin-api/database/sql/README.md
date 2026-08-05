# MMS MySQL SQL Files

Folder ini berisi artefak SQL untuk local development MMS.

## File

- `mms_monitoring_schema.sql`: schema-only. Berisi struktur tabel, index, dan foreign key tanpa data.
- `mms_monitoring_development_data.sql`: data development-only. Berisi INSERT data development tanpa CREATE TABLE.
- `mms_monitoring_development_full.sql`: gabungan schema dan data development untuk restore lokal.
- `mms_monitoring_reset_data_admin_only.sql`: hapus data pada tabel existing dan buat satu akun admin saja.
- `mms_monitoring_fresh_admin_only.sql`: schema lengkap untuk database kosong dengan satu akun admin saja.

## Peringatan

- Hanya untuk local development.
- Jangan digunakan pada production tanpa review ulang.
- Full development SQL berisi password hash development. Reset password sebelum deployment.
- Runtime token, session, cache, dan job data tidak disertakan.
- File dihasilkan dari database local yang sudah dimigrate dan diseed.
- Migration dan seeder Laravel tetap menjadi sumber implementasi utama. SQL dump hanya artefak distribusi dan restore development.

## Import

Schema lalu data:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p nama_database < database/sql/mms_monitoring_schema.sql
mysql -h 127.0.0.1 -P 3306 -u root -p nama_database < database/sql/mms_monitoring_development_data.sql
```

Full development:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p nama_database < database/sql/mms_monitoring_development_full.sql
```

Admin-only untuk database kosong:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p nama_database < database/sql/mms_monitoring_fresh_admin_only.sql
```

Admin-only untuk database yang tabelnya sudah ada:

```bash
mysql -h 127.0.0.1 -P 3306 -u root -p nama_database < database/sql/mms_monitoring_reset_data_admin_only.sql
```

Cara Laravel yang direkomendasikan:

```bash
php artisan migrate
php artisan db:seed
```
