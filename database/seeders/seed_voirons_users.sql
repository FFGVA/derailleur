-- =============================================
-- Dérailleur — Seed Voirons committee as admin users
-- Run AFTER seed_members.sql
-- =============================================
--
-- Credentials (share individually, ask them to reset via "Mot de passe oublié"):
--
-- giulia.antonioli@gmail.com        45872618d502
-- dune.bourquin@gmail.com           409d09ed1dfd
-- olivia.a.chassot@gmail.com        8ec45d915fee
-- carolinegaillard@gmail.com        17348bf98bc7
-- sofiapassaretti@gmail.com         1d6172653d82
-- marta.rodriguez29@hotmail.co.uk   6d2916c858b6
-- margueritevernet@outlook.com      e72e29c710b1
-- azendali@infomaniak.ch            0cd42aab4d2a
--

USE agiletra_ffgva;
SET NAMES utf8mb4;

INSERT INTO users (name, email, password, role, member_id, created_at, updated_at) VALUES
('Giulia Antonioli', 'giulia.antonioli@gmail.com', '$2y$12$pdr7T/So048fVqDCzgPRceAeNcqmDZ4JJ9z51u6xu4SlvMYuoNebO', 'A', (SELECT id FROM members WHERE email = 'giulia.antonioli@gmail.com' LIMIT 1), NOW(), NOW()),
('Dune Bourquin', 'dune.bourquin@gmail.com', '$2y$12$v.zSbWCM8A7FS33/AfwZuup2Drua7b88Yc3uoW2l1eUd2sXqmUhp6', 'A', (SELECT id FROM members WHERE email = 'dune.bourquin@gmail.com' LIMIT 1), NOW(), NOW()),
('Olivia Chassot', 'olivia.a.chassot@gmail.com', '$2y$12$7NE4L1PvSMUDZBgZqYapiuqFLpkzQH7u7DeSOf8obBLpeKGDek83y', 'A', (SELECT id FROM members WHERE email = 'olivia.a.chassot@gmail.com' LIMIT 1), NOW(), NOW()),
('Caroline Gaillard', 'carolinegaillard@gmail.com', '$2y$12$vnCOFRHbxSrHvhNEjiIXwOUHWkh8R3DJuIeULzEIHKq/FukWaUtku', 'A', (SELECT id FROM members WHERE email = 'carolinegaillard@gmail.com' LIMIT 1), NOW(), NOW()),
('Sofia Passaretti', 'sofiapassaretti@gmail.com', '$2y$12$7MTuUs/NviTOyRZCcA3q1.2c7dNa9w3R5qi6xAiBol46YP7uxzn8.', 'A', (SELECT id FROM members WHERE email = 'sofiapassaretti@gmail.com' LIMIT 1), NOW(), NOW()),
('Marta Rodriguez', 'marta.rodriguez29@hotmail.co.uk', '$2y$12$52zJYyQ8AKh4qyXOO64aCOSllMfEELsnsVi64NGxIlArsVX9IDRIi', 'A', (SELECT id FROM members WHERE email = 'marta.rodriguez29@hotmail.co.uk' LIMIT 1), NOW(), NOW()),
('Marguerite Vernet', 'margueritevernet@outlook.com', '$2y$12$KEIljEOo2Lne4NWN4BbiN.GfgWG8Um.3s52dlFGYpIWay4BnRzcfS', 'A', (SELECT id FROM members WHERE email = 'margueritevernet@outlook.com' LIMIT 1), NOW(), NOW()),
('Anne Zendali Dimopoulos', 'azendali@infomaniak.ch', '$2y$12$D7ZnRExsu3OAPXV7fTqGb.Hdb2cTlLfQmf1npLK.R33IcVde7mmrO', 'A', (SELECT id FROM members WHERE email = 'azendali@infomaniak.ch' LIMIT 1), NOW(), NOW());

-- Link Livia Wagner's existing user to her member record
UPDATE users SET member_id = (SELECT id FROM members WHERE email = 'livia.wagner@gmail.com' LIMIT 1) WHERE email = 'livia.wagner@gmail.com';
