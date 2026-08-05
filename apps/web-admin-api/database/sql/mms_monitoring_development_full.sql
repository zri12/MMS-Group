
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `daily_operational_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `daily_operational_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `local_uuid` char(36) DEFAULT NULL,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `report_date` date NOT NULL,
  `report_time` time NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `resort` varchar(100) NOT NULL,
  `storting` bigint(20) unsigned NOT NULL,
  `insurance_amount` bigint(20) unsigned NOT NULL,
  `drop_amount` bigint(20) unsigned NOT NULL,
  `withdrawal_saving` bigint(20) unsigned NOT NULL,
  `previous_target_amount` bigint(20) unsigned NOT NULL,
  `previous_target_people` smallint(5) unsigned NOT NULL,
  `incoming_target_amount` bigint(20) unsigned NOT NULL,
  `incoming_target_people` smallint(5) unsigned NOT NULL,
  `outgoing_target_amount` bigint(20) unsigned NOT NULL,
  `outgoing_target_people` smallint(5) unsigned NOT NULL,
  `total_target_amount` bigint(20) unsigned NOT NULL,
  `total_target_people` smallint(5) unsigned NOT NULL,
  `new_drop` bigint(20) unsigned NOT NULL,
  `continued_drop` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `sync_status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `daily_operational_reports_local_uuid_unique` (`local_uuid`),
  KEY `idx_reports_marketing_date` (`marketing_profile_id`,`report_date`),
  KEY `idx_reports_date` (`report_date`),
  KEY `idx_reports_day` (`day_name`),
  KEY `idx_reports_resort` (`resort`),
  KEY `idx_reports_sync_status` (`sync_status`),
  CONSTRAINT `daily_operational_reports_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketing_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketing_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `code` varchar(10) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `area` varchar(100) NOT NULL,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketing_profiles_user_id_unique` (`user_id`),
  UNIQUE KEY `marketing_profiles_code_unique` (`code`),
  KEY `idx_marketing_profiles_phone` (`phone`),
  KEY `idx_marketing_profiles_area` (`area`),
  CONSTRAINT `marketing_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketing_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketing_schedules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `prospect_id` bigint(20) unsigned DEFAULT NULL,
  `day_name` varchar(20) NOT NULL,
  `schedule_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `consumer_name_snapshot` varchar(150) DEFAULT NULL,
  `agenda` varchar(255) NOT NULL,
  `area` varchar(100) NOT NULL,
  `resort` varchar(100) DEFAULT NULL,
  `destination` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(30) NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `marketing_schedules_prospect_id_foreign` (`prospect_id`),
  KEY `marketing_schedules_created_by_foreign` (`created_by`),
  KEY `idx_schedules_marketing_date` (`marketing_profile_id`,`schedule_date`),
  KEY `idx_schedules_status_date` (`status`,`schedule_date`),
  KEY `idx_schedules_day` (`day_name`),
  KEY `idx_schedules_area` (`area`),
  KEY `idx_schedules_resort` (`resort`),
  CONSTRAINT `marketing_schedules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `marketing_schedules_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`),
  CONSTRAINT `marketing_schedules_prospect_id_foreign` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `marketing_work_days`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `marketing_work_days` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_work_days_marketing_day` (`marketing_profile_id`,`day_name`),
  KEY `idx_work_days_day` (`day_name`),
  CONSTRAINT `marketing_work_days_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `members` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `local_uuid` char(36) DEFAULT NULL,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `source_prospect_id` bigint(20) unsigned DEFAULT NULL,
  `resort` varchar(100) NOT NULL,
  `input_date` date NOT NULL,
  `input_time` time NOT NULL,
  `name` varchar(150) NOT NULL,
  `member_number` varchar(50) NOT NULL,
  `loan_number` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(30) NOT NULL,
  `business` varchar(150) NOT NULL,
  `loan_amount` bigint(20) unsigned NOT NULL,
  `installment_amount` bigint(20) unsigned NOT NULL,
  `insurance_amount` bigint(20) unsigned NOT NULL,
  `collateral` varchar(255) NOT NULL,
  `approval_status` varchar(30) NOT NULL,
  `member_photo_path` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `location_address` varchar(255) DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejected_by` bigint(20) unsigned DEFAULT NULL,
  `rejected_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `sync_status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `members_member_number_unique` (`member_number`),
  UNIQUE KEY `members_loan_number_unique` (`loan_number`),
  UNIQUE KEY `members_local_uuid_unique` (`local_uuid`),
  UNIQUE KEY `uq_members_source_prospect` (`source_prospect_id`),
  KEY `members_approved_by_foreign` (`approved_by`),
  KEY `members_rejected_by_foreign` (`rejected_by`),
  KEY `idx_members_marketing_date` (`marketing_profile_id`,`input_date`),
  KEY `idx_members_approval_resort` (`approval_status`,`resort`),
  KEY `idx_members_input_date` (`input_date`),
  KEY `idx_members_sync_status` (`sync_status`),
  KEY `idx_members_name` (`name`),
  KEY `idx_members_phone` (`phone`),
  CONSTRAINT `members_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `members_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`),
  CONSTRAINT `members_rejected_by_foreign` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `members_source_prospect_id_foreign` FOREIGN KEY (`source_prospect_id`) REFERENCES `prospects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `operational_recap_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operational_recap_rows` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `operational_recap_id` bigint(20) unsigned NOT NULL,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `mg` varchar(20) NOT NULL,
  `members_l` int(10) unsigned NOT NULL,
  `members_m` int(10) unsigned NOT NULL,
  `members_k` int(10) unsigned NOT NULL,
  `members_s` int(10) unsigned NOT NULL,
  `target_previous` bigint(20) unsigned NOT NULL,
  `target_incoming` bigint(20) unsigned NOT NULL,
  `target_outgoing` bigint(20) unsigned NOT NULL,
  `target_s` bigint(20) unsigned NOT NULL,
  `drop_previous` bigint(20) unsigned NOT NULL,
  `drop_current` bigint(20) unsigned NOT NULL,
  `drop_total` bigint(20) unsigned NOT NULL,
  `storting_previous` bigint(20) unsigned NOT NULL,
  `storting_current` bigint(20) unsigned NOT NULL,
  `storting_total` bigint(20) unsigned NOT NULL,
  `percentage` decimal(7,2) DEFAULT NULL,
  `previous_circulation` bigint(20) unsigned NOT NULL,
  `current_circulation` bigint(20) unsigned NOT NULL,
  `followed_by` varchar(150) NOT NULL,
  `morning_cash` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_recap_marketing` (`operational_recap_id`,`marketing_profile_id`),
  KEY `idx_recap_rows_marketing` (`marketing_profile_id`),
  CONSTRAINT `operational_recap_rows_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`),
  CONSTRAINT `operational_recap_rows_operational_recap_id_foreign` FOREIGN KEY (`operational_recap_id`) REFERENCES `operational_recaps` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `operational_recaps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operational_recaps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `report_number` varchar(50) NOT NULL,
  `recap_date` date NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `status` varchar(30) NOT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `operational_recaps_report_number_unique` (`report_number`),
  UNIQUE KEY `operational_recaps_recap_date_unique` (`recap_date`),
  KEY `operational_recaps_created_by_foreign` (`created_by`),
  KEY `idx_operational_recaps_day` (`day_name`),
  KEY `idx_operational_recaps_status` (`status`),
  CONSTRAINT `operational_recaps_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  KEY `personal_access_tokens_expires_at_index` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `prospects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prospects` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `local_uuid` char(36) DEFAULT NULL,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `business` varchar(150) NOT NULL,
  `status` varchar(30) NOT NULL,
  `initial_visit_result` text NOT NULL,
  `notes` text DEFAULT NULL,
  `resort` varchar(100) NOT NULL,
  `input_date` date NOT NULL,
  `input_time` time NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `location_address` varchar(255) DEFAULT NULL,
  `sync_status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `prospects_local_uuid_unique` (`local_uuid`),
  KEY `idx_prospects_marketing_date` (`marketing_profile_id`,`input_date`),
  KEY `idx_prospects_status_resort` (`status`,`resort`),
  KEY `idx_prospects_input_date` (`input_date`),
  KEY `idx_prospects_sync_status` (`sync_status`),
  KEY `idx_prospects_name` (`name`),
  KEY `idx_prospects_phone` (`phone`),
  CONSTRAINT `prospects_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tracking_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracking_points` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `local_uuid` char(36) DEFAULT NULL,
  `tracking_session_id` bigint(20) unsigned NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `accuracy_meters` decimal(8,2) DEFAULT NULL,
  `speed_mps` decimal(8,2) DEFAULT NULL,
  `heading` decimal(8,2) DEFAULT NULL,
  `altitude_meters` decimal(10,2) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `point_type` varchar(30) NOT NULL,
  `recorded_at` datetime NOT NULL,
  `received_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracking_points_local_uuid_unique` (`local_uuid`),
  KEY `idx_tracking_points_session_time` (`tracking_session_id`,`recorded_at`),
  KEY `idx_tracking_points_type` (`point_type`),
  KEY `idx_tracking_points_recorded` (`recorded_at`),
  CONSTRAINT `tracking_points_tracking_session_id_foreign` FOREIGN KEY (`tracking_session_id`) REFERENCES `tracking_sessions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tracking_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tracking_sessions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `local_uuid` char(36) DEFAULT NULL,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `schedule_id` bigint(20) unsigned DEFAULT NULL,
  `session_date` date NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `started_at` datetime NOT NULL,
  `ended_at` datetime DEFAULT NULL,
  `status` varchar(30) NOT NULL,
  `distance_meters` int(10) unsigned DEFAULT NULL,
  `visit_count` smallint(5) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tracking_sessions_local_uuid_unique` (`local_uuid`),
  KEY `idx_tracking_sessions_marketing_date` (`marketing_profile_id`,`session_date`),
  KEY `idx_tracking_sessions_schedule` (`schedule_id`),
  KEY `idx_tracking_sessions_date` (`session_date`),
  KEY `idx_tracking_sessions_day` (`day_name`),
  KEY `idx_tracking_sessions_started` (`started_at`),
  KEY `idx_tracking_sessions_status` (`status`),
  CONSTRAINT `tracking_sessions_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`),
  CONSTRAINT `tracking_sessions_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `marketing_schedules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(30) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `visit_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visit_reports` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `local_uuid` char(36) DEFAULT NULL,
  `prospect_id` bigint(20) unsigned NOT NULL,
  `marketing_profile_id` bigint(20) unsigned NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time NOT NULL,
  `day_name` varchar(20) NOT NULL,
  `visit_purpose` varchar(255) NOT NULL,
  `visit_result` varchar(50) NOT NULL,
  `prospect_status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `photo_caption` varchar(255) DEFAULT NULL,
  `resort` varchar(100) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `location_address` varchar(255) DEFAULT NULL,
  `sync_status` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `visit_reports_local_uuid_unique` (`local_uuid`),
  KEY `idx_visit_reports_prospect` (`prospect_id`),
  KEY `idx_visit_reports_marketing_date` (`marketing_profile_id`,`visit_date`),
  KEY `idx_visit_reports_result_status` (`visit_result`,`prospect_status`),
  KEY `idx_visit_reports_follow_up` (`follow_up_date`),
  KEY `idx_visit_reports_day` (`day_name`),
  KEY `idx_visit_reports_resort` (`resort`),
  KEY `idx_visit_reports_sync_status` (`sync_status`),
  CONSTRAINT `visit_reports_marketing_profile_id_foreign` FOREIGN KEY (`marketing_profile_id`) REFERENCES `marketing_profiles` (`id`),
  CONSTRAINT `visit_reports_prospect_id_foreign` FOREIGN KEY (`prospect_id`) REFERENCES `prospects` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



-- MMS development data

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `daily_operational_reports` WRITE;
/*!40000 ALTER TABLE `daily_operational_reports` DISABLE KEYS */;
INSERT INTO `daily_operational_reports` VALUES (1,'00000000-0000-4000-8000-000000000301',1,'2026-07-20','16:00:00','Senin','Gedebage',2300000,150000,5500000,200000,6500000,2,2000000,2,500000,1,8000000,4,2500000,3000000,'Laporan operasional development M01.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(2,'00000000-0000-4000-8000-000000000302',2,'2026-07-20','16:00:00','Senin','Rancasari',2500000,160000,6000000,210000,7000000,3,2000000,2,500000,1,8500000,5,2500000,3500000,'Laporan operasional development M02.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(3,'00000000-0000-4000-8000-000000000303',3,'2026-07-20','16:00:00','Senin','Buahbatu',2700000,170000,6500000,220000,7500000,4,2000000,2,500000,1,9000000,6,2500000,4000000,'Laporan operasional development M03.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(4,'00000000-0000-4000-8000-000000000304',4,'2026-07-20','16:00:00','Senin','Ujungberung',2800000,180000,6500000,230000,7500000,2,2000000,2,500000,1,9000000,7,2500000,4000000,'Laporan operasional development M04.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(5,'00000000-0000-4000-8000-000000000305',5,'2026-07-20','16:00:00','Senin','Cibiru',2900000,190000,7000000,240000,8000000,3,2000000,2,500000,1,9500000,4,2500000,4500000,'Laporan operasional development M05.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(6,'00000000-0000-4000-8000-000000000306',6,'2026-07-20','16:00:00','Senin','Antapani',3000000,200000,7000000,250000,8000000,4,2000000,2,500000,1,9500000,5,2500000,4500000,'Laporan operasional development M06.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(7,'00000000-0000-4000-8000-000000000307',7,'2026-07-20','16:00:00','Senin','Kiaracondong',3100000,210000,7500000,260000,8500000,2,2000000,2,500000,1,10000000,6,2500000,5000000,'Laporan operasional development M07.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(8,'00000000-0000-4000-8000-000000000308',8,'2026-07-20','16:00:00','Senin','Cicaheum',3200000,220000,7500000,270000,8500000,3,2000000,2,500000,1,10000000,7,2500000,5000000,'Laporan operasional development M08.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(9,'00000000-0000-4000-8000-000000000309',9,'2026-07-20','16:00:00','Senin','Sukajadi',3300000,230000,7500000,280000,9000000,4,2000000,2,500000,1,10500000,4,2500000,5000000,'Laporan operasional development M09.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(10,'00000000-0000-4000-8000-000000000310',10,'2026-07-20','16:00:00','Senin','Lengkong',3400000,240000,8000000,290000,9000000,2,2000000,2,500000,1,10500000,5,2500000,5500000,'Laporan operasional development M10.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(11,'00000000-0000-4000-8000-000000000311',11,'2026-07-20','16:00:00','Senin','Arcamanik',3500000,250000,8000000,300000,9500000,3,2000000,2,500000,1,11000000,6,2500000,5500000,'Laporan operasional development M11.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(12,'00000000-0000-4000-8000-000000000312',12,'2026-07-20','16:00:00','Senin','Cimahi',2500000,260000,7500000,310000,9500000,4,2000000,2,500000,1,11000000,7,2500000,5000000,'Laporan operasional development M12.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09'),(13,'00000000-0000-4000-8000-000000000313',13,'2026-07-20','16:00:00','Senin','Cileunyi',2500000,270000,6500000,320000,5500000,2,2000000,2,500000,1,7000000,4,2500000,4000000,'Laporan operasional development M13.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09');
/*!40000 ALTER TABLE `daily_operational_reports` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `marketing_profiles` WRITE;
/*!40000 ALTER TABLE `marketing_profiles` DISABLE KEYS */;
INSERT INTO `marketing_profiles` VALUES (1,2,'M01',NULL,'Gedebage',NULL,'2026-07-29 04:56:06','2026-07-29 04:56:06'),(2,3,'M02',NULL,'Rancasari',NULL,'2026-07-29 04:56:06','2026-07-29 04:56:06'),(3,4,'M03',NULL,'Buahbatu',NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07'),(4,5,'M04',NULL,'Ujungberung',NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07'),(5,6,'M05',NULL,'Cibiru',NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07'),(6,7,'M06',NULL,'Antapani',NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07'),(7,8,'M07',NULL,'Kiaracondong',NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07'),(8,9,'M08',NULL,'Cicaheum',NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08'),(9,10,'M09',NULL,'Sukajadi',NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08'),(10,11,'M10',NULL,'Lengkong',NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08'),(11,12,'M11',NULL,'Arcamanik',NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08'),(12,13,'M12',NULL,'Cimahi',NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08'),(13,14,'M13',NULL,'Cileunyi',NULL,'2026-07-29 04:56:09','2026-07-29 04:56:09');
/*!40000 ALTER TABLE `marketing_profiles` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `marketing_schedules` WRITE;
/*!40000 ALTER TABLE `marketing_schedules` DISABLE KEYS */;
INSERT INTO `marketing_schedules` VALUES (1,1,1,'Senin','2026-07-20','08:30:00',NULL,'Ahmad Hidayat','Presentasi produk tabungan','Gedebage','Gedebage','Gedebage, Kota Bandung',NULL,'Selesai',1,'2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(2,1,NULL,'Senin','2026-07-20','10:00:00',NULL,'Herman Malik','Survei pengajuan anggota','Buahbatu','Buahbatu','Buahbatu, Kota Bandung',NULL,'Berlangsung',1,'2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(3,1,2,'Senin','2026-07-20','13:30:00',NULL,'Siti Nurjanah','Follow up produk simpanan','Rancasari','Rancasari','Rancasari, Kota Bandung',NULL,'Belum Dikunjungi',1,'2026-07-29 04:56:09','2026-07-29 04:56:09',NULL);
/*!40000 ALTER TABLE `marketing_schedules` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `marketing_work_days` WRITE;
/*!40000 ALTER TABLE `marketing_work_days` DISABLE KEYS */;
INSERT INTO `marketing_work_days` VALUES (1,1,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(2,1,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(3,1,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(4,1,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(5,1,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(6,1,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(7,2,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(8,2,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(9,2,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(10,2,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(11,2,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(12,2,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(13,3,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(14,3,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(15,3,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(16,3,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(17,3,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(18,3,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(19,4,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(20,4,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(21,4,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(22,4,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(23,4,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(24,4,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(25,5,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(26,5,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(27,5,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(28,5,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(29,5,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(30,5,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(31,6,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(32,6,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(33,6,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(34,6,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(35,6,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(36,6,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(37,7,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(38,7,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(39,7,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(40,7,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(41,7,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(42,7,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(43,8,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(44,8,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(45,8,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(46,8,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(47,8,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(48,8,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(49,9,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(50,9,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(51,9,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(52,9,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(53,9,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(54,9,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(55,10,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(56,10,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(57,10,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(58,10,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(59,10,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(60,10,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(61,11,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(62,11,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(63,11,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(64,11,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(65,11,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(66,11,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(67,12,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(68,12,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(69,12,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(70,12,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(71,12,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(72,12,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(73,13,'Senin','2026-07-29 04:56:09','2026-07-29 04:56:09'),(74,13,'Selasa','2026-07-29 04:56:09','2026-07-29 04:56:09'),(75,13,'Rabu','2026-07-29 04:56:09','2026-07-29 04:56:09'),(76,13,'Kamis','2026-07-29 04:56:09','2026-07-29 04:56:09'),(77,13,'Jumat','2026-07-29 04:56:09','2026-07-29 04:56:09'),(78,13,'Sabtu','2026-07-29 04:56:09','2026-07-29 04:56:09');
/*!40000 ALTER TABLE `marketing_work_days` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'00000000-0000-4000-8000-000000000201',1,NULL,'Gedebage','2026-07-20','09:00:00','Herman Malik','0468','LN-0468','Alamat development M01-A','081211120301','Toko Kelontong',4000000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9300000,107.7000000,'Gedebage, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(2,'00000000-0000-4000-8000-000000000202',1,NULL,'Gedebage','2026-07-20','10:00:00','Wawan Setiawan','0469','LN-0469','Alamat development M01-B','081211120302','Warung Sembako',5500000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9301000,107.7001000,'Gedebage, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(3,'00000000-0000-4000-8000-000000000203',1,NULL,'Gedebage','2026-07-20','11:00:00','Yuli Astuti','0470','LN-0470','Alamat development M01-C','081211120303','Jasa Harian',7000000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9302000,107.7002000,'Gedebage, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(4,'00000000-0000-4000-8000-000000000204',2,NULL,'Rancasari','2026-07-20','09:10:00','Anggota Demo M02 Menunggu','0471','LN-0471','Alamat development M02-A','081211120304','Toko Kelontong',4100000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9310000,107.7010000,'Rancasari, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(5,'00000000-0000-4000-8000-000000000205',2,NULL,'Rancasari','2026-07-20','10:10:00','Anggota Demo M02 Disetujui','0472','LN-0472','Alamat development M02-B','081211120305','Warung Sembako',5600000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9311000,107.7011000,'Rancasari, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(6,'00000000-0000-4000-8000-000000000206',2,NULL,'Rancasari','2026-07-20','11:10:00','Anggota Demo M02 Ditolak','0473','LN-0473','Alamat development M02-C','081211120306','Jasa Harian',7100000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9312000,107.7012000,'Rancasari, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(7,'00000000-0000-4000-8000-000000000207',3,NULL,'Buahbatu','2026-07-20','09:20:00','Anggota Demo M03 Menunggu','0474','LN-0474','Alamat development M03-A','081211120307','Toko Kelontong',4200000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9320000,107.7020000,'Buahbatu, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(8,'00000000-0000-4000-8000-000000000208',3,NULL,'Buahbatu','2026-07-20','10:20:00','Anggota Demo M03 Disetujui','0475','LN-0475','Alamat development M03-B','081211120308','Warung Sembako',5700000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9321000,107.7021000,'Buahbatu, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(9,'00000000-0000-4000-8000-000000000209',3,NULL,'Buahbatu','2026-07-20','11:20:00','Anggota Demo M03 Ditolak','0476','LN-0476','Alamat development M03-C','081211120309','Jasa Harian',7200000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9322000,107.7022000,'Buahbatu, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(10,'00000000-0000-4000-8000-000000000210',4,NULL,'Ujungberung','2026-07-20','09:30:00','Anggota Demo M04 Menunggu','0477','LN-0477','Alamat development M04-A','081211120310','Toko Kelontong',4300000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9330000,107.7030000,'Ujungberung, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(11,'00000000-0000-4000-8000-000000000211',4,NULL,'Ujungberung','2026-07-20','10:30:00','Anggota Demo M04 Disetujui','0478','LN-0478','Alamat development M04-B','081211120311','Warung Sembako',5800000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9331000,107.7031000,'Ujungberung, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(12,'00000000-0000-4000-8000-000000000212',4,NULL,'Ujungberung','2026-07-20','11:30:00','Anggota Demo M04 Ditolak','0479','LN-0479','Alamat development M04-C','081211120312','Jasa Harian',7300000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9332000,107.7032000,'Ujungberung, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(13,'00000000-0000-4000-8000-000000000213',5,NULL,'Cibiru','2026-07-20','09:00:00','Anggota Demo M05 Menunggu','0480','LN-0480','Alamat development M05-A','081211120313','Toko Kelontong',4400000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9340000,107.7040000,'Cibiru, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(14,'00000000-0000-4000-8000-000000000214',5,NULL,'Cibiru','2026-07-20','10:00:00','Anggota Demo M05 Disetujui','0481','LN-0481','Alamat development M05-B','081211120314','Warung Sembako',5900000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9341000,107.7041000,'Cibiru, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(15,'00000000-0000-4000-8000-000000000215',5,NULL,'Cibiru','2026-07-20','11:00:00','Anggota Demo M05 Ditolak','0482','LN-0482','Alamat development M05-C','081211120315','Jasa Harian',7400000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9342000,107.7042000,'Cibiru, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(16,'00000000-0000-4000-8000-000000000216',6,NULL,'Antapani','2026-07-20','09:10:00','Anggota Demo M06 Menunggu','0483','LN-0483','Alamat development M06-A','081211120316','Toko Kelontong',4500000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9350000,107.7050000,'Antapani, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(17,'00000000-0000-4000-8000-000000000217',6,NULL,'Antapani','2026-07-20','10:10:00','Anggota Demo M06 Disetujui','0484','LN-0484','Alamat development M06-B','081211120317','Warung Sembako',6000000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9351000,107.7051000,'Antapani, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(18,'00000000-0000-4000-8000-000000000218',6,NULL,'Antapani','2026-07-20','11:10:00','Anggota Demo M06 Ditolak','0485','LN-0485','Alamat development M06-C','081211120318','Jasa Harian',7500000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9352000,107.7052000,'Antapani, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(19,'00000000-0000-4000-8000-000000000219',7,NULL,'Kiaracondong','2026-07-20','09:20:00','Anggota Demo M07 Menunggu','0486','LN-0486','Alamat development M07-A','081211120319','Toko Kelontong',4600000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9360000,107.7060000,'Kiaracondong, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(20,'00000000-0000-4000-8000-000000000220',7,NULL,'Kiaracondong','2026-07-20','10:20:00','Anggota Demo M07 Disetujui','0487','LN-0487','Alamat development M07-B','081211120320','Warung Sembako',6100000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9361000,107.7061000,'Kiaracondong, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(21,'00000000-0000-4000-8000-000000000221',7,NULL,'Kiaracondong','2026-07-20','11:20:00','Anggota Demo M07 Ditolak','0488','LN-0488','Alamat development M07-C','081211120321','Jasa Harian',7600000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9362000,107.7062000,'Kiaracondong, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(22,'00000000-0000-4000-8000-000000000222',8,NULL,'Cicaheum','2026-07-20','09:30:00','Anggota Demo M08 Menunggu','0489','LN-0489','Alamat development M08-A','081211120322','Toko Kelontong',4700000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9370000,107.7070000,'Cicaheum, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(23,'00000000-0000-4000-8000-000000000223',8,NULL,'Cicaheum','2026-07-20','10:30:00','Anggota Demo M08 Disetujui','0490','LN-0490','Alamat development M08-B','081211120323','Warung Sembako',6200000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9371000,107.7071000,'Cicaheum, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(24,'00000000-0000-4000-8000-000000000224',8,NULL,'Cicaheum','2026-07-20','11:30:00','Anggota Demo M08 Ditolak','0491','LN-0491','Alamat development M08-C','081211120324','Jasa Harian',7700000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9372000,107.7072000,'Cicaheum, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(25,'00000000-0000-4000-8000-000000000225',9,NULL,'Sukajadi','2026-07-20','09:00:00','Anggota Demo M09 Menunggu','0492','LN-0492','Alamat development M09-A','081211120325','Toko Kelontong',4800000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9380000,107.7080000,'Sukajadi, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(26,'00000000-0000-4000-8000-000000000226',9,NULL,'Sukajadi','2026-07-20','10:00:00','Anggota Demo M09 Disetujui','0493','LN-0493','Alamat development M09-B','081211120326','Warung Sembako',6300000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9381000,107.7081000,'Sukajadi, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(27,'00000000-0000-4000-8000-000000000227',9,NULL,'Sukajadi','2026-07-20','11:00:00','Anggota Demo M09 Ditolak','0494','LN-0494','Alamat development M09-C','081211120327','Jasa Harian',7800000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9382000,107.7082000,'Sukajadi, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(28,'00000000-0000-4000-8000-000000000228',10,NULL,'Lengkong','2026-07-20','09:10:00','Anggota Demo M10 Menunggu','0495','LN-0495','Alamat development M10-A','081211120328','Toko Kelontong',4900000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9390000,107.7090000,'Lengkong, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(29,'00000000-0000-4000-8000-000000000229',10,NULL,'Lengkong','2026-07-20','10:10:00','Anggota Demo M10 Disetujui','0496','LN-0496','Alamat development M10-B','081211120329','Warung Sembako',6400000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9391000,107.7091000,'Lengkong, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(30,'00000000-0000-4000-8000-000000000230',10,NULL,'Lengkong','2026-07-20','11:10:00','Anggota Demo M10 Ditolak','0497','LN-0497','Alamat development M10-C','081211120330','Jasa Harian',7900000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9392000,107.7092000,'Lengkong, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(31,'00000000-0000-4000-8000-000000000231',11,NULL,'Arcamanik','2026-07-20','09:20:00','Anggota Demo M11 Menunggu','0498','LN-0498','Alamat development M11-A','081211120331','Toko Kelontong',5000000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9400000,107.7100000,'Arcamanik, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(32,'00000000-0000-4000-8000-000000000232',11,NULL,'Arcamanik','2026-07-20','10:20:00','Anggota Demo M11 Disetujui','0499','LN-0499','Alamat development M11-B','081211120332','Warung Sembako',6500000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9401000,107.7101000,'Arcamanik, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(33,'00000000-0000-4000-8000-000000000233',11,NULL,'Arcamanik','2026-07-20','11:20:00','Anggota Demo M11 Ditolak','0500','LN-0500','Alamat development M11-C','081211120333','Jasa Harian',8000000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9402000,107.7102000,'Arcamanik, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(34,'00000000-0000-4000-8000-000000000234',12,NULL,'Cimahi','2026-07-20','09:30:00','Anggota Demo M12 Menunggu','0501','LN-0501','Alamat development M12-A','081211120334','Toko Kelontong',5100000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9410000,107.7110000,'Cimahi, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(35,'00000000-0000-4000-8000-000000000235',12,NULL,'Cimahi','2026-07-20','10:30:00','Anggota Demo M12 Disetujui','0502','LN-0502','Alamat development M12-B','081211120335','Warung Sembako',6600000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9411000,107.7111000,'Cimahi, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(36,'00000000-0000-4000-8000-000000000236',12,NULL,'Cimahi','2026-07-20','11:30:00','Anggota Demo M12 Ditolak','0503','LN-0503','Alamat development M12-C','081211120336','Jasa Harian',8100000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9412000,107.7112000,'Cimahi, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(37,'00000000-0000-4000-8000-000000000237',13,NULL,'Cileunyi','2026-07-20','09:00:00','Anggota Demo M13 Menunggu','0504','LN-0504','Alamat development M13-A','081211120337','Toko Kelontong',5200000,200000,90000,'BPKB motor','Menunggu',NULL,-6.9420000,107.7120000,'Cileunyi, Kota Bandung',NULL,NULL,NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(38,'00000000-0000-4000-8000-000000000238',13,NULL,'Cileunyi','2026-07-20','10:00:00','Anggota Demo M13 Disetujui','0505','LN-0505','Alamat development M13-B','081211120338','Warung Sembako',6700000,275000,105000,'BPKB motor','Disetujui',NULL,-6.9421000,107.7121000,'Cileunyi, Kota Bandung',1,'2026-07-20 07:00:00',NULL,NULL,NULL,'Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(39,'00000000-0000-4000-8000-000000000239',13,NULL,'Cileunyi','2026-07-20','11:00:00','Anggota Demo M13 Ditolak','0506','LN-0506','Alamat development M13-C','081211120339','Jasa Harian',8200000,350000,120000,'BPKB motor','Ditolak',NULL,-6.9422000,107.7122000,'Cileunyi, Kota Bandung',NULL,NULL,1,'2026-07-20 07:30:00','Data pengajuan belum memenuhi ketentuan development.','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (16,'0001_01_01_000000_create_users_table',1),(17,'0001_01_01_000001_create_cache_table',1),(18,'0001_01_01_000002_create_jobs_table',1),(19,'2026_07_27_194411_create_personal_access_tokens_table',1),(20,'2026_07_29_000001_create_marketing_profiles_table',1),(21,'2026_07_29_000002_create_marketing_work_days_table',1),(22,'2026_07_29_000003_create_prospects_table',1),(23,'2026_07_29_000004_create_members_table',1),(24,'2026_07_29_000005_create_marketing_schedules_table',1),(25,'2026_07_29_000006_create_daily_operational_reports_table',1),(26,'2026_07_29_000007_create_visit_reports_table',1),(27,'2026_07_29_000008_create_tracking_sessions_table',1),(28,'2026_07_29_000009_create_tracking_points_table',1),(29,'2026_07_29_000010_create_operational_recaps_table',1),(30,'2026_07_29_000011_create_operational_recap_rows_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `operational_recap_rows` WRITE;
/*!40000 ALTER TABLE `operational_recap_rows` DISABLE KEYS */;
INSERT INTO `operational_recap_rows` VALUES (1,1,1,'M01',1,1,1,0,8000000,0,0,8000000,0,5500000,5500000,0,2300000,2300000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(2,1,2,'M02',1,1,1,0,8500000,0,0,8500000,0,6000000,6000000,0,2500000,2500000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(3,1,3,'M03',1,1,1,0,9000000,0,0,9000000,0,6500000,6500000,0,2700000,2700000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(4,1,4,'M04',1,1,1,0,9000000,0,0,9000000,0,6500000,6500000,0,2800000,2800000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(5,1,5,'M05',1,1,1,0,9500000,0,0,9500000,0,7000000,7000000,0,2900000,2900000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(6,1,6,'M06',1,1,1,0,9500000,0,0,9500000,0,7000000,7000000,0,3000000,3000000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(7,1,7,'M07',1,1,1,0,10000000,0,0,10000000,0,7500000,7500000,0,3100000,3100000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(8,1,8,'M08',1,1,1,0,10000000,0,0,10000000,0,7500000,7500000,0,3200000,3200000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(9,1,9,'M09',1,1,1,0,10500000,0,0,10500000,0,7500000,7500000,0,3300000,3300000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(10,1,10,'M10',1,1,1,0,10500000,0,0,10500000,0,8000000,8000000,0,3400000,3400000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(11,1,11,'M11',1,1,1,0,11000000,0,0,11000000,0,8000000,8000000,0,3500000,3500000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(12,1,12,'M12',1,1,1,0,11000000,0,0,11000000,0,7500000,7500000,0,2500000,2500000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(13,1,13,'M13',1,1,1,0,7000000,0,0,7000000,0,6500000,6500000,0,2500000,2500000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(14,2,1,'M01',1,1,1,0,8100000,0,0,8100000,0,5600000,5600000,0,2310000,2310000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(15,2,2,'M02',1,1,1,0,8600000,0,0,8600000,0,6100000,6100000,0,2510000,2510000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(16,2,3,'M03',1,1,1,0,9100000,0,0,9100000,0,6600000,6600000,0,2710000,2710000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(17,2,4,'M04',1,1,1,0,9200000,0,0,9200000,0,6700000,6700000,0,2810000,2810000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(18,2,5,'M05',1,1,1,0,9600000,0,0,9600000,0,7100000,7100000,0,2910000,2910000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(19,2,6,'M06',1,1,1,0,9700000,0,0,9700000,0,7200000,7200000,0,3010000,3010000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(20,2,7,'M07',1,1,1,0,10100000,0,0,10100000,0,7600000,7600000,0,3110000,3110000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(21,2,8,'M08',1,1,1,0,10200000,0,0,10200000,0,7700000,7700000,0,3210000,3210000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(22,2,9,'M09',1,1,1,0,10600000,0,0,10600000,0,7600000,7600000,0,3310000,3310000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(23,2,10,'M10',1,1,1,0,10700000,0,0,10700000,0,8100000,8100000,0,3410000,3410000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(24,2,11,'M11',1,1,1,0,11100000,0,0,11100000,0,8100000,8100000,0,3510000,3510000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(25,2,12,'M12',1,1,1,0,11200000,0,0,11200000,0,7600000,7600000,0,2510000,2510000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(26,2,13,'M13',1,1,1,0,7100000,0,0,7100000,0,6600000,6600000,0,2510000,2510000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(27,3,1,'M01',1,1,1,0,8200000,0,0,8200000,0,5700000,5700000,0,2320000,2320000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(28,3,2,'M02',1,1,1,0,8700000,0,0,8700000,0,6200000,6200000,0,2520000,2520000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(29,3,3,'M03',1,1,1,0,9200000,0,0,9200000,0,6700000,6700000,0,2720000,2720000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(30,3,4,'M04',1,1,1,0,9300000,0,0,9300000,0,6800000,6800000,0,2820000,2820000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(31,3,5,'M05',1,1,1,0,9700000,0,0,9700000,0,7200000,7200000,0,2920000,2920000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(32,3,6,'M06',1,1,1,0,9800000,0,0,9800000,0,7300000,7300000,0,3020000,3020000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(33,3,7,'M07',1,1,1,0,10200000,0,0,10200000,0,7700000,7700000,0,3120000,3120000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(34,3,8,'M08',1,1,1,0,10300000,0,0,10300000,0,7800000,7800000,0,3220000,3220000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(35,3,9,'M09',1,1,1,0,10700000,0,0,10700000,0,7700000,7700000,0,3320000,3320000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(36,3,10,'M10',1,1,1,0,10800000,0,0,10800000,0,8200000,8200000,0,3420000,3420000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(37,3,11,'M11',1,1,1,0,11200000,0,0,11200000,0,8200000,8200000,0,3520000,3520000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(38,3,12,'M12',1,1,1,0,11300000,0,0,11300000,0,7700000,7700000,0,2520000,2520000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(39,3,13,'M13',1,1,1,0,7200000,0,0,7200000,0,6700000,6700000,0,2520000,2520000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(40,4,1,'M01',1,1,1,0,8300000,0,0,8300000,0,5800000,5800000,0,2330000,2330000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(41,4,2,'M02',1,1,1,0,8800000,0,0,8800000,0,6300000,6300000,0,2530000,2530000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(42,4,3,'M03',1,1,1,0,9300000,0,0,9300000,0,6800000,6800000,0,2730000,2730000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(43,4,4,'M04',1,1,1,0,9400000,0,0,9400000,0,6900000,6900000,0,2830000,2830000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(44,4,5,'M05',1,1,1,0,9800000,0,0,9800000,0,7300000,7300000,0,2930000,2930000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(45,4,6,'M06',1,1,1,0,9900000,0,0,9900000,0,7400000,7400000,0,3030000,3030000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(46,4,7,'M07',1,1,1,0,10300000,0,0,10300000,0,7800000,7800000,0,3130000,3130000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(47,4,8,'M08',1,1,1,0,10400000,0,0,10400000,0,7900000,7900000,0,3230000,3230000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(48,4,9,'M09',1,1,1,0,10800000,0,0,10800000,0,7800000,7800000,0,3330000,3330000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(49,4,10,'M10',1,1,1,0,10900000,0,0,10900000,0,8300000,8300000,0,3430000,3430000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(50,4,11,'M11',1,1,1,0,11300000,0,0,11300000,0,8300000,8300000,0,3530000,3530000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(51,4,12,'M12',1,1,1,0,11400000,0,0,11400000,0,7800000,7800000,0,2530000,2530000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(52,4,13,'M13',1,1,1,0,7300000,0,0,7300000,0,6800000,6800000,0,2530000,2530000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(53,5,1,'M01',1,1,1,0,8400000,0,0,8400000,0,5900000,5900000,0,2340000,2340000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(54,5,2,'M02',1,1,1,0,8900000,0,0,8900000,0,6400000,6400000,0,2540000,2540000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(55,5,3,'M03',1,1,1,0,9400000,0,0,9400000,0,6900000,6900000,0,2740000,2740000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(56,5,4,'M04',1,1,1,0,9500000,0,0,9500000,0,7000000,7000000,0,2840000,2840000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(57,5,5,'M05',1,1,1,0,9900000,0,0,9900000,0,7400000,7400000,0,2940000,2940000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(58,5,6,'M06',1,1,1,0,10000000,0,0,10000000,0,7500000,7500000,0,3040000,3040000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(59,5,7,'M07',1,1,1,0,10400000,0,0,10400000,0,7900000,7900000,0,3140000,3140000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(60,5,8,'M08',1,1,1,0,10500000,0,0,10500000,0,8000000,8000000,0,3240000,3240000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(61,5,9,'M09',1,1,1,0,10900000,0,0,10900000,0,7900000,7900000,0,3340000,3340000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(62,5,10,'M10',1,1,1,0,11000000,0,0,11000000,0,8400000,8400000,0,3440000,3440000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(63,5,11,'M11',1,1,1,0,11400000,0,0,11400000,0,8400000,8400000,0,3540000,3540000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(64,5,12,'M12',1,1,1,0,11500000,0,0,11500000,0,7900000,7900000,0,2540000,2540000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(65,5,13,'M13',1,1,1,0,7400000,0,0,7400000,0,6900000,6900000,0,2540000,2540000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(66,6,1,'M01',1,1,1,0,8500000,0,0,8500000,0,6000000,6000000,0,2350000,2350000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(67,6,2,'M02',1,1,1,0,9000000,0,0,9000000,0,6500000,6500000,0,2550000,2550000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(68,6,3,'M03',1,1,1,0,9500000,0,0,9500000,0,7000000,7000000,0,2750000,2750000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(69,6,4,'M04',1,1,1,0,9600000,0,0,9600000,0,7100000,7100000,0,2850000,2850000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(70,6,5,'M05',1,1,1,0,10000000,0,0,10000000,0,7500000,7500000,0,2950000,2950000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(71,6,6,'M06',1,1,1,0,10100000,0,0,10100000,0,7600000,7600000,0,3050000,3050000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(72,6,7,'M07',1,1,1,0,10500000,0,0,10500000,0,8000000,8000000,0,3150000,3150000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(73,6,8,'M08',1,1,1,0,10600000,0,0,10600000,0,8100000,8100000,0,3250000,3250000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(74,6,9,'M09',1,1,1,0,11000000,0,0,11000000,0,8000000,8000000,0,3350000,3350000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(75,6,10,'M10',1,1,1,0,11100000,0,0,11100000,0,8500000,8500000,0,3450000,3450000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(76,6,11,'M11',1,1,1,0,11500000,0,0,11500000,0,8500000,8500000,0,3550000,3550000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(77,6,12,'M12',1,1,1,0,11600000,0,0,11600000,0,8000000,8000000,0,2550000,2550000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10'),(78,6,13,'M13',1,1,1,0,7500000,0,0,7500000,0,7000000,7000000,0,2550000,2550000,NULL,0,0,'Admin KSP MMS',2500000,'2026-07-29 04:56:10','2026-07-29 04:56:10');
/*!40000 ALTER TABLE `operational_recap_rows` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `operational_recaps` WRITE;
/*!40000 ALTER TABLE `operational_recaps` DISABLE KEYS */;
INSERT INTO `operational_recaps` VALUES (1,'RKP-20260720','2026-07-20','Senin','Draft',1,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(2,'RKP-20260721','2026-07-21','Selasa','Draft',1,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(3,'RKP-20260722','2026-07-22','Rabu','Draft',1,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(4,'RKP-20260723','2026-07-23','Kamis','Draft',1,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(5,'RKP-20260724','2026-07-24','Jumat','Draft',1,'2026-07-29 04:56:09','2026-07-29 04:56:09'),(6,'RKP-20260725','2026-07-25','Sabtu','Draft',1,'2026-07-29 04:56:10','2026-07-29 04:56:10');
/*!40000 ALTER TABLE `operational_recaps` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `prospects` WRITE;
/*!40000 ALTER TABLE `prospects` DISABLE KEYS */;
INSERT INTO `prospects` VALUES (1,'00000000-0000-4000-8000-000000000101',1,'Ahmad Hidayat','081211112201','Jl. Gedebage Selatan No. 21','Toko Kelontong','Tertarik','Bersedia menerima presentasi produk','Tertarik produk tabungan usaha.','Gedebage','2026-07-20','08:30:00',-6.9388000,107.7079000,'Gedebage, Kota Bandung','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(2,'00000000-0000-4000-8000-000000000102',1,'Siti Nurjanah','081211112202','Jl. Rancasari No. 8','Warung Sembako','Perlu Follow Up','Meminta follow up produk simpanan','Perlu dikunjungi ulang.','Rancasari','2026-07-20','10:15:00',-6.9541000,107.6817000,'Rancasari, Kota Bandung','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL),(3,'00000000-0000-4000-8000-000000000103',1,'Toko Berkah Jaya','081211112203','Jl. Buahbatu No. 14','Grosir','Baru','Baru dicatat untuk kunjungan awal',NULL,'Buahbatu','2026-07-20','13:00:00',-6.9469000,107.6388000,'Buahbatu, Kota Bandung','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09',NULL);
/*!40000 ALTER TABLE `prospects` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `tracking_points` WRITE;
/*!40000 ALTER TABLE `tracking_points` DISABLE KEYS */;
INSERT INTO `tracking_points` VALUES (1,'00000000-0000-4000-8000-000000000511',1,-6.9401000,107.7080000,8.50,0.00,0.00,710.00,'Kantor KSP MMS, Bandung','Mulai','2026-07-20 08:05:00','2026-07-20 08:05:05','2026-07-20 01:05:05'),(2,'00000000-0000-4000-8000-000000000512',1,-6.9388000,107.7079000,9.00,1.20,120.00,711.00,'Gedebage, Kota Bandung','Perjalanan','2026-07-20 08:30:00','2026-07-20 08:30:05','2026-07-20 01:30:05'),(3,'00000000-0000-4000-8000-000000000513',1,-6.9388000,107.7079000,7.00,0.00,0.00,711.00,'Ahmad Hidayat, Gedebage','Kunjungan','2026-07-20 09:30:00','2026-07-20 09:30:05','2026-07-20 02:30:05'),(4,'00000000-0000-4000-8000-000000000514',1,-6.9378000,107.7090000,8.00,1.50,80.00,709.00,'Gedebage Timur, Kota Bandung','Perjalanan','2026-07-20 09:50:00','2026-07-20 09:50:05','2026-07-20 02:50:05'),(5,'00000000-0000-4000-8000-000000000515',1,-6.9378000,107.7090000,7.00,0.00,0.00,709.00,'Herman Malik, Gedebage Timur','Kunjungan','2026-07-20 10:00:00','2026-07-20 10:00:05','2026-07-20 03:00:05'),(6,'00000000-0000-4000-8000-000000000516',1,-6.9541000,107.6817000,10.00,1.50,210.00,705.00,'Rancasari, Kota Bandung','Perjalanan','2026-07-20 13:20:00','2026-07-20 13:20:05','2026-07-20 06:20:05'),(7,'00000000-0000-4000-8000-000000000517',1,-6.9541000,107.6817000,8.00,0.00,0.00,705.00,'Siti Nurjanah, Rancasari','Kunjungan','2026-07-20 13:30:00','2026-07-20 13:30:05','2026-07-20 06:30:05'),(8,'00000000-0000-4000-8000-000000000518',1,-6.9389000,107.7069000,9.00,1.40,20.00,710.00,'Perjalanan kembali ke Gedebage','Perjalanan','2026-07-20 15:00:00','2026-07-20 15:00:05','2026-07-20 08:00:05'),(9,'00000000-0000-4000-8000-000000000519',1,-6.9389000,107.7069000,8.00,0.00,0.00,710.00,'Gedebage, Kota Bandung','Selesai','2026-07-20 15:30:00','2026-07-20 15:30:05','2026-07-20 08:30:05');
/*!40000 ALTER TABLE `tracking_points` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `tracking_sessions` WRITE;
/*!40000 ALTER TABLE `tracking_sessions` DISABLE KEYS */;
INSERT INTO `tracking_sessions` VALUES (1,'00000000-0000-4000-8000-000000000501',1,1,'2026-07-20','Senin','2026-07-20 08:05:00','2026-07-20 15:30:00','Offline',8400,3,'2026-07-29 04:56:09','2026-07-29 04:56:09');
/*!40000 ALTER TABLE `tracking_sessions` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator MMS','admin','admin@mms.local',NULL,'$2y$12$X/l86Nkzbo8ghdOXzia4Re6SusDDhiewENG4uJO4BN/iSWq8bdLuq','admin',1,NULL,NULL,'2026-07-29 04:56:06','2026-07-29 04:56:06',NULL),(2,'Deden','m01.deden','m01@mms.local',NULL,'$2y$12$fkruLMrjAtHrcygqWDl2xuxCjl5gqtfXtzoaDv5LC1pgUt3/MYTjC','marketing',1,NULL,NULL,'2026-07-29 04:56:06','2026-07-29 04:56:06',NULL),(3,'Angil','m02.angil','m02@mms.local',NULL,'$2y$12$l5DdQ.tOO7lhmAn4CGF8h.WjBe4HaBv4JH18gKj2v8N6V2rIVsV3G','marketing',1,NULL,NULL,'2026-07-29 04:56:06','2026-07-29 04:56:06',NULL),(4,'Ari','m03.ari','m03@mms.local',NULL,'$2y$12$kYxkYoh/JiP5sawAv8K/Tefzgy0Tzes1N.zPVUa3qV2/B.iK.MkMm','marketing',1,NULL,NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07',NULL),(5,'Feri','m04.feri','m04@mms.local',NULL,'$2y$12$dGhA/2o91uzCI5.GG9MUQOhYhER1VbI38gOTwXHXu3LCCwe1SAJHG','marketing',1,NULL,NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07',NULL),(6,'Sukma','m05.sukma','m05@mms.local',NULL,'$2y$12$Gklzcl/CZtU3zaRzShEJ5.WlwYHvwrF4JlfFPR2yskrslF.ZgFfbi','marketing',1,NULL,NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07',NULL),(7,'Sandi','m06.sandi','m06@mms.local',NULL,'$2y$12$HxuhbArM0zu5ITURxEndpeGSiUdHKABq/z/C3UG5UZSsbJrAxSGZq','marketing',1,NULL,NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07',NULL),(8,'Vikri','m07.vikri','m07@mms.local',NULL,'$2y$12$kJwyvhyr1RjMdREqOP8R5uAOMAE0RUzuAKD2vAGc8fi.d3W3GasaK','marketing',1,NULL,NULL,'2026-07-29 04:56:07','2026-07-29 04:56:07',NULL),(9,'Farhad','m08.farhad','m08@mms.local',NULL,'$2y$12$UUeBJMvoxQJS77JFb5jhJexllg1XAih/2BCNgbvkwNt6dgyqOW5Ia','marketing',1,NULL,NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08',NULL),(10,'Doni','m09.doni','m09@mms.local',NULL,'$2y$12$sEIxzO6.8FNoUr.ZTXFaVex7Pe4jHXWZhvC34EVPJIpi8QsQb8pEG','marketing',1,NULL,NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08',NULL),(11,'Faiz','m10.faiz','m10@mms.local',NULL,'$2y$12$0H/yvmkrglSsOOn0m.cOj.R/xyxeKtn38MtEOa/acHOZSkg.NjEqq','marketing',1,NULL,NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08',NULL),(12,'Agung','m11.agung','m11@mms.local',NULL,'$2y$12$Fcvrdc2Yb39WmRnsZWTE5uBRw.wwrYGHJVqsnVU9Ahl1k9uEidVmy','marketing',1,NULL,NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08',NULL),(13,'Faisal','m12.faisal','m12@mms.local',NULL,'$2y$12$7v/Qn3FZig/3ptXftcbaFe4RN49QgLdQzgHiJkpU58Nrmsytw6rbS','marketing',1,NULL,NULL,'2026-07-29 04:56:08','2026-07-29 04:56:08',NULL),(14,'Agnes','m13.agnes','m13@mms.local',NULL,'$2y$12$uXr.qMEEfEx2WHLx1NofzecANc4zVfNhiQ8ul5nQMP4cmpNIWoU5K','marketing',1,NULL,NULL,'2026-07-29 04:56:09','2026-07-29 04:56:09',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

LOCK TABLES `visit_reports` WRITE;
/*!40000 ALTER TABLE `visit_reports` DISABLE KEYS */;
INSERT INTO `visit_reports` VALUES (1,'00000000-0000-4000-8000-000000000401',1,1,'2026-07-20','09:30:00','Senin','Presentasi produk tabungan','Berhasil Bertemu','Tertarik','Prospek bersedia menerima follow up.','2026-07-22','visit-reports/development/visit-reference.webp','Foto kunjungan development','Gedebage',-6.9388000,107.7079000,'Gedebage, Kota Bandung','Tersinkronisasi','2026-07-29 04:56:09','2026-07-29 04:56:09');
/*!40000 ALTER TABLE `visit_reports` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



