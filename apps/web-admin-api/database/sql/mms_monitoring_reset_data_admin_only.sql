-- MMS Monitoring reset data for phpMyAdmin.
-- Purpose: remove development/demo/runtime data and recreate only one admin account.
-- Admin username: admin
-- Temporary password: AdminMMS123!
-- Change this password after first login.
SET FOREIGN_KEY_CHECKS=0;
TRUNCATE TABLE `tracking_points`;
TRUNCATE TABLE `tracking_sessions`;
TRUNCATE TABLE `visit_reports`;
TRUNCATE TABLE `daily_operational_reports`;
TRUNCATE TABLE `operational_recap_rows`;
TRUNCATE TABLE `operational_recaps`;
TRUNCATE TABLE `marketing_schedules`;
TRUNCATE TABLE `members`;
TRUNCATE TABLE `prospects`;
TRUNCATE TABLE `marketing_work_days`;
TRUNCATE TABLE `marketing_profiles`;
TRUNCATE TABLE `personal_access_tokens`;
TRUNCATE TABLE `sessions`;
TRUNCATE TABLE `password_reset_tokens`;
TRUNCATE TABLE `jobs`;
TRUNCATE TABLE `job_batches`;
TRUNCATE TABLE `failed_jobs`;
TRUNCATE TABLE `cache_locks`;
TRUNCATE TABLE `cache`;
TRUNCATE TABLE `users`;
SET FOREIGN_KEY_CHECKS=1;
-- Admin-only initial account.
-- Username: admin
-- Temporary password: AdminMMS123!
-- Change this password after first login.
INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `last_login_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Administrator MMS', 'admin', 'admin@mms.local', NULL, '$2y$10$c9CvIFx0UkkDB/m.C8d15.Mig5XmnrL6I5t3C2YPa6VNXuK0GZRMK', 'admin', 1, NULL, NULL, '2026-08-02 02:35:00', '2026-08-02 02:35:00', NULL);
