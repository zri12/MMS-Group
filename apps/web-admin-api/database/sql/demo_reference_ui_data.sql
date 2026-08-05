-- MMS Marketing Monitoring - demo reference UI data
-- Import via phpMyAdmin after migrations are already installed.
-- This script is repeatable for demo rows that use the demo-ui-* local_uuid prefix.

SET FOREIGN_KEY_CHECKS = 0;

DELETE tp
FROM tracking_points tp
JOIN tracking_sessions ts ON ts.id = tp.tracking_session_id
WHERE ts.local_uuid LIKE 'demo-ui-%';

DELETE FROM tracking_sessions WHERE local_uuid LIKE 'demo-ui-%';
DELETE FROM visit_reports WHERE local_uuid LIKE 'demo-ui-%';
DELETE FROM daily_operational_reports WHERE local_uuid LIKE 'demo-ui-%';
DELETE FROM members WHERE local_uuid LIKE 'demo-ui-%';
DELETE FROM prospects WHERE local_uuid LIKE 'demo-ui-%';

DELETE ms
FROM marketing_schedules ms
JOIN marketing_profiles mp ON mp.id = ms.marketing_profile_id
WHERE ms.note LIKE 'DEMO UI:%' AND mp.code BETWEEN 'M01' AND 'M13';

DELETE FROM operational_recaps WHERE report_number LIKE 'DEMO-%';

SET FOREIGN_KEY_CHECKS = 1;

SET @now = NOW();
SET @today = CURDATE();
SET @demo_day = 'Senin';

-- Demo account base. Keep the existing account if it already exists.
INSERT INTO users (name, username, email, email_verified_at, password, role, is_active, last_login_at, remember_token, created_at, updated_at, deleted_at)
VALUES
('Administrator MMS', 'admin', 'admin@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'admin', 1, NULL, NULL, @now, @now, NULL)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    email = VALUES(email),
    role = VALUES(role),
    is_active = VALUES(is_active),
    updated_at = @now,
    deleted_at = NULL;

SET @admin_id = (SELECT id FROM users WHERE username = 'admin' LIMIT 1);

INSERT INTO users (name, username, email, email_verified_at, password, role, is_active, last_login_at, remember_token, created_at, updated_at, deleted_at)
VALUES
('Deden', 'm01.deden', 'm01@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Angil', 'm02.angil', 'm02@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Ari', 'm03.ari', 'm03@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Feri', 'm04.feri', 'm04@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Sukma', 'm05.sukma', 'm05@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Sandi', 'm06.sandi', 'm06@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Vikri', 'm07.vikri', 'm07@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Farhad', 'm08.farhad', 'm08@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Doni', 'm09.doni', 'm09@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Faiz', 'm10.faiz', 'm10@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Agung', 'm11.agung', 'm11@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Faisal', 'm12.faisal', 'm12@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL),
('Agnes', 'm13.agnes', 'm13@mms.local', NULL, '$2y$12$U.LtcuVF6VRCmSq09YfZous6O6TCBFjwlvXe3XNBv0vYiOIKE9D06', 'marketing', 1, NULL, NULL, @now, @now, NULL)
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    email = VALUES(email),
    role = VALUES(role),
    is_active = VALUES(is_active),
    updated_at = @now,
    deleted_at = NULL;

INSERT INTO marketing_profiles (user_id, code, phone, area, profile_photo_path, created_at, updated_at)
VALUES
((SELECT id FROM users WHERE username = 'm01.deden'), 'M01', '081220260001', 'Gedebage', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm02.angil'), 'M02', '081220260002', 'Rancasari', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm03.ari'), 'M03', '081220260003', 'Buahbatu', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm04.feri'), 'M04', '081220260004', 'Ujungberung', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm05.sukma'), 'M05', '081220260005', 'Cibiru', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm06.sandi'), 'M06', '081220260006', 'Antapani', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm07.vikri'), 'M07', '081220260007', 'Kiaracondong', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm08.farhad'), 'M08', '081220260008', 'Cicaheum', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm09.doni'), 'M09', '081220260009', 'Sukajadi', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm10.faiz'), 'M10', '081220260010', 'Lengkong', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm11.agung'), 'M11', '081220260011', 'Arcamanik', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm12.faisal'), 'M12', '081220260012', 'Cimahi', NULL, @now, @now),
((SELECT id FROM users WHERE username = 'm13.agnes'), 'M13', '081220260013', 'Cileunyi', NULL, @now, @now)
ON DUPLICATE KEY UPDATE
    user_id = VALUES(user_id),
    phone = VALUES(phone),
    area = VALUES(area),
    updated_at = @now;

INSERT IGNORE INTO marketing_work_days (marketing_profile_id, day_name, created_at, updated_at)
SELECT mp.id, d.day_name, @now, @now
FROM marketing_profiles mp
JOIN (
    SELECT 'Senin' AS day_name UNION ALL SELECT 'Selasa' UNION ALL SELECT 'Rabu'
    UNION ALL SELECT 'Kamis' UNION ALL SELECT 'Jumat' UNION ALL SELECT 'Sabtu'
) d
WHERE mp.code BETWEEN 'M01' AND 'M13';

INSERT INTO prospects (local_uuid, marketing_profile_id, name, phone, address, business, status, initial_visit_result, notes, resort, input_date, input_time, latitude, longitude, location_address, sync_status, created_at, updated_at, deleted_at)
VALUES
('demo-ui-prospect-001', (SELECT id FROM marketing_profiles WHERE code='M01'), 'Asep Hidayat', '082100010001', 'Jl. Soekarno Hatta No. 120, Gedebage', 'Warung sembako', 'Baru', 'Prospek membutuhkan modal tambahan stok barang.', 'Prioritas follow up sore.', 'Gedebage', @today, '08:20:00', -6.9458700, 107.6985400, 'Gedebage, Kota Bandung', 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-prospect-002', (SELECT id FROM marketing_profiles WHERE code='M02'), 'Siti Nurjanah', '082100010002', 'Jl. Cipamokolan, Rancasari', 'Katering rumahan', 'Tertarik', 'Tertarik pembiayaan usaha katering.', NULL, 'Rancasari', @today, '08:45:00', -6.9512200, 107.6724800, 'Rancasari, Kota Bandung', 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-prospect-003', (SELECT id FROM marketing_profiles WHERE code='M03'), 'Dadan Ramdani', '082100010003', 'Jl. Buah Batu Dalam, Buahbatu', 'Bengkel motor', 'Perlu Follow Up', 'Minta simulasi angsuran.', 'Hubungi kembali setelah jam operasional bengkel.', 'Buahbatu', @today, '09:10:00', -6.9557500, 107.6372000, 'Buahbatu, Kota Bandung', 'Menunggu Sinkronisasi', @now, @now, NULL),
('demo-ui-prospect-004', (SELECT id FROM marketing_profiles WHERE code='M04'), 'Yuyun Yuningsih', '082100010004', 'Jl. AH Nasution, Ujungberung', 'Toko kelontong', 'Baru', 'Butuh modal untuk kulakan mingguan.', NULL, 'Ujungberung', @today, '09:35:00', -6.9147200, 107.7011800, 'Ujungberung, Kota Bandung', 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-prospect-005', (SELECT id FROM marketing_profiles WHERE code='M05'), 'Rudi Hermawan', '082100010005', 'Jl. Cibiru Hilir, Cibiru', 'Pedagang sayur', 'Tertarik', 'Lokasi usaha aktif dan ramai.', NULL, 'Cibiru', @today, '10:00:00', -6.9354000, 107.7219000, 'Cibiru, Kota Bandung', 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-prospect-006', (SELECT id FROM marketing_profiles WHERE code='M06'), 'Neni Kartika', '082100010006', 'Jl. Terusan Jakarta, Antapani', 'Laundry kiloan', 'Selesai', 'Berkas lengkap dan siap menjadi anggota.', NULL, 'Antapani', @today, '10:25:00', -6.9141800, 107.6617400, 'Antapani, Kota Bandung', 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-prospect-007', (SELECT id FROM marketing_profiles WHERE code='M07'), 'Vina Marlina', '082100010007', 'Jl. Ibrahim Adjie, Kiaracondong', 'Konter pulsa', 'Perlu Follow Up', 'Menunggu keputusan keluarga.', NULL, 'Kiaracondong', @today, '10:50:00', -6.9273600, 107.6429800, 'Kiaracondong, Kota Bandung', 'Gagal', @now, @now, NULL),
('demo-ui-prospect-008', (SELECT id FROM marketing_profiles WHERE code='M08'), 'Dadang Sukandar', '082100010008', 'Jl. PHH Mustofa, Cicaheum', 'Servis elektronik', 'Baru', 'Prospek dari referral anggota lama.', NULL, 'Cicaheum', @today, '11:15:00', -6.8984100, 107.6532900, 'Cicaheum, Kota Bandung', 'Tersinkronisasi', @now, @now, NULL);

INSERT INTO members (local_uuid, marketing_profile_id, source_prospect_id, resort, input_date, input_time, name, member_number, loan_number, address, phone, business, loan_amount, installment_amount, insurance_amount, collateral, approval_status, member_photo_path, latitude, longitude, location_address, approved_by, approved_at, rejected_by, rejected_at, rejection_reason, sync_status, created_at, updated_at, deleted_at)
VALUES
('demo-ui-member-001', (SELECT id FROM marketing_profiles WHERE code='M01'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-001'), 'Gedebage', @today, '09:00:00', 'Asep Hidayat', 'AGT-DEMO-001', 'PJM-DEMO-001', 'Jl. Soekarno Hatta No. 120, Gedebage', '082100010001', 'Warung sembako', 5000000, 250000, 50000, 'BPKB Motor', 'Disetujui', NULL, -6.9458700, 107.6985400, 'Gedebage, Kota Bandung', @admin_id, @now, NULL, NULL, NULL, 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-member-002', (SELECT id FROM marketing_profiles WHERE code='M02'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-002'), 'Rancasari', @today, '09:30:00', 'Siti Nurjanah', 'AGT-DEMO-002', 'PJM-DEMO-002', 'Jl. Cipamokolan, Rancasari', '082100010002', 'Katering rumahan', 7000000, 350000, 70000, 'Sertifikat kios', 'Menunggu', NULL, -6.9512200, 107.6724800, 'Rancasari, Kota Bandung', NULL, NULL, NULL, NULL, NULL, 'Menunggu Sinkronisasi', @now, @now, NULL),
('demo-ui-member-003', (SELECT id FROM marketing_profiles WHERE code='M05'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-005'), 'Cibiru', @today, '10:35:00', 'Rudi Hermawan', 'AGT-DEMO-003', 'PJM-DEMO-003', 'Jl. Cibiru Hilir, Cibiru', '082100010005', 'Pedagang sayur', 4500000, 225000, 45000, 'BPKB Motor', 'Disetujui', NULL, -6.9354000, 107.7219000, 'Cibiru, Kota Bandung', @admin_id, @now, NULL, NULL, NULL, 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-member-004', (SELECT id FROM marketing_profiles WHERE code='M06'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-006'), 'Antapani', @today, '10:55:00', 'Neni Kartika', 'AGT-DEMO-004', 'PJM-DEMO-004', 'Jl. Terusan Jakarta, Antapani', '082100010006', 'Laundry kiloan', 8000000, 400000, 80000, 'Peralatan laundry', 'Disetujui', NULL, -6.9141800, 107.6617400, 'Antapani, Kota Bandung', @admin_id, @now, NULL, NULL, NULL, 'Tersinkronisasi', @now, @now, NULL),
('demo-ui-member-005', (SELECT id FROM marketing_profiles WHERE code='M07'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-007'), 'Kiaracondong', @today, '11:20:00', 'Vina Marlina', 'AGT-DEMO-005', 'PJM-DEMO-005', 'Jl. Ibrahim Adjie, Kiaracondong', '082100010007', 'Konter pulsa', 3000000, 150000, 30000, 'Etalase toko', 'Ditolak', NULL, -6.9273600, 107.6429800, 'Kiaracondong, Kota Bandung', NULL, NULL, @admin_id, @now, 'Penghasilan belum memenuhi kriteria.', 'Tersinkronisasi', @now, @now, NULL);

INSERT INTO marketing_schedules (marketing_profile_id, prospect_id, day_name, schedule_date, start_time, end_time, consumer_name_snapshot, agenda, area, resort, destination, note, status, created_by, created_at, updated_at, deleted_at)
VALUES
((SELECT id FROM marketing_profiles WHERE code='M01'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-001'), @demo_day, @today, '08:00:00', '09:00:00', 'Asep Hidayat', 'Kunjungan prospek modal usaha', 'Gedebage', 'Gedebage', 'Pasar Gedebage', 'DEMO UI: jadwal referensi', 'Selesai', @admin_id, @now, @now, NULL),
((SELECT id FROM marketing_profiles WHERE code='M02'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-002'), @demo_day, @today, '08:30:00', '09:30:00', 'Siti Nurjanah', 'Survey usaha katering', 'Rancasari', 'Rancasari', 'Cipamokolan', 'DEMO UI: jadwal referensi', 'Berlangsung', @admin_id, @now, @now, NULL),
((SELECT id FROM marketing_profiles WHERE code='M03'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-003'), @demo_day, @today, '09:00:00', '10:00:00', 'Dadan Ramdani', 'Follow up simulasi angsuran', 'Buahbatu', 'Buahbatu', 'Buah Batu Dalam', 'DEMO UI: jadwal referensi', 'Belum Dikunjungi', @admin_id, @now, @now, NULL),
((SELECT id FROM marketing_profiles WHERE code='M04'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-004'), @demo_day, @today, '09:30:00', '10:30:00', 'Yuyun Yuningsih', 'Pengenalan produk pembiayaan', 'Ujungberung', 'Ujungberung', 'AH Nasution', 'DEMO UI: jadwal referensi', 'Belum Dikunjungi', @admin_id, @now, @now, NULL),
((SELECT id FROM marketing_profiles WHERE code='M05'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-005'), @demo_day, @today, '10:00:00', '11:00:00', 'Rudi Hermawan', 'Verifikasi lokasi usaha', 'Cibiru', 'Cibiru', 'Cibiru Hilir', 'DEMO UI: jadwal referensi', 'Selesai', @admin_id, @now, @now, NULL),
((SELECT id FROM marketing_profiles WHERE code='M06'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-006'), @demo_day, @today, '10:30:00', '11:30:00', 'Neni Kartika', 'Pengambilan data anggota', 'Antapani', 'Antapani', 'Terusan Jakarta', 'DEMO UI: jadwal referensi', 'Selesai', @admin_id, @now, @now, NULL),
((SELECT id FROM marketing_profiles WHERE code='M07'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-007'), @demo_day, @today, '11:00:00', '12:00:00', 'Vina Marlina', 'Follow up prospek', 'Kiaracondong', 'Kiaracondong', 'Ibrahim Adjie', 'DEMO UI: jadwal referensi', 'Berlangsung', @admin_id, @now, @now, NULL),
((SELECT id FROM marketing_profiles WHERE code='M08'), (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-008'), @demo_day, @today, '13:00:00', '14:00:00', 'Dadang Sukandar', 'Kunjungan awal', 'Cicaheum', 'Cicaheum', 'PHH Mustofa', 'DEMO UI: jadwal referensi', 'Belum Dikunjungi', @admin_id, @now, @now, NULL);

INSERT INTO daily_operational_reports (local_uuid, marketing_profile_id, report_date, report_time, day_name, resort, storting, insurance_amount, drop_amount, withdrawal_saving, previous_target_amount, previous_target_people, incoming_target_amount, incoming_target_people, outgoing_target_amount, outgoing_target_people, total_target_amount, total_target_people, new_drop, continued_drop, notes, sync_status, created_at, updated_at)
VALUES
('demo-ui-report-001', (SELECT id FROM marketing_profiles WHERE code='M01'), @today, '15:10:00', @demo_day, 'Gedebage', 1650000, 50000, 3200000, 250000, 10000000, 20, 1800000, 4, 900000, 2, 10900000, 22, 2200000, 1000000, 'Setoran lancar.', 'Tersinkronisasi', @now, @now),
('demo-ui-report-002', (SELECT id FROM marketing_profiles WHERE code='M02'), @today, '15:15:00', @demo_day, 'Rancasari', 2100000, 70000, 4200000, 300000, 12000000, 24, 2400000, 5, 1100000, 2, 13300000, 27, 3000000, 1200000, 'Drop calon anggota baru.', 'Tersinkronisasi', @now, @now),
('demo-ui-report-003', (SELECT id FROM marketing_profiles WHERE code='M03'), @today, '15:20:00', @demo_day, 'Buahbatu', 1250000, 30000, 2500000, 150000, 8000000, 16, 1200000, 3, 700000, 1, 8500000, 18, 1500000, 1000000, NULL, 'Menunggu Sinkronisasi', @now, @now),
('demo-ui-report-004', (SELECT id FROM marketing_profiles WHERE code='M04'), @today, '15:25:00', @demo_day, 'Ujungberung', 980000, 25000, 1800000, 100000, 7500000, 14, 1000000, 2, 500000, 1, 8000000, 15, 1000000, 800000, NULL, 'Tersinkronisasi', @now, @now),
('demo-ui-report-005', (SELECT id FROM marketing_profiles WHERE code='M05'), @today, '15:30:00', @demo_day, 'Cibiru', 1750000, 45000, 3500000, 200000, 9000000, 18, 1600000, 4, 800000, 2, 9800000, 20, 2000000, 1500000, NULL, 'Tersinkronisasi', @now, @now),
('demo-ui-report-006', (SELECT id FROM marketing_profiles WHERE code='M06'), @today, '15:35:00', @demo_day, 'Antapani', 2400000, 80000, 5000000, 350000, 14000000, 28, 2600000, 6, 1300000, 3, 15300000, 31, 3500000, 1500000, 'Target harian tercapai.', 'Tersinkronisasi', @now, @now);

INSERT INTO visit_reports (local_uuid, prospect_id, marketing_profile_id, visit_date, visit_time, day_name, visit_purpose, visit_result, prospect_status, notes, follow_up_date, photo_path, photo_caption, resort, latitude, longitude, location_address, sync_status, created_at, updated_at)
VALUES
('demo-ui-visit-001', (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-001'), (SELECT id FROM marketing_profiles WHERE code='M01'), @today, '08:45:00', @demo_day, 'Kunjungan prospek modal usaha', 'Transaksi Selesai', 'Selesai', 'Berkas anggota lengkap.', NULL, NULL, NULL, 'Gedebage', -6.9458700, 107.6985400, 'Gedebage, Kota Bandung', 'Tersinkronisasi', @now, @now),
('demo-ui-visit-002', (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-002'), (SELECT id FROM marketing_profiles WHERE code='M02'), @today, '09:10:00', @demo_day, 'Survey usaha katering', 'Berhasil Bertemu', 'Tertarik', 'Minta waktu melengkapi dokumen.', DATE_ADD(@today, INTERVAL 2 DAY), NULL, NULL, 'Rancasari', -6.9512200, 107.6724800, 'Rancasari, Kota Bandung', 'Tersinkronisasi', @now, @now),
('demo-ui-visit-003', (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-003'), (SELECT id FROM marketing_profiles WHERE code='M03'), @today, '09:55:00', @demo_day, 'Follow up simulasi angsuran', 'Berhasil Bertemu', 'Perlu Follow Up', 'Prospek meminta simulasi tenor lain.', DATE_ADD(@today, INTERVAL 1 DAY), NULL, NULL, 'Buahbatu', -6.9557500, 107.6372000, 'Buahbatu, Kota Bandung', 'Menunggu Sinkronisasi', @now, @now),
('demo-ui-visit-004', (SELECT id FROM prospects WHERE local_uuid='demo-ui-prospect-007'), (SELECT id FROM marketing_profiles WHERE code='M07'), @today, '11:30:00', @demo_day, 'Follow up prospek', 'Tidak Bertemu', 'Perlu Follow Up', 'Pemilik sedang keluar.', DATE_ADD(@today, INTERVAL 3 DAY), NULL, NULL, 'Kiaracondong', -6.9273600, 107.6429800, 'Kiaracondong, Kota Bandung', 'Gagal', @now, @now);

INSERT INTO operational_recaps (report_number, recap_date, day_name, status, created_by, created_at, updated_at)
VALUES (CONCAT('DEMO-', DATE_FORMAT(@today, '%Y%m%d')), @today, @demo_day, 'Selesai', @admin_id, @now, @now)
ON DUPLICATE KEY UPDATE
    day_name = VALUES(day_name),
    status = VALUES(status),
    created_by = VALUES(created_by),
    updated_at = @now;

SET @recap_id = (SELECT id FROM operational_recaps WHERE report_number = CONCAT('DEMO-', DATE_FORMAT(@today, '%Y%m%d')) LIMIT 1);

INSERT INTO operational_recap_rows (operational_recap_id, marketing_profile_id, mg, members_l, members_m, members_k, members_s, target_previous, target_incoming, target_outgoing, target_s, drop_previous, drop_current, drop_total, storting_previous, storting_current, storting_total, percentage, previous_circulation, current_circulation, followed_by, morning_cash, created_at, updated_at)
VALUES
(@recap_id, (SELECT id FROM marketing_profiles WHERE code='M01'), 'M01', 18, 2, 1, 21, 10000000, 1800000, 900000, 10900000, 1200000, 2200000, 3400000, 900000, 1650000, 2550000, 31.19, 8500000, 11100000, 'Deden', 1200000, @now, @now),
(@recap_id, (SELECT id FROM marketing_profiles WHERE code='M02'), 'M02', 22, 3, 1, 26, 12000000, 2400000, 1100000, 13300000, 1600000, 3000000, 4600000, 1200000, 2100000, 3300000, 34.58, 10400000, 13300000, 'Angil', 1500000, @now, @now),
(@recap_id, (SELECT id FROM marketing_profiles WHERE code='M03'), 'M03', 15, 2, 0, 17, 8000000, 1200000, 700000, 8500000, 1000000, 1500000, 2500000, 800000, 1250000, 2050000, 29.41, 7000000, 8950000, 'Ari', 900000, @now, @now),
(@recap_id, (SELECT id FROM marketing_profiles WHERE code='M04'), 'M04', 14, 1, 0, 15, 7500000, 1000000, 500000, 8000000, 800000, 1000000, 1800000, 650000, 980000, 1630000, 22.50, 6700000, 8170000, 'Feri', 700000, @now, @now),
(@recap_id, (SELECT id FROM marketing_profiles WHERE code='M05'), 'M05', 16, 2, 1, 19, 9000000, 1600000, 800000, 9800000, 1000000, 2000000, 3000000, 800000, 1750000, 2550000, 30.61, 8000000, 10250000, 'Sukma', 1000000, @now, @now),
(@recap_id, (SELECT id FROM marketing_profiles WHERE code='M06'), 'M06', 25, 3, 1, 29, 14000000, 2600000, 1300000, 15300000, 1500000, 3500000, 5000000, 1100000, 2400000, 3500000, 32.68, 12500000, 16000000, 'Sandi', 1800000, @now, @now)
ON DUPLICATE KEY UPDATE
    mg = VALUES(mg),
    members_l = VALUES(members_l),
    members_m = VALUES(members_m),
    members_k = VALUES(members_k),
    members_s = VALUES(members_s),
    target_previous = VALUES(target_previous),
    target_incoming = VALUES(target_incoming),
    target_outgoing = VALUES(target_outgoing),
    target_s = VALUES(target_s),
    drop_previous = VALUES(drop_previous),
    drop_current = VALUES(drop_current),
    drop_total = VALUES(drop_total),
    storting_previous = VALUES(storting_previous),
    storting_current = VALUES(storting_current),
    storting_total = VALUES(storting_total),
    percentage = VALUES(percentage),
    previous_circulation = VALUES(previous_circulation),
    current_circulation = VALUES(current_circulation),
    followed_by = VALUES(followed_by),
    morning_cash = VALUES(morning_cash),
    updated_at = @now;

INSERT INTO tracking_sessions (local_uuid, marketing_profile_id, schedule_id, session_date, day_name, started_at, ended_at, status, distance_meters, visit_count, created_at, updated_at)
VALUES
('demo-ui-tracking-001', (SELECT id FROM marketing_profiles WHERE code='M01'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M01') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 08:00:00'), NULL, 'Aktif', 4260, 2, @now, @now),
('demo-ui-tracking-002', (SELECT id FROM marketing_profiles WHERE code='M02'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M02') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 08:20:00'), NULL, 'Aktif', 3880, 1, @now, @now),
('demo-ui-tracking-003', (SELECT id FROM marketing_profiles WHERE code='M03'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M03') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 08:40:00'), NULL, 'Offline', 2150, 1, @now, @now),
('demo-ui-tracking-004', (SELECT id FROM marketing_profiles WHERE code='M04'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M04') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 09:00:00'), NULL, 'GPS Tidak Aktif', 1280, 0, @now, @now),
('demo-ui-tracking-005', (SELECT id FROM marketing_profiles WHERE code='M05'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M05') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 09:15:00'), NULL, 'Aktif', 5120, 2, @now, @now),
('demo-ui-tracking-006', (SELECT id FROM marketing_profiles WHERE code='M06'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M06') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 09:30:00'), NULL, 'Aktif', 6780, 2, @now, @now),
('demo-ui-tracking-007', (SELECT id FROM marketing_profiles WHERE code='M07'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M07') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 09:45:00'), NULL, 'Offline', 3010, 1, @now, @now),
('demo-ui-tracking-008', (SELECT id FROM marketing_profiles WHERE code='M08'), (SELECT id FROM marketing_schedules WHERE note='DEMO UI: jadwal referensi' AND marketing_profile_id=(SELECT id FROM marketing_profiles WHERE code='M08') LIMIT 1), @today, @demo_day, CONCAT(@today, ' 10:00:00'), NULL, 'Aktif', 2450, 1, @now, @now);

INSERT INTO tracking_points (local_uuid, tracking_session_id, latitude, longitude, accuracy_meters, speed_mps, heading, altitude_meters, address, point_type, recorded_at, received_at, created_at)
VALUES
('demo-ui-point-001-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-001'), -6.9458700, 107.6985400, 18.50, 2.30, 75.00, 710.00, 'Pasar Gedebage, Kota Bandung', 'Mulai', CONCAT(@today, ' 08:05:00'), CONCAT(@today, ' 08:05:05'), @now),
('demo-ui-point-001-b', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-001'), -6.9397200, 107.6864100, 16.00, 3.10, 88.00, 708.00, 'Summarecon Bandung, Gedebage', 'Perjalanan', CONCAT(@today, ' 10:15:00'), CONCAT(@today, ' 10:15:05'), @now),
('demo-ui-point-002-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-002'), -6.9512200, 107.6724800, 22.00, 1.80, 110.00, 705.00, 'Cipamokolan, Rancasari', 'Mulai', CONCAT(@today, ' 08:25:00'), CONCAT(@today, ' 08:25:05'), @now),
('demo-ui-point-002-b', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-002'), -6.9561200, 107.6789500, 20.00, 2.20, 122.00, 706.00, 'Rancasari, Kota Bandung', 'Kunjungan', CONCAT(@today, ' 10:20:00'), CONCAT(@today, ' 10:20:05'), @now),
('demo-ui-point-003-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-003'), -6.9557500, 107.6372000, 25.00, 0.00, 180.00, 699.00, 'Buah Batu Dalam, Kota Bandung', 'Kunjungan', CONCAT(@today, ' 09:55:00'), CONCAT(@today, ' 09:55:06'), @now),
('demo-ui-point-004-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-004'), -6.9147200, 107.7011800, 65.00, 0.00, 0.00, 718.00, 'Ujungberung, Kota Bandung', 'Perjalanan', CONCAT(@today, ' 09:40:00'), CONCAT(@today, ' 09:40:08'), @now),
('demo-ui-point-005-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-005'), -6.9354000, 107.7219000, 19.00, 2.60, 95.00, 720.00, 'Cibiru Hilir, Kota Bandung', 'Kunjungan', CONCAT(@today, ' 10:35:00'), CONCAT(@today, ' 10:35:04'), @now),
('demo-ui-point-006-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-006'), -6.9141800, 107.6617400, 15.00, 3.20, 210.00, 704.00, 'Antapani, Kota Bandung', 'Kunjungan', CONCAT(@today, ' 10:55:00'), CONCAT(@today, ' 10:55:04'), @now),
('demo-ui-point-007-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-007'), -6.9273600, 107.6429800, 27.00, 0.00, 150.00, 702.00, 'Kiaracondong, Kota Bandung', 'Kunjungan', CONCAT(@today, ' 11:30:00'), CONCAT(@today, ' 11:30:07'), @now),
('demo-ui-point-008-a', (SELECT id FROM tracking_sessions WHERE local_uuid='demo-ui-tracking-008'), -6.8984100, 107.6532900, 18.00, 2.10, 65.00, 713.00, 'Cicaheum, Kota Bandung', 'Perjalanan', CONCAT(@today, ' 11:40:00'), CONCAT(@today, ' 11:40:05'), @now);

SELECT 'Demo reference UI data imported' AS status,
       @today AS demo_date,
       @demo_day AS demo_day,
       (SELECT COUNT(*) FROM marketing_profiles WHERE code BETWEEN 'M01' AND 'M13') AS marketing_count,
       (SELECT COUNT(*) FROM tracking_sessions WHERE local_uuid LIKE 'demo-ui-%') AS tracking_session_count,
       (SELECT COUNT(*) FROM prospects WHERE local_uuid LIKE 'demo-ui-%') AS prospect_count,
       (SELECT COUNT(*) FROM members WHERE local_uuid LIKE 'demo-ui-%') AS member_count;
