-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql14j15.db.hostpoint.internal
-- Erstellungszeit: 22. Mrz 2026 um 13:59
-- Server-Version: 10.11.14-MariaDB-log
-- PHP-Version: 8.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `agiletra_ffgva`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `max_participants` int(10) UNSIGNED DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `statuscode` char(1) NOT NULL DEFAULT 'N',
  `gpx_file` varchar(255) DEFAULT NULL,
  `chef_peloton_id` bigint(20) UNSIGNED DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Trigger `events`
--
DELIMITER $$
CREATE TRIGGER `events_before_delete` BEFORE DELETE ON `events` FOR EACH ROW BEGIN
    INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `statuscode`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`, OLD.`statuscode`, OLD.`gpx_file`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `events_before_update` BEFORE UPDATE ON `events` FOR EACH ROW BEGIN
    INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `statuscode`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`, OLD.`statuscode`, OLD.`gpx_file`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `events_audit`
--

CREATE TABLE `events_audit` (
  `audit_id` bigint(20) UNSIGNED NOT NULL,
  `audit_action` char(1) NOT NULL COMMENT 'U=update, D=delete',
  `audit_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `audit_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `max_participants` int(10) UNSIGNED DEFAULT NULL,
  `price` decimal(8,2) DEFAULT 0.00,
  `statuscode` char(1) DEFAULT 'N',
  `gpx_file` varchar(255) DEFAULT NULL,
  `chef_peloton_id` bigint(20) UNSIGNED DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `event_member`
--

CREATE TABLE `event_member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `status` char(1) NOT NULL DEFAULT 'N',
  `present` tinyint(1) DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Trigger `event_member`
--
DELIMITER $$
CREATE TRIGGER `event_member_before_delete` BEFORE DELETE ON `event_member` FOR EACH ROW BEGIN
    INSERT INTO `event_member_audit` (`audit_action`, `audit_user_id`, `id`, `event_id`, `member_id`, `status`, `present`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`event_id`, OLD.`member_id`, OLD.`status`, OLD.`present`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `event_member_before_update` BEFORE UPDATE ON `event_member` FOR EACH ROW BEGIN
    INSERT INTO `event_member_audit` (`audit_action`, `audit_user_id`, `id`, `event_id`, `member_id`, `status`, `present`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`event_id`, OLD.`member_id`, OLD.`status`, OLD.`present`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `event_member_audit`
--

CREATE TABLE `event_member_audit` (
  `audit_id` bigint(20) UNSIGNED NOT NULL,
  `audit_action` char(1) NOT NULL COMMENT 'U=update, D=delete',
  `audit_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `audit_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `status` char(1) DEFAULT 'N',
  `present` tinyint(1) DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `type` char(1) NOT NULL DEFAULT 'C',
  `cotisation_year` smallint(5) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(20) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `statuscode` char(1) NOT NULL DEFAULT 'N',
  `payment_date` date DEFAULT NULL,
  `pdf_filename` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Trigger `invoices`
--
DELIMITER $$
CREATE TRIGGER `invoices_before_delete` BEFORE DELETE ON `invoices` FOR EACH ROW BEGIN
    INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`type`, OLD.`cotisation_year`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`pdf_filename`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `invoices_before_update` BEFORE UPDATE ON `invoices` FOR EACH ROW BEGIN
    INSERT INTO `invoices_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`type`, OLD.`cotisation_year`, OLD.`invoice_number`, OLD.`amount`, OLD.`statuscode`, OLD.`payment_date`, OLD.`pdf_filename`, OLD.`notes`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoices_audit`
--

CREATE TABLE `invoices_audit` (
  `audit_id` bigint(20) UNSIGNED NOT NULL,
  `audit_action` char(1) NOT NULL COMMENT 'U=update, D=delete',
  `audit_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `audit_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `type` char(1) DEFAULT 'C',
  `cotisation_year` smallint(5) UNSIGNED DEFAULT NULL,
  `invoice_number` varchar(20) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `statuscode` char(1) DEFAULT 'N',
  `payment_date` date DEFAULT NULL,
  `pdf_filename` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoice_event`
--

CREATE TABLE `invoice_event` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `event_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `invoice_lines`
--

CREATE TABLE `invoice_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `invoice_id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_number` varchar(4) DEFAULT NULL,
  `first_name` varchar(40) NOT NULL,
  `last_name` varchar(60) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(2) NOT NULL DEFAULT 'CH',
  `statuscode` char(1) NOT NULL DEFAULT 'D',
  `membership_start` date DEFAULT NULL,
  `membership_end` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_invitee` tinyint(1) NOT NULL DEFAULT 0,
  `photo_ok` tinyint(1) NOT NULL DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `activation_token` varchar(64) DEFAULT NULL,
  `activation_sent_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Trigger `members`
--
DELIMITER $$
CREATE TRIGGER `members_before_delete` BEFORE DELETE ON `members` FOR EACH ROW BEGIN
    INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `members_before_update` BEFORE UPDATE ON `members` FOR EACH ROW BEGIN
    INSERT INTO `members_audit` (`audit_action`, `audit_user_id`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_number`, OLD.`first_name`, OLD.`last_name`, OLD.`email`, OLD.`date_of_birth`, OLD.`address`, OLD.`postal_code`, OLD.`city`, OLD.`country`, OLD.`statuscode`, OLD.`membership_start`, OLD.`membership_end`, OLD.`notes`, OLD.`is_invitee`, OLD.`photo_ok`, OLD.`metadata`, OLD.`activation_token`, OLD.`activation_sent_at`, OLD.`email_verified_at`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `members_audit`
--

CREATE TABLE `members_audit` (
  `audit_id` bigint(20) UNSIGNED NOT NULL,
  `audit_action` char(1) NOT NULL COMMENT 'U=update, D=delete',
  `audit_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `audit_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_number` varchar(4) DEFAULT NULL,
  `first_name` varchar(40) NOT NULL,
  `last_name` varchar(60) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(2) DEFAULT 'CH',
  `statuscode` char(1) DEFAULT 'D',
  `membership_start` date DEFAULT NULL,
  `membership_end` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_invitee` tinyint(1) DEFAULT 0,
  `photo_ok` tinyint(1) DEFAULT 1,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `activation_token` varchar(64) DEFAULT NULL,
  `activation_sent_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `member_magic_tokens`
--

CREATE TABLE `member_magic_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `token_hash` binary(32) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `member_phones`
--

CREATE TABLE `member_phones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `label` varchar(40) DEFAULT NULL,
  `is_whatsapp` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Trigger `member_phones`
--
DELIMITER $$
CREATE TRIGGER `member_phones_before_delete` BEFORE DELETE ON `member_phones` FOR EACH ROW BEGIN
    INSERT INTO `member_phones_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `phone_number`, `label`, `is_whatsapp`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('D', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`phone_number`, OLD.`label`, OLD.`is_whatsapp`, OLD.`sort_order`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `member_phones_before_update` BEFORE UPDATE ON `member_phones` FOR EACH ROW BEGIN
    INSERT INTO `member_phones_audit` (`audit_action`, `audit_user_id`, `id`, `member_id`, `phone_number`, `label`, `is_whatsapp`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`)
    VALUES ('U', @current_user_id, OLD.`id`, OLD.`member_id`, OLD.`phone_number`, OLD.`label`, OLD.`is_whatsapp`, OLD.`sort_order`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `member_phones_audit`
--

CREATE TABLE `member_phones_audit` (
  `audit_id` bigint(20) UNSIGNED NOT NULL,
  `audit_action` char(1) NOT NULL COMMENT 'U=update, D=delete',
  `audit_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `audit_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `label` varchar(40) DEFAULT NULL,
  `is_whatsapp` tinyint(1) DEFAULT 0,
  `sort_order` tinyint(3) UNSIGNED DEFAULT 0,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `portal_audit_log`
--

CREATE TABLE `portal_audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `member_number` varchar(4) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `detail` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` char(1) NOT NULL DEFAULT 'C',
  `member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `member_id`, `created_at`, `updated_at`) VALUES
(1, 'Oliver Wagner', 'oliver.wagner@smartgecko.ch', NULL, '$2y$12$hZLW2DRdSSRI3/HX6KR/t.BpYdQkWFzP5yITNKOHnh01u/ONWvTYO', NULL, 'A', NULL, '2026-03-22 12:54:36', '2026-03-22 12:54:36'),
(2, 'Livia Wagner', 'livia.wagner@gmail.com', NULL, '$2y$12$fvSMXmpeE9merfLPy4CqJurtFka9qDJGejjbboCGVeruIZxy/7RwW', NULL, 'A', NULL, '2026-03-22 12:54:36', '2026-03-22 12:54:36');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indizes für die Tabelle `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indizes für die Tabelle `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_chef_peloton_id_foreign` (`chef_peloton_id`),
  ADD KEY `events_modified_by_id_foreign` (`modified_by_id`);

--
-- Indizes für die Tabelle `events_audit`
--
ALTER TABLE `events_audit`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `events_audit_user_id_foreign` (`audit_user_id`);

--
-- Indizes für die Tabelle `event_member`
--
ALTER TABLE `event_member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `event_member_unique` (`event_id`,`member_id`),
  ADD KEY `event_member_member_id_foreign` (`member_id`),
  ADD KEY `event_member_modified_by_id_foreign` (`modified_by_id`);

--
-- Indizes für die Tabelle `event_member_audit`
--
ALTER TABLE `event_member_audit`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `event_member_audit_user_id_foreign` (`audit_user_id`);

--
-- Indizes für die Tabelle `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- Indizes für die Tabelle `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_number` (`invoice_number`),
  ADD KEY `invoices_member_id_foreign` (`member_id`),
  ADD KEY `invoices_modified_by_id_foreign` (`modified_by_id`);

--
-- Indizes für die Tabelle `invoices_audit`
--
ALTER TABLE `invoices_audit`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `invoices_audit_user_id_foreign` (`audit_user_id`);

--
-- Indizes für die Tabelle `invoice_event`
--
ALTER TABLE `invoice_event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_event_unique` (`invoice_id`,`event_id`),
  ADD KEY `invoice_event_event_id_foreign` (`event_id`);

--
-- Indizes für die Tabelle `invoice_lines`
--
ALTER TABLE `invoice_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_lines_invoice_id_foreign` (`invoice_id`);

--
-- Indizes für die Tabelle `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indizes für die Tabelle `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `member_number` (`member_number`),
  ADD KEY `members_modified_by_id_foreign` (`modified_by_id`);

--
-- Indizes für die Tabelle `members_audit`
--
ALTER TABLE `members_audit`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `members_audit_user_id_foreign` (`audit_user_id`);

--
-- Indizes für die Tabelle `member_magic_tokens`
--
ALTER TABLE `member_magic_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_member_magic_token_hash` (`token_hash`),
  ADD KEY `idx_member_magic_token_member` (`member_id`),
  ADD KEY `idx_member_magic_token_expires` (`expires_at`);

--
-- Indizes für die Tabelle `member_phones`
--
ALTER TABLE `member_phones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_phones_member_id_foreign` (`member_id`),
  ADD KEY `member_phones_modified_by_id_foreign` (`modified_by_id`);

--
-- Indizes für die Tabelle `member_phones_audit`
--
ALTER TABLE `member_phones_audit`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `member_phones_audit_user_id_foreign` (`audit_user_id`);

--
-- Indizes für die Tabelle `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indizes für die Tabelle `portal_audit_log`
--
ALTER TABLE `portal_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_portal_audit_member` (`member_id`),
  ADD KEY `idx_portal_audit_created` (`created_at`);

--
-- Indizes für die Tabelle `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `users_member_id_foreign` (`member_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `events_audit`
--
ALTER TABLE `events_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `event_member`
--
ALTER TABLE `event_member`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `event_member_audit`
--
ALTER TABLE `event_member_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoices_audit`
--
ALTER TABLE `invoices_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoice_event`
--
ALTER TABLE `invoice_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoice_lines`
--
ALTER TABLE `invoice_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `members_audit`
--
ALTER TABLE `members_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `member_magic_tokens`
--
ALTER TABLE `member_magic_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `member_phones`
--
ALTER TABLE `member_phones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `member_phones_audit`
--
ALTER TABLE `member_phones_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `portal_audit_log`
--
ALTER TABLE `portal_audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_chef_peloton_id_foreign` FOREIGN KEY (`chef_peloton_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `events_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `events_audit`
--
ALTER TABLE `events_audit`
  ADD CONSTRAINT `events_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `event_member`
--
ALTER TABLE `event_member`
  ADD CONSTRAINT `event_member_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `event_member_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `event_member_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `event_member_audit`
--
ALTER TABLE `event_member_audit`
  ADD CONSTRAINT `event_member_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `invoices_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `invoices_audit`
--
ALTER TABLE `invoices_audit`
  ADD CONSTRAINT `invoices_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `invoice_event`
--
ALTER TABLE `invoice_event`
  ADD CONSTRAINT `invoice_event_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `invoice_event_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints der Tabelle `invoice_lines`
--
ALTER TABLE `invoice_lines`
  ADD CONSTRAINT `invoice_lines_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints der Tabelle `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `members_audit`
--
ALTER TABLE `members_audit`
  ADD CONSTRAINT `members_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `member_magic_tokens`
--
ALTER TABLE `member_magic_tokens`
  ADD CONSTRAINT `fk_member_magic_token_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints der Tabelle `member_phones`
--
ALTER TABLE `member_phones`
  ADD CONSTRAINT `member_phones_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `member_phones_modified_by_id_foreign` FOREIGN KEY (`modified_by_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `member_phones_audit`
--
ALTER TABLE `member_phones_audit`
  ADD CONSTRAINT `member_phones_audit_user_id_foreign` FOREIGN KEY (`audit_user_id`) REFERENCES `users` (`id`);

--
-- Constraints der Tabelle `portal_audit_log`
--
ALTER TABLE `portal_audit_log`
  ADD CONSTRAINT `fk_portal_audit_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);

--
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
