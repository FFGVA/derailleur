-- Local-dev admin seed. DO NOT run on production.
--
-- Creates (or refreshes) admin@ffgva.ch with password "password" and role 'A'.
-- Idempotent: running multiple times resets the password/role but never duplicates.
--
-- Runs automatically at the end of scripts/db-load.sh.

INSERT INTO `users` (`name`, `email`, `email_verified_at`, `password`, `role`, `is_locked`, `created_at`, `updated_at`)
VALUES (
    'Admin Local',
    'admin@ffgva.ch',
    NOW(),
    '$2y$12$asVCJBFNfGcCkFIO3vxzNez1MSXJxhNRRLrnnx5FJdG2QbpHDY5aK',
    'A',
    0,
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    `password`   = VALUES(`password`),
    `role`       = 'A',
    `is_locked`  = 0,
    `updated_at` = NOW();
