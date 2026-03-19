-- =============================================
-- Drop all domain tables, audit tables, and triggers
-- Preserves Laravel system tables (users, sessions, cache, jobs, etc.)
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

-- Audit tables
DROP TABLE IF EXISTS `members_audit`;
DROP TABLE IF EXISTS `events_audit`;
DROP TABLE IF EXISTS `event_member_audit`;
DROP TABLE IF EXISTS `member_phones_audit`;

-- Domain tables (order matters for FK)
DROP TABLE IF EXISTS `member_phones`;
DROP TABLE IF EXISTS `event_member`;
DROP TABLE IF EXISTS `events`;

-- Remove FK from users before dropping members
ALTER TABLE `users` DROP FOREIGN KEY `users_member_id_foreign`;

DROP TABLE IF EXISTS `members`;

SET FOREIGN_KEY_CHECKS = 1;
