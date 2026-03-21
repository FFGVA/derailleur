-- =============================================
-- Dérailleur — Fast and Female Geneva
-- Production database script for MariaDB 10.11+
-- Run drop_all.sql FIRST, then this script.
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

USE `agiletra_ffgva`;

-- =============================================
-- SECTION 1: Laravel system tables
-- =============================================

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP NULL,
    `password` VARCHAR(255) NOT NULL,
    `remember_token` VARCHAR(100) NULL,
    `role` CHAR(1) NOT NULL DEFAULT 'C',
    `member_id` BIGINT UNSIGNED NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
    `email` VARCHAR(255) NOT NULL PRIMARY KEY,
    `token` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
    `id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `payload` LONGTEXT NOT NULL,
    `last_activity` INT NOT NULL,
    INDEX `sessions_user_id_index` (`user_id`),
    INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `queue` VARCHAR(255) NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `attempts` TINYINT UNSIGNED NOT NULL,
    `reserved_at` INT UNSIGNED NULL,
    `available_at` INT UNSIGNED NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
    `id` VARCHAR(255) NOT NULL PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `total_jobs` INT NOT NULL,
    `pending_jobs` INT NOT NULL,
    `failed_jobs` INT NOT NULL,
    `failed_job_ids` LONGTEXT NOT NULL,
    `options` MEDIUMTEXT NULL,
    `cancelled_at` INT NULL,
    `created_at` INT NOT NULL,
    `finished_at` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `uuid` VARCHAR(255) NOT NULL UNIQUE,
    `connection` TEXT NOT NULL,
    `queue` TEXT NOT NULL,
    `payload` LONGTEXT NOT NULL,
    `exception` LONGTEXT NOT NULL,
    `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `migration` VARCHAR(255) NOT NULL,
    `batch` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SECTION 2: Domain tables
-- =============================================

CREATE TABLE `members` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `member_number` VARCHAR(4) NULL UNIQUE,
    `first_name` VARCHAR(40) NOT NULL,
    `last_name` VARCHAR(60) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `date_of_birth` DATE NULL,
    `address` TEXT NULL,
    `postal_code` VARCHAR(10) NULL,
    `city` VARCHAR(255) NULL,
    `country` VARCHAR(2) NOT NULL DEFAULT 'CH',
    `statuscode` CHAR(1) NOT NULL DEFAULT 'D',
    `membership_start` DATE NULL,
    `membership_end` DATE NULL,
    `notes` TEXT NULL,
    `is_invitee` TINYINT(1) NOT NULL DEFAULT 0,
    `photo_ok` TINYINT(1) NOT NULL DEFAULT 1,
    `metadata` JSON NULL,
    `activation_token` VARCHAR(64) NULL,
    `activation_sent_at` TIMESTAMP NULL,
    `email_verified_at` TIMESTAMP NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `members_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `events` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `location` VARCHAR(255) NULL,
    `starts_at` DATETIME NOT NULL,
    `ends_at` DATETIME NULL,
    `max_participants` INT UNSIGNED NULL,
    `price` DECIMAL(8,2) NOT NULL DEFAULT 0,
    `statuscode` CHAR(1) NOT NULL DEFAULT 'N',
    `chef_peloton_id` BIGINT UNSIGNED NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `events_chef_peloton_id_foreign` FOREIGN KEY (`chef_peloton_id`) REFERENCES `members` (`id`),
    CONSTRAINT `events_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `event_member` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `event_id` BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `status` CHAR(1) NOT NULL DEFAULT 'N',
    `present` TINYINT(1) NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    UNIQUE KEY `event_member_unique` (`event_id`, `member_id`),
    CONSTRAINT `event_member_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
    CONSTRAINT `event_member_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
    CONSTRAINT `event_member_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `member_phones` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `phone_number` VARCHAR(20) NOT NULL,
    `label` VARCHAR(40) NULL,
    `is_whatsapp` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `member_phones_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
    CONSTRAINT `member_phones_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoices` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `type` CHAR(1) NOT NULL DEFAULT 'C',
    `cotisation_year` SMALLINT UNSIGNED NULL,
    `invoice_number` VARCHAR(20) NOT NULL UNIQUE,
    `amount` DECIMAL(8,2) NOT NULL,
    `statuscode` CHAR(1) NOT NULL DEFAULT 'N',
    `payment_date` DATE NULL,
    `pdf_filename` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `invoices_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
    CONSTRAINT `invoices_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoice_lines` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` BIGINT UNSIGNED NOT NULL,
    `description` TEXT NOT NULL,
    `amount` DECIMAL(8,2) NOT NULL,
    `sort_order` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `invoice_lines_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoice_event` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` BIGINT UNSIGNED NOT NULL,
    `event_id` BIGINT UNSIGNED NOT NULL,
    UNIQUE KEY `invoice_event_unique` (`invoice_id`, `event_id`),
    CONSTRAINT `invoice_event_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
    CONSTRAINT `invoice_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SECTION 2b: Utility tables
-- =============================================

CREATE TABLE `portal_audit_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `member_number` VARCHAR(4) NULL,
    `action` VARCHAR(50) NOT NULL,
    `detail` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_portal_audit_member` (`member_id`),
    KEY `idx_portal_audit_created` (`created_at`),
    CONSTRAINT `fk_portal_audit_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `member_magic_tokens` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `token_hash` BINARY(32) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `used_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_member_magic_token_hash` (`token_hash`),
    KEY `idx_member_magic_token_member` (`member_id`),
    KEY `idx_member_magic_token_expires` (`expires_at`),
    CONSTRAINT `fk_member_magic_token_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SECTION 3: Cross-table foreign keys
-- =============================================

ALTER TABLE `users`
    ADD CONSTRAINT `users_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

-- =============================================
-- SECTION 4: Audit tables
-- =============================================

CREATE TABLE `members_audit` (
    `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `audit_action` CHAR(1) NOT NULL COMMENT 'U=update, D=delete',
    `audit_user_id` BIGINT UNSIGNED NULL,
    `audit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id` BIGINT UNSIGNED NOT NULL,
    `member_number` VARCHAR(4) NULL,
    `first_name` VARCHAR(40) NOT NULL,
    `last_name` VARCHAR(60) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `date_of_birth` DATE NULL,
    `address` TEXT NULL,
    `postal_code` VARCHAR(10) NULL,
    `city` VARCHAR(255) NULL,
    `country` VARCHAR(2) DEFAULT 'CH',
    `statuscode` CHAR(1) DEFAULT 'D',
    `membership_start` DATE NULL,
    `membership_end` DATE NULL,
    `notes` TEXT NULL,
    `is_invitee` TINYINT(1) DEFAULT 0,
    `photo_ok` TINYINT(1) DEFAULT 1,
    `metadata` JSON NULL,
    `activation_token` VARCHAR(64) NULL,
    `activation_sent_at` TIMESTAMP NULL,
    `email_verified_at` TIMESTAMP NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `members_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `events_audit` (
    `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `audit_action` CHAR(1) NOT NULL COMMENT 'U=update, D=delete',
    `audit_user_id` BIGINT UNSIGNED NULL,
    `audit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `location` VARCHAR(255) NULL,
    `starts_at` DATETIME NOT NULL,
    `ends_at` DATETIME NULL,
    `max_participants` INT UNSIGNED NULL,
    `price` DECIMAL(8,2) DEFAULT 0,
    `statuscode` CHAR(1) DEFAULT 'N',
    `chef_peloton_id` BIGINT UNSIGNED NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `events_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `event_member_audit` (
    `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `audit_action` CHAR(1) NOT NULL COMMENT 'U=update, D=delete',
    `audit_user_id` BIGINT UNSIGNED NULL,
    `audit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id` BIGINT UNSIGNED NOT NULL,
    `event_id` BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `status` CHAR(1) DEFAULT 'N',
    `present` TINYINT(1) NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `event_member_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `member_phones_audit` (
    `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `audit_action` CHAR(1) NOT NULL COMMENT 'U=update, D=delete',
    `audit_user_id` BIGINT UNSIGNED NULL,
    `audit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id` BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `phone_number` VARCHAR(20) NOT NULL,
    `label` VARCHAR(40) NULL,
    `is_whatsapp` TINYINT(1) DEFAULT 0,
    `sort_order` TINYINT UNSIGNED DEFAULT 0,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `member_phones_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `invoices_audit` (
    `audit_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `audit_action` CHAR(1) NOT NULL COMMENT 'U=update, D=delete',
    `audit_user_id` BIGINT UNSIGNED NULL,
    `audit_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id` BIGINT UNSIGNED NOT NULL,
    `member_id` BIGINT UNSIGNED NOT NULL,
    `type` CHAR(1) DEFAULT 'C',
    `cotisation_year` SMALLINT UNSIGNED NULL,
    `invoice_number` VARCHAR(20) NOT NULL,
    `amount` DECIMAL(8,2) NOT NULL,
    `statuscode` CHAR(1) DEFAULT 'N',
    `payment_date` DATE NULL,
    `pdf_filename` VARCHAR(255) NULL,
    `notes` TEXT NULL,
    `modified_by_id` BIGINT UNSIGNED NULL,
    `updated_at` TIMESTAMP NULL,
    `deleted_at` TIMESTAMP NULL,
    CONSTRAINT `invoices_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- SECTION 5: Triggers
-- =============================================

DELIMITER $$

-- ── Members ──

CREATE TRIGGER `members_before_update`
BEFORE UPDATE ON `members`
FOR EACH ROW
BEGIN
    INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `members_before_delete`
BEFORE DELETE ON `members`
FOR EACH ROW
BEGIN
    INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

-- ── Events ──

CREATE TRIGGER `events_before_update`
BEFORE UPDATE ON `events`
FOR EACH ROW
BEGIN
    INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `statuscode`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`, OLD.`statuscode`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `events_before_delete`
BEFORE DELETE ON `events`
FOR EACH ROW
BEGIN
    INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `statuscode`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`, OLD.`statuscode`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

-- ── Event-Member ──

CREATE TRIGGER `event_member_before_update`
BEFORE UPDATE ON `event_member`
FOR EACH ROW
BEGIN
    INSERT INTO `event_member_audit` (`audit_action`, `audit_user_id`, `id`, `event_id`, `member_id`, `status`, `present`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`event_id`, OLD.`member_id`, OLD.`status`, OLD.`present`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `event_member_before_delete`
BEFORE DELETE ON `event_member`
FOR EACH ROW
BEGIN
    INSERT INTO `event_member_audit` (`audit_action`, `audit_user_id`, `id`, `event_id`, `member_id`, `status`, `present`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`event_id`, OLD.`member_id`, OLD.`status`, OLD.`present`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

-- ── Member phones ──

CREATE TRIGGER `member_phones_before_update`
BEFORE UPDATE ON `member_phones`
FOR EACH ROW
BEGIN
    INSERT INTO `member_phones_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `phone_number`, `label`, `is_whatsapp`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`phone_number`, OLD.`label`, OLD.`is_whatsapp`, OLD.`sort_order`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `member_phones_before_delete`
BEFORE DELETE ON `member_phones`
FOR EACH ROW
BEGIN
    INSERT INTO `member_phones_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `phone_number`, `label`, `is_whatsapp`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`phone_number`, OLD.`label`, OLD.`is_whatsapp`, OLD.`sort_order`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

-- ── Invoices ──

CREATE TRIGGER `invoices_before_update`
BEFORE UPDATE ON `invoices`
FOR EACH ROW
BEGIN
    INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`type`, OLD.`cotisation_year`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`pdf_filename`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

CREATE TRIGGER `invoices_before_delete`
BEFORE DELETE ON `invoices`
FOR EACH ROW
BEGIN
    INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`type`, OLD.`cotisation_year`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`pdf_filename`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END$$

DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;
