-- =============================================================================
-- Dérailleur — Strava Integration
-- Date: 23.03.2026
-- Description: Add tables and columns for Strava API integration
--              (events sync, member sync, routes)
-- Compatible: MariaDB 10.11+
-- Based on: agiletra_ffgva.sql export 23.03.2026 10:31
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. New table: member_strava (OAuth tokens per member)
-- -----------------------------------------------------------------------------

CREATE TABLE `member_strava` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `strava_athlete_id` bigint(20) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) NOT NULL,
  `token_expires_at` datetime NOT NULL,
  `scopes` varchar(255) NOT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_member_strava_member` (`member_id`),
  UNIQUE KEY `uq_member_strava_athlete` (`strava_athlete_id`),
  KEY `member_strava_modified_by_id_foreign` (`modified_by_id`),
  CONSTRAINT `member_strava_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  CONSTRAINT `member_strava_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. Audit table: member_strava_audit
-- -----------------------------------------------------------------------------

CREATE TABLE `member_strava_audit` (
  `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `audit_action` char(1) NOT NULL COMMENT 'U=update, D=delete',
  `audit_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `audit_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `strava_athlete_id` bigint(20) NOT NULL,
  `access_token` varchar(255) NOT NULL,
  `refresh_token` varchar(255) NOT NULL,
  `token_expires_at` datetime NOT NULL,
  `scopes` varchar(255) NOT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`audit_id`),
  KEY `member_strava_audit_user_id_foreign` (`audit_user_id`),
  CONSTRAINT `member_strava_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Audit triggers for member_strava
-- -----------------------------------------------------------------------------

DELIMITER $$

CREATE TRIGGER `member_strava_before_update` BEFORE UPDATE ON `member_strava` FOR EACH ROW
BEGIN
    INSERT INTO `member_strava_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `strava_athlete_id`, `access_token`, `refresh_token`, `token_expires_at`, `scopes`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`strava_athlete_id`, OLD.`access_token`, OLD.`refresh_token`, OLD.`token_expires_at`, OLD.`scopes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `member_strava_before_delete` BEFORE DELETE ON `member_strava` FOR EACH ROW
BEGIN
    INSERT INTO `member_strava_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `strava_athlete_id`, `access_token`, `refresh_token`, `token_expires_at`, `scopes`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`strava_athlete_id`, OLD.`access_token`, OLD.`refresh_token`, OLD.`token_expires_at`, OLD.`scopes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

DELIMITER ;

-- -----------------------------------------------------------------------------
-- 4. ALTER events: add Strava event ID and route ID
-- -----------------------------------------------------------------------------

ALTER TABLE `events`
  ADD COLUMN `strava_event_id` bigint(20) DEFAULT NULL AFTER `statuscode`,
  ADD COLUMN `strava_route_id` bigint(20) DEFAULT NULL AFTER `strava_event_id`;

-- -----------------------------------------------------------------------------
-- 5. ALTER events_audit: add matching columns
-- -----------------------------------------------------------------------------

ALTER TABLE `events_audit`
  ADD COLUMN `strava_event_id` bigint(20) DEFAULT NULL AFTER `statuscode`,
  ADD COLUMN `strava_route_id` bigint(20) DEFAULT NULL AFTER `strava_event_id`;

-- -----------------------------------------------------------------------------
-- 6. Recreate events triggers to include new columns
--    (based on production triggers from agiletra_ffgva.sql export)
-- -----------------------------------------------------------------------------

DROP TRIGGER IF EXISTS `events_before_delete`;
DROP TRIGGER IF EXISTS `events_before_update`;

DELIMITER $$

CREATE TRIGGER `events_before_delete` BEFORE DELETE ON `events` FOR EACH ROW
BEGIN
    INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `event_type`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `price_non_member`, `statuscode`, `strava_event_id`, `strava_route_id`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`event_type`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`, OLD.`price_non_member`, OLD.`statuscode`, OLD.`strava_event_id`, OLD.`strava_route_id`, OLD.`gpx_file`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `events_before_update` BEFORE UPDATE ON `events` FOR EACH ROW
BEGIN
    INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `event_type`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `price_non_member`, `statuscode`, `strava_event_id`, `strava_route_id`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`event_type`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`, OLD.`price_non_member`, OLD.`statuscode`, OLD.`strava_event_id`, OLD.`strava_route_id`, OLD.`gpx_file`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

DELIMITER ;

-- =============================================================================
-- Verification queries (all counts should be >= 1)
-- =============================================================================

SELECT 'member_strava' AS `check`, COUNT(*) AS `ok`
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'member_strava'
UNION ALL
SELECT 'member_strava_audit', COUNT(*)
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'member_strava_audit'
UNION ALL
SELECT 'events.strava_event_id', COUNT(*)
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'events' AND column_name = 'strava_event_id'
UNION ALL
SELECT 'events.strava_route_id', COUNT(*)
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'events' AND column_name = 'strava_route_id'
UNION ALL
SELECT 'events_audit.strava_event_id', COUNT(*)
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'events_audit' AND column_name = 'strava_event_id'
UNION ALL
SELECT 'events_audit.strava_route_id', COUNT(*)
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'events_audit' AND column_name = 'strava_route_id';
