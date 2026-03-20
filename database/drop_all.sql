-- =============================================
-- Drop everything in agiletra_ffgva
-- Run BEFORE create_database.sql
-- =============================================

USE `agiletra_ffgva`;

SET FOREIGN_KEY_CHECKS = 0;

-- Triggers
DROP TRIGGER IF EXISTS `members_before_update`;
DROP TRIGGER IF EXISTS `members_before_delete`;
DROP TRIGGER IF EXISTS `events_before_update`;
DROP TRIGGER IF EXISTS `events_before_delete`;
DROP TRIGGER IF EXISTS `event_member_before_update`;
DROP TRIGGER IF EXISTS `event_member_before_delete`;
DROP TRIGGER IF EXISTS `member_phones_before_update`;
DROP TRIGGER IF EXISTS `member_phones_before_delete`;
DROP TRIGGER IF EXISTS `invoices_before_update`;
DROP TRIGGER IF EXISTS `invoices_before_delete`;

-- Audit tables
DROP TABLE IF EXISTS `members_audit`;
DROP TABLE IF EXISTS `events_audit`;
DROP TABLE IF EXISTS `event_member_audit`;
DROP TABLE IF EXISTS `member_phones_audit`;
DROP TABLE IF EXISTS `invoices_audit`;

-- Drop FK on users first (blocks members drop)
ALTER TABLE `users` DROP FOREIGN KEY `users_member_id_foreign`;

-- Domain tables
DROP TABLE IF EXISTS `invoices`;
DROP TABLE IF EXISTS `member_phones`;
DROP TABLE IF EXISTS `event_member`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `members`;

-- Laravel system tables
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;
