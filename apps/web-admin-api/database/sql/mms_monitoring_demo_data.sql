-- MMS development demo data. Import after mms_monitoring_schema.sql.
-- No users, passwords, tokens, or credentials are included.
INSERT INTO marketing_profiles (user_id, display_name, code, phone, area, created_at, updated_at)
VALUES
  (NULL, 'Andi Pratama', 'PDL01', '081200000001', 'Cimahi Tengah', NOW(), NOW()),
  (NULL, 'Budi Santoso', 'PDL02', '081200000002', 'Cimahi Selatan', NOW(), NOW()),
  (NULL, 'Citra Ramadhan', 'PDL03', '081200000003', 'Cimahi Utara', NOW(), NOW()),
  (NULL, 'Deni Kurniawan', 'PDL04', '081200000004', 'Bandung Barat', NOW(), NOW()),
  (NULL, 'Eka Putra', 'PDL05', '081200000005', 'Padalarang', NOW(), NOW()),
  (NULL, 'Fajar Nugraha', 'PDL06', '081200000006', 'Batujajar', NOW(), NOW())
ON DUPLICATE KEY UPDATE display_name=VALUES(display_name), area=VALUES(area), updated_at=NOW();

-- Use the Laravel seeder for the complete relational dataset, local assets, tracking points, and attachments:
-- php artisan mms:seed-demo --force-local
