-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql14j15.db.hostpoint.internal
-- Erstellungszeit: 23. Mrz 2026 um 10:31
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

--
-- Daten für Tabelle `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('derailleur-cache-08f9cb59bd15b45db78fe041a9a3af464de059a4', 'i:3;', 1774254546),
('derailleur-cache-08f9cb59bd15b45db78fe041a9a3af464de059a4:timer', 'i:1774254546;', 1774254546),
('derailleur-cache-1b6453892473a467d07372d45eb05abc2031647a', 'i:2;', 1774209953),
('derailleur-cache-1b6453892473a467d07372d45eb05abc2031647a:timer', 'i:1774209953;', 1774209953),
('derailleur-cache-1d70ab6dd583657f00f42b43a6517b1466e7d1d6', 'i:1;', 1774218337),
('derailleur-cache-1d70ab6dd583657f00f42b43a6517b1466e7d1d6:timer', 'i:1774218337;', 1774218337),
('derailleur-cache-2196fbf39833a29f544706505ffde667649b889a', 'i:2;', 1774209799),
('derailleur-cache-2196fbf39833a29f544706505ffde667649b889a:timer', 'i:1774209799;', 1774209799),
('derailleur-cache-7216e67b0261018d1eae79b2cf0c5830231a26ae', 'i:2;', 1774257843),
('derailleur-cache-7216e67b0261018d1eae79b2cf0c5830231a26ae:timer', 'i:1774257843;', 1774257843),
('derailleur-cache-778912fb31a3feefc2749f719a297707c5905ebc', 'i:2;', 1774244953),
('derailleur-cache-778912fb31a3feefc2749f719a297707c5905ebc:timer', 'i:1774244953;', 1774244953),
('derailleur-cache-9965cfcb43b2f7abb597337eb4d90c808adaf83e', 'i:1;', 1774207953),
('derailleur-cache-9965cfcb43b2f7abb597337eb4d90c808adaf83e:timer', 'i:1774207953;', 1774207953),
('derailleur-cache-9b7693045b253268d9cfa054532c85d9', 'i:2;', 1774209760),
('derailleur-cache-9b7693045b253268d9cfa054532c85d9:timer', 'i:1774209760;', 1774209760),
('derailleur-cache-ace019b777b953fab64e6f5d5ea32bbbed7116a1', 'i:2;', 1774210151),
('derailleur-cache-ace019b777b953fab64e6f5d5ea32bbbed7116a1:timer', 'i:1774210151;', 1774210151),
('derailleur-cache-b49300fd10f5a54c5ad82e270abfff3744a583a7', 'i:2;', 1774207936),
('derailleur-cache-b49300fd10f5a54c5ad82e270abfff3744a583a7:timer', 'i:1774207936;', 1774207936),
('derailleur-cache-c8e6585454d3e38596e4df2fbff331e303d77ee6', 'i:1;', 1774257845),
('derailleur-cache-c8e6585454d3e38596e4df2fbff331e303d77ee6:timer', 'i:1774257845;', 1774257845),
('derailleur-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0', 'i:1;', 1774216683),
('derailleur-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0:timer', 'i:1774216683;', 1774216683),
('derailleur-cache-f30bcc68e0804f60d9e5be94f10bd58696111b56', 'i:1;', 1774218675),
('derailleur-cache-f30bcc68e0804f60d9e5be94f10bd58696111b56:timer', 'i:1774218675;', 1774218675),
('derailleur-cache-livewire-rate-limiter:009aad54c515b16fca5456e89ac2b0b6fb4c57e4', 'i:1;', 1774198249),
('derailleur-cache-livewire-rate-limiter:009aad54c515b16fca5456e89ac2b0b6fb4c57e4:timer', 'i:1774198249;', 1774198249),
('derailleur-cache-livewire-rate-limiter:0a55c22c979be90523f1f1b952b5972759216d28', 'i:1;', 1774189615),
('derailleur-cache-livewire-rate-limiter:0a55c22c979be90523f1f1b952b5972759216d28:timer', 'i:1774189615;', 1774189615),
('derailleur-cache-livewire-rate-limiter:366aa33b15fe0b43e8b885ec8b8dc351983379b7', 'i:1;', 1774196281),
('derailleur-cache-livewire-rate-limiter:366aa33b15fe0b43e8b885ec8b8dc351983379b7:timer', 'i:1774196281;', 1774196281),
('derailleur-cache-livewire-rate-limiter:62ec52b8a05633808f6e59cfec047ef6815d499b', 'i:1;', 1774196223),
('derailleur-cache-livewire-rate-limiter:62ec52b8a05633808f6e59cfec047ef6815d499b:timer', 'i:1774196223;', 1774196223),
('derailleur-cache-livewire-rate-limiter:a2eeb05e31246f6fddd8880e3f4faf037682252d', 'i:1;', 1774221224),
('derailleur-cache-livewire-rate-limiter:a2eeb05e31246f6fddd8880e3f4faf037682252d:timer', 'i:1774221224;', 1774221224),
('derailleur-cache-livewire-rate-limiter:d39365b58b758d8456a85b18b4e266a2fd23f674', 'i:1;', 1774196266),
('derailleur-cache-livewire-rate-limiter:d39365b58b758d8456a85b18b4e266a2fd23f674:timer', 'i:1774196266;', 1774196266),
('derailleur-cache-livewire-rate-limiter:d8492be2719a5d61d276d23b2e77d2dd1fd75b39', 'i:1;', 1774256962),
('derailleur-cache-livewire-rate-limiter:d8492be2719a5d61d276d23b2e77d2dd1fd75b39:timer', 'i:1774256962;', 1774256962);

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
  `event_type` char(1) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `max_participants` int(10) UNSIGNED DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `price_non_member` decimal(8,2) DEFAULT NULL,
  `statuscode` char(1) NOT NULL DEFAULT 'N',
  `gpx_file` varchar(255) DEFAULT NULL,
  `chef_peloton_id` bigint(20) UNSIGNED DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `events`
--

INSERT INTO `events` (`id`, `event_type`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `price_non_member`, `statuscode`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 'E', 'Entrainement #4 Jura', '<p>On commence gentiment à regarder les paysages depuis un peu plus haut !<br>RDV devant le <strong>dépôt bagages à l’intérieur de la Gare Cornavin</strong>. Nous prendrons le train aux alentours de <strong>9h30 jusqu’à Nyon</strong> pour rejoindre la Vallée de Joux et redescendre via St. Cergues (ou Gex, selon les dispositions du groupe).<br><br>Points d’interêt:<br>⛰️ Col du Marchairuz<br>⛰️ Vallée de Joux<br>⛰️ Lac des Rousses<br>⛰️ Col de la Givrine<br><br>La sortie est un entraînement pour une course de groupe, mais elle reste <strong>ouverte à toutes</strong>. Le profil du parcours est une bonne occasion pour se mesurer aux &gt;1300D+ et prendre confiance à rouler en groupe.<br><strong>Pas d’allure moyenne prédéfinie</strong>. Ensemble sur le plat, chacune à son rythme en montée, café en haut (ou champagne) en attendant que tout le monde arrive.<br><br></p>', 'Gare Cornavin', '2026-03-27 09:30:00', '2026-03-27 14:30:00', 15, 0.00, 0.00, 'P', NULL, 32, NULL, '2026-03-22 20:45:12', NULL),
(2, 'T', 'Workshop #2 Atelier Position', '<p>Ciclissimo nous reçoit pour préparer notre saison et atteindre nos défis sportifs!</p><p>L\'idée reçue qu\'il faut souffrir pour être performant est balayée. Le résumé tient en une phrase : Un cycliste bien posé est un cycliste efficace. Le \"fit\" n\'est pas qu\'une affaire de centimètre, mais de physiologie. Le vélo doit s\'adapter à l\'humain, et non l\'inverse.</p><p>Ce workshop est une session pour comprendre comment optimiser chaque point de contact entre nous et notre vélo afin de rouler plus longtemps, plus vite et surtout sans douleur.</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-03-26 18:30:00', '2026-03-26 20:30:00', 35, 0.00, 0.00, 'P', NULL, 31, NULL, '2026-03-22 14:38:22', NULL),
(3, 'T', 'Workshop #3 Mécanique', '<p>Parce qu’une sortie réussie ne devrait jamais s’arrêter au bord de la route, Fast and Female Geneva s’associe à nouveau avec Ciclissimo pour vous donner les clés de l’autonomie !</p><p>On pense souvent que la mécanique est une affaire de spécialistes ou de force brute. La réalité est plus simple : comprendre sa machine, c’est s’offrir la liberté d\'aller plus loin. Savoir régler un dérailleur qui saute ou réparer une crevaison en cinq minutes, ce n’est pas juste du dépannage, c’est de la confiance en soi pure.</p><p>Ce workshop est une session pratique pour passer de l\'autre côté du guidon. Nous allons décortiquer les composants essentiels de votre vélo pour que vous sachiez diagnostiquer, entretenir et réparer. L\'objectif ? Que la seule chose qui vous arrête durant vos sorties engagées soit la vue au sommet du col, et non un saut de chaîne.</p><p><strong>Ne subissez plus la technique, maîtrisez-la.</strong></p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-04-21 18:30:00', '2026-04-21 20:30:00', 40, 0.00, 0.00, 'N', NULL, 31, NULL, '2026-03-22 14:43:59', NULL),
(4, 'T', 'Workshop #4 Mécanique', '<p>Parce qu’une sortie réussie ne devrait jamais s’arrêter au bord de la route, Fast and Female Geneva s’associe à nouveau avec Ciclissimo pour vous donner les clés de l’autonomie !</p><p>On pense souvent que la mécanique est une affaire de spécialistes ou de force brute. La réalité est plus simple : comprendre sa machine, c’est s’offrir la liberté d\'aller plus loin. Savoir régler un dérailleur qui saute ou réparer une crevaison en cinq minutes, ce n’est pas juste du dépannage, c’est de la confiance en soi pure.</p><p>Ce workshop est une session pratique pour passer de l\'autre côté du guidon. Nous allons décortiquer les composants essentiels de votre vélo pour que vous sachiez diagnostiquer, entretenir et réparer. L\'objectif ? Que la seule chose qui vous arrête durant vos sorties engagées soit la vue au sommet du col, et non un saut de chaîne.</p><p>Ne subissez plus la technique, maîtrisez-la.</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-05-27 18:30:00', '2026-05-27 18:30:00', 40, 0.00, 0.00, 'N', NULL, 31, NULL, '2026-03-22 14:45:02', NULL),
(5, 'P', 'FFGVA X VILLE DE CAROUGE X CICLISSIMO', '<p>Le cyclisme féminin prend ses quartiers à Carouge pour une matinée d\'exception, entre partage, expertise et inspiration.</p><p>En collaboration avec la Ville de Carouge, Fast and Female Geneva vous invite à une immersion totale dans l\'univers du vélo. Que vous soyez débutante curieuse ou cycliste confirmée, cette matinée a été pensée pour célébrer la petite reine sous toutes ses coutures.</p><p>Au programme de votre matinée :</p><ul><li><strong>Ateliers &amp; Mise en jambe :</strong> On commence par de la pratique avec des ateliers dédiés et des sorties tranquilles (par groupes de niveaux) pour rouler ensemble, sans pression, juste pour le plaisir du peloton.</li><li><strong>Cycle de Conférences :</strong> On pose les vélos pour nourrir l\'esprit. Des experts partageront leurs conseils pour booster votre pratique.</li><li><strong>Rencontre avec une Icône :</strong> Nous avons l\'immense honneur d\'accueillir <strong>Elise Chabbey</strong> ! La championne genevoise viendra partager son parcours, son mental d\'acier et sa vision du cyclisme pro. Une occasion unique d\'échanger avec celle qui nous inspire à chaque coup de pédale.</li></ul><p><strong style=\"text-decoration: underline;\">Inscriptions obligatoires</strong> : Pour participer à cet événement gratuit mais exclusif, rendez-vous directement sur le site de la <strong>Ville de Carouge</strong>. Attention, les places s\'envolent vite !</p><blockquote><strong>[Lien vers le site de la Ville de Carouge]</strong></blockquote><p>Venez partager votre passion, poser vos questions et repartir avec une motivation au sommet !</p>', 'Café Félin, Rue des Horlogers 13, 1227 Carouge', '2026-06-13 09:00:00', '0206-06-13 18:00:00', 10, 0.00, 0.00, 'N', NULL, 34, NULL, '2026-03-22 20:10:41', NULL),
(6, 'C', 'La Reine - Gstaad', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à La Reine ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Gstaad', '2026-06-20 07:00:00', '2025-06-20 20:00:00', NULL, 0.00, 0.00, 'P', NULL, NULL, NULL, '2026-03-22 15:19:48', NULL),
(7, 'C', 'La Classique', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à La Classique ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Rampe de Choully 17, 1242 Satigny', '2026-06-21 07:15:00', '2025-06-21 13:00:00', NULL, 0.00, NULL, 'P', NULL, NULL, NULL, '2026-03-22 15:20:08', NULL),
(8, 'C', 'Etape Reine TDFF', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à L\'Etape ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Vaison-la-Romaine, 84110 France', '2026-08-06 07:00:00', '2026-08-06 18:00:00', 10, 0.00, NULL, 'P', NULL, NULL, NULL, '2026-03-22 18:49:58', NULL),
(9, 'C', 'Le Tour des Stations', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez au Tour des Stations ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul><p>Retraits des dossards : <strong>VERBIER │ Place de l’Ermitage</strong></p><ul><li>Vendredi 28 août 2026 10h00 – 20h00 Retrait des dossards &amp; Village exposants</li><li>Important : Pour les participants au départ de l’Ultrafondo avec le départ à 02h30, il est impératif de récupérer son dossard à Verbier à l’horaire indiqué ci-dessus</li></ul><p>Retraits des dossards : <strong>LE CHÂBLE │ Espace Saint-Marc&nbsp;</strong></p><ul><li>Samedi 29 août 2026 04h00 – 08h30 Retrait tardif des dossards</li></ul>', 'Verbier', '2026-08-29 08:00:00', '2026-08-29 17:30:00', NULL, 0.00, NULL, 'P', NULL, NULL, NULL, '2026-03-22 19:28:38', NULL),
(10, 'C', 'Cyclotour du Léman', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez au Cyclotour du Léman ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Lausanne', '2026-10-18 07:00:00', '2026-10-18 18:00:00', NULL, 0.00, 0.00, 'P', NULL, NULL, NULL, '2026-03-22 15:29:56', NULL),
(11, 'P', '1 AN FFGVA', '<p><strong>Un an de coups de pédales, de sommets conquis et de sororité... Ça se fête en mode exploration !</strong></p><p>Pour souffler notre première bougie, on oublie (un instant) les moyennes horaires et les segments Strava. On vous embarque dans une <strong>Course d\'Orientation spéciale 1 an</strong> !</p><p>Le concept ? Un mélange de fun, de stratégie et de découverte. Pas besoin d\'être une boussole humaine, il faudra juste avoir l\'œil, l\'esprit d\'équipe et l\'envie de célébrer cette année incroyable toutes ensemble.</p><h3>Ce qu\'on peut déjà vous dire :</h3><ul><li><strong>Le terrain de jeu :</strong> Genève et ses pépites cachées.</li><li><strong>Le format :</strong> En équipe (parce qu\'à plusieurs, on va toujours plus loin... et on rigole plus !).</li><li><strong>Le défi :</strong> Des balises à débusquer, des énigmes à résoudre et quelques surprises sur la route.</li></ul><p><strong>Restez connectées !</strong> Les détails croustillants, le lieu de rendez-vous et les modalités d\'inscription arrivent très vite. Préparez vos mollets et votre sens de l\'observation, cette sortie d\'anniversaire va rester dans les mémoires.</p><p><strong>Un an de FFGVA, et ce n\'est que le début de l\'aventure.</strong></p>', 'A définir', '2026-05-17 09:00:00', '2026-05-17 18:00:00', NULL, 1.00, 11.00, 'N', NULL, 29, NULL, '2026-03-22 16:31:40', NULL),
(12, 'W', 'Aurora Ride - Annecy', NULL, 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-01-03 11:00:00', '2026-01-03 17:00:00', 3, 0.00, 0.00, 'T', 'gpx/COURSE_425231111.gpx', NULL, NULL, '2026-03-22 21:02:28', NULL),
(13, 'E', 'Entrainement #2 Tour du Vuache', '<p>Sortie entraînement – Étape Swift Tour de France Femmes / Mont Ventoux<br><br>🗺️ Parcours :<br>Carouge → Chancy → Vuache&nbsp; → Marlioz → Soral → retour Carouge<br><br>📏 Distance : 87,7 km<br>⏱️ Temps estimé : ~4h30<br>⚡ Allure prévue : 20–25 km/h<br>(18,8 km/h théorique, mais on ira plus vite)<br><br>⛰️ D+ : 1’160 m<br><br>🧰 Matériel obligatoire :<br>	•	Vélo en parfait état (freins, transmission, pneus OK)<br>	•	Casque<br>	•	Kit de repérage de base (chambre à air, démonte-pneus, pompe/CO₂, multi-outils)<br>	•	Habits adaptés à la saison<br>→ gants<br>→ surchaussures<br><br>Sortie d’entraînement. Niveau et autonomie requis.</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-01 10:00:00', '2026-02-01 17:00:00', 5, 0.00, 0.00, 'T', 'gpx/#FFGva - Boucle Genève-Tour du Vuache-Genève - entrainement TDF.gpx', 31, NULL, '2026-03-22 20:46:47', NULL),
(14, 'E', 'Entrainement #3 Tour du Salève', '<p>L’association Fast and Female GVA commence gentiment la mise en œuvre son calendrier avec un événement ad hoc, Le Tour du Salève.<br><br>⚠️ le Tour ce n’est pas la montée du Salève! On garde cela pour quand la glace fondra sur la Croisette 😜<br><br>Le parcours est à la portée de toutes, selon l’allure qui vous plaît le plus. 🐢⚡️<br><br>Les règles sont toujours les mêmes: en montée chacune à son rhytme, et on s’attend en haut - idéalement dans un bar autour d’un café/cappuccino/barre/steak. Si vous souhaitez exploser sur un col, c’est le bon endroit. Si vous souhaitez la balade bla-bla du dimanche, c’est aussi le bon endroit. LET’S GO !!!</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-15 08:00:00', '2026-02-15 11:30:00', 10, 0.00, 0.00, 'T', 'gpx/COURSE_432261634.gpx', 32, NULL, '2026-03-22 21:01:54', NULL),
(15, 'T', 'Workshop #1 Coaching', '<p>Parce que rouler ensemble, c’est aussi s’entraider pour progresser ! 🤝<br><br>On vous donne rendez-vous pour un workshop spécial coaching en collaboration avec <a href=\"https://www.instagram.com/johannferre/\">@johannferre</a> et <a href=\"https://www.instagram.com/ymontagnon/\">@ymontagnon</a> . On se retrouve chez Ciclissimo Carouge pour un moment de partage, avec une option visio pour celles qui ne peuvent pas se déplacer. Que vous souhaitiez progresser techniquement ou optimiser votre préparation, cet atelier est fait pour vous.</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-26 18:30:00', '2026-02-26 20:30:00', 30, 0.00, 0.00, 'T', NULL, 35, NULL, '2026-03-22 21:09:31', NULL),
(16, 'A', 'After Work', '<p>Les After Work sont de retour !</p><p>Le principe ? Un chouette tour dans la campagne Genevoise, suivi toujours d\'un petit verre sur la terasse du cinéma Bio.</p><p>Alors, on se retrouve ?</p><p>Matériel obligatoire :</p><ul><li>Vélo en parfait état (freins, transmission, pneus OK)</li><li>Casque</li><li>Kit de repérage de base (chambre à air, démonte-pneus, pompe/CO₂, multi-outils)</li><li>Habits adaptés à la saison</li><li>Lumières avant &amp; arrières</li></ul><p>Plusieurs conseils pour préparer une sortie vélo sont sur la page On en parle - FAQ</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-04-23 18:30:00', '2026-04-23 20:00:00', 20, 0.00, 0.00, 'N', NULL, NULL, NULL, '2026-03-22 22:02:57', NULL);

--
-- Trigger `events`
--
DELIMITER $$
CREATE TRIGGER `events_before_delete` BEFORE DELETE ON `events` FOR EACH ROW BEGIN
      INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `event_type`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`,
  `price_non_member`, `statuscode`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
      VALUES ('D', @current_user_id, OLD.`id`, OLD.`event_type`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`,
  OLD.`price_non_member`, OLD.`statuscode`, OLD.`gpx_file`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
  END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `events_before_update` BEFORE UPDATE ON `events` FOR EACH ROW BEGIN
      INSERT INTO `events_audit` (`audit_action`, `audit_user_id`, `id`, `event_type`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`,
  `price_non_member`, `statuscode`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`)
      VALUES ('U', @current_user_id, OLD.`id`, OLD.`event_type`, OLD.`title`, OLD.`description`, OLD.`location`, OLD.`starts_at`, OLD.`ends_at`, OLD.`max_participants`, OLD.`price`,
  OLD.`price_non_member`, OLD.`statuscode`, OLD.`gpx_file`, OLD.`chef_peloton_id`, OLD.`modified_by_id`, OLD.`updated_at`, OLD.`deleted_at`);
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
  `event_type` char(1) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `starts_at` datetime NOT NULL,
  `ends_at` datetime DEFAULT NULL,
  `max_participants` int(10) UNSIGNED DEFAULT NULL,
  `price` decimal(8,2) DEFAULT 0.00,
  `price_non_member` decimal(8,2) DEFAULT NULL,
  `statuscode` char(1) DEFAULT 'N',
  `gpx_file` varchar(255) DEFAULT NULL,
  `chef_peloton_id` bigint(20) UNSIGNED DEFAULT NULL,
  `modified_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `events_audit`
--

INSERT INTO `events_audit` (`audit_id`, `audit_action`, `audit_user_id`, `audit_timestamp`, `id`, `event_type`, `title`, `description`, `location`, `starts_at`, `ends_at`, `max_participants`, `price`, `price_non_member`, `statuscode`, `gpx_file`, `chef_peloton_id`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 'U', NULL, '2026-03-22 15:38:22', 2, 'T', 'Workshop #2 Atelier Position', '<p>Ciclissimo nous reçoit pour préparer notre saison et atteindre nos défis sportifs!</p><p>L\'idée reçue qu\'il faut souffrir pour être performant est balayée. Le résumé tient en une phrase : Un cycliste bien posé est un cycliste efficace. Le \"fit\" n\'est pas qu\'une affaire de centimètre, mais de physiologie. Le vélo doit s\'adapter à l\'humain, et non l\'inverse.</p><p>Ce workshop est une session pour comprendre comment optimiser chaque point de contact entre nous et notre vélo afin de rouler plus longtemps, plus vite et surtout sans douleur.</p>', NULL, '2026-03-26 18:30:00', '2026-03-26 20:30:00', 35, 0.00, 0.00, 'P', NULL, 31, NULL, '2026-03-22 14:37:35', NULL),
(2, 'U', NULL, '2026-03-22 16:07:10', 6, 'C', 'La Reine - Gstaad', '<p>Gran Fondo · 140 km · 3’000 D+<br>Medio Fondo · 90 km · 1’800 D+</p><p>En disant que vous participez à cette course, il est possible ensuite de se coordonner pour faire les trajets ensemble.</p>', 'Gstaad', '2026-06-20 07:00:00', '2025-06-20 20:00:00', NULL, 0.00, 0.00, 'N', NULL, NULL, NULL, '2026-03-22 14:59:54', NULL),
(3, 'U', NULL, '2026-03-22 16:12:53', 8, 'C', 'Etape Reine TDFF', NULL, 'Vaison-la-Romaine, 84110 France', '2026-08-05 16:00:00', '2026-08-07 15:00:00', 10, 0.00, NULL, 'N', NULL, NULL, NULL, '2026-03-22 15:12:02', NULL),
(4, 'U', NULL, '2026-03-22 16:13:08', 7, 'C', 'La Classique', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à La Reine ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Rampe de Choully 17, 1242 Satigny', '2026-06-21 07:15:00', '2025-06-21 13:00:00', NULL, 0.00, NULL, 'N', NULL, NULL, NULL, '2026-03-22 15:08:46', NULL),
(5, 'U', NULL, '2026-03-22 16:19:48', 6, 'C', 'La Reine - Gstaad', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à La Reine ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Gstaad', '2026-06-20 07:00:00', '2025-06-20 20:00:00', NULL, 0.00, 0.00, 'N', NULL, NULL, NULL, '2026-03-22 15:07:10', NULL),
(6, 'U', NULL, '2026-03-22 16:20:08', 7, 'C', 'La Classique', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à La Classique ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Rampe de Choully 17, 1242 Satigny', '2026-06-21 07:15:00', '2025-06-21 13:00:00', NULL, 0.00, NULL, 'N', NULL, NULL, NULL, '2026-03-22 15:13:08', NULL),
(7, 'U', NULL, '2026-03-22 16:20:48', 8, 'C', 'Etape Reine TDFF', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à L\'Etape ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Vaison-la-Romaine, 84110 France', '2026-08-05 16:00:00', '2026-08-07 15:00:00', 10, 0.00, NULL, 'N', NULL, NULL, NULL, '2026-03-22 15:12:53', NULL),
(8, 'U', NULL, '2026-03-22 16:21:08', 9, 'C', 'Le Tour des Sations', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez au Tour des Stations ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul><p>Retraits des dossards : <strong>VERBIER │ Place de l’Ermitage</strong></p><ul><li>Vendredi 28 août 2026 10h00 – 20h00 Retrait des dossards &amp; Village exposants</li><li>Important : Pour les participants au départ de l’Ultrafondo avec le départ à 02h30, il est impératif de récupérer son dossard à Verbier à l’horaire indiqué ci-dessus</li></ul><p>Retraits des dossards : <strong>LE CHÂBLE │ Espace Saint-Marc&nbsp;</strong></p><ul><li>Samedi 29 août 2026 04h00 – 08h30 Retrait tardif des dossards</li></ul>', 'Verbier', '2026-08-29 08:00:00', '2026-08-29 17:30:00', NULL, 0.00, NULL, 'N', NULL, NULL, NULL, '2026-03-22 15:16:18', NULL),
(9, 'U', NULL, '2026-03-22 17:02:08', 1, 'E', 'Training #4 Jura', '<p>On commence gentiment à regarder les paysages depuis un peu plus en haut!<br>RDV devant le <strong>dépôt bagages à l’intérieur de la Gare Cornavin</strong>. Nous prendrons le train aux alentours de <strong>9h30 jusqu’à Nyon</strong> pour rejoindre la Vallée de Joux et redescendre via St. Cergues (ou Gex, selon les dispositions du groupe).<br><br>Points d’interêt:<br>⛰️ Col du Marchairuz<br>⛰️ Vallée de Joux<br>⛰️ Lac des Rousses<br>⛰️ Col de la Givrine<br><br>La sortie est un entraînement pour une course de groupe, mais elle reste <strong>ouverte à toutes</strong>. Le profil du parcours est une bonne occasion pour se mésurer aux &gt;1500D+ et prendre confiance à rouler en groupe.<br><strong>Pas d’allure moyenne prédéfinie</strong>. Ensemble sur le plat, chacune à son rythme en montée, café en haut (ou champagne) en attendant que tout le monde arrive.<br><br></p>', 'Gare Cornavin', '2026-03-27 09:30:00', '2026-03-27 14:30:00', 15, 0.00, 0.00, 'P', 'gpx/COURSE_440782888.gpx', 32, NULL, '2026-03-22 14:28:52', NULL),
(10, 'U', NULL, '2026-03-22 17:06:04', 1, 'E', 'Training #4 Jura', '<p>On commence gentiment à regarder les paysages depuis un peu plus en haut!<br>RDV devant le <strong>dépôt bagages à l’intérieur de la Gare Cornavin</strong>. Nous prendrons le train aux alentours de <strong>9h30 jusqu’à Nyon</strong> pour rejoindre la Vallée de Joux et redescendre via St. Cergues (ou Gex, selon les dispositions du groupe).<br><br>Points d’interêt:<br>⛰️ Col du Marchairuz<br>⛰️ Vallée de Joux<br>⛰️ Lac des Rousses<br>⛰️ Col de la Givrine<br><br>La sortie est un entraînement pour une course de groupe, mais elle reste <strong>ouverte à toutes</strong>. Le profil du parcours est une bonne occasion pour se mésurer aux &gt;1500D+ et prendre confiance à rouler en groupe.<br><strong>Pas d’allure moyenne prédéfinie</strong>. Ensemble sur le plat, chacune à son rythme en montée, café en haut (ou champagne) en attendant que tout le monde arrive.<br><br></p>', 'Gare Cornavin', '2026-03-27 09:30:00', '2026-03-27 14:30:00', 15, 0.00, 0.00, 'P', NULL, 29, NULL, '2026-03-22 16:02:08', NULL),
(11, 'U', NULL, '2026-03-22 19:48:41', 8, 'C', 'Etape Reine TDFF', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à L\'Etape ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Vaison-la-Romaine, 84110 France', '2026-08-05 16:00:00', '2026-08-07 15:00:00', 10, 0.00, NULL, 'P', NULL, NULL, NULL, '2026-03-22 15:20:48', NULL),
(12, 'U', NULL, '2026-03-22 19:49:58', 8, 'C', 'Etape Reine TDFF', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez à L\'Etape ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul>', 'Vaison-la-Romaine, 84110 France', '2026-05-06 07:00:00', '2026-08-06 18:00:00', 10, 0.00, NULL, 'P', NULL, NULL, NULL, '2026-03-22 18:48:41', NULL),
(13, 'U', NULL, '2026-03-22 20:28:38', 9, 'C', 'Le Tour des Sations', '<p><strong>On ne court jamais vraiment seule !</strong></p><p>Vous participez au Tour des Stations ? Ne restez pas isolées dans votre préparation. Cet événement \"Fast and Female\" n\'est pas une plateforme d\'inscription officielle, mais notre <strong>point de ralliement communautaire</strong>.</p><p>En indiquant votre participation ici, vous permettez à toutes de :</p><ul><li><strong>Se coordonner pour les trajets :</strong> Covoiturage, partage de frais et moins de stress pour se garer.</li><li><strong>Former des équipes :</strong> Pourquoi rouler seule quand on peut porter nos couleurs ensemble ?</li><li><strong>Partager la logistique :</strong> Conseils sur le parcours, reco de dernière minute et soutien mutuel.</li></ul><p>Retraits des dossards : <strong>VERBIER │ Place de l’Ermitage</strong></p><ul><li>Vendredi 28 août 2026 10h00 – 20h00 Retrait des dossards &amp; Village exposants</li><li>Important : Pour les participants au départ de l’Ultrafondo avec le départ à 02h30, il est impératif de récupérer son dossard à Verbier à l’horaire indiqué ci-dessus</li></ul><p>Retraits des dossards : <strong>LE CHÂBLE │ Espace Saint-Marc&nbsp;</strong></p><ul><li>Samedi 29 août 2026 04h00 – 08h30 Retrait tardif des dossards</li></ul>', 'Verbier', '2026-08-29 08:00:00', '2026-08-29 17:30:00', NULL, 0.00, NULL, 'P', NULL, NULL, NULL, '2026-03-22 15:21:08', NULL),
(14, 'U', NULL, '2026-03-22 20:31:21', 1, 'E', 'Training #4 Jura', '<p>On commence gentiment à regarder les paysages depuis un peu plus en haut!<br>RDV devant le <strong>dépôt bagages à l’intérieur de la Gare Cornavin</strong>. Nous prendrons le train aux alentours de <strong>9h30 jusqu’à Nyon</strong> pour rejoindre la Vallée de Joux et redescendre via St. Cergues (ou Gex, selon les dispositions du groupe).<br><br>Points d’interêt:<br>⛰️ Col du Marchairuz<br>⛰️ Vallée de Joux<br>⛰️ Lac des Rousses<br>⛰️ Col de la Givrine<br><br>La sortie est un entraînement pour une course de groupe, mais elle reste <strong>ouverte à toutes</strong>. Le profil du parcours est une bonne occasion pour se mésurer aux &gt;1500D+ et prendre confiance à rouler en groupe.<br><strong>Pas d’allure moyenne prédéfinie</strong>. Ensemble sur le plat, chacune à son rythme en montée, café en haut (ou champagne) en attendant que tout le monde arrive.<br><br></p>', 'Gare Cornavin', '2026-03-27 09:30:00', '2026-03-27 14:30:00', 15, 0.00, 0.00, 'P', NULL, 32, NULL, '2026-03-22 16:06:04', NULL),
(15, 'U', NULL, '2026-03-22 21:04:51', 12, 'W', 'Aurora Ride - Annecy', NULL, NULL, '2026-01-03 11:00:00', '2026-01-03 17:00:00', NULL, 0.00, 0.00, 'N', 'gpx/COURSE_425231111.gpx', NULL, NULL, '2026-03-22 20:04:28', NULL),
(16, 'U', NULL, '2026-03-22 21:06:00', 12, 'W', 'Aurora Ride - Annecy', NULL, NULL, '2026-01-03 11:00:00', '2026-01-03 17:00:00', 3, 0.00, 0.00, 'P', 'gpx/COURSE_425231111.gpx', NULL, NULL, '2026-03-22 20:04:51', NULL),
(17, 'U', NULL, '2026-03-22 21:10:41', 5, 'P', 'FFGVA X VILLE DE CAROUGE X CICLISSIMO', '<p>Le cyclisme féminin prend ses quartiers à Carouge pour une matinée d\'exception, entre partage, expertise et inspiration.</p><p>En collaboration avec la Ville de Carouge, Fast and Female Geneva vous invite à une immersion totale dans l\'univers du vélo. Que vous soyez débutante curieuse ou cycliste confirmée, cette matinée a été pensée pour célébrer la petite reine sous toutes ses coutures.</p><p>Au programme de votre matinée :</p><ul><li><strong>Ateliers &amp; Mise en jambe :</strong> On commence par de la pratique avec des ateliers dédiés et des sorties tranquilles (par groupes de niveaux) pour rouler ensemble, sans pression, juste pour le plaisir du peloton.</li><li><strong>Cycle de Conférences :</strong> On pose les vélos pour nourrir l\'esprit. Des experts partageront leurs conseils pour booster votre pratique.</li><li><strong>Rencontre avec une Icône :</strong> Nous avons l\'immense honneur d\'accueillir <strong>Elise Chabbey</strong> ! La championne genevoise viendra partager son parcours, son mental d\'acier et sa vision du cyclisme pro. Une occasion unique d\'échanger avec celle qui nous inspire à chaque coup de pédale.</li></ul><p>Inscriptions obligatoires : Pour participer à cet événement gratuit mais exclusif, rendez-vous directement sur le site de la <strong>Ville de Carouge</strong>. Attention, les places s\'envolent vite !</p><blockquote><strong>[Lien vers le site de la Ville de Carouge]</strong></blockquote><p>Venez partager votre passion, poser vos questions et repartir avec une motivation au sommet !</p>', 'Café Félin, Rue des Horlogers 13, 1227 CArouge', '2026-06-13 09:00:00', '0206-06-13 18:00:00', 10, 0.00, 0.00, 'N', NULL, 34, NULL, '2026-03-22 14:53:59', NULL),
(18, 'U', NULL, '2026-03-22 21:44:57', 13, 'W', 'Entrainement #2', NULL, 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-01 10:00:00', '2026-02-01 17:00:00', 5, 0.00, 0.00, 'P', NULL, NULL, NULL, '2026-03-22 20:41:15', NULL),
(19, 'U', NULL, '2026-03-22 21:45:12', 1, 'E', 'Training #4 Jura', '<p>On commence gentiment à regarder les paysages depuis un peu plus haut !<br>RDV devant le <strong>dépôt bagages à l’intérieur de la Gare Cornavin</strong>. Nous prendrons le train aux alentours de <strong>9h30 jusqu’à Nyon</strong> pour rejoindre la Vallée de Joux et redescendre via St. Cergues (ou Gex, selon les dispositions du groupe).<br><br>Points d’interêt:<br>⛰️ Col du Marchairuz<br>⛰️ Vallée de Joux<br>⛰️ Lac des Rousses<br>⛰️ Col de la Givrine<br><br>La sortie est un entraînement pour une course de groupe, mais elle reste <strong>ouverte à toutes</strong>. Le profil du parcours est une bonne occasion pour se mesurer aux &gt;1300D+ et prendre confiance à rouler en groupe.<br><strong>Pas d’allure moyenne prédéfinie</strong>. Ensemble sur le plat, chacune à son rythme en montée, café en haut (ou champagne) en attendant que tout le monde arrive.<br><br></p>', 'Gare Cornavin', '2026-03-27 09:30:00', '2026-03-27 14:30:00', 15, 0.00, 0.00, 'P', NULL, 32, NULL, '2026-03-22 19:31:21', NULL),
(20, 'U', NULL, '2026-03-22 21:45:38', 13, 'W', 'Entrainement #2 Tour du Vuache', '<p>Sortie entraînement – Étape Swift Tour de France Femmes / Mont Ventoux<br><br>🗺️ Parcours :<br>Carouge → Chancy → Vuache&nbsp; → Marlioz → Soral → retour Carouge<br><br>📏 Distance : 87,7 km<br>⏱️ Temps estimé : ~4h30<br>⚡ Allure prévue : 20–25 km/h<br>(18,8 km/h théorique, mais on ira plus vite)<br><br>⛰️ D+ : 1’160 m<br><br>🧰 Matériel obligatoire :<br>	•	Vélo en parfait état (freins, transmission, pneus OK)<br>	•	Casque<br>	•	Kit de repérage de base (chambre à air, démonte-pneus, pompe/CO₂, multi-outils)<br>	•	Habits adaptés à la saison<br>→ gants<br>→ surchaussures<br><br>Sortie d’entraînement. Niveau et autonomie requis.</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-01 10:00:00', '2026-02-01 17:00:00', 5, 0.00, 0.00, 'P', NULL, 31, NULL, '2026-03-22 20:44:57', NULL),
(21, 'U', NULL, '2026-03-22 21:46:47', 13, 'W', 'Entrainement #2 Tour du Vuache', '<p>Sortie entraînement – Étape Swift Tour de France Femmes / Mont Ventoux<br><br>🗺️ Parcours :<br>Carouge → Chancy → Vuache&nbsp; → Marlioz → Soral → retour Carouge<br><br>📏 Distance : 87,7 km<br>⏱️ Temps estimé : ~4h30<br>⚡ Allure prévue : 20–25 km/h<br>(18,8 km/h théorique, mais on ira plus vite)<br><br>⛰️ D+ : 1’160 m<br><br>🧰 Matériel obligatoire :<br>	•	Vélo en parfait état (freins, transmission, pneus OK)<br>	•	Casque<br>	•	Kit de repérage de base (chambre à air, démonte-pneus, pompe/CO₂, multi-outils)<br>	•	Habits adaptés à la saison<br>→ gants<br>→ surchaussures<br><br>Sortie d’entraînement. Niveau et autonomie requis.</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-01 10:00:00', '2026-02-01 17:00:00', 5, 0.00, 0.00, 'T', 'gpx/#FFGva - Boucle Genève-Tour du Vuache-Genève - entrainement TDF.gpx', 31, NULL, '2026-03-22 20:45:38', NULL),
(22, 'U', NULL, '2026-03-22 22:01:54', 14, 'E', 'Entrainement #3 Tour du Salève', '<p>L’association Fast and Female GVA commence gentiment la mise en œuvre son calendrier avec un événement ad hoc, Le Tour du Salève.<br><br>⚠️ le Tour ce n’est pas la montée du Salève! On garde cela pour quand la glace fondra sur la Croisette 😜<br><br>Le parcours est à la portée de toutes, selon l’allure qui vous plaît le plus. 🐢⚡️<br><br>Les règles sont toujours les mêmes: en montée chacune à son rhytme, et on s’attend en haut - idéalement dans un bar autour d’un café/cappuccino/barre/steak. Si vous souhaitez exploser sur un col, c’est le bon endroit. Si vous souhaitez la balade bla-bla du dimanche, c’est aussi le bon endroit. LET’S GO !!!</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-15 08:00:00', '2026-02-15 11:30:00', 10, 0.00, 0.00, 'P', 'gpx/COURSE_432261634.gpx', 32, NULL, '2026-03-22 20:58:02', NULL),
(23, 'U', NULL, '2026-03-22 22:02:28', 12, 'W', 'Aurora Ride - Annecy', NULL, NULL, '2026-01-03 11:00:00', '2026-01-03 17:00:00', 3, 0.00, 0.00, 'T', 'gpx/COURSE_425231111.gpx', NULL, NULL, '2026-03-22 20:06:00', NULL),
(24, 'U', NULL, '2026-03-22 22:09:31', 15, 'T', 'Workshop #1 Coaching', '<p>Parce que rouler ensemble, c’est aussi s’entraider pour progresser ! 🤝<br><br>On vous donne rendez-vous pour un workshop spécial coaching en collaboration avec <a href=\"https://www.instagram.com/johannferre/\">@johannferre</a> et <a href=\"https://www.instagram.com/ymontagnon/\">@ymontagnon</a> . On se retrouve chez Ciclissimo Carouge pour un moment de partage, avec une option visio pour celles qui ne peuvent pas se déplacer. Que vous souhaitiez progresser techniquement ou optimiser votre préparation, cet atelier est fait pour vous.</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-02-26 18:30:00', '2026-02-26 20:30:00', 30, 0.00, 0.00, 'P', NULL, 35, NULL, '2026-03-22 21:05:20', NULL),
(25, 'U', NULL, '2026-03-22 23:02:57', 16, 'A', 'After Work', '<p>Les After Work sont de retour !</p><p>Le principe ? Un chouette tour dans la campagne Genevoise, suivi toujours d\'un petit verre sur la terasse du cinéma Bio.</p><p>Alors, on se retrouve ?</p><p>🧰 Matériel obligatoire :</p><ul><li>Vélo en parfait état (freins, transmission, pneus OK)</li><li>Casque</li><li>Kit de repérage de base (chambre à air, démonte-pneus, pompe/CO₂, multi-outils)</li><li>Habits adaptés à la saison</li><li>Lumières avant &amp; arrières</li></ul><p>Plusieurs conseils pour préparer une sortie vélo sont sur la page On en parle - FAQ</p>', 'Ciclissimo Carouge, place du Marché 10, 1227 Carouge', '2026-04-23 18:30:00', '2026-04-23 20:00:00', 20, 0.00, 0.00, 'N', NULL, NULL, NULL, '2026-03-22 22:02:35', NULL);

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
-- Daten für Tabelle `event_member`
--

INSERT INTO `event_member` (`id`, `event_id`, `member_id`, `status`, `present`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 1, 35, 'C', NULL, NULL, '2026-03-22 16:05:19', NULL),
(2, 2, 35, 'C', NULL, NULL, '2026-03-22 15:00:46', NULL),
(3, 2, 29, 'C', NULL, NULL, '2026-03-22 15:58:19', NULL),
(4, 1, 29, 'C', NULL, NULL, '2026-03-22 16:05:15', NULL),
(5, 8, 29, 'C', NULL, NULL, '2026-03-22 15:58:46', NULL),
(6, 8, 35, 'C', NULL, NULL, '2026-03-22 16:57:03', NULL),
(7, 7, 29, 'C', NULL, NULL, '2026-03-22 15:59:10', NULL),
(8, 1, 31, 'C', NULL, NULL, '2026-03-22 16:05:14', NULL),
(10, 1, 32, 'C', NULL, NULL, '2026-03-22 19:04:37', NULL),
(11, 9, 32, 'C', NULL, NULL, '2026-03-22 19:05:49', NULL),
(12, 8, 28, 'C', NULL, NULL, '2026-03-22 19:10:05', NULL),
(13, 10, 28, 'C', NULL, NULL, '2026-03-22 19:10:14', NULL),
(14, 7, 28, 'C', NULL, NULL, '2026-03-22 19:10:20', NULL),
(15, 1, 28, 'C', NULL, NULL, '2026-03-22 19:10:28', NULL),
(16, 2, 28, 'C', NULL, NULL, '2026-03-22 19:10:34', NULL),
(17, 12, 31, 'C', 1, NULL, '2026-03-22 20:05:22', NULL),
(18, 12, 29, 'C', 1, NULL, '2026-03-22 20:05:34', NULL),
(19, 12, 35, 'C', 1, NULL, '2026-03-22 20:05:48', NULL),
(20, 8, 21, 'C', NULL, NULL, '2026-03-22 20:27:49', NULL),
(21, 8, 30, 'C', NULL, NULL, '2026-03-22 20:28:05', NULL),
(22, 8, 34, 'N', NULL, NULL, '2026-03-22 20:28:21', NULL),
(23, 8, 33, 'C', NULL, NULL, '2026-03-22 20:28:40', NULL),
(24, 8, 31, 'C', NULL, NULL, '2026-03-22 20:29:01', NULL),
(25, 8, 12, 'C', NULL, NULL, '2026-03-22 20:29:15', NULL),
(26, 13, 12, 'C', 1, NULL, '2026-03-22 20:44:07', NULL),
(27, 13, 31, 'C', 1, NULL, '2026-03-22 20:44:15', NULL),
(28, 13, 22, 'C', 1, NULL, '2026-03-22 20:44:39', NULL),
(29, 14, 32, 'C', 1, NULL, '2026-03-22 21:00:24', NULL),
(30, 14, 13, 'C', 1, NULL, '2026-03-22 21:00:42', NULL),
(31, 14, 15, 'C', 1, NULL, '2026-03-22 21:00:49', NULL),
(32, 14, 22, 'C', 1, NULL, '2026-03-22 21:01:04', NULL),
(33, 14, 34, 'C', 1, NULL, '2026-03-22 21:01:24', NULL),
(34, 15, 35, 'C', 1, NULL, '2026-03-22 21:05:41', NULL),
(35, 15, 11, 'C', 1, NULL, '2026-03-22 21:06:05', NULL),
(36, 15, 12, 'C', 1, NULL, '2026-03-22 21:06:12', NULL),
(37, 15, 14, 'C', 1, NULL, '2026-03-22 21:06:29', NULL),
(38, 15, 15, 'C', 1, NULL, '2026-03-22 21:06:36', NULL),
(39, 15, 21, 'C', 1, NULL, '2026-03-22 21:06:54', NULL),
(40, 15, 22, 'C', 1, NULL, '2026-03-22 21:07:03', NULL),
(41, 15, 23, 'C', 1, NULL, '2026-03-22 21:07:10', NULL),
(42, 15, 28, 'C', 1, NULL, '2026-03-22 21:07:23', NULL),
(43, 15, 29, 'C', 1, NULL, '2026-03-22 21:07:32', NULL),
(44, 15, 32, 'C', 1, NULL, '2026-03-22 21:07:40', NULL),
(45, 15, 31, 'C', 1, NULL, '2026-03-22 21:07:56', NULL),
(46, 15, 38, 'C', 1, NULL, '2026-03-22 21:08:02', NULL),
(47, 15, 53, 'C', 1, NULL, '2026-03-22 21:08:15', NULL),
(48, 10, 31, 'C', NULL, NULL, '2026-03-23 04:47:37', NULL),
(49, 2, 60, 'X', NULL, NULL, '2026-03-23 07:30:17', NULL);

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

--
-- Daten für Tabelle `event_member_audit`
--

INSERT INTO `event_member_audit` (`audit_id`, `audit_action`, `audit_user_id`, `audit_timestamp`, `id`, `event_id`, `member_id`, `status`, `present`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 'U', NULL, '2026-03-22 17:01:29', 1, 1, 35, 'N', NULL, NULL, '2026-03-22 14:31:09', NULL),
(2, 'U', NULL, '2026-03-22 17:05:12', 4, 1, 29, 'C', NULL, NULL, '2026-03-22 15:58:30', NULL),
(3, 'U', NULL, '2026-03-22 17:05:14', 8, 1, 31, 'C', 1, NULL, '2026-03-22 16:04:53', NULL),
(4, 'U', NULL, '2026-03-22 17:05:15', 4, 1, 29, 'C', 1, NULL, '2026-03-22 16:05:12', NULL),
(5, 'U', NULL, '2026-03-22 17:05:16', 1, 1, 35, 'C', NULL, NULL, '2026-03-22 14:31:09', NULL),
(6, 'U', NULL, '2026-03-22 17:05:19', 1, 1, 35, 'C', 1, NULL, '2026-03-22 16:05:16', NULL),
(7, 'U', NULL, '2026-03-22 17:56:53', 6, 8, 35, 'C', NULL, NULL, '2026-03-22 15:58:54', NULL),
(8, 'U', NULL, '2026-03-22 17:57:03', 6, 8, 35, 'X', NULL, NULL, '2026-03-22 16:56:53', NULL),
(9, 'U', NULL, '2026-03-22 19:32:14', 9, 2, 78, 'C', NULL, NULL, '2026-03-22 18:14:29', NULL),
(10, 'U', NULL, '2026-03-22 19:32:15', 9, 2, 78, 'C', 1, NULL, '2026-03-22 18:14:29', NULL),
(11, 'U', NULL, '2026-03-22 19:32:27', 9, 2, 78, 'C', 0, NULL, '2026-03-22 18:14:29', NULL),
(12, 'D', NULL, '2026-03-22 19:34:24', 9, 2, 78, 'X', 0, NULL, '2026-03-22 18:14:29', NULL),
(13, 'U', NULL, '2026-03-22 20:28:55', 4, 1, 29, 'C', 0, NULL, '2026-03-22 16:05:15', NULL),
(14, 'U', NULL, '2026-03-22 20:28:56', 8, 1, 31, 'C', 0, NULL, '2026-03-22 16:05:14', NULL),
(15, 'U', NULL, '2026-03-22 20:28:58', 1, 1, 35, 'C', 0, NULL, '2026-03-22 16:05:19', NULL),
(16, 'U', NULL, '2026-03-22 21:05:50', 18, 12, 29, 'C', NULL, NULL, '2026-03-22 20:05:34', NULL),
(17, 'U', NULL, '2026-03-22 21:05:52', 17, 12, 31, 'C', NULL, NULL, '2026-03-22 20:05:22', NULL),
(18, 'U', NULL, '2026-03-22 21:05:53', 19, 12, 35, 'C', NULL, NULL, '2026-03-22 20:05:48', NULL),
(19, 'U', NULL, '2026-03-22 21:44:40', 26, 13, 12, 'C', NULL, NULL, '2026-03-22 20:44:07', NULL),
(20, 'U', NULL, '2026-03-22 21:44:41', 28, 13, 22, 'C', NULL, NULL, '2026-03-22 20:44:39', NULL),
(21, 'U', NULL, '2026-03-22 21:44:42', 27, 13, 31, 'C', NULL, NULL, '2026-03-22 20:44:15', NULL),
(22, 'U', NULL, '2026-03-22 22:01:26', 30, 14, 13, 'C', NULL, NULL, '2026-03-22 21:00:42', NULL),
(23, 'U', NULL, '2026-03-22 22:01:27', 31, 14, 15, 'C', NULL, NULL, '2026-03-22 21:00:49', NULL),
(24, 'U', NULL, '2026-03-22 22:01:28', 32, 14, 22, 'C', NULL, NULL, '2026-03-22 21:01:04', NULL),
(25, 'U', NULL, '2026-03-22 22:01:29', 32, 14, 22, 'C', 1, NULL, '2026-03-22 21:01:04', NULL),
(26, 'U', NULL, '2026-03-22 22:01:30', 32, 14, 22, 'C', 0, NULL, '2026-03-22 21:01:04', NULL),
(27, 'U', NULL, '2026-03-22 22:01:31', 32, 14, 22, 'C', NULL, NULL, '2026-03-22 21:01:04', NULL),
(28, 'U', NULL, '2026-03-22 22:01:32', 29, 14, 32, 'C', NULL, NULL, '2026-03-22 21:00:24', NULL),
(29, 'U', NULL, '2026-03-22 22:01:33', 33, 14, 34, 'C', NULL, NULL, '2026-03-22 21:01:24', NULL),
(30, 'U', NULL, '2026-03-22 22:09:07', 35, 15, 11, 'C', NULL, NULL, '2026-03-22 21:06:05', NULL),
(31, 'U', NULL, '2026-03-22 22:09:08', 36, 15, 12, 'C', NULL, NULL, '2026-03-22 21:06:12', NULL),
(32, 'U', NULL, '2026-03-22 22:09:09', 37, 15, 14, 'C', NULL, NULL, '2026-03-22 21:06:29', NULL),
(33, 'U', NULL, '2026-03-22 22:09:10', 38, 15, 15, 'C', NULL, NULL, '2026-03-22 21:06:36', NULL),
(34, 'U', NULL, '2026-03-22 22:09:11', 39, 15, 21, 'C', NULL, NULL, '2026-03-22 21:06:54', NULL),
(35, 'U', NULL, '2026-03-22 22:09:12', 40, 15, 22, 'C', NULL, NULL, '2026-03-22 21:07:03', NULL),
(36, 'U', NULL, '2026-03-22 22:09:13', 41, 15, 23, 'C', NULL, NULL, '2026-03-22 21:07:10', NULL),
(37, 'U', NULL, '2026-03-22 22:09:14', 42, 15, 28, 'C', NULL, NULL, '2026-03-22 21:07:23', NULL),
(38, 'U', NULL, '2026-03-22 22:09:15', 43, 15, 29, 'C', NULL, NULL, '2026-03-22 21:07:32', NULL),
(39, 'U', NULL, '2026-03-22 22:09:18', 45, 15, 31, 'C', NULL, NULL, '2026-03-22 21:07:56', NULL),
(40, 'U', NULL, '2026-03-22 22:09:19', 44, 15, 32, 'C', NULL, NULL, '2026-03-22 21:07:40', NULL),
(41, 'U', NULL, '2026-03-22 22:09:20', 34, 15, 35, 'C', NULL, NULL, '2026-03-22 21:05:41', NULL),
(42, 'U', NULL, '2026-03-22 22:09:21', 46, 15, 38, 'C', NULL, NULL, '2026-03-22 21:08:02', NULL),
(43, 'U', NULL, '2026-03-22 22:09:22', 47, 15, 53, 'C', NULL, NULL, '2026-03-22 21:08:15', NULL),
(44, 'U', NULL, '2026-03-23 08:30:17', 49, 2, 60, 'C', NULL, NULL, '2026-03-23 07:30:12', NULL);

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
-- Daten für Tabelle `invoices`
--

INSERT INTO `invoices` (`id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'C', 2026, '2026-001-001', 50.00, 'N', NULL, NULL, NULL, NULL, '2026-03-22 18:38:06', NULL),
(2, 2, 'C', 2026, '2026-002-001', 50.00, 'N', NULL, NULL, NULL, NULL, '2026-03-22 18:38:06', NULL),
(3, 3, 'C', 2026, '2026-003-001', 50.00, 'N', NULL, NULL, NULL, NULL, '2026-03-22 18:38:06', NULL),
(4, 4, 'C', 2026, '2026-004-001', 50.00, 'N', NULL, NULL, NULL, NULL, '2026-03-22 18:38:06', NULL),
(5, 5, 'C', 2026, '2026-005-001', 50.00, 'N', NULL, NULL, NULL, NULL, '2026-03-22 18:38:06', NULL),
(6, 78, 'C', 2026, '2026-078-001', 50.00, 'E', NULL, 'ffgva_TESTTEST_Stéfane-facture-2026-078-001.pdf', NULL, NULL, '2026-03-22 18:07:01', '2026-03-22 18:07:01');

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

--
-- Daten für Tabelle `invoices_audit`
--

INSERT INTO `invoices_audit` (`audit_id`, `audit_action`, `audit_user_id`, `audit_timestamp`, `id`, `member_id`, `type`, `cotisation_year`, `invoice_number`, `amount`, `statuscode`, `payment_date`, `pdf_filename`, `notes`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 'U', NULL, '2026-03-22 19:03:42', 6, 78, 'C', 2026, '2026-078-001', 50.00, 'N', NULL, NULL, NULL, NULL, '2026-03-22 18:03:41', NULL),
(2, 'U', NULL, '2026-03-22 19:03:42', 6, 78, 'C', 2026, '2026-078-001', 50.00, 'N', NULL, 'ffgva_TESTTEST_Stéfane-facture-2026-078-001.pdf', NULL, NULL, '2026-03-22 18:03:42', NULL),
(3, 'U', NULL, '2026-03-22 19:07:01', 6, 78, 'C', 2026, '2026-078-001', 50.00, 'E', NULL, 'ffgva_TESTTEST_Stéfane-facture-2026-078-001.pdf', NULL, NULL, '2026-03-22 18:03:42', NULL);

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

--
-- Daten für Tabelle `invoice_lines`
--

INSERT INTO `invoice_lines` (`id`, `invoice_id`, `description`, `amount`, `sort_order`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Cotisation annuelle 2026', 50.00, 0, '2026-03-22 18:38:06', NULL),
(2, 2, 'Cotisation annuelle 2026', 50.00, 0, '2026-03-22 18:38:06', NULL),
(3, 3, 'Cotisation annuelle 2026', 50.00, 0, '2026-03-22 18:38:06', NULL),
(4, 4, 'Cotisation annuelle 2026', 50.00, 0, '2026-03-22 18:38:06', NULL),
(5, 5, 'Cotisation annuelle 2026', 50.00, 0, '2026-03-22 18:38:06', NULL),
(6, 6, 'Cotisation annuelle 2026 — période du 22.03.2026 au 31.12.2026', 50.00, 0, '2026-03-22 18:03:41', NULL);

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
  `email` varchar(255) DEFAULT NULL,
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
-- Daten für Tabelle `members`
--

INSERT INTO `members` (`id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Kayci', 'Browne', 'kaycibrowne@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '[]', NULL, NULL, NULL, NULL, '2026-03-22 18:11:54', NULL),
(2, NULL, 'Sirella', 'Férédie', 'sirella.feredie@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Sirella_frd\"}', NULL, NULL, NULL, NULL, '2026-03-22 18:12:23', NULL),
(3, NULL, 'Justine', 'Francheteau', 'justine.francheteau@yahoo.fr', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Justine.frcht\"}', NULL, NULL, NULL, NULL, '2026-03-22 18:12:55', NULL),
(4, NULL, 'Anna', 'Rizzi', 'anna.rizzi.2009@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"annaa_rizzi_\"}', NULL, NULL, NULL, NULL, '2026-03-22 18:13:23', NULL),
(5, NULL, 'Louise', 'Rossiter-Levrard', 'lrossiterlevrard@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '[]', NULL, NULL, NULL, NULL, '2026-03-22 18:13:55', NULL),
(6, NULL, 'Gaia', 'BRUGNOLI', NULL, NULL, NULL, NULL, NULL, 'CH', 'E', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Membre Graine\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(7, NULL, 'Laetitia', 'CIMINO', NULL, NULL, NULL, NULL, NULL, 'CH', 'E', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"S\", \"groupe\": \"Membre Graine\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(8, NULL, 'Victoria', 'CIMINO', NULL, NULL, NULL, NULL, NULL, 'CH', 'E', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Membre Graine\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(9, '0010', 'Maité', 'Barroso', 'maite.barroso@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"maitechub\", \"taille_maillot\": \"S\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:40:47', NULL),
(10, '0011', 'Elise', 'Dupuis', 'elise.lozeron@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\":\"elise.lozeron\",\"taille_maillot\":\"M\",\"aec\":\"1\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:41:07', NULL),
(11, '0012', 'Sonia', 'Glowacki', 'sonia.glowacki@gmail.com', NULL, 'Chemin de la Tuiliere 690', '74140', 'Veigy-Foncenex', 'FR', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"soniaglw\", \"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:41:32', NULL),
(12, '0013', 'Julie', 'Hug', 'blackburn.hug@gmail.com', '1996-03-04', 'Avenue du Bouchet 5', '1209', 'Genève', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Julielahug\", \"taille_maillot\": \"M\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:41:59', NULL),
(13, '0014', 'Brigitte', 'Lüth', 'b.lueth@afia.at', NULL, 'Chemin de Dessous-Saint-Loup 4c', '1290', 'Versoix', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"lueth\", \"taille_maillot\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:42:22', NULL),
(14, '0015', 'Monica', 'Marinucci', 'monicamarinucci@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\":\"S\",\"aec\":\"1\",\"bib\":\"M\",\"gilet\":\"M\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:42:47', NULL),
(15, '0016', 'Sophie', 'Massobre', 'sophiemassobre@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\":\"mg.sophie\",\"taille_maillot\":\"M\",\"aec\":\"1\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:43:14', NULL),
(16, '0017', 'Ana', 'Menchero Gonzalez', 'amenchero@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\":\"XL\",\"aec\":\"1\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:43:43', NULL),
(17, '0018', 'Eloise', 'Miceli', 'eloise.miceli@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"M\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:44:03', NULL),
(18, '0019', 'Christina', 'Nassivera', 'chrisnassivera@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\":\"XL\",\"aec\":\"1\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:44:28', NULL),
(19, '0020', 'Allison', 'Neapole', 'aneapole@bluewin.ch', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"aneapole\", \"taille_maillot\": \"2XL\", \"bib\": \"2XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:44:48', NULL),
(20, '0021', 'Alice', 'Noel', 'nalice@hotmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"nalicegeneva81\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:45:03', NULL),
(21, '0022', 'Emma', 'O\'Leary', 'emmamaryoleary@gmail.com', NULL, 'Rue des Peupliers 18', '1205', 'Geneve', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"emmamaryoh\", \"taille_maillot\": \"XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:45:23', NULL),
(22, '0023', 'Monica', 'Pereira Lima', 'monipere@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\":\"moni.mo_ni\",\"taille_maillot\":\"M\",\"aec\":\"1\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:45:44', NULL),
(23, '0024', 'Raquel', 'Soles', 'raquelsoles72@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\":\"solesraquel\",\"taille_maillot\":\"M\",\"aec\":\"1\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:45:56', NULL),
(24, '0025', 'Alina', 'Stanculescu', 'stanculescu.alina.elena@gmail.com', NULL, 'Chemin de Saule 74', '1233', 'Bernex', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"S\", \"bib\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:46:30', NULL),
(25, '0026', 'Géraldine', 'Tornare', 'tornare.geraldine@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\":\"grldneanorrt\",\"taille_maillot\":\"M\",\"aec\":\"1\",\"bib\":\"L\",\"groupe\":\"Membre Sal\\u00e8ve\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:46:51', NULL),
(26, '0027', 'Olwen', 'Wilson', 'olwenwilson95@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"olwenwilson\", \"taille_maillot\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:47:09', NULL),
(27, '0028', 'Mahesha', 'Yapa', 'mahesha.yapa@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:47:24', NULL),
(28, '0003', 'Giulia', 'Antonioli', 'giulia.antonioli@gmail.com', NULL, 'Route de Malagnou 172', '1224', 'Chêne-Bougeries', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\":\"the.giuliagram\",\"taille_maillot\":\"M\",\"aec\":\"1\",\"bib\":\"M\",\"gilet\":\"M\",\"fonction\":\"Tr\\u00e9sori\\u00e8re\",\"groupe\":\"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:33:35', NULL),
(29, '0005', 'Dune', 'Bourquin', 'dune.bourquin@gmail.com', '1994-12-18', 'Chemin du Champ-Baron 14', '1209', 'Genève', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Dune_3\", \"taille_maillot\": \"S\", \"bib\": \"S\", \"gilet\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:35:43', NULL),
(30, '0008', 'Olivia', 'Chassot', 'olivia.a.chassot@gmail.com', NULL, 'Rue de Genève 24', '01160', 'Ferney-Voltaire', 'FR', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"S\", \"bib\": \"S\", \"gilet\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:36:34', NULL),
(31, '0001', 'Caroline', 'Gaillard', 'carolinegaillard@gmail.com', '1982-05-28', 'Chemin de Pinchat 42C', '1234', 'Vessy', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"carolineglrd\", \"taille_maillot\": \"M\", \"bib\": \"L\", \"gilet\": \"M\", \"fonction\": \"Présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:32:56', NULL),
(32, '0002', 'Sofia', 'Passaretti', 'sofiapassaretti@gmail.com', NULL, 'Chemin des Palettes 15', '1212', 'Grand-Lancy', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\":\"sofiapssrettti\",\"taille_maillot\":\"L\",\"aec\":\"1\",\"fonction\":\"Vice-pr\\u00e9sidente\",\"groupe\":\"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:35:04', NULL),
(33, '0009', 'Marta', 'Rodriguez', 'marta.rodriguez29@hotmail.co.uk', NULL, 'Chemin de la Seymaz 24C', '1253', 'Vandoeuvres', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Nevertoolateee29\", \"taille_maillot\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:37:13', NULL),
(34, '0007', 'Marguerite', 'Vernet', 'margueritevernet@outlook.com', NULL, 'Boulevard des Philosophes 11', '1205', 'Genève', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\":\"margoo.vrnt\",\"taille_maillot\":\"M\",\"aec\":\"1\",\"fonction\":\"Membre comit\\u00e9\",\"groupe\":\"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:37:32', NULL),
(35, '0004', 'Livia', 'Wagner', 'livia.wagner@gmail.com', '1994-07-29', 'Chemin du Champ-Baron 14', '1209', 'Genève', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Liviawae_\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"gilet\": \"L\", \"fonction\": \"Secrétaire + com\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:35:18', NULL),
(36, '0006', 'Anne', 'Zendali Dimopoulos', 'azendali@infomaniak.ch', NULL, 'Chemin du Borbolet 5A', '1213', 'Onex', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"taille_maillot\":\"S\",\"aec\":\"1\",\"fonction\":\"Membre comit\\u00e9\",\"groupe\":\"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:36:15', NULL),
(37, NULL, 'Amélie', 'Abbet', 'abbetmel082@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:47:53', NULL),
(38, NULL, 'Maelle', 'Achard', 'maelle.achard@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:48:11', NULL),
(39, NULL, 'Morgane', 'BALMER', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(40, NULL, 'Marie', 'BARBEY CHAPPUIS', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"marie.barbet.chappuis\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(41, NULL, 'Jenia', 'Boriskovskaia', 'boriskovskaia@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"lesprenomsdegenie\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:49:35', NULL),
(42, NULL, 'Camille', 'BOURGAUD', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(43, NULL, 'Pauline', 'BOURGAUD', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(44, NULL, 'Laila', 'Castaldo', 'laila.castaldo@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Laila Castaldo\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:50:02', NULL),
(45, NULL, 'Heike', 'D', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(46, NULL, 'Noemie', 'DARLIX-HUG', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(47, NULL, 'Paulien', 'De Haes', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(48, NULL, 'Pauline', 'DOTTRENS', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(49, NULL, 'Laurine', 'Eisenhuth', 'laurine.eisenhuth@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Laurineeisenhuth\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:50:47', NULL),
(50, NULL, 'Alicia', 'FABBRI ?', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(51, NULL, 'Vanessa', 'GEROTTO', 'vanessa.gerotto@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"roulerpourmagda\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(52, NULL, 'Helena', 'JJOVIC', 'helena.jjovic@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(53, NULL, 'Carla', 'Karam', 'carlakaram@outlook.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"carlouchi\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 20:49:04', NULL),
(54, NULL, 'Marie', 'LAMASSIAUDE', 'm.lamassiaude@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(55, NULL, 'Lucile', 'Moulin', 'lucilemoulin.02@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:51:10', NULL),
(56, NULL, 'Silvia', 'NECCO ?', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(57, NULL, 'Carmen', 'PECHARROMAN', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(58, NULL, 'Laetitia', 'PELLEGRINI', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(59, NULL, 'Ana', 'POLTERA', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(60, NULL, 'Laura', 'Roser', 'laura@niviuk.ch', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:51:28', NULL),
(61, NULL, 'Lola', 'SAUGY', 'lola.saugy@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(62, NULL, 'Sammie', 'Stuart', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(63, NULL, 'Pernilla', 'W', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(64, NULL, 'Nadine', 'ZILBER', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(65, NULL, 'Estelle', '—', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(66, NULL, 'Jacqueline', '—', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(67, NULL, 'Maelle', 'Bicyclette bleue', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(68, NULL, 'Maud', 'Bicyclette bleue', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(69, NULL, 'Corinne', '—', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(70, NULL, 'Hortense', '—', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(71, NULL, 'IZA', '—', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(72, NULL, 'Ju', '—', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(73, NULL, 'Célia', 'EGLI', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(74, NULL, 'Noemi', 'GALLI', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(75, NULL, 'Prune', 'GALLI GEISSMANN', NULL, NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(76, NULL, 'Myriam', 'PAPERMAN', 'myriampaperman@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"mental_myriam ou notyouraveragebobs\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(77, NULL, 'Chiara', 'Bombardi', 'chiara.bombardi86@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"chiabomba\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 19:48:59', NULL),
(78, NULL, 'Stéfane', 'TESTTEST', 'stefane.clavel@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"statuts_ok\":\"oui\",\"cotisation_ok\":\"oui\"}', NULL, '2026-03-22 18:02:40', '2026-03-22 18:03:41', NULL, '2026-03-22 18:34:53', '2026-03-22 18:34:53');

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
  `email` varchar(255) DEFAULT NULL,
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

--
-- Daten für Tabelle `members_audit`
--

INSERT INTO `members_audit` (`audit_id`, `audit_action`, `audit_user_id`, `audit_timestamp`, `id`, `member_number`, `first_name`, `last_name`, `email`, `date_of_birth`, `address`, `postal_code`, `city`, `country`, `statuscode`, `membership_start`, `membership_end`, `notes`, `is_invitee`, `photo_ok`, `metadata`, `activation_token`, `activation_sent_at`, `email_verified_at`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 'U', NULL, '2026-03-22 17:39:56', 31, NULL, 'Caroline', 'GAILLARD', 'carolinegaillard@gmail.com', NULL, 'Chemin de Pinchat 42C', '1234', 'Vessy', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"carolineglrd\", \"taille_maillot\": \"M\", \"bib\": \"L\", \"gilet\": \"M\", \"fonction\": \"Présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(2, 'U', NULL, '2026-03-22 17:42:54', 31, NULL, 'Caroline', 'GAILLARD', 'carolinegaillard@gmail.com', '1982-05-28', 'Chemin de Pinchat 42C', '1234', 'Vessy', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"carolineglrd\", \"taille_maillot\": \"M\", \"bib\": \"L\", \"gilet\": \"M\", \"fonction\": \"Présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 16:39:56', NULL),
(3, 'U', NULL, '2026-03-22 17:43:20', 28, NULL, 'Giulia', 'ANTONIOLI', 'giulia.antonioli@gmail.com', NULL, 'Route de Malagnou 172', '1224', 'Chêne-Bougeries', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"the.giuliagram\", \"taille_maillot\": \"M\", \"aec\": true, \"bib\": \"M\", \"gilet\": \"M\", \"fonction\": \"Trésorière\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(4, 'U', NULL, '2026-03-22 17:45:09', 31, NULL, 'Caroline', 'GAILLARD', 'carolinegaillard@gmail.com', '1982-05-28', 'Chemin de Pinchat 42C', '1234', 'Vessy', 'CH', 'A', '2026-01-01', NULL, NULL, 0, 1, '{\"instagram\": \"carolineglrd\", \"taille_maillot\": \"M\", \"bib\": \"L\", \"gilet\": \"M\", \"fonction\": \"Présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 16:39:56', NULL),
(5, 'U', NULL, '2026-03-22 17:45:49', 28, NULL, 'Giulia', 'ANTONIOLI', 'giulia.antonioli@gmail.com', NULL, 'Route de Malagnou 172', '1224', 'Chêne-Bougeries', 'CH', 'A', '2026-01-01', NULL, NULL, 0, 1, '{\"instagram\": \"the.giuliagram\", \"taille_maillot\": \"M\", \"aec\": true, \"bib\": \"M\", \"gilet\": \"M\", \"fonction\": \"Trésorière\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(6, 'U', NULL, '2026-03-22 17:46:22', 35, NULL, 'Livia', 'WAGNER', 'livia.wagner@gmail.com', NULL, 'Chemin du Champ-Baron 14', '1209', 'Genève', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Liviawae_\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"gilet\": \"L\", \"fonction\": \"Secrétaire + com\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(7, 'U', NULL, '2026-03-22 17:46:34', 28, '0002', 'Giulia', 'ANTONIOLI', 'giulia.antonioli@gmail.com', NULL, 'Route de Malagnou 172', '1224', 'Chêne-Bougeries', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"the.giuliagram\", \"taille_maillot\": \"M\", \"aec\": true, \"bib\": \"M\", \"gilet\": \"M\", \"fonction\": \"Trésorière\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(8, 'U', NULL, '2026-03-22 17:46:58', 32, NULL, 'Sofia', 'PASSARETTI', 'sofiapassaretti@gmail.com', NULL, 'Chemin des Palettes 15', '1212', 'Grand-Lancy', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"sofiapssrettti\", \"taille_maillot\": \"L\", \"aec\": true, \"fonction\": \"Vice-présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(9, 'U', NULL, '2026-03-22 17:47:44', 29, NULL, 'Dune', 'BOURQUIN', 'dune.bourquin@gmail.com', NULL, 'Chemin du Champ-Baron 14', '1209', 'Genève', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Dune_3\", \"taille_maillot\": \"S\", \"bib\": \"S\", \"gilet\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(10, 'U', NULL, '2026-03-22 17:50:54', 36, NULL, 'Anne', 'ZENDALI DIMOPOULOS', 'azendali@infomaniak.ch', NULL, 'Chemin du Borbolet 5A', '1213', 'Onex', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"S\", \"aec\": true, \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(11, 'U', NULL, '2026-03-22 17:51:27', 34, NULL, 'Marguerite', 'VERNET', 'margueritevernet@outlook.com', NULL, 'Boulevard des Philosophes 11', '1205', 'Genève', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"margoo.vrnt\", \"taille_maillot\": \"M\", \"aec\": true, \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(12, 'U', NULL, '2026-03-22 17:52:23', 30, NULL, 'Olivia', 'CHASSOT', 'olivia.a.chassot@gmail.com', NULL, 'Rue de Genève 24', '01160', 'Ferney-Voltaire', 'FR', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"S\", \"bib\": \"S\", \"gilet\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(13, 'U', NULL, '2026-03-22 17:53:04', 33, NULL, 'Marta', 'RODRIGUEZ', 'marta.rodriguez29@hotmail.co.uk', NULL, 'Chemin de la Seymaz 24C', '1253', 'Vandoeuvres', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Nevertoolateee29\", \"taille_maillot\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(14, 'U', NULL, '2026-03-22 17:54:41', 31, '0001', 'Caroline', 'GAILLARD', 'carolinegaillard@gmail.com', '1982-05-28', 'Chemin de Pinchat 42C', '1234', 'Vessy', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"carolineglrd\", \"taille_maillot\": \"M\", \"bib\": \"L\", \"gilet\": \"M\", \"fonction\": \"Présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 16:39:56', NULL),
(15, 'U', NULL, '2026-03-22 18:31:01', 9, NULL, 'Maité', 'BARROSO', 'maite.barroso@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"maitechub\", \"taille_maillot\": \"S\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(16, 'U', NULL, '2026-03-22 18:31:01', 10, NULL, 'Elise', 'DUPUIS', 'elise.lozeron@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"elise.lozeron\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(17, 'U', NULL, '2026-03-22 18:31:01', 11, NULL, 'Sonia', 'GLOWACKI', 'sonia.glowacki@gmail.com', NULL, 'Chemin de la Tuiliere 690', '74140', 'Veigy-Foncenex', 'FR', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"soniaglw\", \"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(18, 'U', NULL, '2026-03-22 18:31:01', 12, NULL, 'Julie', 'HUG', 'blackburn.hug@gmail.com', NULL, 'Avenue du Bouchet 5', '1209', 'Genève', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Julielahug\", \"taille_maillot\": \"M\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(19, 'U', NULL, '2026-03-22 18:31:01', 13, NULL, 'Brigitte', 'LÜTH', 'b.lueth@afia.at', NULL, 'Chemin de Dessous-Saint-Loup 4c', '1290', 'Versoix', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"lueth\", \"taille_maillot\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(20, 'U', NULL, '2026-03-22 18:31:01', 14, NULL, 'Monica', 'MARINUCCI', 'monicamarinucci@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"S\", \"aec\": true, \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(21, 'U', NULL, '2026-03-22 18:31:01', 15, NULL, 'Sophie', 'MASSOBRE', 'sophiemassobre@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"mg.sophie\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(22, 'U', NULL, '2026-03-22 18:31:01', 16, NULL, 'Ana', 'MENCHERO GONZALEZ', 'amenchero@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"XL\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(23, 'U', NULL, '2026-03-22 18:31:01', 17, NULL, 'Eloise', 'MICELI', 'eloise.miceli@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"M\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(24, 'U', NULL, '2026-03-22 18:31:01', 18, NULL, 'Chris', 'NASSIVERA', 'chrisnassivera@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"XL\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(25, 'U', NULL, '2026-03-22 18:31:01', 19, NULL, 'Allison', 'NEAPOLE', 'aneapole@bluewin.ch', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"aneapole\", \"taille_maillot\": \"2XL\", \"bib\": \"2XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(26, 'U', NULL, '2026-03-22 18:31:01', 20, NULL, 'Alice', 'NOEL', 'nalice@hotmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"nalicegeneva81\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(27, 'U', NULL, '2026-03-22 18:31:02', 21, NULL, 'Emma', 'O\'LEARY', 'emmamaryoleary@gmail.com', NULL, 'Rue des Peupliers 18', '1205', 'Geneve', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"emmamaryoh\", \"taille_maillot\": \"XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(28, 'U', NULL, '2026-03-22 18:31:02', 22, NULL, 'Monica', 'PEREIRA LIMA', 'monipere@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"moni.mo_ni\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(29, 'U', NULL, '2026-03-22 18:31:02', 23, NULL, 'Raquel', 'SOLES', 'raquelsoles72@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"solesraquel\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(30, 'U', NULL, '2026-03-22 18:31:02', 24, NULL, 'Alina', 'STANCULESCU', 'stanculescu.alina.elena@gmail.com', NULL, 'Chemin de Saule 74', '1233', 'Bernex', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"S\", \"bib\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(31, 'U', NULL, '2026-03-22 18:31:02', 25, NULL, 'Géraldine', 'TORNARE', 'tornare.geraldine@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"grldneanorrt\", \"taille_maillot\": \"M\", \"aec\": true, \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(32, 'U', NULL, '2026-03-22 18:31:02', 26, NULL, 'Olwen', 'WILSON', 'olwenwilson95@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"olwenwilson\", \"taille_maillot\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(33, 'U', NULL, '2026-03-22 18:31:02', 27, NULL, 'Mahesha', 'YAPA', 'mahesha.yapa@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(34, 'U', NULL, '2026-03-22 18:51:01', 35, '0004', 'Livia', 'WAGNER', 'livia.wagner@gmail.com', NULL, 'Chemin du Champ-Baron 14', '1209', 'Genève', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Liviawae_\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"gilet\": \"L\", \"fonction\": \"Secrétaire + com\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(35, 'U', NULL, '2026-03-22 19:02:40', 78, NULL, 'Stéfane', 'TESTTEST', 'stefane.clavel@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"statuts_ok\":\"oui\",\"cotisation_ok\":\"oui\"}', NULL, NULL, NULL, NULL, '2026-03-22 18:02:40', NULL),
(36, 'U', NULL, '2026-03-22 19:03:41', 78, NULL, 'Stéfane', 'TESTTEST', 'stefane.clavel@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"statuts_ok\":\"oui\",\"cotisation_ok\":\"oui\"}', '$2y$12$NgA8djaDg20AO3N8WTCPUucM5DbwNloP.BoO23zhLDE6FLoOOhK9q', '2026-03-22 18:02:40', NULL, NULL, '2026-03-22 18:02:40', NULL),
(37, 'U', NULL, '2026-03-22 19:07:18', 78, NULL, 'Stéfane', 'TESTTEST', 'stefane.clavel@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"statuts_ok\":\"oui\",\"cotisation_ok\":\"oui\"}', NULL, '2026-03-22 18:02:40', '2026-03-22 18:03:41', NULL, '2026-03-22 18:03:41', NULL),
(38, 'U', NULL, '2026-03-22 19:11:54', 1, NULL, 'Kayci', 'BROWNE', 'kaycibrowne@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(39, 'U', NULL, '2026-03-22 19:12:23', 2, NULL, 'Sirella', 'FÉRÉDIE', 'sirella.feredie@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Sirella_frd\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(40, 'U', NULL, '2026-03-22 19:12:55', 3, NULL, 'Justine', 'FRANCHETEAU', 'justine.francheteau@yahoo.fr', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Justine.frcht\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(41, 'U', NULL, '2026-03-22 19:13:23', 4, NULL, 'Anna', 'RIZZI', 'anna.rizzi.2009@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"annaa_rizzi_\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(42, 'U', NULL, '2026-03-22 19:13:55', 5, NULL, 'Louise', 'ROSSITER-LEVRARD', 'lrossiterlevrard@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(43, 'U', NULL, '2026-03-22 19:14:29', 78, NULL, 'Stéfane', 'TESTTEST', 'stefane.clavel@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"statuts_ok\":\"oui\",\"cotisation_ok\":\"oui\"}', NULL, '2026-03-22 18:02:40', '2026-03-22 18:03:41', NULL, '2026-03-22 18:07:18', '2026-03-22 18:07:18'),
(45, 'U', NULL, '2026-03-22 19:34:53', 78, NULL, 'Stéfane', 'TESTTEST', 'stefane.clavel@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'P', NULL, NULL, NULL, 0, 1, '{\"statuts_ok\":\"oui\",\"cotisation_ok\":\"oui\"}', NULL, '2026-03-22 18:02:40', '2026-03-22 18:03:41', NULL, '2026-03-22 18:14:29', NULL),
(46, 'U', NULL, '2026-03-22 19:46:41', 9, '0010', 'Maité', 'BARROSO', 'maite.barroso@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"maitechub\", \"taille_maillot\": \"S\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(47, 'U', NULL, '2026-03-22 19:46:41', 10, '0011', 'Elise', 'DUPUIS', 'elise.lozeron@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"elise.lozeron\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(48, 'U', NULL, '2026-03-22 19:46:41', 11, '0012', 'Sonia', 'GLOWACKI', 'sonia.glowacki@gmail.com', NULL, 'Chemin de la Tuiliere 690', '74140', 'Veigy-Foncenex', 'FR', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"soniaglw\", \"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(49, 'U', NULL, '2026-03-22 19:46:41', 12, '0013', 'Julie', 'HUG', 'blackburn.hug@gmail.com', NULL, 'Avenue du Bouchet 5', '1209', 'Genève', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Julielahug\", \"taille_maillot\": \"M\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(50, 'U', NULL, '2026-03-22 19:46:41', 13, '0014', 'Brigitte', 'LÜTH', 'b.lueth@afia.at', NULL, 'Chemin de Dessous-Saint-Loup 4c', '1290', 'Versoix', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"lueth\", \"taille_maillot\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(51, 'U', NULL, '2026-03-22 19:46:41', 14, '0015', 'Monica', 'MARINUCCI', 'monicamarinucci@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"S\", \"aec\": true, \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(52, 'U', NULL, '2026-03-22 19:46:41', 15, '0016', 'Sophie', 'MASSOBRE', 'sophiemassobre@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"mg.sophie\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(53, 'U', NULL, '2026-03-22 19:46:41', 16, '0017', 'Ana', 'MENCHERO GONZALEZ', 'amenchero@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"XL\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(54, 'U', NULL, '2026-03-22 19:46:41', 17, '0018', 'Eloise', 'MICELI', 'eloise.miceli@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"M\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(55, 'U', NULL, '2026-03-22 19:46:41', 18, '0019', 'Chris', 'NASSIVERA', 'chrisnassivera@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"XL\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(56, 'U', NULL, '2026-03-22 19:46:41', 19, '0020', 'Allison', 'NEAPOLE', 'aneapole@bluewin.ch', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"aneapole\", \"taille_maillot\": \"2XL\", \"bib\": \"2XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(57, 'U', NULL, '2026-03-22 19:46:41', 20, '0021', 'Alice', 'NOEL', 'nalice@hotmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"nalicegeneva81\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(58, 'U', NULL, '2026-03-22 19:46:41', 21, '0022', 'Emma', 'O\'LEARY', 'emmamaryoleary@gmail.com', NULL, 'Rue des Peupliers 18', '1205', 'Geneve', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"emmamaryoh\", \"taille_maillot\": \"XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(59, 'U', NULL, '2026-03-22 19:46:41', 22, '0023', 'Monica', 'PEREIRA LIMA', 'monipere@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"moni.mo_ni\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(60, 'U', NULL, '2026-03-22 19:46:41', 23, '0024', 'Raquel', 'SOLES', 'raquelsoles72@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"solesraquel\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(61, 'U', NULL, '2026-03-22 19:46:41', 24, '0025', 'Alina', 'STANCULESCU', 'stanculescu.alina.elena@gmail.com', NULL, 'Chemin de Saule 74', '1233', 'Bernex', 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"S\", \"bib\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(62, 'U', NULL, '2026-03-22 19:46:41', 25, '0026', 'Géraldine', 'TORNARE', 'tornare.geraldine@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"grldneanorrt\", \"taille_maillot\": \"M\", \"aec\": true, \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(63, 'U', NULL, '2026-03-22 19:46:41', 26, '0027', 'Olwen', 'WILSON', 'olwenwilson95@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"olwenwilson\", \"taille_maillot\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(64, 'U', NULL, '2026-03-22 19:46:41', 27, '0028', 'Mahesha', 'YAPA', 'mahesha.yapa@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, NULL, NULL, 0, 1, '{\"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(65, 'U', NULL, '2026-03-22 20:32:56', 31, '0001', 'Caroline', 'GAILLARD', 'carolinegaillard@gmail.com', '1982-05-28', 'Chemin de Pinchat 42C', '1234', 'Vessy', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"carolineglrd\", \"taille_maillot\": \"M\", \"bib\": \"L\", \"gilet\": \"M\", \"fonction\": \"Présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 16:39:56', NULL),
(66, 'U', NULL, '2026-03-22 20:33:35', 28, '0003', 'Giulia', 'ANTONIOLI', 'giulia.antonioli@gmail.com', NULL, 'Route de Malagnou 172', '1224', 'Chêne-Bougeries', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"the.giuliagram\", \"taille_maillot\": \"M\", \"aec\": true, \"bib\": \"M\", \"gilet\": \"M\", \"fonction\": \"Trésorière\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(67, 'U', NULL, '2026-03-22 20:35:04', 32, '0002', 'Sofia', 'PASSARETTI', 'sofiapassaretti@gmail.com', NULL, 'Chemin des Palettes 15', '1212', 'Grand-Lancy', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"sofiapssrettti\", \"taille_maillot\": \"L\", \"aec\": true, \"fonction\": \"Vice-présidente\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(68, 'U', NULL, '2026-03-22 20:35:18', 35, '0004', 'Livia', 'WAGNER', 'livia.wagner@gmail.com', '1994-07-29', 'Chemin du Champ-Baron 14', '1209', 'Genève', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Liviawae_\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"gilet\": \"L\", \"fonction\": \"Secrétaire + com\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 17:51:01', NULL),
(69, 'U', NULL, '2026-03-22 20:35:43', 29, '0005', 'Dune', 'BOURQUIN', 'dune.bourquin@gmail.com', NULL, 'Chemin du Champ-Baron 14', '1209', 'Genève', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Dune_3\", \"taille_maillot\": \"S\", \"bib\": \"S\", \"gilet\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(70, 'U', NULL, '2026-03-22 20:36:15', 36, '0006', 'Anne', 'ZENDALI DIMOPOULOS', 'azendali@infomaniak.ch', NULL, 'Chemin du Borbolet 5A', '1213', 'Onex', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"S\", \"aec\": true, \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(71, 'U', NULL, '2026-03-22 20:36:34', 30, '0008', 'Olivia', 'CHASSOT', 'olivia.a.chassot@gmail.com', NULL, 'Rue de Genève 24', '01160', 'Ferney-Voltaire', 'FR', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"S\", \"bib\": \"S\", \"gilet\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(72, 'U', NULL, '2026-03-22 20:37:13', 33, '0009', 'Marta', 'RODRIGUEZ', 'marta.rodriguez29@hotmail.co.uk', NULL, 'Chemin de la Seymaz 24C', '1253', 'Vandoeuvres', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Nevertoolateee29\", \"taille_maillot\": \"S\", \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(73, 'U', NULL, '2026-03-22 20:37:32', 34, '0007', 'Marguerite', 'VERNET', 'margueritevernet@outlook.com', NULL, 'Boulevard des Philosophes 11', '1205', 'Genève', 'CH', 'A', '2026-01-01', '2026-12-31', NULL, 0, 1, '{\"instagram\": \"margoo.vrnt\", \"taille_maillot\": \"M\", \"aec\": true, \"fonction\": \"Membre comité\", \"groupe\": \"Membre Voirons\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(74, 'U', NULL, '2026-03-22 20:40:47', 9, '0010', 'Maité', 'BARROSO', 'maite.barroso@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"maitechub\", \"taille_maillot\": \"S\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(75, 'U', NULL, '2026-03-22 20:41:07', 10, '0011', 'Elise', 'DUPUIS', 'elise.lozeron@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"elise.lozeron\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(76, 'U', NULL, '2026-03-22 20:41:32', 11, '0012', 'Sonia', 'GLOWACKI', 'sonia.glowacki@gmail.com', NULL, 'Chemin de la Tuiliere 690', '74140', 'Veigy-Foncenex', 'FR', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"soniaglw\", \"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(77, 'U', NULL, '2026-03-22 20:41:59', 12, '0013', 'Julie', 'HUG', 'blackburn.hug@gmail.com', NULL, 'Avenue du Bouchet 5', '1209', 'Genève', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"Julielahug\", \"taille_maillot\": \"M\", \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(78, 'U', NULL, '2026-03-22 20:42:22', 13, '0014', 'Brigitte', 'LÜTH', 'b.lueth@afia.at', NULL, 'Chemin de Dessous-Saint-Loup 4c', '1290', 'Versoix', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"lueth\", \"taille_maillot\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(79, 'U', NULL, '2026-03-22 20:42:47', 14, '0015', 'Monica', 'MARINUCCI', 'monicamarinucci@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"S\", \"aec\": true, \"bib\": \"M\", \"gilet\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(80, 'U', NULL, '2026-03-22 20:43:14', 15, '0016', 'Sophie', 'MASSOBRE', 'sophiemassobre@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"mg.sophie\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(81, 'U', NULL, '2026-03-22 20:43:43', 16, '0017', 'Ana', 'MENCHERO GONZALEZ', 'amenchero@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"XL\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(82, 'U', NULL, '2026-03-22 20:44:03', 17, '0018', 'Eloise', 'MICELI', 'eloise.miceli@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"M\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(83, 'U', NULL, '2026-03-22 20:44:28', 18, '0019', 'Chris', 'NASSIVERA', 'chrisnassivera@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"XL\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(84, 'U', NULL, '2026-03-22 20:44:48', 19, '0020', 'Allison', 'NEAPOLE', 'aneapole@bluewin.ch', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"aneapole\", \"taille_maillot\": \"2XL\", \"bib\": \"2XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(85, 'U', NULL, '2026-03-22 20:45:03', 20, '0021', 'Alice', 'NOEL', 'nalice@hotmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"nalicegeneva81\", \"taille_maillot\": \"L\", \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(86, 'U', NULL, '2026-03-22 20:45:23', 21, '0022', 'Emma', 'O\'LEARY', 'emmamaryoleary@gmail.com', NULL, 'Rue des Peupliers 18', '1205', 'Geneve', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"emmamaryoh\", \"taille_maillot\": \"XL\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(87, 'U', NULL, '2026-03-22 20:45:44', 22, '0023', 'Monica', 'PEREIRA LIMA', 'monipere@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"moni.mo_ni\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(88, 'U', NULL, '2026-03-22 20:45:56', 23, '0024', 'Raquel', 'SOLES', 'raquelsoles72@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"solesraquel\", \"taille_maillot\": \"M\", \"aec\": true, \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(89, 'U', NULL, '2026-03-22 20:46:30', 24, '0025', 'Alina', 'STANCULESCU', 'stanculescu.alina.elena@gmail.com', NULL, 'Chemin de Saule 74', '1233', 'Bernex', 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"S\", \"bib\": \"S\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(90, 'U', NULL, '2026-03-22 20:46:51', 25, '0026', 'Géraldine', 'TORNARE', 'tornare.geraldine@icloud.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"grldneanorrt\", \"taille_maillot\": \"M\", \"aec\": true, \"bib\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(91, 'U', NULL, '2026-03-22 20:47:09', 26, '0027', 'Olwen', 'WILSON', 'olwenwilson95@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"instagram\": \"olwenwilson\", \"taille_maillot\": \"L\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(92, 'U', NULL, '2026-03-22 20:47:24', 27, '0028', 'Mahesha', 'YAPA', 'mahesha.yapa@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'A', NULL, '2026-12-31', NULL, 0, 1, '{\"taille_maillot\": \"M\", \"groupe\": \"Membre Salève\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(93, 'U', NULL, '2026-03-22 20:47:53', 37, NULL, 'Amélie', 'ABBET', 'abbetmel082@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(94, 'U', NULL, '2026-03-22 20:48:11', 38, NULL, 'Maelle', 'ACHARD', 'maelle.achard@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(95, 'U', NULL, '2026-03-22 20:48:59', 77, NULL, 'Chiara', 'BOMBARDI', 'chiara.bombardi86@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"chiabomba\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(96, 'U', NULL, '2026-03-22 20:49:35', 41, NULL, 'Jenia', 'BORISKOVSKAIA', 'boriskovskaia@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"lesprenomsdegenie\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(97, 'U', NULL, '2026-03-22 20:50:02', 44, NULL, 'Laila', 'CASTALDO', 'laila.castaldo@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Laila Castaldo\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(98, 'U', NULL, '2026-03-22 20:50:47', 49, NULL, 'Laurine', 'EISENHUTH', 'laurine.eisenhuth@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"Laurineeisenhuth\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(99, 'U', NULL, '2026-03-22 20:51:10', 55, NULL, 'Lucile', 'MOULIN', 'lucilemoulin.02@gmail.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(100, 'U', NULL, '2026-03-22 20:51:28', 60, NULL, 'Laura', 'ROSER', 'laura@niviuk.ch', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL),
(101, 'U', NULL, '2026-03-22 21:49:04', 53, NULL, 'Carla', 'KARAM', 'carlakaram@outlook.com', NULL, NULL, NULL, NULL, 'CH', 'N', NULL, NULL, NULL, 0, 1, '{\"instagram\": \"carlouchi\", \"groupe\": \"Participantes\"}', NULL, NULL, NULL, NULL, '2026-03-22 14:18:56', NULL);

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

--
-- Daten für Tabelle `member_magic_tokens`
--

INSERT INTO `member_magic_tokens` (`id`, `member_id`, `token_hash`, `expires_at`, `used_at`, `created_at`) VALUES
(1, 35, 0x6654b2dcc89c25c926390f7d283be944bb85651a47418b16f2e4c68925a1cd82, '2026-03-22 15:44:23', '2026-03-22 15:29:40', '2026-03-22 16:29:23'),
(2, 29, 0xf252e84168104e5fca35b8d5457660e462d5f73c223f1979f881c645595f66c2, '2026-03-22 17:12:14', '2026-03-22 16:57:33', '2026-03-22 17:57:14'),
(3, 29, 0x0f0968a243a20a1179a04cf4f92f05d9f6bbfae32df70b7d02520e336b1aa5f2, '2026-03-22 17:18:06', '2026-03-22 17:03:19', '2026-03-22 18:03:06'),
(4, 35, 0x1caaa7fcee33f1e59584c24fd9d69e10ea8d8ab3ae39c704c3947fbbfa82447f, '2026-03-22 19:46:16', '2026-03-22 19:31:24', '2026-03-22 20:31:16'),
(5, 32, 0x63814e0e58346f82af1cba930e3d77046edfec025934acf8f180e7f3617398df, '2026-03-22 20:17:19', '2026-03-22 20:02:45', '2026-03-22 21:02:19'),
(6, 29, 0xf6da94374cbd734291a11db9fbe51a7c8384880dd794f55d67992adca4fe0bef, '2026-03-22 20:19:53', '2026-03-22 20:05:03', '2026-03-22 21:04:53'),
(7, 28, 0x2f40777597ce1b4c2a6074e833d05bc4fbfe98c51d6113070d9650f9e1897bc4, '2026-03-22 20:23:11', '2026-03-22 20:08:26', '2026-03-22 21:08:11'),
(8, 31, 0x965db3f5d1a29ebea8798ff56a3e2035f212bbbce0f226ac80bd27389b48ff6f, '2026-03-23 06:02:06', '2026-03-23 05:47:22', '2026-03-23 06:47:06'),
(9, 31, 0xb9db44a0358b997ad494423ee9a1b1d9d113e907afa4fd3e2ffac3376f4b18af, '2026-03-23 06:03:13', '2026-03-23 05:48:22', '2026-03-23 06:48:13'),
(10, 60, 0xbadfb6b2c7aa8d1654ac6eaefbbed2252297871e80f7a209c3752db232a2aebe, '2026-03-23 08:43:20', '2026-03-23 08:28:28', '2026-03-23 09:28:20'),
(11, 60, 0x79b0c628c1dad1fc9442bed589904b56dfbad7215031c39fc41fbdfca17fa167, '2026-03-23 09:37:00', '2026-03-23 09:23:08', '2026-03-23 10:22:00'),
(12, 60, 0x286d0e26790f880e31593c87d36b017391f40b24ac888558bb09b3ec7cd438da, '2026-03-23 09:38:03', NULL, '2026-03-23 10:23:03');

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
-- Daten für Tabelle `member_phones`
--

INSERT INTO `member_phones` (`id`, `member_id`, `phone_number`, `label`, `is_whatsapp`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 1, '+41 76 627 54 15', 'Mobile principal', 1, 1, NULL, '2026-03-22 18:11:54', NULL),
(2, 2, '+41 78 318 92 77', 'Mobile principal', 1, 1, NULL, '2026-03-22 18:12:23', NULL),
(3, 3, '+33 609 45 54 95', 'Mobile principal', 1, 1, NULL, '2026-03-22 18:12:55', NULL),
(4, 4, '+41 76 739 27 08', 'Mobile principal', 1, 1, NULL, '2026-03-22 18:13:23', NULL),
(5, 5, '+33 683 47 41 18', 'Mobile principal', 1, 1, NULL, '2026-03-22 18:13:55', NULL),
(6, 9, '+41 75 411 47 54', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:40:47', NULL),
(7, 10, '+41 76 615 67 30', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:41:07', NULL),
(8, 11, '+33 749 76 28 97', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:41:32', NULL),
(9, 12, '+41 79 128 51 71', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:41:59', NULL),
(10, 13, '+41 79 882 93 53', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:42:22', NULL),
(11, 14, '+41 79 572 00 38', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:42:47', NULL),
(12, 15, '+33 649 00 01 49', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:43:14', NULL),
(13, 16, '+41 78 606 38 32', 'Mobile principal', 0, 1, NULL, '2026-03-22 19:43:43', NULL),
(14, 16, '+34 645 80 86 18', 'Mobile secondaire', 1, 2, NULL, '2026-03-22 19:43:43', NULL),
(15, 17, '+41 78 705 92 98', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:44:03', NULL),
(16, 18, '+41 79 909 89 35', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:44:28', NULL),
(17, 19, '+41 78 605 68 98', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:44:48', NULL),
(18, 20, '+41 76 616 81 92', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:45:03', NULL),
(19, 21, '+41 78 806 83 08', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:45:23', NULL),
(20, 22, '+41 78 676 51 50', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:45:44', NULL),
(21, 23, '+33 648 72 51 04', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:45:56', NULL),
(22, 24, '+41 77 413 25 17', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:46:30', NULL),
(23, 25, '+41 77 445 13 11', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:46:51', NULL),
(24, 26, '+41 77 993 00 99', 'Mobile principal', 0, 1, NULL, '2026-03-22 19:47:09', NULL),
(25, 26, '+44 74 87 74 80 57', 'Mobile secondaire', 1, 2, NULL, '2026-03-22 19:47:09', NULL),
(26, 27, '+41 79 537 77 78', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:47:24', NULL),
(27, 28, '+41 78 899 40 78', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:33:35', NULL),
(28, 29, '+41 79 942 04 29', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:35:43', NULL),
(29, 30, '+33 673 96 05 19', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:36:34', NULL),
(30, 31, '+41 78 708 41 13', 'Mobile principal', 1, 1, NULL, '2026-03-22 16:39:56', NULL),
(31, 32, '+41 78 344 35 35', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:35:04', NULL),
(32, 33, '+41 78 227 26 87', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:37:13', NULL),
(33, 34, '+41 78 335 31 91', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:37:32', NULL),
(34, 35, '+41 76 395 14 54', 'Mobile principal', 1, 1, NULL, '2026-03-22 17:51:01', NULL),
(35, 36, '+41 76 378 57 91', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:36:15', NULL),
(36, 37, '+41 78 855 02 13', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:47:53', NULL),
(37, 38, '+33 637 83 28 11', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:48:11', NULL),
(38, 39, '079 697 88 11', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(39, 40, '079 754 45 84', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(40, 41, '+41 76 498 30 69', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:49:35', NULL),
(41, 42, '0033 6 81 26 02 63', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(42, 43, '078 202 67 62', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(43, 44, '+41 79 682 37 90', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:50:02', NULL),
(44, 45, '079 460 14 66', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(45, 46, '078 215 75 25', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(46, 48, '079 723 35 75', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(47, 49, '+41 76 220 72 46', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:50:47', NULL),
(48, 50, '079 948 47 66', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(49, 55, '+33 782 79 54 44', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:51:10', NULL),
(50, 56, '076 271 64 27', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(51, 57, '076 374 55 91', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(52, 58, '0033 6 52 10 66 44', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(53, 59, '078 611 51 19', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(54, 60, '+41 78 736 07 43', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:51:28', NULL),
(55, 63, '079 817 80 05', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(56, 64, '0033 6 77 00 61 12', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(57, 65, '0033 6 62 50 62 99', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(58, 67, '0033 6 52 38 10 24', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(59, 68, '077 468 89 28', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(60, 69, '078 638 99 71', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(61, 70, '0033 7 88 79 94 02', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(62, 71, '079 285 78 67', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(63, 72, '079 294 44 24', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(64, 73, '079 229 63 49', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(65, 74, '078 814 80 75', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(66, 75, '079 886 50 48', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(67, 76, '0033 7 69 90 90 83', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(68, 78, '+41 76 395 14 54', 'Mobile principal', 0, 0, NULL, '2026-03-22 18:07:18', '2026-03-22 18:07:18'),
(69, 77, '+41 79 780 72 96', 'Mobile principal', 1, 1, NULL, '2026-03-22 19:48:59', NULL),
(70, 53, '+41 76 342 22 65', 'Mobile principal', 1, 1, NULL, '2026-03-22 20:49:04', NULL);

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

--
-- Daten für Tabelle `member_phones_audit`
--

INSERT INTO `member_phones_audit` (`audit_id`, `audit_action`, `audit_user_id`, `audit_timestamp`, `id`, `member_id`, `phone_number`, `label`, `is_whatsapp`, `sort_order`, `modified_by_id`, `updated_at`, `deleted_at`) VALUES
(1, 'U', NULL, '2026-03-22 14:21:59', 1, 1, '076 627 54 15', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(2, 'U', NULL, '2026-03-22 14:21:59', 2, 2, '078 318 92 77', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(3, 'U', NULL, '2026-03-22 14:21:59', 3, 3, '0033 6 09 45 54 95', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(4, 'U', NULL, '2026-03-22 14:21:59', 4, 4, '076 739 27 08', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(5, 'U', NULL, '2026-03-22 14:21:59', 5, 5, '0033 6 83 47 41 18', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(6, 'U', NULL, '2026-03-22 14:21:59', 6, 9, '075 411 47 54', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(7, 'U', NULL, '2026-03-22 14:21:59', 7, 10, '076 615 67 30', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(8, 'U', NULL, '2026-03-22 14:21:59', 8, 11, '0033 7 49 76 28 97', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(9, 'U', NULL, '2026-03-22 14:21:59', 9, 12, '079 128 51 71', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(10, 'U', NULL, '2026-03-22 14:21:59', 10, 13, '079 882 93 53', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(11, 'U', NULL, '2026-03-22 14:21:59', 11, 14, '079 572 00 38', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(12, 'U', NULL, '2026-03-22 14:21:59', 12, 15, '0033 6 49 00 01 49', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(13, 'U', NULL, '2026-03-22 14:21:59', 15, 17, '078 705 92 98', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(14, 'U', NULL, '2026-03-22 14:21:59', 16, 18, '079 909 89 35', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(15, 'U', NULL, '2026-03-22 14:21:59', 17, 19, '078 605 68 98', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(16, 'U', NULL, '2026-03-22 14:21:59', 18, 20, '076 616 81 92', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(17, 'U', NULL, '2026-03-22 14:21:59', 19, 21, '078 806 83 08', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(18, 'U', NULL, '2026-03-22 14:21:59', 20, 22, '078 676 51 50', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(19, 'U', NULL, '2026-03-22 14:21:59', 21, 23, '0033 6 48 72 51 04', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(20, 'U', NULL, '2026-03-22 14:21:59', 22, 24, '077 413 25 17', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(21, 'U', NULL, '2026-03-22 14:21:59', 23, 25, '077 445 13 11', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(22, 'U', NULL, '2026-03-22 14:21:59', 26, 27, '079 537 77 78', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(23, 'U', NULL, '2026-03-22 14:21:59', 27, 28, '078 899 40 78', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(24, 'U', NULL, '2026-03-22 14:21:59', 28, 29, '079 942 04 29', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(25, 'U', NULL, '2026-03-22 14:21:59', 29, 30, '0033 6 73 96 05 19', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(26, 'U', NULL, '2026-03-22 14:21:59', 30, 31, '078 708 41 13', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(27, 'U', NULL, '2026-03-22 14:21:59', 31, 32, '078 344 35 35', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(28, 'U', NULL, '2026-03-22 14:21:59', 32, 33, '078 227 26 87', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(29, 'U', NULL, '2026-03-22 14:21:59', 33, 34, '078 335 31 91', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(30, 'U', NULL, '2026-03-22 14:21:59', 34, 35, '076 395 14 54', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(31, 'U', NULL, '2026-03-22 14:21:59', 35, 36, '076 378 57 91', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(32, 'U', NULL, '2026-03-22 14:21:59', 36, 37, '078 855 02 13', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(33, 'U', NULL, '2026-03-22 14:21:59', 37, 38, '0033 6 37 83 28 11', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(34, 'U', NULL, '2026-03-22 14:21:59', 38, 39, '079 697 88 11', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(35, 'U', NULL, '2026-03-22 14:21:59', 39, 40, '079 754 45 84', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(36, 'U', NULL, '2026-03-22 14:21:59', 40, 41, '076 498 30 69', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(37, 'U', NULL, '2026-03-22 14:21:59', 41, 42, '0033 6 81 26 02 63', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(38, 'U', NULL, '2026-03-22 14:21:59', 42, 43, '078 202 67 62', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(39, 'U', NULL, '2026-03-22 14:21:59', 43, 44, '079 682 37 90', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(40, 'U', NULL, '2026-03-22 14:21:59', 44, 45, '079 460 14 66', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(41, 'U', NULL, '2026-03-22 14:21:59', 45, 46, '078 215 75 25', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(42, 'U', NULL, '2026-03-22 14:21:59', 46, 48, '079 723 35 75', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(43, 'U', NULL, '2026-03-22 14:21:59', 47, 49, '076 220 72 46', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(44, 'U', NULL, '2026-03-22 14:21:59', 48, 50, '079 948 47 66', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(45, 'U', NULL, '2026-03-22 14:21:59', 49, 55, '0033 7 82 79 54 44', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(46, 'U', NULL, '2026-03-22 14:21:59', 50, 56, '076 271 64 27', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(47, 'U', NULL, '2026-03-22 14:21:59', 51, 57, '076 374 55 91', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(48, 'U', NULL, '2026-03-22 14:21:59', 52, 58, '0033 6 52 10 66 44', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(49, 'U', NULL, '2026-03-22 14:21:59', 53, 59, '078 611 51 19', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(50, 'U', NULL, '2026-03-22 14:21:59', 54, 60, '078 736 07 43', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(51, 'U', NULL, '2026-03-22 14:21:59', 55, 63, '079 817 80 05', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(52, 'U', NULL, '2026-03-22 14:21:59', 56, 64, '0033 6 77 00 61 12', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(53, 'U', NULL, '2026-03-22 14:21:59', 57, 65, '0033 6 62 50 62 99', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(54, 'U', NULL, '2026-03-22 14:21:59', 58, 67, '0033 6 52 38 10 24', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(55, 'U', NULL, '2026-03-22 14:21:59', 59, 68, '077 468 89 28', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(56, 'U', NULL, '2026-03-22 14:21:59', 60, 69, '078 638 99 71', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(57, 'U', NULL, '2026-03-22 14:21:59', 61, 70, '0033 7 88 79 94 02', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(58, 'U', NULL, '2026-03-22 14:21:59', 62, 71, '079 285 78 67', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(59, 'U', NULL, '2026-03-22 14:21:59', 63, 72, '079 294 44 24', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(60, 'U', NULL, '2026-03-22 14:21:59', 64, 73, '079 229 63 49', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(61, 'U', NULL, '2026-03-22 14:21:59', 65, 74, '078 814 80 75', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(62, 'U', NULL, '2026-03-22 14:21:59', 66, 75, '079 886 50 48', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(63, 'U', NULL, '2026-03-22 14:21:59', 67, 76, '0033 7 69 90 90 83', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(64, 'U', NULL, '2026-03-22 17:39:56', 30, 31, '078 708 41 13', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(65, 'U', NULL, '2026-03-22 18:51:01', 34, 35, '076 395 14 54', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(66, 'U', NULL, '2026-03-22 19:07:18', 68, 78, '+41 76 395 14 54', 'Mobile principal', 0, 0, NULL, '2026-03-22 18:02:40', NULL),
(67, 'U', NULL, '2026-03-22 19:11:54', 1, 1, '076 627 54 15', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(68, 'U', NULL, '2026-03-22 19:12:23', 2, 2, '078 318 92 77', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(69, 'U', NULL, '2026-03-22 19:12:55', 3, 3, '0033 6 09 45 54 95', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(70, 'U', NULL, '2026-03-22 19:13:23', 4, 4, '076 739 27 08', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(71, 'U', NULL, '2026-03-22 19:13:55', 5, 5, '0033 6 83 47 41 18', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(72, 'U', NULL, '2026-03-22 20:33:35', 27, 28, '078 899 40 78', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(73, 'U', NULL, '2026-03-22 20:35:04', 31, 32, '078 344 35 35', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(74, 'U', NULL, '2026-03-22 20:35:43', 28, 29, '079 942 04 29', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(75, 'U', NULL, '2026-03-22 20:36:15', 35, 36, '076 378 57 91', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(76, 'U', NULL, '2026-03-22 20:36:34', 29, 30, '0033 6 73 96 05 19', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(77, 'U', NULL, '2026-03-22 20:37:13', 32, 33, '078 227 26 87', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(78, 'U', NULL, '2026-03-22 20:37:32', 33, 34, '078 335 31 91', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(79, 'U', NULL, '2026-03-22 20:40:47', 6, 9, '075 411 47 54', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(80, 'U', NULL, '2026-03-22 20:41:07', 7, 10, '076 615 67 30', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(81, 'U', NULL, '2026-03-22 20:41:32', 8, 11, '0033 7 49 76 28 97', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(82, 'U', NULL, '2026-03-22 20:41:59', 9, 12, '079 128 51 71', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(83, 'U', NULL, '2026-03-22 20:42:22', 10, 13, '079 882 93 53', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(84, 'U', NULL, '2026-03-22 20:42:47', 11, 14, '079 572 00 38', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(85, 'U', NULL, '2026-03-22 20:43:14', 12, 15, '0033 6 49 00 01 49', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(86, 'U', NULL, '2026-03-22 20:43:43', 13, 16, '078 606 38 32', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(87, 'U', NULL, '2026-03-22 20:43:43', 14, 16, '0034 645 80 86 18', 'Mobile secondaire', 1, 1, NULL, '2026-03-22 14:18:56', NULL),
(88, 'U', NULL, '2026-03-22 20:44:03', 15, 17, '078 705 92 98', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(89, 'U', NULL, '2026-03-22 20:44:28', 16, 18, '079 909 89 35', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(90, 'U', NULL, '2026-03-22 20:44:48', 17, 19, '078 605 68 98', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(91, 'U', NULL, '2026-03-22 20:45:03', 18, 20, '076 616 81 92', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(92, 'U', NULL, '2026-03-22 20:45:23', 19, 21, '078 806 83 08', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(93, 'U', NULL, '2026-03-22 20:45:44', 20, 22, '078 676 51 50', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(94, 'U', NULL, '2026-03-22 20:45:56', 21, 23, '0033 6 48 72 51 04', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(95, 'U', NULL, '2026-03-22 20:46:30', 22, 24, '077 413 25 17', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(96, 'U', NULL, '2026-03-22 20:46:51', 23, 25, '077 445 13 11', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(97, 'U', NULL, '2026-03-22 20:47:09', 24, 26, '077 993 00 99', 'Mobile principal', 0, 0, NULL, '2026-03-22 14:18:56', NULL),
(98, 'U', NULL, '2026-03-22 20:47:09', 25, 26, '0044 7487 748057', 'Mobile secondaire', 1, 1, NULL, '2026-03-22 14:18:56', NULL),
(99, 'U', NULL, '2026-03-22 20:47:24', 26, 27, '079 537 77 78', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(100, 'U', NULL, '2026-03-22 20:47:53', 36, 37, '078 855 02 13', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(101, 'U', NULL, '2026-03-22 20:48:11', 37, 38, '0033 6 37 83 28 11', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(102, 'U', NULL, '2026-03-22 20:49:35', 40, 41, '076 498 30 69', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(103, 'U', NULL, '2026-03-22 20:50:02', 43, 44, '079 682 37 90', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(104, 'U', NULL, '2026-03-22 20:50:47', 47, 49, '076 220 72 46', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(105, 'U', NULL, '2026-03-22 20:51:10', 49, 55, '0033 7 82 79 54 44', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL),
(106, 'U', NULL, '2026-03-22 20:51:28', 54, 60, '078 736 07 43', 'Mobile principal', 1, 0, NULL, '2026-03-22 14:18:56', NULL);

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

--
-- Daten für Tabelle `portal_audit_log`
--

INSERT INTO `portal_audit_log` (`id`, `member_id`, `member_number`, `action`, `detail`, `ip_address`, `created_at`) VALUES
(1, 35, NULL, 'login', NULL, '82.220.93.54', '2026-03-22 16:29:40'),
(2, 35, NULL, 'inscription', 'Événement #2 — Workshop #2 Atelier Position', '82.220.93.54', '2026-03-22 17:00:46'),
(3, 29, NULL, 'login', NULL, '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 17:57:33'),
(4, 29, NULL, 'inscription', 'Événement #2 — Workshop #2 Atelier Position', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 17:58:19'),
(5, 29, NULL, 'inscription', 'Événement #1 — Training #4 Jura', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 17:58:30'),
(6, 29, NULL, 'inscription', 'Événement #8 — Etape Reine TDFF', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 17:58:46'),
(7, 35, NULL, 'inscription', 'Événement #8 — Etape Reine TDFF', '82.220.93.54', '2026-03-22 17:58:54'),
(8, 29, NULL, 'inscription', 'Événement #7 — La Classique', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 17:59:10'),
(9, 29, NULL, 'logout', NULL, '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:03:04'),
(10, 29, NULL, 'login', NULL, '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:03:19'),
(11, 29, NULL, 'ajout participante', 'Événement #1 — Caroline GAILLARD', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:04:53'),
(12, 29, NULL, 'présence', 'Événement #1 — Dune BOURQUIN: présente', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:05:12'),
(13, 29, NULL, 'présence', 'Événement #1 — Caroline GAILLARD: absente', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:05:14'),
(14, 29, NULL, 'présence', 'Événement #1 — Dune BOURQUIN: absente', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:05:15'),
(15, 29, NULL, 'présence', 'Événement #1 — Livia WAGNER: présente', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:05:16'),
(16, 29, NULL, 'présence', 'Événement #1 — Livia WAGNER: absente', '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 18:05:20'),
(17, 35, '0004', 'annulation', 'Événement #8 — Etape Reine TDFF', '82.220.93.54', '2026-03-22 18:56:53'),
(18, 35, '0004', 'inscription', 'Événement #8 — Etape Reine TDFF', '82.220.93.54', '2026-03-22 18:57:03'),
(19, 78, NULL, 'logout', NULL, '82.220.93.54', '2026-03-22 20:31:13'),
(20, 35, '0004', 'login', NULL, '82.220.93.54', '2026-03-22 20:31:24'),
(21, 32, '0002', 'login', NULL, '31.165.143.207', '2026-03-22 21:02:45'),
(22, 32, '0002', 'inscription', 'Événement #1 — Training #4 Jura', '31.165.143.207', '2026-03-22 21:04:37'),
(23, 29, '0005', 'login', NULL, '2001:861:2d46:df30:99ac:6f07:1a4b:dd74', '2026-03-22 21:05:03'),
(24, 32, '0002', 'inscription', 'Événement #9 — Le Tour des Sations', '31.165.143.207', '2026-03-22 21:05:49'),
(25, 28, '0003', 'login', NULL, '194.230.146.219', '2026-03-22 21:08:26'),
(26, 28, '0003', 'inscription', 'Événement #8 — Etape Reine TDFF', '194.230.146.219', '2026-03-22 21:10:05'),
(27, 28, '0003', 'inscription', 'Événement #10 — Cyclotour du Léman', '194.230.146.219', '2026-03-22 21:10:14'),
(28, 28, '0003', 'inscription', 'Événement #7 — La Classique', '194.230.146.219', '2026-03-22 21:10:20'),
(29, 28, '0003', 'inscription', 'Événement #1 — Training #4 Jura', '194.230.146.219', '2026-03-22 21:10:28'),
(30, 28, '0003', 'inscription', 'Événement #2 — Workshop #2 Atelier Position', '194.230.146.219', '2026-03-22 21:10:34'),
(31, 31, '0001', 'login', NULL, '2a02:1210:546a:1800:ddc1:2dd1:aac1:67fd', '2026-03-23 06:47:22'),
(32, 31, '0001', 'inscription', 'Événement #10 — Cyclotour du Léman', '2a02:1210:546a:1800:ddc1:2dd1:aac1:67fd', '2026-03-23 06:47:38'),
(33, 31, '0001', 'logout', NULL, '2a02:1210:546a:1800:ddc1:2dd1:aac1:67fd', '2026-03-23 06:47:47'),
(34, 31, '0001', 'login', NULL, '2a02:1210:546a:1800:ddc1:2dd1:aac1:67fd', '2026-03-23 06:48:22'),
(35, 60, NULL, 'login', NULL, '2a02:1210:5493:8200:156b:c6:58f3:fd46', '2026-03-23 09:28:28'),
(36, 60, NULL, 'inscription', 'Événement #2 — Workshop #2 Atelier Position', '2a02:1210:5493:8200:156b:c6:58f3:fd46', '2026-03-23 09:30:12'),
(37, 60, NULL, 'annulation', 'Événement #2 — Workshop #2 Atelier Position', '2a02:1210:5493:8200:156b:c6:58f3:fd46', '2026-03-23 09:30:17'),
(38, 60, NULL, 'login', NULL, '129.194.89.239', '2026-03-23 10:23:08');

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

--
-- Daten für Tabelle `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('AhlHL4mWzBaxHo3BNltXAiTPXF4casWOxpPV7POO', NULL, '129.194.89.239', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', 'ZXlKcGRpSTZJa1ExV0c5SVp6ZExXa2xFYTJwYVFVWlBjQzlqZFZFOVBTSXNJblpoYkhWbElqb2lWV3BrZEZsbkwwSkhPVGxuTURRMVpqbFRTREF2VTBsMmFFUTFaMVoxTUZOclNWTmhiMDV3YnpWU1MyWlhNVUl4ZVRGdlRHSXdXRFJaZVVrcldtSmhUVE00U0U5T1oxZDFkMVZDTUdsT2RsSklVakJsZW5kQ2RHbG1Wa1k1VVVvd1NtbExRalppUzNKUFJtMVBNMU5xU1RSU1lWTnZWelYzVFVwMlkyTnNaR2RaUmpGc2RrNVdaM1kxUjJOdWRIbEJNeXR2VHpsamFscHdUeXMyTjJoQk5FcGxjVFIzZVZWYVYyOXhORkV2V2xkcksyUjFZMlZ1UzNwM2RFZEhTVlk1V2l0VWRtNURaRmRVUzFKQ2NFMWlTVEJCUkRGQldIbHRhV0U0YUhod1JWVlVOeXN6U0V4amFVWTRja2hxVFVGb1JFdFpSV2g2V0VGVGR6VlBXWHAxTTBsbWJsbEtUMU5rTkhOeFZuRnhVa054U1RKMlEwa3phbEpMTjFoaU5uTXhTV04wTWxrd2VIbGFSM0l4ZWtGeVJsb3hWV1Y2TWxwbFdITlBlWEJtUmpZaUxDSnRZV01pT2lKbU1XSm1aRGxrT0dJMU0ySm1aRFU0TlRBd056WTJOelkzWVdOa016TTNPV1JoWTJRMlltTTBOekJrTWpNMU0ySXlNR1V6T1dRd1ltWmlNemRpTW1Saklpd2lkR0ZuSWpvaUluMD0=', 1774257784),
('ntzp14h10Va60BYoobtO8jXWc3ao9MohblUW1jJr', NULL, '2a02:1210:5493:8200:156b:c6:58f3:fd46', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_6_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Mobile/15E148 Safari/604.1', 'ZXlKcGRpSTZJa1ZDWXpFNU9UUnhaRmt3Ukd4VlFsSk1jR1pPTkhjOVBTSXNJblpoYkhWbElqb2llSFF3VEhwdFkyRm5SaXRhVUdGaGFGZEtXREphTDI5RWMwaGlNbUpzVlVNNFFuRnhaM3B3WVV0cVEwRnNNbU41VkZkTGNraEVSRmRIVGs5TE5HTmtVU3N4VGt0TlZsUjBibWhyYVhsM1FtdzNXV1JGUVVoRlowaFplVEJZUm1wbk0wRldhbXg2UVRFNU5rdFZiWEZLTDJwWGVpc3ZWSGRXTUhwcVltcExUbHB6TlVrMFprSTRXQ3RwVEZNd1ZFZzVZbVpTT1U4MWQyTkJlVlZOU1V0S1lXbFFVbUpHVTBaME1UVXhORW80WmxwT2VGSnRkbTlDY2xBMFlXcGpPRTUzWVRkd1FtMUtha3BQYzJ4eE1XbHNOa0pRV2tSbFMybFdaM3BxVWpSM1luZFJRWEpaWTA5dGNHUm9hVFZJVHl0QmQzVkhjVFJyYUU5RldHWlFVazlSWVZveFExWjVWRFI2TTNwNVJIVm9NWGxNZGpkNFUzWXZUR1l5WVZkNk5tdDBZa3BrYWpSeWVEZFBWbVZpUjFCRGJWazRRMU5tWjAxS1ducExRV2hCZGxFaUxDSnRZV01pT2lJNU1EbGlNekprT0RobFl6bGlOalV4TVdFM09XSmpOakE1WlRVNE1EZGtOMk0yWXprME1HSXhaRFkzWlRnM00yTTFaV05pTmpnMk5EWTVZV0kwTmpoa0lpd2lkR0ZuSWpvaUluMD0=', 1774254500),
('o2jWfDgXOIWJ4OjH7ifgr4NWrCGlHhgFeWqtUH2g', NULL, '194.230.146.69', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Mobile Safari/537.36', 'ZXlKcGRpSTZJbTl0VkhkQk9VRjJNRW94TW1OSVIyWjRaMVl6ZEZFOVBTSXNJblpoYkhWbElqb2lNSHBTVHpoNk9WTnBlQzlNYVRrd1ZXTm1UR04wU0ZaVmFrVkdOMlJ1WW5waFkzUnNhMlUzZDFScFVqaEdhbmxUUXpKbmIweFRVR3BaVEhKbVdHa3dORmg0V0ZvMFRVWXpWMWt3VDBSWVpWbDNVWEpJYTB4WFQyeDRZbGtyT1hKWU5Gb3hZVTVqUlZoUWVqVm1VRVZUVGtFMlNGRktLMlJOSzBKNFdXZHlTMFE0YW1zd01ETTNjVk5rVURCUllqY3ZhSEYyVlhaeFowWlJTM1k0VEV3d2QwOVlaVlJqYm0xYVl6VlBVa00wV1VoRFFYZzJjRU16YjFOQ1p6SjZORTE2UkdOWmJHOXVhMHAwU0RWM2JuSnRTelJ6VDJaWlFXUTRLMU5RU0ZONFRXSnBRbXBFVkZwelVFRlRlVEIzZWpjcmJTODRiSFZLYkdsWlQzZDBUazVKYW5CTVoxWmtSblJzU0c1MFdXRlZNVEpXVDFSbFFucFliUzlPY1ZOUFRHTnFSbXAyUW5OV09HWm1TRTl4VVZkbFdEZFRURzFsWTBSRGMwcDFPVFZzTDBWYVYwTjRhbkp0ZDA5bFVrRm5iV2xyV1ZCSFREUkxPRlJ6TWtJd1lWTklPR3BHYVZFMU9WWnFOak5YYlVKS1RYSTBhbU5MZGxSM1VVZDZSVmN5YUd0a1lYUmxSbTgxUnpaWVIxSkZVMUpxVWxRdlZXNXBPSEkxVUVkdVJXVmtjMHcwU1N0S2VFZ3Jlblk1YjB0T1ptOXpXRzFFUmpnMFExRTFObWRwVUU4d1dUTnFiMFZ2TW1acU1uVnRNV2MyWjFadldFSmxWWGM5UFNJc0ltMWhZeUk2SWpNM1lqQmlZbUZpTjJJMllUVm1NV0ZqTW1FeU9URTVNVGcyWm1FeFpESTROVGt4TlRFeFlqazVObU15WkRjek5tVTNaalU0WmpKbU1UUTBOVE15WVdRaUxDSjBZV2NpT2lJaWZRPT0=', 1774256516),
('RJyF1DZyzIuYiXeLQJ9Qk8R6LjEAL9t75uXuiqWj', 1, '82.220.93.54', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:148.0) Gecko/20100101 Firefox/148.0', 'ZXlKcGRpSTZJazlFV2s1R2EzTlpWR3d2ZUVaTVVUVm1kVWcxWjFFOVBTSXNJblpoYkhWbElqb2ljVUozTVN0bVZTc3djM3B1VkVKV2NWZ3JTbVYwWkVkemNITTJZMVY0VW5SaWJraHFaa1Z3UjFjelpYcENMMnhwU1RkSFEyRkROMWRNZFhOS2NVVmhSMWQyTmtRek1XVTVTVFZJTm1kVFNYSkxjbVpPWkdjd1dVVlNZbkl4Y21oTFFqVkNVa2N2TDNOdmMxcEJVVmhqWlhWUk5EbENVRE5vU1ZwSFptVkRPRTkwSzBsT1FtdERTWE5PYzBGR2MwcHdWbkZTWldaRVpHZzBWRUV4THpsRmFHcGpOek14UjB0eGRFZE5VRGxEVlM5VmVXdGxSbXgzU1RWUWNUVnZSRTlsUmxWcGVIRXdSM1kzUzFreE0xWXJSSGxMTjNsbU1ETllTMUl6Y0hsdVNVVjVZbU5hTUdoTmVWRmxjSFowVTBNM1IwZG1aa3BuUTFKYVJYaGtXR3RJUnpOb2VrOXpWVVJ5VjNsaVlucDVMM1pWU2xCcGIwRldkMmcyVFdGbFVHNVhZV3AzUkhFMmJGQnZUbWxtZWtvelEza3JaMXBWYmpZd1JsUkZPSFpQWjNoUEwxWjNNblpMYTBkdkwzZ3hNbkp6YTFaeVJYaHZlU3RzUW5jek5rVndabGt3U25saU16SlZjWE5wTnpOek1sTTVTVEZoV0cwMEwxZFZibWxZYzBsVlFWcEhNaXN3VTNSTGRIbzBZbmRyWW5aaU5FNTJPRzFxU2pSaGIyOWlPRTQwZUVneFExTk5SR00yUnl0dVkybFVTbVJLVm1oWWRXWlZjR3MzUkZwVlNXRTBVMjVCVm1FM1J5dHZiMVZzU2psNFlUa3ZTRVpsYWt0c2QxUnRNVlZHVWtSV2NIaG5OM1phV2pkUk1tOTVOeTlPWkROd1ZXUklPRk5HVkVZNFl6Wk5SRmgyVjA4Mk5HdEtUbFJXY1hSUE9YZFVURzFxZGpOTWJrbHJZVFIyUlhacFRWVmhla2RTTlZWT2RHaHdiVzlWUkZsbk5Ua3hhR3BaYmpkSlduWlliMWRLZFdNNVpYbGxaVVJ0UmxwMFEzTkxkVUl2WnowOUlpd2liV0ZqSWpvaVptVmlNRGMxTjJVeVpEQXdaRGMzWXpnek1USmtObVEzWkdZMFptWTBZVFl5Tnpaall6ZzVZVGczTlRBNVkyWTNORFV6TjJSaE1XTTVOVGswTnpOaU9DSXNJblJoWnlJNklpSjk=', 1774256986),
('RsJBAyfeXN6pFlMZUnUB6bh9r15nR3JUzKKhyWuS', NULL, '2a02:1210:5493:8200:156b:c6:58f3:fd46', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_6_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Mobile/15E148 Safari/604.1', 'ZXlKcGRpSTZJalpUY25GeGRuZGxTRVphWVUxWmJqZFpibUptWkVFOVBTSXNJblpoYkhWbElqb2lPR2RTSzBoNEwwZDRSell6U1daTFZrNVNZV05xU1dGSWFuZE1SMEZ6T0d4Rk9WVldhR3BCWVdwclNWZHBaRE5JZGpsdlJFd3ZkR3hXUzA1SFZrNWpVVXRXZEV4ck1URjBTV1ZDWjNsS1kwd3JMeXQzV21KbWJUaEdlVVYwWjA1c1pHMU9OM1k0WW5abldEQkJVbVp6TW5jd05tTm9WaXRvYkhGVGIyOHJNa3hTTTJ0RlNUQk9ObEl3ZW1OQ05IUTRjSGRqU3pWb2JGbFNTbWh5UWtSb2VGZFlUVEpzZHpFdmRXUk5VQ3RoWTNsa1IxaFpUV3BFUzNOa1NqRk9iMXBtT1dSbVMzUXpZM1JKTld4aVRqSlhTbE5VY0c1RVQyUnVhVmcyWm5SVFNYTnZkbTFsTWxKMVUxUkRaVkJpVTB3eU9WUklPU3R6TlRkcU5uaHllRmRQWWt0QmFtMWlUbWRGVWxOUmJtbFhjblZYY1dFd2VVMXdVRkJLVlNzME1VNXhOSFFyVFdSS1RGTTJlbTVzTDJZd2JGSkdOVzk0TjB4b2NqVTJPRzR6WlRaR1pHWXZURlp3UjNCMEwybFNUMWxaVVVZMmFESlhNR2h4ZGtSM2JqQndiV1J2U0VWcVMyaGthRzl1Ym0xbFNpdFhOekE1ZUhSUVZFVnVPWGxLTURSNFV6SndOazE1Ym5wcWJVZ3ZjaXMzUjNoQlJFMHlMM2N3ZVVGb1pFVjBXRWxLWlU1NFYyaERPVkpGWVRsdEwwVnVSbFpqVjJockwza3ZNRXh6YTBkeGRTSXNJbTFoWXlJNkltWXpOemhpTm1RellqYzFNbUpqTmpWbFpEQmpOV0l6TW1GaU16STBNRFUwTkRWa04yWTJOemcwTUROa1pUQTVaV0pqTURFeE56UXlNemszTjJabU1XVWlMQ0owWVdjaU9pSWlmUT09', 1774254617),
('Tdkrxl06ZrJze2BaGZHr9YK77EW4fpMxGFPDQwVB', NULL, '129.194.89.239', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', 'ZXlKcGRpSTZJbU1yVlZKc1dqZERaVWxsVmxKalZuVlhibWQ1UTBFOVBTSXNJblpoYkhWbElqb2lVMlptUW1GWFoyMUVPVkY1UkRnd2RtMWFkMDVwVUU1MVRtYzFkM0pwWlhKTUsydHRVWEp3ZVhkNk1IUlVVV0YwTjJ4MlIxZG5RVUoxWW1ZeVdTdG9OVlIwWTBoRU1HTjBLelp3SzNFMWNHNWxWSFJDWmtOVlZXOWpVRmxCT1ROQ2IyazJZMjQzUm1wallTdERiVkV2WTNOdFMzRTRZaTl4YVVOdWFVSlBZV3RpV1Vob01UQkJPVmxpYkdaM1V6Z3lOVVo1VFdKQmREZFhVVE41U1V0UVJHZDJOR0Y2UzFOUmQybDRVbWRMYWl0WlMwTnNVVWhITW1GVVVWSXhhM05VWnpVNFZrb3ZURGQ0ZFZOb1pXTlpLMjlUWjJ0RlFXWmtSeXR6YVVnd2VFZFFRMGRFU0hkeFNHWjVSbk5HUkRCQ1FqSnFiU3RqUjB4Vk1FVTFNR3d4TWxsaFUxZ3ZNWGRZVVRONVpXcElLMGhHSzNSak5XaGhaWFJNYm5sclJ6WkZReXQ2Y1VoWFRFeDNUbEV2ZG04NVIzQkhVMVo1WWtGaVlubHdUalZwTldNeGFFUlBOVkZMZVRoUVNtNXZkVTA0TjNoTGNqZDNORFJRVURCTVV6UmxlVTFuWjA4ME0xTk5RM3BFZVV4UlZqQk5TRlJPT0hsM2RrUkxUMEpVTW5KRFptZFhhREJEZUhkYVkzZG1jbWhDUWtKa1ZsVlhOREp4U0VocUx6SjZiRzF1TUVKakwyWXdORXR1U1RGNE5Hb3JTVm9yTWprMVJUQnVXRTh2Wm5WWU1DSXNJbTFoWXlJNkltTm1aamczTlRjeU1XVmtNekZqT1RkaFlUVmlOV1l4TURnMFlUZG1ZMkkzWm1KbFltTXdNR0ZqTVdVNVlURXdNR0pqWkRJM09ETXdZamN4WmpReVpEY2lMQ0owWVdjaU9pSWlmUT09', 1774257791),
('Yvnmq5VLZQXLttrWV7r3UBs3jRnBRSfq6U5vpEvz', NULL, '54.38.234.228', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36 OPR/117.0.0.0', 'ZXlKcGRpSTZJbGw2WW5OMEt6Qk9ZV2c0YXk5Q1JERkJVbFp5ZEdjOVBTSXNJblpoYkhWbElqb2lOWEpRU25KalRVWnBVMnhPZGswM00ybDVUSFJKVEcxWlZVbDJabTFwU25WMWJVOWtkMHcwVERSQmMzRjJWa05uUVhJM2EwMVFTV2xzZHpZME5FSlVXR0pCSzBKU1lqSlFaa2MxVFdsRVMydG5OelJKYzBsaFYxTnNPR3BzTlRWa2NIQjJUWEp3YWpWMWRXeEpNakpMZUc1bFNVeHFiRTFTVldaMVZIUnRabUZaTmpodE5YZHBkelp5Vm5OcFdrWnRTRGR4TTJaV0t6UmxjRlpaYjA1Wk1GRkNjbVZZUzJkcGRGbGtOVTlYUTBWWWFrOXpZbkpQUkVoeVRYVTRNMlZsTVhkc2VucGhjMXBCY2xWSFltSkVUWEpRYjJWbE9UUXZRWGxMWlVaYWIyMXNNVXAyWnpSS1RFSmFkMWRuYzFkT1ZWZFBNVFZKZERnMmVVeFFNazh6TkZKV2N6SkRWVWRQWW5rMFZqRlpRWFptUzBremQyRTFPRVJFVURack4xWTJUVEpLVHpKRFUweHNjWEpOU1hoMGFXeFROR1pOU21sRWMyVXlhVEEyYjNvaUxDSnRZV01pT2lKbE5qTmhNMkl5WXpJMk5ETXhZMk5tTjJKbU0yUmxPRFU1Tmpnek9HWXpZMlZtTURrek1XWmtaV1JtWmpZNFpXTmxNalkwWXpNeFpXRTFOMkZpTldVNElpd2lkR0ZuSWpvaUluMD0=', 1774257785);

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
(1, 'Oliver Wagner', 'oliver.wagner@smartgecko.ch', NULL, '$2y$12$hZLW2DRdSSRI3/HX6KR/t.BpYdQkWFzP5yITNKOHnh01u/ONWvTYO', 'lwtY6K51QkbkgEUAHgPAfIZqo4yZG0XYHDjkF3YzdPO7mjmUIjgOAnT5MuYF', 'A', NULL, '2026-03-22 12:54:36', '2026-03-22 12:54:36'),
(2, 'Livia Wagner', 'livia.wagner@gmail.com', NULL, '$2y$12$fvSMXmpeE9merfLPy4CqJurtFka9qDJGejjbboCGVeruIZxy/7RwW', NULL, 'A', NULL, '2026-03-22 12:54:36', '2026-03-22 12:54:36'),
(3, 'Giulia Antonioli', 'giulia.antonioli@gmail.com', NULL, '$2y$12$pdr7T/So048fVqDCzgPRceAeNcqmDZ4JJ9z51u6xu4SlvMYuoNebO', NULL, 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 14:17:45'),
(4, 'Dune Bourquin', 'dune.bourquin@gmail.com', NULL, '$2y$12$HHf5p0X2xdQJ5z/jtOKYAO8g2t56HW3w9l8deqXWHkj58XDyPzubi', 'NxfEa1Zp1pMx4TqZ5qCvxzeeYRGHaril4wqi1xeI3jVrGeiXgTxq0At4rj8B', 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 15:16:47'),
(5, 'Olivia Chassot', 'olivia.a.chassot@gmail.com', NULL, '$2y$12$7NE4L1PvSMUDZBgZqYapiuqFLpkzQH7u7DeSOf8obBLpeKGDek83y', NULL, 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 14:17:45'),
(6, 'Caroline Gaillard', 'carolinegaillard@gmail.com', NULL, '$2y$12$vnCOFRHbxSrHvhNEjiIXwOUHWkh8R3DJuIeULzEIHKq/FukWaUtku', NULL, 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 14:17:45'),
(7, 'Sofia Passaretti', 'sofiapassaretti@gmail.com', NULL, '$2y$12$7MTuUs/NviTOyRZCcA3q1.2c7dNa9w3R5qi6xAiBol46YP7uxzn8.', NULL, 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 14:17:45'),
(8, 'Marta Rodriguez', 'marta.rodriguez29@hotmail.co.uk', NULL, '$2y$12$52zJYyQ8AKh4qyXOO64aCOSllMfEELsnsVi64NGxIlArsVX9IDRIi', NULL, 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 14:17:45'),
(9, 'Marguerite Vernet', 'margueritevernet@outlook.com', NULL, '$2y$12$KEIljEOo2Lne4NWN4BbiN.GfgWG8Um.3s52dlFGYpIWay4BnRzcfS', NULL, 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 14:17:45'),
(10, 'Anne Zendali Dimopoulos', 'azendali@infomaniak.ch', NULL, '$2y$12$D7ZnRExsu3OAPXV7fTqGb.Hdb2cTlLfQmf1npLK.R33IcVde7mmrO', NULL, 'A', NULL, '2026-03-22 14:17:45', '2026-03-22 14:17:45');

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
  ADD UNIQUE KEY `member_number` (`member_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `uk_member_number` (`member_number`),
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT für Tabelle `events_audit`
--
ALTER TABLE `events_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT für Tabelle `event_member`
--
ALTER TABLE `event_member`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT für Tabelle `event_member_audit`
--
ALTER TABLE `event_member_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT für Tabelle `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `invoices_audit`
--
ALTER TABLE `invoices_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `invoice_event`
--
ALTER TABLE `invoice_event`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `invoice_lines`
--
ALTER TABLE `invoice_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT für Tabelle `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT für Tabelle `members_audit`
--
ALTER TABLE `members_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT für Tabelle `member_magic_tokens`
--
ALTER TABLE `member_magic_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT für Tabelle `member_phones`
--
ALTER TABLE `member_phones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT für Tabelle `member_phones_audit`
--
ALTER TABLE `member_phones_audit`
  MODIFY `audit_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT für Tabelle `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT für Tabelle `portal_audit_log`
--
ALTER TABLE `portal_audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
-- Constraints der Tabelle `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
