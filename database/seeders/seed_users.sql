-- =============================================
-- Seed admin users for Dérailleur
-- Run AFTER create_database.sql
-- =============================================

USE `agiletra_ffgva`;

INSERT INTO `users` (`name`, `email`, `password`, `role`, `member_id`, `created_at`, `updated_at`)
VALUES
    ('Oliver Wagner', 'oliver.wagner@smartgecko.ch', '$2y$12$hZLW2DRdSSRI3/HX6KR/t.BpYdQkWFzP5yITNKOHnh01u/ONWvTYO', 'A', NULL, NOW(), NOW()),
    ('Livia Wagner', 'livia.wagner@gmail.com', '$2y$12$fvSMXmpeE9merfLPy4CqJurtFka9qDJGejjbboCGVeruIZxy/7RwW', 'A', NULL, NOW(), NOW());
