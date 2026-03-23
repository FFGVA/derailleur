-- =============================================
-- Dérailleur — Seed members from FFGva CSV export
-- Generated from: Membres FFGva - Feuille 1.csv
-- =============================================

USE agiletra_ffgva;
SET NAMES utf8mb4;

START TRANSACTION;

-- Allow NULL emails for members imported without one
ALTER TABLE members MODIFY email VARCHAR(255) NULL;
-- Drop the unique index and re-add to allow multiple NULLs explicitly
ALTER TABLE members DROP INDEX email;
ALTER TABLE members ADD UNIQUE INDEX email (email);

-- =============================================
-- MEMBERS
-- =============================================

-- Demande membre (statuscode P)
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Kayci', 'BROWNE', 'kaycibrowne@gmail.com', 'P', 0, 1, NULL, NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Sirella', 'FÉRÉDIE', 'sirella.feredie@gmail.com', 'P', 0, 1, '{"instagram": "Sirella_frd"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, country, updated_at) VALUES
('Justine', 'FRANCHETEAU', 'justine.francheteau@yahoo.fr', 'P', 0, 1, '{"instagram": "Justine.frcht"}', 'CH', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Anna', 'RIZZI', 'anna.rizzi.2009@gmail.com', 'P', 0, 1, '{"instagram": "annaa_rizzi_"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Louise', 'ROSSITER-LEVRARD', 'lrossiterlevrard@gmail.com', 'P', 0, 1, NULL, NOW());

-- Membre Graine (statuscode E — mineure)
-- NOTE: BRUGNOLI Gaia has no email and no unique identifier beyond name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Gaia', 'BRUGNOLI', NULL, 'E', 0, 1, '{"groupe": "Membre Graine"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Laetitia', 'CIMINO', NULL, 'E', 0, 1, '{"taille_maillot": "S", "groupe": "Membre Graine"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Victoria', 'CIMINO', NULL, 'E', 0, 1, '{"groupe": "Membre Graine"}', NOW());

-- Membre Salève (statuscode A)
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Maité', 'BARROSO', 'maite.barroso@gmail.com', 'A', 0, 1, '{"instagram": "maitechub", "taille_maillot": "S", "bib": "M", "gilet": "M", "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Elise', 'DUPUIS', 'elise.lozeron@gmail.com', 'A', 0, 1, '{"instagram": "elise.lozeron", "taille_maillot": "M", "aec": true, "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, country, updated_at) VALUES
('Sonia', 'GLOWACKI', 'sonia.glowacki@gmail.com', 'A', 0, 1, '{"instagram": "soniaglw", "taille_maillot": "M", "groupe": "Membre Salève"}', 'Chemin de la Tuiliere 690', '74140', 'Veigy-Foncenex', 'FR', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Julie', 'HUG', 'blackburn.hug@gmail.com', 'A', 0, 1, '{"instagram": "Julielahug", "taille_maillot": "M", "bib": "M", "gilet": "M", "groupe": "Membre Salève"}', 'Avenue du Bouchet 5', '1209', 'Genève', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Brigitte', 'LÜTH', 'b.lueth@afia.at', 'A', 0, 1, '{"instagram": "lueth", "taille_maillot": "S", "groupe": "Membre Salève"}', 'Chemin de Dessous-Saint-Loup 4c', '1290', 'Versoix', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Monica', 'MARINUCCI', 'monicamarinucci@gmail.com', 'A', 0, 1, '{"taille_maillot": "S", "aec": true, "bib": "M", "gilet": "M", "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Sophie', 'MASSOBRE', 'sophiemassobre@gmail.com', 'A', 0, 1, '{"instagram": "mg.sophie", "taille_maillot": "M", "aec": true, "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Ana', 'MENCHERO GONZALEZ', 'amenchero@gmail.com', 'A', 0, 1, '{"taille_maillot": "XL", "aec": true, "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Eloise', 'MICELI', 'eloise.miceli@gmail.com', 'A', 0, 1, '{"taille_maillot": "M", "bib": "L", "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Chris', 'NASSIVERA', 'chrisnassivera@gmail.com', 'A', 0, 1, '{"taille_maillot": "XL", "aec": true, "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Allison', 'NEAPOLE', 'aneapole@bluewin.ch', 'A', 0, 1, '{"instagram": "aneapole", "taille_maillot": "2XL", "bib": "2XL", "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Alice', 'NOEL', 'nalice@hotmail.com', 'A', 0, 1, '{"instagram": "nalicegeneva81", "taille_maillot": "L", "bib": "L", "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Emma', 'O''LEARY', 'emmamaryoleary@gmail.com', 'A', 0, 1, '{"instagram": "emmamaryoh", "taille_maillot": "XL", "groupe": "Membre Salève"}', 'Rue des Peupliers 18', '1205', 'Geneve', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Monica', 'PEREIRA LIMA', 'monipere@icloud.com', 'A', 0, 1, '{"instagram": "moni.mo_ni", "taille_maillot": "M", "aec": true, "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Raquel', 'SOLES', 'raquelsoles72@gmail.com', 'A', 0, 1, '{"instagram": "solesraquel", "taille_maillot": "M", "aec": true, "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Alina', 'STANCULESCU', 'stanculescu.alina.elena@gmail.com', 'A', 0, 1, '{"taille_maillot": "S", "bib": "S", "groupe": "Membre Salève"}', 'Chemin de Saule 74', '1233', 'Bernex', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Géraldine', 'TORNARE', 'tornare.geraldine@icloud.com', 'A', 0, 1, '{"instagram": "grldneanorrt", "taille_maillot": "M", "aec": true, "bib": "L", "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Olwen', 'WILSON', 'olwenwilson95@gmail.com', 'A', 0, 1, '{"instagram": "olwenwilson", "taille_maillot": "L", "groupe": "Membre Salève"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Mahesha', 'YAPA', 'mahesha.yapa@gmail.com', 'A', 0, 1, '{"taille_maillot": "M", "groupe": "Membre Salève"}', NOW());

-- Membre Voirons (statuscode A)
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Giulia', 'ANTONIOLI', 'giulia.antonioli@gmail.com', 'A', 0, 1, '{"instagram": "the.giuliagram", "taille_maillot": "M", "aec": true, "bib": "M", "gilet": "M", "fonction": "Trésorière", "groupe": "Membre Voirons"}', 'Route de Malagnou 172', '1224', 'Chêne-Bougeries', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Dune', 'BOURQUIN', 'dune.bourquin@gmail.com', 'A', 0, 1, '{"instagram": "Dune_3", "taille_maillot": "S", "bib": "S", "gilet": "S", "fonction": "Membre comité", "groupe": "Membre Voirons"}', 'Chemin du Champ-Baron 14', '1209', 'Genève', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, country, updated_at) VALUES
('Olivia', 'CHASSOT', 'olivia.a.chassot@gmail.com', 'A', 0, 1, '{"taille_maillot": "S", "bib": "S", "gilet": "S", "fonction": "Membre comité", "groupe": "Membre Voirons"}', 'Rue de Genève 24', '01160', 'Ferney-Voltaire', 'FR', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Caroline', 'GAILLARD', 'carolinegaillard@gmail.com', 'A', 0, 1, '{"instagram": "carolineglrd", "taille_maillot": "M", "bib": "L", "gilet": "M", "fonction": "Présidente", "groupe": "Membre Voirons"}', 'Chemin de Pinchat 42C', '1234', 'Vessy', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Sofia', 'PASSARETTI', 'sofiapassaretti@gmail.com', 'A', 0, 1, '{"instagram": "sofiapssrettti", "taille_maillot": "L", "aec": true, "fonction": "Vice-présidente", "groupe": "Membre Voirons"}', 'Chemin des Palettes 15', '1212', 'Grand-Lancy', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Marta', 'RODRIGUEZ', 'marta.rodriguez29@hotmail.co.uk', 'A', 0, 1, '{"instagram": "Nevertoolateee29", "taille_maillot": "S", "fonction": "Membre comité", "groupe": "Membre Voirons"}', 'Chemin de la Seymaz 24C', '1253', 'Vandoeuvres', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Marguerite', 'VERNET', 'margueritevernet@outlook.com', 'A', 0, 1, '{"instagram": "margoo.vrnt", "taille_maillot": "M", "aec": true, "fonction": "Membre comité", "groupe": "Membre Voirons"}', 'Boulevard des Philosophes 11', '1205', 'Genève', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Livia', 'WAGNER', 'livia.wagner@gmail.com', 'A', 0, 1, '{"instagram": "Liviawae_", "taille_maillot": "L", "bib": "L", "gilet": "L", "fonction": "Secrétaire + com", "groupe": "Membre Voirons"}', 'Chemin du Champ-Baron 14', '1209', 'Genève', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, address, postal_code, city, updated_at) VALUES
('Anne', 'ZENDALI DIMOPOULOS', 'azendali@infomaniak.ch', 'A', 0, 1, '{"taille_maillot": "S", "aec": true, "fonction": "Membre comité", "groupe": "Membre Voirons"}', 'Chemin du Borbolet 5A', '1213', 'Onex', NOW());

-- Participantes (statuscode N)
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Amélie', 'ABBET', 'abbetmel082@gmail.com', 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Maelle', 'ACHARD', 'maelle.achard@gmail.com', 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: BALMER Morgane has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Morgane', 'BALMER', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: BARBEY CHAPPUIS Marie has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Marie', 'BARBEY CHAPPUIS', NULL, 'N', 0, 1, '{"instagram": "marie.barbet.chappuis", "groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Jenia', 'BORISKOVSKAIA', 'boriskovskaia@gmail.com', 'N', 0, 1, '{"instagram": "lesprenomsdegenie", "groupe": "Participantes"}', NOW());

-- NOTE: BOURGAUD Camille has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Camille', 'BOURGAUD', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: BOURGAUD Pauline has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Pauline', 'BOURGAUD', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Laila', 'CASTALDO', 'laila.castaldo@gmail.com', 'N', 0, 1, '{"instagram": "Laila Castaldo", "groupe": "Participantes"}', NOW());

-- NOTE: D Heike — last name is just "D", no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Heike', 'D', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: DARLIX-HUG Noemie has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Noemie', 'DARLIX-HUG', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: De Haes Paulien has no email AND no phone — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Paulien', 'De Haes', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: DOTTRENS Pauline has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Pauline', 'DOTTRENS', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Laurine', 'EISENHUTH', 'laurine.eisenhuth@gmail.com', 'N', 0, 1, '{"instagram": "Laurineeisenhuth", "groupe": "Participantes"}', NOW());

-- NOTE: FABBRI ? Alicia — last name contains "?", no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Alicia', 'FABBRI ?', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Vanessa', 'GEROTTO', 'vanessa.gerotto@gmail.com', 'N', 0, 1, '{"instagram": "roulerpourmagda", "groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Helena', 'JJOVIC', 'helena.jjovic@gmail.com', 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Carla', 'KARAM', 'carlakaram@outlook.com', 'N', 0, 1, '{"instagram": "carlouchi", "groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Marie', 'LAMASSIAUDE', 'm.lamassiaude@gmail.com', 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Lucile', 'MOULIN', 'lucilemoulin.02@gmail.com', 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: NECCO ? Silvia — last name contains "?", no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Silvia', 'NECCO ?', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: PECHARROMAN Carmen has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Carmen', 'PECHARROMAN', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: PELLEGRINI Laetitia has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Laetitia', 'PELLEGRINI', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: POLTERA Ana has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Ana', 'POLTERA', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Laura', 'ROSER', 'laura@niviuk.ch', 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Lola', 'SAUGY', 'lola.saugy@gmail.com', 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: Stuart Sammie has no email AND no phone — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Sammie', 'Stuart', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: W Pernilla — last name is just "W", no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Pernilla', 'W', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: ZILBER Nadine has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Nadine', 'ZILBER', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: Estelle with no last name, no email — edge case, identified by first_name + last_name '—'
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Estelle', '—', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: Jacqueline with no last name, no email, no phone — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Jacqueline', '—', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: "Bicyclette bleue" is used as last name for two members (Maelle and Maud) — no email
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Maelle', 'Bicyclette bleue', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Maud', 'Bicyclette bleue', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: Corinne with no last name, no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Corinne', '—', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: Hortense with no last name, no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Hortense', '—', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: IZA with no last name, no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('IZA', '—', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- NOTE: Ju with no last name, no email — edge case
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Ju', '—', NULL, 'N', 0, 1, '{"groupe": "Participantes"}', NOW());

-- X - Abandon (statuscode N — non-membre)
-- NOTE: EGLI Célia has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Célia', 'EGLI', NULL, 'N', 0, 1, NULL, NOW());

-- NOTE: GALLI Noemi has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Noemi', 'GALLI', NULL, 'N', 0, 1, NULL, NOW());

-- NOTE: GALLI GEISSMANN Prune has no email — lookup by first_name + last_name
INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Prune', 'GALLI GEISSMANN', NULL, 'N', 0, 1, NULL, NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Myriam', 'PAPERMAN', 'myriampaperman@gmail.com', 'N', 0, 1, '{"instagram": "mental_myriam ou notyouraveragebobs"}', NOW());

INSERT INTO members (first_name, last_name, email, statuscode, is_invitee, photo_ok, metadata, updated_at) VALUES
('Chiara', 'BOMBARDI', 'chiara.bombardi86@gmail.com', 'N', 0, 1, '{"instagram": "chiabomba", "groupe": "Participantes"}', NOW());

-- =============================================
-- MEMBER PHONES
-- =============================================

-- BROWNE Kayci
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'kaycibrowne@gmail.com' LIMIT 1), '076 627 54 15', 'Mobile principal', 0, 0, NOW());

-- FÉRÉDIE Sirella
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'sirella.feredie@gmail.com' LIMIT 1), '078 318 92 77', 'Mobile principal', 0, 0, NOW());

-- FRANCHETEAU Justine
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'justine.francheteau@yahoo.fr' LIMIT 1), '0033 6 09 45 54 95', 'Mobile principal', 0, 0, NOW());

-- RIZZI Anna
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'anna.rizzi.2009@gmail.com' LIMIT 1), '076 739 27 08', 'Mobile principal', 0, 0, NOW());

-- ROSSITER-LEVRARD Louise
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'lrossiterlevrard@gmail.com' LIMIT 1), '0033 6 83 47 41 18', 'Mobile principal', 0, 0, NOW());

-- BRUGNOLI Gaia (no email, no phone — skip)

-- CIMINO Laetitia (no email, no phone — skip)

-- CIMINO Victoria (no email, no phone — skip)

-- BARROSO Maité
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'maite.barroso@gmail.com' LIMIT 1), '075 411 47 54', 'Mobile principal', 0, 0, NOW());

-- DUPUIS Elise
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'elise.lozeron@gmail.com' LIMIT 1), '076 615 67 30', 'Mobile principal', 0, 0, NOW());

-- GLOWACKI Sonia
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'sonia.glowacki@gmail.com' LIMIT 1), '0033 7 49 76 28 97', 'Mobile principal', 0, 0, NOW());

-- HUG Julie
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'blackburn.hug@gmail.com' LIMIT 1), '079 128 51 71', 'Mobile principal', 0, 0, NOW());

-- LÜTH Brigitte
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'b.lueth@afia.at' LIMIT 1), '079 882 93 53', 'Mobile principal', 0, 0, NOW());

-- MARINUCCI Monica
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'monicamarinucci@gmail.com' LIMIT 1), '079 572 00 38', 'Mobile principal', 0, 0, NOW());

-- MASSOBRE Sophie
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'sophiemassobre@gmail.com' LIMIT 1), '0033 6 49 00 01 49', 'Mobile principal', 0, 0, NOW());

-- MENCHERO GONZALEZ Ana — main phone + different WhatsApp number
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'amenchero@gmail.com' LIMIT 1), '078 606 38 32', 'Mobile principal', 0, 0, NOW());

INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'amenchero@gmail.com' LIMIT 1), '0034 645 80 86 18', 'Mobile secondaire', 1, 1, NOW());

-- MICELI Eloise
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'eloise.miceli@gmail.com' LIMIT 1), '078 705 92 98', 'Mobile principal', 0, 0, NOW());

-- NASSIVERA Chris
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'chrisnassivera@gmail.com' LIMIT 1), '079 909 89 35', 'Mobile principal', 0, 0, NOW());

-- NEAPOLE Allison
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'aneapole@bluewin.ch' LIMIT 1), '078 605 68 98', 'Mobile principal', 0, 0, NOW());

-- NOEL Alice
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'nalice@hotmail.com' LIMIT 1), '076 616 81 92', 'Mobile principal', 0, 0, NOW());

-- O'LEARY Emma
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'emmamaryoleary@gmail.com' LIMIT 1), '078 806 83 08', 'Mobile principal', 0, 0, NOW());

-- PEREIRA LIMA Monica
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'monipere@icloud.com' LIMIT 1), '078 676 51 50', 'Mobile principal', 0, 0, NOW());

-- SOLES Raquel
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'raquelsoles72@gmail.com' LIMIT 1), '0033 6 48 72 51 04', 'Mobile principal', 0, 0, NOW());

-- STANCULESCU Alina
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'stanculescu.alina.elena@gmail.com' LIMIT 1), '077 413 25 17', 'Mobile principal', 0, 0, NOW());

-- TORNARE Géraldine
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'tornare.geraldine@icloud.com' LIMIT 1), '077 445 13 11', 'Mobile principal', 0, 0, NOW());

-- WILSON Olwen — main phone + different WhatsApp number
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'olwenwilson95@gmail.com' LIMIT 1), '077 993 00 99', 'Mobile principal', 0, 0, NOW());

INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'olwenwilson95@gmail.com' LIMIT 1), '0044 7487 748057', 'Mobile secondaire', 1, 1, NOW());

-- YAPA Mahesha
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'mahesha.yapa@gmail.com' LIMIT 1), '079 537 77 78', 'Mobile principal', 0, 0, NOW());

-- ANTONIOLI Giulia
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'giulia.antonioli@gmail.com' LIMIT 1), '078 899 40 78', 'Mobile principal', 0, 0, NOW());

-- BOURQUIN Dune
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'dune.bourquin@gmail.com' LIMIT 1), '079 942 04 29', 'Mobile principal', 0, 0, NOW());

-- CHASSOT Olivia
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'olivia.a.chassot@gmail.com' LIMIT 1), '0033 6 73 96 05 19', 'Mobile principal', 0, 0, NOW());

-- GAILLARD Caroline
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'carolinegaillard@gmail.com' LIMIT 1), '078 708 41 13', 'Mobile principal', 0, 0, NOW());

-- PASSARETTI Sofia
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'sofiapassaretti@gmail.com' LIMIT 1), '078 344 35 35', 'Mobile principal', 0, 0, NOW());

-- RODRIGUEZ Marta
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'marta.rodriguez29@hotmail.co.uk' LIMIT 1), '078 227 26 87', 'Mobile principal', 0, 0, NOW());

-- VERNET Marguerite
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'margueritevernet@outlook.com' LIMIT 1), '078 335 31 91', 'Mobile principal', 0, 0, NOW());

-- WAGNER Livia
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'livia.wagner@gmail.com' LIMIT 1), '076 395 14 54', 'Mobile principal', 0, 0, NOW());

-- ZENDALI DIMOPOULOS Anne
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'azendali@infomaniak.ch' LIMIT 1), '076 378 57 91', 'Mobile principal', 0, 0, NOW());

-- ABBET Amélie
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'abbetmel082@gmail.com' LIMIT 1), '078 855 02 13', 'Mobile principal', 0, 0, NOW());

-- ACHARD Maelle
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'maelle.achard@gmail.com' LIMIT 1), '0033 6 37 83 28 11', 'Mobile principal', 0, 0, NOW());

-- BALMER Morgane (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Morgane' AND last_name = 'BALMER' LIMIT 1), '079 697 88 11', 'Mobile principal', 0, 0, NOW());

-- BARBEY CHAPPUIS Marie (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Marie' AND last_name = 'BARBEY CHAPPUIS' LIMIT 1), '079 754 45 84', 'Mobile principal', 0, 0, NOW());

-- BORISKOVSKAIA Jenia
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'boriskovskaia@gmail.com' LIMIT 1), '076 498 30 69', 'Mobile principal', 0, 0, NOW());

-- BOURGAUD Camille (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Camille' AND last_name = 'BOURGAUD' LIMIT 1), '0033 6 81 26 02 63', 'Mobile principal', 0, 0, NOW());

-- BOURGAUD Pauline (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Pauline' AND last_name = 'BOURGAUD' LIMIT 1), '078 202 67 62', 'Mobile principal', 0, 0, NOW());

-- CASTALDO Laila
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'laila.castaldo@gmail.com' LIMIT 1), '079 682 37 90', 'Mobile principal', 0, 0, NOW());

-- D Heike (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Heike' AND last_name = 'D' LIMIT 1), '079 460 14 66', 'Mobile principal', 0, 0, NOW());

-- DARLIX-HUG Noemie (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Noemie' AND last_name = 'DARLIX-HUG' LIMIT 1), '078 215 75 25', 'Mobile principal', 0, 0, NOW());

-- De Haes Paulien — no phone, skip

-- DOTTRENS Pauline (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Pauline' AND last_name = 'DOTTRENS' LIMIT 1), '079 723 35 75', 'Mobile principal', 0, 0, NOW());

-- EISENHUTH Laurine
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'laurine.eisenhuth@gmail.com' LIMIT 1), '076 220 72 46', 'Mobile principal', 0, 0, NOW());

-- FABBRI ? Alicia (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Alicia' AND last_name = 'FABBRI ?' LIMIT 1), '079 948 47 66', 'Mobile principal', 0, 0, NOW());

-- GEROTTO Vanessa — no phone, skip

-- JJOVIC Helena — no phone, skip

-- KARAM Carla — no phone, skip

-- LAMASSIAUDE Marie — no phone, skip

-- MOULIN Lucile
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'lucilemoulin.02@gmail.com' LIMIT 1), '0033 7 82 79 54 44', 'Mobile principal', 0, 0, NOW());

-- NECCO ? Silvia (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Silvia' AND last_name = 'NECCO ?' LIMIT 1), '076 271 64 27', 'Mobile principal', 0, 0, NOW());

-- PECHARROMAN Carmen (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Carmen' AND last_name = 'PECHARROMAN' LIMIT 1), '076 374 55 91', 'Mobile principal', 0, 0, NOW());

-- PELLEGRINI Laetitia (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Laetitia' AND last_name = 'PELLEGRINI' LIMIT 1), '0033 6 52 10 66 44', 'Mobile principal', 0, 0, NOW());

-- POLTERA Ana (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Ana' AND last_name = 'POLTERA' LIMIT 1), '078 611 51 19', 'Mobile principal', 0, 0, NOW());

-- ROSER Laura
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'laura@niviuk.ch' LIMIT 1), '078 736 07 43', 'Mobile principal', 0, 0, NOW());

-- SAUGY Lola — no phone, skip

-- Stuart Sammie — no phone, skip

-- W Pernilla (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Pernilla' AND last_name = 'W' LIMIT 1), '079 817 80 05', 'Mobile principal', 0, 0, NOW());

-- ZILBER Nadine (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Nadine' AND last_name = 'ZILBER' LIMIT 1), '0033 6 77 00 61 12', 'Mobile principal', 0, 0, NOW());

-- Estelle (no last name, no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Estelle' AND last_name = '—' LIMIT 1), '0033 6 62 50 62 99', 'Mobile principal', 0, 0, NOW());

-- Jacqueline — no phone, skip

-- Bicyclette bleue Maelle (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Maelle' AND last_name = 'Bicyclette bleue' LIMIT 1), '0033 6 52 38 10 24', 'Mobile principal', 0, 0, NOW());

-- Bicyclette bleue Maud (no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Maud' AND last_name = 'Bicyclette bleue' LIMIT 1), '077 468 89 28', 'Mobile principal', 0, 0, NOW());

-- Corinne (no last name, no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Corinne' AND last_name = '—' LIMIT 1), '078 638 99 71', 'Mobile principal', 0, 0, NOW());

-- Hortense (no last name, no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Hortense' AND last_name = '—' LIMIT 1), '0033 7 88 79 94 02', 'Mobile principal', 0, 0, NOW());

-- IZA (no last name, no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'IZA' AND last_name = '—' LIMIT 1), '079 285 78 67', 'Mobile principal', 0, 0, NOW());

-- Ju (no last name, no email)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Ju' AND last_name = '—' LIMIT 1), '079 294 44 24', 'Mobile principal', 0, 0, NOW());

-- EGLI Célia (no email, X - Abandon)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Célia' AND last_name = 'EGLI' LIMIT 1), '079 229 63 49', 'Mobile principal', 0, 0, NOW());

-- GALLI Noemi (no email, X - Abandon)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Noemi' AND last_name = 'GALLI' LIMIT 1), '078 814 80 75', 'Mobile principal', 0, 0, NOW());

-- GALLI GEISSMANN Prune (no email, X - Abandon)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE first_name = 'Prune' AND last_name = 'GALLI GEISSMANN' LIMIT 1), '079 886 50 48', 'Mobile principal', 0, 0, NOW());

-- PAPERMAN Myriam (X - Abandon)
INSERT INTO member_phones (member_id, phone_number, label, is_whatsapp, sort_order, updated_at) VALUES
((SELECT id FROM members WHERE email = 'myriampaperman@gmail.com' LIMIT 1), '0033 7 69 90 90 83', 'Mobile principal', 0, 0, NOW());

-- BOMBARDI Chiara — no phone, skip

COMMIT;
