
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
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `display_name` varchar(150) DEFAULT NULL,
  `code` varchar(10) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `area` varchar(100) NOT NULL,
  `profile_photo_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `marketing_profiles_code_unique` (`code`),
  UNIQUE KEY `marketing_profiles_user_id_unique` (`user_id`),
  KEY `idx_marketing_profiles_phone` (`phone`),
  KEY `idx_marketing_profiles_area` (`area`),
  CONSTRAINT `marketing_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `operational_report_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `operational_report_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `daily_operational_report_id` bigint(20) unsigned NOT NULL,
  `type` varchar(30) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `op_attachment_report_type_unique` (`daily_operational_report_id`,`type`),
  KEY `idx_operational_attachments_type_uploaded` (`type`,`uploaded_at`),
  CONSTRAINT `fk_op_attachment_report` FOREIGN KEY (`daily_operational_report_id`) REFERENCES `daily_operational_reports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

