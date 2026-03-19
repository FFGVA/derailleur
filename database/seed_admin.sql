-- Insert admin user for Dérailleur
-- Run on production: agiletra_ffgva

USE `agiletra_ffgva`;

INSERT INTO `users` (`name`, `email`, `password`, `role`, `member_id`, `created_at`, `updated_at`)
VALUES (
    'Oliver Wagner',
    'oliver.wagner@smartgecko.ch',
    '$2y$12$RbNRSwt2j1dQ51cAugv.neGseP3j6.QgCwgoGMoKlevpf8Yqe5tLe',
    'A',
    NULL,
    NOW(),
    NOW()
);
