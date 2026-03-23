-- =============================================================================
-- Dérailleur — Multiple cheffes de peloton per event
-- Date: 23.03.2026
-- Description: New pivot table event_chef for many-to-many relationship
--              Migrates existing chef_peloton_id data to the new table
--              Old column kept temporarily for backward compatibility
-- Compatible: MariaDB 10.11+
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 1. Create pivot table: event_chef
-- -----------------------------------------------------------------------------

CREATE TABLE `event_chef` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_event_chef` (`event_id`, `member_id`),
  KEY `event_chef_member_id_foreign` (`member_id`),
  KEY `event_chef_modified_by_id_foreign` (`modified_by_id`),
  CONSTRAINT `event_chef_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  CONSTRAINT `event_chef_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  CONSTRAINT `event_chef_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. Create audit table: event_chef_audit
-- -----------------------------------------------------------------------------

CREATE TABLE `event_chef_audit` (
  `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `audit_action` char(1) NOT NULL COMMENT 'U=update, D=delete',
  `audit_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `audit_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`audit_id`),
  KEY `event_chef_audit_user_id_foreign` (`audit_user_id`),
  CONSTRAINT `event_chef_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. Create triggers
-- -----------------------------------------------------------------------------

DELIMITER $$

CREATE TRIGGER `event_chef_before_update` BEFORE UPDATE ON `event_chef` FOR EACH ROW
BEGIN
    INSERT INTO `event_chef_audit` (`audit_action`, `audit_user_id`, `id`, `event_id`, `member_id`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`event_id`, OLD.`member_id`, OLD.`sort_order`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `event_chef_before_delete` BEFORE DELETE ON `event_chef` FOR EACH ROW
BEGIN
    INSERT INTO `event_chef_audit` (`audit_action`, `audit_user_id`, `id`, `event_id`, `member_id`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`event_id`, OLD.`member_id`, OLD.`sort_order`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

DELIMITER ;

-- -----------------------------------------------------------------------------
-- 4. Migrate existing chef_peloton_id data to event_chef
-- -----------------------------------------------------------------------------

INSERT INTO `event_chef` (`event_id`, `member_id`, `sort_order`, `updated_at`)
SELECT `id`, `chef_peloton_id`, 0, `updated_at`
FROM `events`
WHERE `chef_peloton_id` IS NOT NULL
  AND `deleted_at` IS NULL;

-- -----------------------------------------------------------------------------
-- 5. Verify
-- -----------------------------------------------------------------------------

DESCRIBE `event_chef`;
SELECT COUNT(*) AS `migrated_rows` FROM `event_chef`;
