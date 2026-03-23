-- =============================================================================
-- Dérailleur — User locking mechanism
-- Date: 23.03.2026
-- Description: Add is_locked column to users table
--              Locked users cannot log in; email is cleared to prevent recovery
-- Compatible: MariaDB 10.11+
-- =============================================================================

ALTER TABLE `users`
  ADD COLUMN `is_locked` tinyint(1) NOT NULL DEFAULT 0 AFTER `member_id`;

-- Verification
SELECT 'users.is_locked' AS `check`, COUNT(*) AS `ok`
FROM information_schema.columns
WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'is_locked';
