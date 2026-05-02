-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 01:55 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agriflow9`
--

-- --------------------------------------------------------

--
-- Table structure for table `annonces`
--

CREATE TABLE `annonces` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('LOCATION','VENTE') NOT NULL,
  `statut` enum('DISPONIBLE','RESERVEE','LOUEE','VENDUE','EXPIREE') DEFAULT 'DISPONIBLE',
  `prix` decimal(10,2) NOT NULL,
  `unite_prix` varchar(20) DEFAULT 'jour',
  `categorie` varchar(100) DEFAULT NULL,
  `marque` varchar(100) DEFAULT NULL,
  `modele` varchar(100) DEFAULT NULL,
  `annee_fabrication` int(11) DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `proprietaire_id` int(11) NOT NULL,
  `date_debut_disponibilite` date DEFAULT NULL,
  `date_fin_disponibilite` date DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `avec_operateur` tinyint(1) DEFAULT 0,
  `assurance_incluse` tinyint(1) DEFAULT 0,
  `caution` decimal(10,2) DEFAULT 0.00,
  `conditions_location` text DEFAULT NULL,
  `quantite_disponible` int(11) DEFAULT 0,
  `unite_quantite` varchar(20) DEFAULT 'kg',
  `image_url` varchar(255) DEFAULT NULL,
  `localisation_normalisee` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `type`, `statut`, `prix`, `unite_prix`, `categorie`, `marque`, `modele`, `annee_fabrication`, `localisation`, `latitude`, `longitude`, `proprietaire_id`, `date_debut_disponibilite`, `date_fin_disponibilite`, `date_creation`, `date_modification`, `avec_operateur`, `assurance_incluse`, `caution`, `conditions_location`, `quantite_disponible`, `unite_quantite`, `image_url`, `localisation_normalisee`) VALUES
(19, 'Tracteur John Deere 6120M', 'Tracteur puissant 120 CV, parfait pour les grandes exploitations. Entretien regulier, pneus neufs. Disponible avec ou sans operateur.', 'LOCATION', 'DISPONIBLE', 250.00, 'jour', 'Tracteur', NULL, NULL, NULL, 'Sousse', NULL, NULL, 39, '2026-01-01', '2026-12-31', '2026-02-23 12:10:34', '2026-03-02 08:51:35', 1, 0, 500.00, NULL, 0, 'piece', NULL, NULL),
(20, 'Engrais NPK 15-15-15 Premium', 'Engrais equilibre haute qualite pour toutes cultures. Sacs de 50kg, livraison possible sur Sousse et environs.', 'VENTE', 'DISPONIBLE', 45.00, 'sac', 'Engrais', NULL, NULL, NULL, 'Sousse', NULL, NULL, 39, '2026-01-01', '2026-12-31', '2026-02-23 12:10:34', '2026-02-23 12:10:34', 0, 0, 0.00, NULL, 100, 'sac', NULL, NULL),
(21, 'Systeme Irrigation Goutte a Goutte', 'Kit complet irrigation goutte a goutte pour 1 hectare. Tuyaux, goutteurs, filtre et programmateur inclus.', 'VENTE', 'DISPONIBLE', 1200.00, 'unite', 'Irrigation', NULL, NULL, NULL, 'Sousse', NULL, NULL, 39, '2026-01-01', '2026-12-31', '2026-02-23 12:10:34', '2026-02-23 12:10:34', 0, 0, 0.00, NULL, 5, 'unite', NULL, NULL),
(22, 'Moissonneuse-Batteuse Claas Lexion', 'Moissonneuse-batteuse professionnelle, ideale pour ble et orge. Capacite tremie 9000L.', 'LOCATION', 'DISPONIBLE', 800.00, 'jour', 'Moissonneuse', NULL, NULL, NULL, 'Tunis', NULL, NULL, 70, '2026-03-01', '2026-09-30', '2026-02-23 12:10:34', '2026-03-02 08:51:51', 1, 0, 1000.00, NULL, 2, 'piece', NULL, NULL),
(24, 'Pulverisateur Agricole 600L', 'Pulverisateur traine 600 litres avec rampe 12m. Parfait pour traitement phytosanitaire.', 'LOCATION', 'DISPONIBLE', 120.00, 'jour', 'Outil', NULL, NULL, NULL, 'Tunis', NULL, NULL, 70, '2026-01-01', '2026-12-31', '2026-02-23 12:10:34', '2026-03-02 08:52:17', 0, 0, 200.00, NULL, 1, 'piece', NULL, NULL),
(25, 'Olives Chemlali Bio - Recolte 2025', 'Olives fraiches variete Chemlali, agriculture biologique. Ideales pour huile ou conserve.', 'VENTE', 'DISPONIBLE', 8.00, 'kg', 'Fruits', NULL, NULL, NULL, 'Sfax', NULL, NULL, 70, '2026-01-01', '2026-03-31', '2026-02-23 12:10:34', '2026-03-02 08:52:30', 0, 0, 0.00, NULL, 500, 'kg', NULL, NULL),
(26, 'Charrue Reversible 3 Socs', 'Charrue reversible 3 socs pour labour profond. Compatible tracteurs 80-120 CV.', 'LOCATION', 'DISPONIBLE', 80.00, 'jour', 'Outil', NULL, NULL, NULL, 'Sfax', NULL, NULL, 70, '2026-01-01', '2026-12-31', '2026-02-23 12:10:34', '2026-03-02 08:52:46', 0, 0, 100.00, NULL, 1, 'piece', NULL, NULL),
(27, 'Dattes Deglet Nour Premium', 'Dattes Deglet Nour premiere qualite, recolte manuelle. Calibre AAA, caisses de 5kg.', 'VENTE', 'DISPONIBLE', 25.00, 'kg', 'Fruits', NULL, NULL, NULL, 'Tozeur', NULL, NULL, 70, '2026-01-01', '2026-06-30', '2026-02-23 12:10:34', '2026-03-02 08:53:04', 0, 0, 0.00, NULL, 300, 'kg', NULL, NULL),
(28, 'Trakteur', 'Titre de l\'annonce : Trakteur de qualité pour les agriculteurs tunisiens\n\nCatégorie : Trakteur\n\nDescription :\n\nVous cherchez un trakteur fiable et performant pour votre exploitation agricole ? Nous sommes ravis de vous présenter notre trakteur de qualité, conçu pour répondre aux besoins spécifiques des agriculteurs tunisiens.\n\nCe trakteur robuste et polyvalent est équipé de moteur puissant, permettant une efficacité maximale dans les travaux de labour, de moissonnage, de transport et d\'autres opérations agricoles. Son système de transmission et de direction est conçu pour garantir une stabilité et une facilité d\'utilisation, même en conditions difficiles.\n\nLes caractéristiques clés de notre trakteur :\n\n- Puissance motorisée : [puissance du moteur]\n- Capacité de déplacement : [capacité de déplacement]\n- Système de transmission : [système de transmission]\n- Système de direction : [système de direction]\n- Capacité de charge : [capacité de charge]\n\nNotre trakteur est équipé de toutes les fonctionnalités essentielles pour un rendement optimal, y compris :\n\n- Un système de freinage performant pour une sécurité maximale\n- Un système de ventilation pour une efficacité accrue\n- Un système de lubrification automatique pour une durée de vie prolongée\n\nEn choisissant notre trakteur, vous bénéficierez d\'une expérience agricole plus efficace, plus rentable et plus durable. N\'hésitez pas à nous contacter pour plus d\'informations ou pour prendre rendez-vous pour une visite.\n\nPrix : [prix du trakteur]\n\nEmplacement : [emplacement du trakteur]\n\nContact : [informations de contact]', 'LOCATION', 'DISPONIBLE', 450.00, 'jour', 'trakteur', NULL, NULL, 0, 'Tunis', NULL, NULL, 78, NULL, NULL, '2026-03-01 22:40:43', '2026-03-01 22:49:54', 0, 0, 0.00, NULL, 0, 'kg', NULL, NULL),
(29, 'Tracteur', 'Titre de l\'annonce : Tracteur agricole de haute qualité\n\nCatégorie : Matériel agricole\n\nDescription détaillée :\n\n**Tracteur agricole de haute qualité**\n\nNous proposons un tracteur agricole robuste et performant pour répondre aux besoins des agriculteurs tunisiens. Ce tracteur est équipé de caractéristiques avancées pour améliorer la productivité et la rentabilité de vos opérations agricoles.\n\n**Caractéristiques clés :**\n\n- Puissance forte et fiable pour une utilisation intensive\n- Transmission hydraulique pour un fonctionnement précis et efficace\n- Système de sécurité avancé pour garantir votre sécurité et celle de vos équipages\n- Capacité d\'intégration de diverses machines agricoles pour une utilisation flexible\n- Conception robuste pour résister aux conditions climatiques difficiles\n\n**Avantages :**\n\n- Meilleure productivité et rentabilité\n- Moins de temps et d\'énergie consommés\n- Possibilité d\'utiliser diverses machines agricoles\n- Conception robuste pour une longue durée de vie\n\n**Prix compétitif**\n\nNous proposons ce tracteur agricole de haute qualité à un prix compétitif. N\'hésitez pas à nous contacter pour en savoir plus sur nos options de financement et de maintenance.\n\n**Contactez-nous**\n\nPour plus d\'informations ou pour obtenir une démonstration du tracteur, n\'hésitez pas à nous contacter à AgriFlow. Nous sommes à votre disposition pour répondre à vos questions et vous aider à trouver la solution qui convient le mieux à vos besoins.', 'LOCATION', 'DISPONIBLE', 150.00, 'jour', 'Tracteurs', NULL, NULL, 0, 'Tunis', NULL, NULL, 70, NULL, NULL, '2026-03-02 09:18:29', '2026-03-02 09:23:36', 0, 0, 0.00, NULL, 0, 'pièce', NULL, NULL),
(31, 'Drone agricole', 'Drone agricole', 'LOCATION', 'DISPONIBLE', 1200.00, 'jour', 'Drones', NULL, NULL, 0, 'TUNISIE', NULL, NULL, 70, NULL, NULL, '2026-03-02 09:32:05', '2026-03-02 09:32:05', 0, 0, 0.00, NULL, 2, 'pièce', NULL, NULL),
(32, 'Trax agriculteur', 'trax agriculteur', 'LOCATION', 'DISPONIBLE', 150.00, 'jour', 'trax', NULL, NULL, 0, 'kelibia', NULL, NULL, 39, NULL, NULL, '2026-03-02 09:38:58', '2026-03-02 09:40:49', 0, 0, 0.00, NULL, 0, 'pièce', NULL, NULL),
(33, 'tractour', 'aaaaaaaaaaaaaaaaaaaaabbbbbbbbbbbbbbbbbbb', 'VENTE', 'VENDUE', 2000.00, 'jour', 'Matériel', NULL, NULL, NULL, 'nabeul', NULL, NULL, 90, NULL, NULL, '2026-04-05 22:29:53', '2026-04-05 22:32:26', 0, 0, 0.00, NULL, 0, 'kg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSW6fVE3nNZPVwM6DAa8upauWxYVUodv1JPhw&s', NULL),
(34, 'Location de drone professionnel1', 'Drone équipé de caméra haute définition pour des prises de vues professionnelles. Utilisé pour des tournages aériens, inspection de bâtiments, etc.', 'LOCATION', 'LOUEE', 100.00, 'jour', 'Équipement photo et vidéo', NULL, NULL, NULL, 'NABEUL', 36.45128970, 10.73559150, 78, NULL, NULL, '2026-04-15 23:52:28', '2026-04-16 00:10:36', 0, 0, 0.00, NULL, 0, 'kg', 'https://ichef.bbci.co.uk/ace/standard/976/cpsprodpb/54A5/production/_87996612_87996608.jpg', 'Nabeul, Al Hadaek, Délégation Nabeul, Gouvernorat Nabeul, 8000, Tunisie'),
(35, 'Location d\'une caméra photo professionnelle', 'Caméra photo professionnelle de haute qualité pour location. Équipement complet avec objectif et accessoires. Idéal pour les événements, les shootings et les reportages.', 'LOCATION', 'DISPONIBLE', 200.00, 'jour', 'Équipement photo et vidéo', NULL, NULL, NULL, 'TUNIS, TUNISIE', 33.84394080, 9.40013800, 78, NULL, NULL, '2026-04-16 00:03:00', '2026-04-16 00:03:00', 0, 0, 0.00, NULL, 10, 'kg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTrlbiz5LcwN7UQAfHkir3k9Z085kQ67mEecw&s', 'Tunisie'),
(36, 'Motoculteur à louer', 'Motoculteur polyvalent pour travaux de jardinage et d\'entretien de terrain. Propulsé par un moteur puissant et fiable.', 'LOCATION', 'DISPONIBLE', 5000.00, 'jour', 'Machines agricoles', NULL, NULL, NULL, 'NABEUL', 36.45128970, 10.73559150, 78, NULL, NULL, '2026-04-18 22:41:10', '2026-04-18 22:51:46', 0, 0, 0.00, NULL, 1, 'kg', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ5E9rY1jO8Wek3mICrmHLjWI8FMpu3_NNjoA&s', 'Nabeul, Al Hadaek, Délégation Nabeul, Gouvernorat Nabeul, 8000, Tunisie'),
(37, 'Machine agricole à louer', 'Machine agricole robuste et fiable, idéale pour les travaux extérieurs. Elle est équipée de tous les accessoires nécessaires pour une utilisation optimale.', 'LOCATION', 'DISPONIBLE', 200.00, 'jour', 'Machines agricoles', NULL, NULL, NULL, 'Sousse', 35.82882840, 10.64052540, 70, NULL, NULL, '2026-04-20 10:59:14', '2026-04-20 10:59:14', 0, 0, 0.00, NULL, 1, 'kg', 'https://i.ytimg.com/vi/nnIiiQ0M7F8/sddefault.jpg', 'Sousse, Délégation Sousse Medina, Gouvernorat Sousse, Tunisie');

-- --------------------------------------------------------

--
-- Table structure for table `annonce_photos`
--

CREATE TABLE `annonce_photos` (
  `id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `url_photo` varchar(500) NOT NULL,
  `ordre` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `annonce_photos`
--

INSERT INTO `annonce_photos` (`id`, `annonce_id`, `url_photo`, `ordre`) VALUES
(10, 19, 'https://images.unsplash.com/photo-1530267981375-f0de937f5f13?w=400', 0),
(11, 20, 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400', 0),
(12, 21, 'https://images.unsplash.com/photo-1563514227147-6d2ff665a6a0?w=400', 0),
(13, 22, 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=400', 0),
(14, 23, 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=400', 0),
(15, 24, 'https://images.unsplash.com/photo-1592982537447-6f2a6a0c7c10?w=400', 0),
(16, 25, 'https://images.unsplash.com/photo-1445282768818-728615cc910a?w=400', 0),
(17, 26, 'https://images.unsplash.com/photo-1500595046743-cd271d694d30?w=400', 0),
(18, 27, 'https://images.unsplash.com/photo-1590779033100-9f60a05a013d?w=400', 0),
(19, 28, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTGAsfif4Q0lIMxU-fEVhCEitlm3Kp7-dTn5A&s', 0),
(20, 29, 'https://tse1.mm.bing.net/th/id/OIP.p9kAf3yGs43lFZiqbb4CsAHaEo?rs=1&pid=ImgDetMain&o=7&rm=3', 0),
(21, 30, 'https://www.excavator.ch/wp-content/uploads/strikingr/images/87_trax-369x277.jpg', 0),
(22, 31, 'https://m.media-amazon.com/images/I/61LlywY7+zL._AC_SL1500_.jpg', 0),
(23, 32, 'https://www.gam.com.tn/wp-content/uploads/2021/04/CUKUROVA-883.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `collab_applications`
--

CREATE TABLE `collab_applications` (
  `id` bigint(20) NOT NULL,
  `request_id` bigint(20) NOT NULL,
  `candidate_id` bigint(20) NOT NULL DEFAULT 1,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `years_of_experience` int(11) NOT NULL DEFAULT 0,
  `motivation` text NOT NULL,
  `expected_salary` decimal(10,2) DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_applications`
--

INSERT INTO `collab_applications` (`id`, `request_id`, `candidate_id`, `full_name`, `phone`, `email`, `years_of_experience`, `motivation`, `expected_salary`, `status`, `applied_at`, `updated_at`) VALUES
(3, 2, 3, 'Mohamed Slimani', '98765432', 'mohamed@example.com', 2, 'Je cherche à apprendre et je suis très sérieux dans mon travail.', 35.00, 'APPROVED', '2026-02-20 00:02:07', '2026-02-21 03:23:17'),
(5, 9, 1, 'aa', '0000000', 'bb@bb.com', 5, 'je suis aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 50.00, 'APPROVED', '2026-02-22 14:39:53', '2026-02-22 14:42:28'),
(8, 8, 1, 'bbbbbbbbbbbbb', '1111111111111', 'bb@bb.bb', 5, 'peut etre  wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww', 100.00, 'PENDING', '2026-02-22 15:03:13', '2026-02-22 15:03:13'),
(9, 6, 1, 'ccc', '00000000', 'aaa@aaa.c', 0, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 10.00, 'PENDING', '2026-02-22 15:24:14', '2026-02-22 15:24:14'),
(11, 11, 1, 'ee', '0000000', 'cccc@cc.cc', 5, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 44.00, 'PENDING', '2026-02-23 02:37:20', '2026-02-23 02:37:20'),
(13, 10, 1, 'aaa', '00000000', 'zzz@zz.v', 5, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 50.00, 'APPROVED', '2026-02-23 09:25:18', '2026-02-23 09:28:23'),
(14, 12, 1, 'yakine', '28121078', 'yakkine@iii.fr', 5, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 50.00, 'REJECTED', '2026-02-26 03:10:37', '2026-02-26 03:12:23'),
(15, 15, 2, 'aymen gh', '00000000', 'yaki@fff.com', 5, 'qqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqqq', 50.00, 'PENDING', '2026-02-27 03:26:29', '2026-02-28 03:26:29'),
(70, 2, 1, 'aymen ghabi', '00000000', 'ayme,@yy.vom', 8, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 50.00, 'APPROVED', '2026-02-28 03:29:50', '2026-02-26 03:41:04'),
(71, 2, 71, 'ahmed garci', '11111111', 'ahmed@gmail.com', 0, 'je suis motivé , je suis passionné eeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeeee', 90.00, 'PENDING', '2026-02-27 03:31:56', '2026-02-26 03:31:56'),
(74, 2, 74, 'imen ben kilani', '28121457', 'imen@imen.cpm', 10, 'motivé motivé  motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé motivé ', 10.00, 'PENDING', '2026-02-27 03:38:15', '2026-02-27 03:38:15'),
(75, 14, 1, 'ayoub', '111111111', 'ayy@yy.com', 5, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 20.00, 'APPROVED', '2026-02-26 23:09:29', '2026-02-26 23:10:03'),
(76, 15, 1, 'Ayoub', '20305177', 'ayoub.maatoug@ipeib.ucar.tn', 6, 'lettre de motivation\niiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii', 30.00, 'PENDING', '2026-02-27 16:03:40', '2026-02-27 16:03:40'),
(77, 16, 1, 'sami', '20305177', 'maatougsami25@gmail.com', 10, 'yesssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssssss', 50.00, 'PENDING', '2026-02-27 16:09:28', '2026-02-27 16:09:28'),
(78, 17, 76, 'Yakine Sahli', '23654987', 'yakinesahli48@gmail.com', 15, 'sérieux et motivé aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 0.00, 'APPROVED', '2026-03-01 16:10:42', '2026-03-01 16:40:47'),
(79, 18, 63, 'Maatoug Sami', '96140807', 'maatougsami25@gmail.com', 20, 'motivé hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 50.00, 'PENDING', '2026-03-01 16:42:40', '2026-03-01 16:42:40'),
(80, 19, 76, 'yakine', '22222222', 'yakinesahli48@gmail.com', 10, 'motivé\nhhhhhhhhhhh22222222222222222222222222222222222hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh222222222', 50.00, 'APPROVED', '2026-03-02 01:25:21', '2026-03-02 01:26:57'),
(81, 21, 63, 'sami', '12345788', 'aa@aa.com', 5, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 50.00, 'PENDING', '2026-03-02 10:09:45', '2026-03-02 10:09:45'),
(82, 21, 77, 'yaki,e', '44444444', 'iii@jjj.com', 13, 'je suis motivé passionné aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 10.00, 'PENDING', '2026-03-02 10:14:32', '2026-03-02 10:14:32'),
(83, 22, 63, 'MAATOUG SAMI', '20222222222222222222', 'maatougsami25@gmail.com', 8, 'yes', 20.00, 'APPROVED', '2026-04-17 00:14:18', '2026-04-17 00:16:25');

-- --------------------------------------------------------

--
-- Table structure for table `collab_requests`
--

CREATE TABLE `collab_requests` (
  `id` bigint(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(100) NOT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `needed_people` int(11) NOT NULL DEFAULT 1,
  `salary` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'PENDING',
  `requester_id` bigint(20) NOT NULL DEFAULT 1,
  `publisher` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `collab_requests`
--

INSERT INTO `collab_requests` (`id`, `title`, `description`, `location`, `latitude`, `longitude`, `start_date`, `end_date`, `needed_people`, `salary`, `status`, `requester_id`, `publisher`, `created_at`, `updated_at`) VALUES
(8, 'Récolte oranges ', 'on recherche ouvrier serieux', 'Bizerte', NULL, NULL, '2026-03-06', '2026-03-08', 1, 70.00, 'APPROVED', 1, 'Ali Ben Ahmed', '2026-02-21 03:29:36', '2026-02-21 03:31:09'),
(9, 'feunouilles', 'besoin dagriculteurs', 'Bizerte', NULL, NULL, '2026-02-24', '2026-03-01', 1, 40.00, 'APPROVED', 1, 'Ali Ben Ahmed', '2026-02-22 14:35:10', '2026-02-22 14:36:22'),
(11, 'recolte fraise', 'recherche', 'Bizerte', NULL, NULL, '2026-02-24', '2026-02-25', 1, 55.00, 'APPROVED', 1, 'Ali Ben Ahmed', '2026-02-23 02:36:14', '2026-02-23 02:36:42'),
(12, 'Olives recolte', 'recherche ', 'Azmour, Délégation Kélibia, Gouvernorat Nabeul, 8055, Tunisie', 36.9279390, 11.0137939, '2026-02-27', '2026-03-01', 1, 50.00, 'APPROVED', 1, 'Ali Ben Ahmed', '2026-02-26 01:59:52', '2026-02-26 02:00:14'),
(13, 'Plantation des Fraises', 'on cherche plantation fraise ', 'Sidi Ahmed, Délégation Bizerte Sud, Gouvernorat Bizerte, Tunisie', 37.2740528, 9.7229004, '2026-02-27', '2026-03-01', 1, 10.00, 'REJECTED', 1, 'Ali Ben Ahmed', '2026-02-26 03:20:11', '2026-04-09 13:21:30'),
(14, 'Recolte des pommes ', 'on cherche des personnes seriesux pou......', 'Cherichira, Délégation Essouassi, Gouvernorat Mahdia, Tunisie', 35.2994355, 10.5249023, '2026-02-28', '2026-03-03', 2, 53.00, 'APPROVED', 1, 'Ali Ben Ahmed', '2026-02-26 23:06:37', '2026-02-26 23:07:56'),
(15, 'recolte oranges', 'on cherche', 'Habib Thameur, Délégation El Hamma, Gouvernorat Gabès, Tunisie', 33.8521697, 9.6679688, '2026-02-28', '2026-03-01', 1, 44.00, 'APPROVED', 1, 'Ali Ben Ahmed', '2026-02-26 23:31:18', '2026-02-26 23:31:41'),
(16, 'Récolte tomate ', 'illi yji ', 'Sidi Harreth, Délégation Kasserine Sud, Gouvernorat Kasserine, Tunisie', 35.2456191, 8.8165283, '2026-02-28', '2026-03-05', 2, 440.00, 'APPROVED', 1, 'Ali Ben Ahmed', '2026-02-27 16:04:09', '2026-02-27 16:05:57'),
(17, 'Récolte pomme de terre ', 'à la recherche d\'un ouvrier ', 'عين فرنة, معتمدية برقو, ولاية سليانة, تونس', 36.1733569, 9.5800781, '2026-03-11', '2026-03-26', 10, 40.00, 'APPROVED', 78, 'AYOUB MAATOUG', '2026-03-01 16:05:37', '2026-03-01 16:07:42'),
(18, 'Récolte Olive', 'Récolte Olive ', 'الطريق الجهوية جبنيانة - المحرس, شعلاب, معتمدية منزل شاكر, ولاية صفاقس, تونس', 35.0389920, 10.5249023, '2026-03-02', '2026-03-31', 9, 35.00, 'APPROVED', 78, 'AYOUB MAATOUG', '2026-03-01 16:39:02', '2026-03-01 16:39:50'),
(19, 'Récolte fjel', 'yes', 'نهج بيروت, التعمير1, التعمير, معتمدية أريانة المدينة, ولاية أريانة, 2080, تونس', 36.8620427, 10.1953125, '2026-03-03', '2026-03-27', 4, 40.00, 'APPROVED', 74, 'Maatoug Ayoub', '2026-03-02 01:18:32', '2026-03-02 01:23:57'),
(20, 'Récolte Fjel 2222', 'azerty', 'بوعطوش, معتمدية الحامة, ولاية قابس, تونس', 34.0890613, 9.4482422, '2026-03-06', '2026-03-26', 8, 80.00, 'REJECTED', 74, 'Maatoug Ayoub', '2026-03-02 01:21:23', '2026-03-02 01:24:01'),
(21, 'Recolte des tomates', 'on recherche des agriculteurs', 'جيملة, معتمدية زغوان, ولاية زغوان, تونس', 36.4831406, 10.2447510, '2026-03-03', '2026-03-10', 1, 40.00, 'APPROVED', 76, 'sahli yakine', '2026-03-02 10:06:13', '2026-03-02 10:06:58'),
(22, 'Récolte batata', 'on  cherche une agriiculteur', 'Ras Jebel', 37.2164590, 10.1304930, '2026-04-18', '2026-04-26', 4, 50.00, 'APPROVED', 78, 'AYOUB MAATOUG', '2026-04-17 00:11:31', '2026-04-17 00:16:25'),
(23, 'Récolte batata', '**Récolte de batata de haute qualité**\n\nDécouvrez notre sélection de récoltes de batata de haute qualité, cultivées avec soin et attention dans les meilleures régions agricoles de Tunisie. Nos batata sont récoltées à la perfection, offrant une saveur unique et une texture croustillante. Elles sont idéales pour les cuisines traditionnelles tunisiennes, mais aussi pour les recettes modernes et créatives.\n\nNotre équipe d\'agriculteurs expérimentés travaille dur pour garantir la qualité et la sécurité de nos produits. Nous utilisons des méthodes de culture respectueuses de l\'environnement et des pratiques agricoles durables pour minimiser l\'impact sur la planète.\n\nEn achetant nos récoltes de batata, vous soutenez les agriculteurs locaux et contribuez à la promotion de l\'agriculture durable en Tunisie. Nous sommes fiers de partager nos produits avec vous et de vous offrir une expérience de qualité supérieure.\n\n**Caractéristiques de nos récoltes de batata :**\n\n- Qualité exceptionnelle\n- Saveur unique et texture croustillante\n- Cultivées avec soin et attention\n- Méthodes de culture respectueuses de l\'environnement\n- Pratiques agricoles durables\n- Soutien aux agriculteurs locaux\n\n**Commandez maintenant et dégustez la vraie saveur de la Tunisie !**', 'Ras Jebel', 37.2168840, 10.1304970, '2026-04-26', '2026-05-07', 4, 50.00, 'APPROVED', 104, 'BADIS BAJI', '2026-04-26 13:15:29', '2026-04-26 13:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `culture`
--

CREATE TABLE `culture` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type_culture` varchar(50) NOT NULL,
  `superficie` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cultures`
--

CREATE TABLE `cultures` (
  `id` int(11) NOT NULL,
  `parcelle_id` int(11) NOT NULL,
  `proprietaire_id` int(11) NOT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `type_culture` enum('BLE','ORGE','MAIS','POMME_DE_TERRE','TOMATE','OLIVIER','AGRUMES','VIGNE','PASTECQUE','FRAISE','LEGUMES','AUTRE') DEFAULT 'AUTRE',
  `superficie` decimal(10,2) DEFAULT NULL,
  `etat` enum('EN_COURS','RECOLTEE','EN_VENTE','VENDUE') DEFAULT 'EN_COURS',
  `date_recolte` date DEFAULT NULL,
  `recolte_estime` decimal(10,2) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `id_acheteur` int(11) DEFAULT NULL,
  `date_vente` date DEFAULT NULL,
  `date_publication` date DEFAULT NULL,
  `prix_vente` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cultures`
--

INSERT INTO `cultures` (`id`, `parcelle_id`, `proprietaire_id`, `nom`, `type_culture`, `superficie`, `etat`, `date_recolte`, `recolte_estime`, `date_creation`, `id_acheteur`, `date_vente`, `date_publication`, `prix_vente`) VALUES
(12, 16, 103, 'tmatem', 'LEGUMES', 20.00, 'EN_COURS', '2026-04-25', 25.00, '2026-04-17 01:29:04', NULL, NULL, NULL, NULL),
(15, 17, 63, 'batata', 'POMME_DE_TERRE', 50.00, 'EN_COURS', '2026-05-02', 125.00, '2026-04-17 22:56:42', NULL, NULL, NULL, NULL),
(16, 17, 63, 'batata', 'POMME_DE_TERRE', 50.00, 'EN_COURS', '2026-05-02', 125.00, '2026-04-17 22:57:01', NULL, NULL, NULL, NULL),
(17, 17, 63, 'besbes', 'LEGUMES', 500.00, 'VENDUE', NULL, 750.00, '2026-04-17 22:58:13', 78, '2026-04-17', '2026-04-17', 50000),
(18, 17, 63, 'fraise', 'FRAISE', 50.00, 'EN_COURS', '2026-04-25', 90.00, '2026-04-17 23:19:13', NULL, NULL, NULL, NULL),
(19, 18, 78, 'felfel', 'LEGUMES', 200.00, 'EN_COURS', '2026-04-25', 300.00, '2026-04-18 22:53:59', NULL, NULL, NULL, NULL),
(20, 19, 104, 'tmatem', 'TOMATE', 400.00, 'EN_COURS', '2026-04-25', 1200.00, '2026-04-20 10:34:27', NULL, NULL, NULL, NULL),
(21, 20, 70, 'batata', 'POMME_DE_TERRE', 500.00, 'RECOLTEE', '2026-04-20', 1250.00, '2026-04-20 10:36:31', 104, '2026-04-20', '2026-04-20', 25000),
(22, 21, 104, 'tmatem', 'TOMATE', 500.00, 'EN_VENTE', '2026-05-01', 1500.00, '2026-04-20 11:41:38', NULL, NULL, '2026-04-20', 10000);

-- --------------------------------------------------------

--
-- Table structure for table `culture_history`
--

CREATE TABLE `culture_history` (
  `id` int(11) NOT NULL,
  `culture_id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `performed_at` datetime NOT NULL,
  `details` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `culture_history`
--

INSERT INTO `culture_history` (`id`, `culture_id`, `utilisateur_id`, `action`, `performed_at`, `details`) VALUES
(1, 15, 63, 'CREATED', '2026-04-17 22:56:42', 'Creation de la culture.'),
(2, 16, 63, 'CREATED', '2026-04-17 22:57:01', 'Creation de la culture.'),
(3, 17, 63, 'CREATED', '2026-04-17 22:58:13', 'Creation de la culture.'),
(4, 17, 63, 'UPDATED', '2026-04-17 22:59:21', 'Modification des informations de la culture.'),
(5, 17, 63, 'HARVESTED', '2026-04-17 23:09:03', 'Recolte de la culture.'),
(6, 17, 63, 'HARVEST_CANCELLED', '2026-04-17 23:09:11', 'Annulation de la recolte. Retour a l etat EN_COURS.'),
(7, 17, 63, 'PUBLISHED', '2026-04-17 23:09:28', 'Mise en vente a 50000.00 TND.'),
(8, 17, 78, 'PURCHASED', '2026-04-17 23:11:40', 'Achat de la culture.'),
(9, 18, 63, 'CREATED', '2026-04-17 23:19:13', 'Creation de la culture.'),
(10, 19, 78, 'CREATED', '2026-04-18 22:53:59', 'Creation de la culture.'),
(11, 20, 104, 'CREATED', '2026-04-20 10:34:27', 'Creation de la culture.'),
(12, 21, 70, 'CREATED', '2026-04-20 10:36:31', 'Creation de la culture.'),
(13, 21, 70, 'UPDATED', '2026-04-20 10:37:00', 'Modification des informations de la culture.'),
(14, 21, 70, 'PUBLISHED', '2026-04-20 10:37:16', 'Mise en vente a 25000.00 TND.'),
(15, 22, 104, 'CREATED', '2026-04-20 11:41:38', 'Creation de la culture.'),
(16, 22, 104, 'PUBLISHED', '2026-04-20 11:42:05', 'Mise en vente a 10000.00 TND.'),
(17, 21, 104, 'PURCHASED', '2026-04-20 11:43:30', 'Achat de la culture.'),
(18, 21, 104, 'HARVESTED', '2026-04-20 11:44:28', 'Recolte de la culture.');

-- --------------------------------------------------------

--
-- Table structure for table `diagnosti`
--

CREATE TABLE `diagnosti` (
  `id_diagnostic` int(11) NOT NULL,
  `id_agriculteur` int(11) NOT NULL,
  `nom_culture` varchar(100) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `reponse_expert` text DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'En attente',
  `date_envoi` datetime DEFAULT NULL,
  `date_reponse` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diagnosti`
--

INSERT INTO `diagnosti` (`id_diagnostic`, `id_agriculteur`, `nom_culture`, `image_path`, `description`, `reponse_expert`, `statut`, `date_envoi`, `date_reponse`) VALUES
(27, 76, 'Besbes', 'C:\\Users\\Crash\\Desktop\\fenouil.png', 'Objet : Réclamation concernant la qualité des plants de fenouil\n\nÀ l\'attention de l\'expert agricole,\n\nJe vous écris pour signaler un problème concernant mes plants de fenouil. Comme vous pouvez le voir sur la photo ci-jointe, les plants de fenouil présentent des feuilles jaunâtres et des tiges faibles.\n\nMalgré un arrosage régulier et une exposition suffisante à la lumière solaire, les plants ne semblent pas se développer normalement. Les feuilles sont également moins nombreuses et moins denses que celles que l\'on peut trouver sur des plants sains.\n\nJe crains que cela puisse être dû à une maladie ou à une carence nutritionnelle. Je vous serais reconnaissant si vous pouviez m\'aider à identifier la cause de ce problème et à trouver une solution pour y remédier.\n\nJe joins à ce message une photo des plants affectés. Je vous remercie d\'avance pour votre attention à cette affaire et je suis impatient de vous entendre.\n\nCordialement,\n[Votre nom]', '\n--- PRODUIT RECOMMANDÉ ---\nNom : Oidium-Fix\nDosage : 1.8 L / hectare\nFréquence : 2 fois par mois\nNote : Ne pas dépasser la dose recommandée. Bien nettoyer le pulvérisateur après usage.\n---------------------------\n', 'Valide', '2026-03-01 22:26:03', NULL),
(34, 78, 'Fraise1', 'C:\\Users\\Crash\\Desktop\\fraise malade.jpg', 'Objet : Demande de réclamation agricole - Fruits endommagés\n\nCher expert agricole,\n\nJe vous écris pour signaler un problème observé sur une culture de fraises dans notre exploitation. Nous avons récemment remarqué que certaines de nos fraises présentaient des dommages importants, comme vous pouvez le voir sur la photo ci-jointe.\n\nLe problème observé est une attaque probable de pourriture, qui semble provenir de Botrytis Cinerea. En effet, on observe des tâches brunes sur les fruits, qui s\'accompagnent d\'un pourrissement rapide. \n\nNous avons déjà mis en place des mesures de prévention, comme la limitation de l\'irrigation, l\'amélioration de la ventilation, et l\'élimination des fruits et feuilles contaminés. Cependant, le problème persiste et nous avons besoin de votre expertise afin d\'identifier la cause exacte de ce problème et de trouver une solution efficace.\n\nPourriez-vous vous déplacer dans notre exploitation afin d\'évaluer la situation et de nous proposer des solutions adaptées ?\n\nJe vous remercie de votre attention à cette affaire et je me tiens à votre disposition pour tout renseignement complémentaire.\n\nCordialement,\n[Votre nom]', '\n--- PRODUIT RECOMMANDÉ ---\nNom : Scab-Control\nDosage : 2 L / hectare\nFréquence : 1 fois tous les 7 jours\nNote : Appliquer avant l’apparition des symptômes. Porter des équipements de protection. Éviter les jours de pluie.\n---------------------------\n', 'Valide', '2026-03-02 11:00:59', NULL),
(36, 78, 'fa9ous', 'fa9ous-69e00cc5b4ff5.png', 'Objet : Réclamation pour courgettes non conformes\r\n\r\nMadame, Monsieur,\r\n\r\nJe vous écris pour signaler un problème concernant une livraison récente de courgettes. En inspectant la marchandise, j\'ai remarqué que certaines courgettes présentent des altérations, des déformations et des tâches. De plus, certaines d\'entres elles sont trop longues. \r\n\r\nJe joins à ce message une photo des courgettes concernées. \r\n\r\nJe vous remercie de votre attention à cette affaire.\r\n\r\nCordialement,\r\n[Votre nom]', '\n--- PRODUIT RECOMMANDÉ ---\nNom : Bio-Protect\nDosage : 4 L / hectare\nFréquence : 1 fois tous les 15 jours\nNote : Produit biologique sans danger pour l’environnement. Bien agiter avant utilisation. Stocker à l’abri de la chaleur.\n---------------------------\n\n\n\n--- PRODUIT RECOMMANDÉ ---\nNom : Bio-Protect\nDosage : 4 L / hectare\nFréquence : 1 fois tous les 15 jours\nNote : Produit biologique sans danger pour l’environnement. Bien agiter avant utilisation. Stocker à l’abri de la chaleur.\n---------------------------\n', 'traite', '2026-04-16 00:10:13', '2026-04-16 00:12:28'),
(37, 78, 'Batata', 'battaata-69e0108e1a1a4.png', 'Objet : Réclamation pour défaut de qualité sur des pommes de terre\r\n\r\nMadame, Monsieur,\r\n\r\nJe vous écris pour signaler un problème de qualité sur une pomme de terre qui présente des défauts. La pomme de terre a une forme irrégulière et des tâches brunes en surface. Elle n\'est pas conforme aux standards de qualité attendus. \r\n\r\nCordialement,\r\n[Votre nom]', NULL, 'en_attente', '2026-04-16 00:26:22', NULL),
(38, 103, 'tmatem', 'maladie-tomate-69e1735e48547.png', 'Demande de réclamation agricole \r\n\r\nObjet : Détection d\'une maladie sur une tomate \r\n\r\nNous vous informons qu\'une maladie a été détectée sur une de nos tomates. En effet,  la photo ci-jointe montre une tâche brune avec un contour irrégulier sur la peau du fruit. \r\n\r\nNous suspectons une contamination par *Botrytis cinerea* ou *Fusarium* sp. \r\n\r\nNous souhaitons votre expertise pour confirmer la présence de cette maladie et nous proposer des solutions. \r\n\r\nCordialement, \r\n\r\nVotre prénom, nom \r\n\r\nSystème AGRIFLOW.', 'on va voi\n--- PRODUIT RECOMMANDÉ ---\nNom : F1-2203\nDosage : 2L/hec\nFréquence : 1 fois /moins\nNote : Ne pas appliquer sur un arbre affaibli. Nettoyer le matériel après usage. Éviter le contact avec la peau.\n---------------------------\n\n--- PRODUIT RECOMMANDÉ ---\nNom : F1-2203\nDosage : 2L/hec\nFréquence : 1 fois /moins\nNote : Ne pas appliquer sur un arbre affaibli. Nettoyer le matériel après usage. Éviter le contact avec la peau.\n---------------------------\n\n\n\n--- PRODUIT RECOMMANDÉ ---\nNom : F1-2203\nDosage : 2L/hec\nFréquence : 1 fois /moins\nNote : Ne pas appliquer sur un arbre affaibli. Nettoyer le matériel après usage. Éviter le contact avec la peau.\n---------------------------\n', 'traite', '2026-04-17 01:40:14', '2026-04-17 01:41:04'),
(39, 78, 'felfel', 'felfefl-69e3efe6c128b.png', 'Demande de réclamation agricole \r\n\r\nObjet : Problème sur des piments \r\n\r\nMadame, Monsieur,\r\n\r\nJe vous écris pour signaler un problème sur une culture de piments. Les piments présentent des tâches brunes. \r\n\r\nJe vous remercie de votre attention à cette affaire. \r\n\r\nCordialement \r\n\r\n[Votre nom]', '\n--- PRODUIT RECOMMANDÉ ---\nNom : Bouillie bordelaise\nDosage : 10 à 20 g/L d\'eau (soit 1 à 2 kg/ha)\nFréquence : Tous les 10 à 14 jours, renouveler après la pluie\nNote : À base de sulfate de cuivre. Délai avant récolte : 21 jours. Éviter l\'application par temps chaud (>25°C). Port de gants et lunettes recommandé. Ne pas contaminer les cours d\'eau. Produit utilisable en agriculture biologique.\n---------------------------\n\n--- PRODUIT RECOMMANDÉ ---\nNom : Bouillie bordelaise\nDosage : 10 à 20 g/L d\'eau (soit 1 à 2 kg/ha)\nFréquence : Tous les 10 à 14 jours, renouveler après la pluie\nNote : À base de sulfate de cuivre. Délai avant récolte : 21 jours. Éviter l\'application par temps chaud (>25°C). Port de gants et lunettes recommandé. Ne pas contaminer les cours d\'eau. Produit utilisable en agriculture biologique.\n---------------------------\n\n\n\n--- PRODUIT RECOMMANDÉ ---\nNom : Bouillie bordelaise\nDosage : 10 à 20 g/L d\'eau (soit 1 à 2 kg/ha)\nFréquence : Tous les 10 à 14 jours, renouveler après la pluie\nNote : À base de sulfate de cuivre. Délai avant récolte : 21 jours. Éviter l\'application par temps chaud (>25°C). Port de gants et lunettes recommandé. Ne pas contaminer les cours d\'eau. Produit utilisable en agriculture biologique.\n---------------------------\n', 'traite', '2026-04-18 22:56:06', '2026-04-18 23:05:47'),
(40, 104, 'tmatem', 'maladie-tomate-69e5f69c0aea6.png', 'Demande de réclamation agricole - Défauts sur fruits. \r\n\r\nObjet : Dommages sur tomates - Demande d\'expertise. \r\n\r\nMadame, Monsieur,\r\n\r\nJe vous écris pour signaler des dommages observés sur mes tomates. En effet, comme vous pouvez le voir sur la photo ci-jointe, certaines de mes tomates présentent des tâches brunes et des trous. \r\n\r\nPouvez-vous m\'aider à identifier la cause de ce problème (maladie, ravageur, carence) et me conseiller sur les mesures à prendre pour éviter que cela ne se reproduise ? \r\n\r\nJe vous remercie de votre aide. \r\n\r\nCordialement,\r\n[Votre nom]', '\n--- PRODUIT RECOMMANDÉ ---\nNom : Oidium-Fix\nDosage : 2,5 à 5 mL/L d\'eau (soit 250 à 500 mL/ha)\nFréquence : Tous les 10 à 14 jours dès les premiers symptômes\nNote : Fongicide anti-oïdium à base de soufre mouillable. Délai avant récolte : 5 jours. Ne pas appliquer par temps chaud (>28°C) pour éviter les brûlures. Port de masque respiratoire conseillé.\n---------------------------\n\n\n\n--- PRODUIT RECOMMANDÉ ---\nNom : Oidium-Fix\nDosage : 2,5 à 5 mL/L d\'eau (soit 250 à 500 mL/ha)\nFréquence : Tous les 10 à 14 jours dès les premiers symptômes\nNote : Fongicide anti-oïdium à base de soufre mouillable. Délai avant récolte : 5 jours. Ne pas appliquer par temps chaud (>28°C) pour éviter les brûlures. Port de masque respiratoire conseillé.\n---------------------------\n', 'traite', '2026-04-20 11:49:15', '2026-04-20 11:51:11');

-- --------------------------------------------------------

--
-- Table structure for table `diagnostic`
--

CREATE TABLE `diagnostic` (
  `id` int(11) NOT NULL,
  `id_agriculteur` int(11) NOT NULL,
  `nom_culture` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `reponse_expert` longtext DEFAULT NULL,
  `statut` varchar(50) NOT NULL,
  `date_envoi` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260403201434', '2026-04-05 15:58:06', 68),
('DoctrineMigrations\\Version20260405173500', '2026-04-05 22:31:12', 115),
('DoctrineMigrations\\Version20260409093000', '2026-04-09 15:51:24', 58),
('DoctrineMigrations\\Version20260409113000', '2026-04-17 22:55:14', 109);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `expediteur_id` int(11) NOT NULL,
  `destinataire_id` int(11) NOT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `contenu` text NOT NULL,
  `annonce_id` int(11) DEFAULT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `lu` tinyint(1) DEFAULT 0,
  `date_lecture` timestamp NULL DEFAULT NULL,
  `date_envoi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parcelle`
--

CREATE TABLE `parcelle` (
  `id` int(11) NOT NULL,
  `agriculteur_id` int(11) NOT NULL,
  `nom` varchar(150) DEFAULT NULL,
  `superficie` decimal(10,2) DEFAULT NULL,
  `type_terre` enum('ARGILEUSE','SABLEUSE','LIMONEUSE','CALCAIRE','HUMIFERE','SALINE','MIXTE','AUTRE') DEFAULT 'AUTRE',
  `localisation` varchar(150) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parcelle`
--

INSERT INTO `parcelle` (`id`, `agriculteur_id`, `nom`, `superficie`, `type_terre`, `localisation`, `date_creation`) VALUES
(1, 75, 'Senya', 100.00, 'HUMIFERE', '22.819,41.573', '2026-02-28 12:19:52'),
(2, 78, 'Senya batata', 500.00, 'SABLEUSE', '52.839,-1.889', '2026-03-01 15:09:59'),
(3, 79, 'eeee', 5000.00, 'CALCAIRE', '52.839,-1.889', '2026-03-01 16:13:50'),
(4, 78, 'waha', 600.00, 'SABLEUSE', 'العذارة, معتمدية دوز الجنوبية, ولاية قبلي, تونس', '2026-03-01 18:07:56'),
(5, 78, 'Wa7a gabes', 1000.00, 'MIXTE', '33.833920,9.783325', '2026-03-01 18:16:10'),
(7, 80, 'Senya Batata', 500.00, 'HUMIFERE', '36.853252,10.898438', '2026-03-02 00:40:16'),
(8, 74, 'Henchuir fjel', 500.00, 'HUMIFERE', '37.212832,9.942627', '2026-03-02 02:04:55'),
(9, 74, 'wa7a gbelli', 1500.00, 'SALINE', '33.045508,8.942871', '2026-03-02 09:24:23'),
(10, 90, 'parcelletest', 10000.00, 'MIXTE', '35.281501,13.886719', '2026-03-02 10:42:43'),
(11, 78, 'parcelle8', 500.00, 'ARGILEUSE', '36.809285,9.536133', '2026-03-02 10:56:25'),
(12, 96, 'Saniya', 24.00, 'SABLEUSE', 'Sounine', '2026-04-09 13:00:35'),
(13, 100, 'Senya3', 22.00, 'HUMIFERE', 'Ras Jebel', '2026-04-11 22:43:26'),
(14, 98, 'Saniya ADAM', 500.00, 'SALINE', 'Ras Jebel - bizerte', '2026-04-11 23:06:39'),
(15, 78, 'parcelle9', 50.00, 'ARGILEUSE', 'Ariana soghra', '2026-04-16 00:05:18'),
(16, 103, 'test adam', 500.00, 'CALCAIRE', 'Bizerte', '2026-04-17 01:28:30'),
(17, 63, 'HENCHIR 2', 10000.00, 'MIXTE', '37.190002,10.181693', '2026-04-17 22:31:40'),
(18, 78, 'ghaba', 500.00, 'LIMONEUSE', '36.966980,10.203810', '2026-04-18 22:52:59'),
(19, 104, 'parcelle 1', 6000.00, 'LIMONEUSE', '36.049778,9.946444', '2026-04-20 10:33:41'),
(20, 70, 'senya batata', 10000.00, 'SABLEUSE', '33.340409,5.793510', '2026-04-20 10:35:43'),
(21, 104, 'parcelle 1', 5000.00, 'SABLEUSE', '35.427339,10.940498', '2026-04-20 11:38:44');

-- --------------------------------------------------------

--
-- Table structure for table `plans_irrigation`
--

CREATE TABLE `plans_irrigation` (
  `plan_id` int(11) NOT NULL,
  `id_culture` int(11) DEFAULT NULL,
  `nom_culture` varchar(100) DEFAULT NULL,
  `date_demande` datetime DEFAULT current_timestamp(),
  `statut` varchar(50) DEFAULT 'en_attente',
  `volume_eau_propose` float DEFAULT NULL,
  `temp_irrigation` time DEFAULT NULL,
  `temp` datetime DEFAULT NULL,
  `donnees_meteo_json` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans_irrigation`
--

INSERT INTO `plans_irrigation` (`plan_id`, `id_culture`, `nom_culture`, `date_demande`, `statut`, `volume_eau_propose`, `temp_irrigation`, `temp`, `donnees_meteo_json`) VALUES
(4, 1, NULL, '2026-02-15 19:13:35', 'brouillon', 35, '00:00:00', '2026-02-15 19:13:35', NULL),
(5, 3, NULL, '2026-02-15 19:13:48', 'brouillon', 50, '00:00:00', '2026-02-15 19:13:48', NULL),
(7, 9, 'pomme de terre', '2026-02-15 22:33:20', 'soumis', 1303.07, NULL, NULL, NULL),
(8, 2, NULL, '2026-02-16 11:00:04', 'brouillon', 40, '00:00:00', '2026-02-16 11:00:04', NULL),
(9, 4, 'Besbes', '2026-02-18 09:46:53', 'soumis', 3224.87, NULL, NULL, NULL),
(10, 8, NULL, '2026-02-18 09:47:00', 'brouillon', 70, '00:00:00', '2026-02-18 09:47:00', NULL),
(11, 11, NULL, '2026-02-18 20:38:52', 'brouillon', 45, '00:00:00', '2026-02-18 20:38:52', NULL),
(12, 5, 'Récolte Fjel', '2026-02-19 19:01:10', 'soumis', 1329.55, NULL, NULL, NULL),
(13, 3, NULL, '2026-02-20 11:32:16', 'brouillon', 50, '00:00:00', '2026-02-20 11:32:16', NULL),
(14, 1, NULL, '2026-02-20 11:32:25', 'brouillon', 35, '00:00:00', '2026-02-20 11:32:25', NULL),
(15, 1, NULL, '2026-02-20 11:52:18', 'brouillon', 35, '00:00:00', '2026-02-20 11:52:18', NULL),
(16, 7, 'culture1', '2026-03-02 10:53:09', 'rempli', 27831, NULL, NULL, NULL),
(17, 11, 'fa9ous', '2026-04-16 00:07:23', 'rempli', 150, NULL, NULL, NULL),
(18, 12, 'tmatem', '2026-04-17 01:29:21', 'rempli', 150, NULL, NULL, NULL),
(19, 19, 'felfel', '2026-04-18 22:56:50', 'rempli', 1500, NULL, NULL, NULL),
(20, 20, 'tmatem', '2026-04-20 11:47:18', 'rempli', 150, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `plans_irrigation_jour`
--

CREATE TABLE `plans_irrigation_jour` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `jour` varchar(10) NOT NULL,
  `eau_mm` float DEFAULT 0,
  `temps_min` int(11) DEFAULT 0,
  `temp_c` float DEFAULT 0,
  `semaine_debut` date NOT NULL DEFAULT '2024-01-01',
  `humidite` float DEFAULT 0,
  `pluie` float DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plans_irrigation_jour`
--

INSERT INTO `plans_irrigation_jour` (`id`, `plan_id`, `jour`, `eau_mm`, `temps_min`, `temp_c`, `semaine_debut`, `humidite`, `pluie`) VALUES
(498, 15, 'THU', 4.2, 21, 13.5, '2026-02-16', 97, 0.3),
(499, 15, 'TUE', 0.7, 4, 11, '2026-02-16', 95, 3.8),
(500, 15, 'WED', 4.5, 23, 11.3, '2026-02-16', 95, 0),
(501, 15, 'SAT', 1.2, 6, 10.3, '2026-02-16', 93, 3.3),
(502, 15, 'FRI', 1.5, 8, 10.9, '2026-02-16', 91, 3),
(503, 15, 'MON', 4.5, 23, 11.9, '2026-02-16', 92, 0),
(504, 15, 'SUN', 0.0999999, 1, 6.6, '2026-02-16', 92, 4.4),
(512, 13, 'THU', 6.42857, 33, 13.9, '2026-02-16', 96, 0),
(513, 13, 'TUE', 4.62857, 24, 10.8, '2026-02-16', 95, 1.8),
(514, 13, 'WED', 6.42857, 33, 11.9, '2026-02-16', 96, 0),
(515, 13, 'SAT', 2.12857, 11, 10.3, '2026-02-16', 95, 4.3),
(516, 13, 'FRI', 6.22857, 32, 11.6, '2026-02-16', 94, 0.2),
(517, 13, 'MON', 6.42857, 33, 11.6, '2026-02-16', 92, 0),
(518, 13, 'SUN', 2.02857, 11, 6.6, '2026-02-16', 92, 4.4),
(519, 10, 'THU', 8.7, 44, 13.5, '2026-02-16', 97, 0.3),
(520, 10, 'TUE', 5.2, 26, 11, '2026-02-16', 95, 3.8),
(521, 10, 'WED', 9, 45, 11.3, '2026-02-16', 95, 0),
(522, 10, 'SAT', 5.7, 29, 10.3, '2026-02-16', 93, 3.3),
(523, 10, 'FRI', 6, 30, 10.9, '2026-02-16', 91, 3),
(524, 10, 'MON', 9, 45, 11.9, '2026-02-16', 92, 0),
(525, 10, 'SUN', 4.6, 23, 6.6, '2026-02-16', 92, 4.4),
(533, 10, 'MON', 9, 45, 11.9, '2026-02-23', 92, 0),
(534, 10, 'TUE', 5.2, 26, 11, '2026-02-23', 95, 3.8),
(535, 10, 'WED', 9, 45, 11.3, '2026-02-23', 95, 0),
(536, 10, 'THU', 8.7, 44, 13.5, '2026-02-23', 97, 0.3),
(537, 10, 'FRI', 6, 30, 10.9, '2026-02-23', 91, 3),
(538, 10, 'SAT', 5.7, 29, 10.3, '2026-02-23', 93, 3.3),
(539, 10, 'SUN', 4.6, 23, 6.6, '2026-02-23', 92, 4.4),
(561, 8, 'THU', 5.71429, 29, 25.5, '2026-02-16', 30, 0),
(562, 8, 'TUE', 5.71429, 29, 29.1, '2026-02-16', 42, 0),
(563, 8, 'WED', 5.71429, 29, 28, '2026-02-16', 60, 0),
(564, 8, 'SAT', 5.71429, 29, 26.4, '2026-02-16', 52, 0),
(565, 8, 'FRI', 5.71429, 29, 28.6, '2026-02-16', 41, 0),
(566, 8, 'MON', 5.71429, 29, 28.4, '2026-02-16', 26, 0),
(567, 8, 'SUN', 5.71429, 29, 25.5, '2026-02-16', 56, 0),
(568, 15, 'THU', 25.71, 129, 9.4, '2026-02-23', 96, 0),
(569, 15, 'TUE', 24.51, 123, 13.5, '2026-02-23', 93, 1.2),
(570, 15, 'WED', 25.71, 129, 9.1, '2026-02-23', 92, 0),
(571, 15, 'SAT', 20.91, 105, 9.5, '2026-02-23', 99, 4.8),
(572, 15, 'FRI', 25.71, 129, 13.2, '2026-02-23', 94, 0),
(573, 15, 'MON', 24.71, 124, 11.6, '2026-02-23', 97, 1),
(574, 15, 'SUN', 25.11, 126, 9.8, '2026-02-23', 97, 0.6),
(610, 8, 'THU', 192.857, 965, 10.5, '2026-02-23', 100, 0),
(611, 8, 'TUE', 192.257, 962, 13.8, '2026-02-23', 92, 0.6),
(612, 8, 'WED', 192.557, 963, 10.7, '2026-02-23', 94, 0.3),
(613, 8, 'SAT', 192.857, 965, 8.4, '2026-02-23', 87, 0),
(614, 8, 'FRI', 187.757, 939, 10.6, '2026-02-23', 98, 5.1),
(615, 8, 'MON', 192.857, 965, 11.4, '2026-02-23', 96, 0),
(616, 8, 'SUN', 192.857, 965, 9.4, '2026-02-23', 96, 0),
(617, 13, 'THU', 257.14, 1286, 24.4, '2026-02-23', 90, 0),
(618, 13, 'TUE', 257.14, 1286, 20.8, '2026-02-23', 96, 0),
(619, 13, 'WED', 285.71, 1429, 22.4, '2026-02-23', 71, 0),
(620, 13, 'SAT', 238.59, 1193, 18.6, '2026-02-23', 92, 18.55),
(621, 13, 'FRI', 256.64, 1284, 19.6, '2026-02-23', 95, 0.5),
(622, 13, 'MON', 257.14, 1286, 20.3, '2026-02-23', 100, 0),
(623, 13, 'SUN', 257.14, 1286, 18.3, '2026-02-23', 88, 0),
(631, 9, 'THU', 462.56, 2313, 21.7, '2026-02-23', 94, 0.3),
(632, 9, 'TUE', 462.86, 2315, 19.5, '2026-02-23', 100, 0),
(633, 9, 'WED', 462.86, 2315, 18.9, '2026-02-23', 96, 0),
(634, 9, 'SAT', 449.41, 2248, 15.5, '2026-02-23', 96, 13.45),
(635, 9, 'FRI', 461.46, 2308, 17.3, '2026-02-23', 94, 1.4),
(636, 9, 'MON', 462.86, 2315, 18.8, '2026-02-23', 96, 0),
(637, 9, 'SUN', 462.86, 2315, 16.5, '2026-02-23', 98, 0),
(666, 12, 'THU', 190.657, 954, 17, '2026-03-02', 92, 2.2),
(667, 12, 'TUE', 192.757, 964, 19.6, '2026-03-02', 94, 0.1),
(668, 12, 'WED', 192.757, 964, 21.5, '2026-03-02', 92, 0.1),
(669, 12, 'SAT', 192.857, 965, 18.5, '2026-03-02', 100, 0),
(670, 12, 'FRI', 174.807, 875, 15.3, '2026-03-02', 94, 18.05),
(671, 12, 'MON', 192.857, 965, 19.9, '2026-03-02', 93, 0),
(672, 12, 'SUN', 192.857, 965, 19.5, '2026-03-02', 100, 0),
(687, 16, 'THU', 3857.14, 19286, 16.9, '2026-03-02', 84, 0),
(688, 16, 'TUE', 4285.71, 21429, 17, '2026-03-02', 79, 0),
(689, 16, 'WED', 3857.14, 19286, 17, '2026-03-02', 81, 0),
(690, 16, 'SAT', 3839.04, 19196, 15.9, '2026-03-02', 89, 18.1),
(691, 16, 'FRI', 3849.94, 19250, 16.7, '2026-03-02', 87, 7.2),
(692, 16, 'MON', 4285.71, 21429, 17, '2026-03-02', 77, 0),
(693, 16, 'SUN', 3856.34, 19282, 16.5, '2026-03-02', 82, 0.8),
(694, 7, 'THU', 192.56, 963, 18.5, '2026-03-02', 90, 0.3),
(695, 7, 'TUE', 192.26, 962, 17.8, '2026-03-02', 88, 0.6),
(696, 7, 'WED', 192.86, 965, 20.8, '2026-03-02', 91, 0),
(697, 7, 'SAT', 187.41, 938, 15.6, '2026-03-02', 98, 5.45),
(698, 7, 'FRI', 154.06, 771, 13.5, '2026-03-02', 93, 38.8),
(699, 7, 'MON', 192.86, 965, 19, '2026-03-02', 93, 0),
(700, 7, 'SUN', 191.06, 956, 16.8, '2026-03-02', 99, 1.8),
(722, 16, 'LUN', 0.6, 3, 0, '2026-04-06', 0, 0),
(723, 16, 'MAR', 0.3, 0, 0, '2026-04-06', 0, 0),
(724, 16, 'MER', 0, 0, 0, '2026-04-06', 0, 0),
(725, 16, 'JEU', 0, 0, 0, '2026-04-06', 0, 0),
(726, 16, 'VEN', 0, 0, 0, '2026-04-06', 0, 0),
(727, 16, 'SAM', 0, 0, 0, '2026-04-06', 0, 0),
(728, 16, 'DIM', 0, 0, 0, '2026-04-06', 0, 0),
(729, 17, 'LUN', 0, 0, 18.1, '2026-04-13', 90, 11.3),
(730, 17, 'MAR', 0, 0, 19.4, '2026-04-13', 95, 8.2),
(731, 17, 'MER', 1.06, 6, 21.4, '2026-04-13', 91, 0),
(732, 17, 'JEU', 1.17, 6, 22.9, '2026-04-13', 91, 0),
(733, 17, 'VEN', 1.26, 7, 24.2, '2026-04-13', 96, 0),
(734, 17, 'SAM', 1.54, 8, 27.9, '2026-04-13', 98, 0),
(735, 17, 'DIM', 0.77, 4, 29.5, '2026-04-13', 96, 0.9),
(736, 18, 'LUN', 2.11, 11, 21.4, '2026-04-13', 90, 0),
(737, 18, 'MAR', 2.39, 12, 23.3, '2026-04-13', 89, 0),
(738, 18, 'MER', 2.5, 13, 24, '2026-04-13', 91, 0),
(739, 18, 'JEU', 2.92, 15, 26.8, '2026-04-13', 100, 0),
(740, 18, 'VEN', 2.76, 14, 25.8, '2026-04-13', 95, 0),
(741, 18, 'SAM', 2.58, 13, 24.6, '2026-04-13', 100, 0),
(742, 18, 'DIM', 2.54, 13, 24.3, '2026-04-13', 90, 0),
(743, 19, 'LUN', 24.65, 124, 23.8, '2026-04-13', 89, 0),
(744, 19, 'MAR', 24.8, 125, 23.9, '2026-04-13', 88, 0),
(745, 19, 'MER', 29.31, 147, 26.9, '2026-04-13', 99, 0),
(746, 19, 'JEU', 29.93, 150, 27.3, '2026-04-13', 92, 0),
(747, 19, 'VEN', 25.99, 130, 24.7, '2026-04-13', 95, 0),
(748, 19, 'SAM', 20.24, 102, 20.8, '2026-04-13', 94, 0),
(749, 19, 'DIM', 21.11, 106, 21.4, '2026-04-13', 95, 0),
(750, 20, 'LUN', 56.81, 285, 26.3, '2026-04-20', 97, 0),
(751, 20, 'MAR', 61.69, 309, 27.9, '2026-04-20', 95, 0),
(752, 20, 'MER', 57.41, 288, 26.5, '2026-04-20', 92, 0),
(753, 20, 'JEU', 37.26, 187, 19.7, '2026-04-20', 96, 0),
(754, 20, 'VEN', 38.72, 194, 20.2, '2026-04-20', 93, 0),
(755, 20, 'SAM', 56.81, 285, 26.3, '2026-04-20', 97, 0),
(756, 20, 'DIM', 58.33, 292, 26.8, '2026-04-20', 88, 0);

-- --------------------------------------------------------

--
-- Table structure for table `plan_irrigation`
--

CREATE TABLE `plan_irrigation` (
  `id` int(11) NOT NULL,
  `culture_id` int(11) NOT NULL,
  `besoin_eau` double NOT NULL,
  `statut` varchar(50) NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plan_irrigation_jour`
--

CREATE TABLE `plan_irrigation_jour` (
  `id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `jour` varchar(3) NOT NULL,
  `eau_mm` double NOT NULL,
  `duree_min` int(11) NOT NULL,
  `temperature` double DEFAULT NULL,
  `humidite` double DEFAULT NULL,
  `pluie_mm` double DEFAULT NULL,
  `date_semaine` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produits_phytosanitaires`
--

CREATE TABLE `produits_phytosanitaires` (
  `id_produit` int(11) NOT NULL,
  `nom_produit` varchar(100) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequence_application` varchar(100) DEFAULT NULL,
  `remarques` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produits_phytosanitaires`
--

INSERT INTO `produits_phytosanitaires` (`id_produit`, `nom_produit`, `dosage`, `frequence_application`, `remarques`) VALUES
(17, 'Bio-Protect', '4 L / hectare', '1 fois tous les 15 jours', 'Produit biologique sans danger pour l’environnement. Bien agiter avant utilisation. Stocker à l’abri de la chaleur.'),
(18, 'Scab-Control', '2 L / hectare', '1 fois tous les 7 jours', 'Appliquer avant l’apparition des symptômes. Porter des équipements de protection. Éviter les jours de pluie.'),
(20, 'F1-2203', '2L/hec', '1 fois /moins', 'Ne pas appliquer sur un arbre affaibli. Nettoyer le matériel après usage. Éviter le contact avec la peau.'),
(22, 'ANKORA ', 'Consulter l\'étiquette du produit \"ANKORA\" pour le dosage exact', 'Selon les recommandations du fabricant (généralement tous les 7 à 14 jours)', 'Produit \"ANKORA\" : veuillez consulter l\'étiquette officielle pour les dosages précis, le délai avant récolte et les précautions d\'emploi. Porter toujours des équipements de protection individuelle (gants, masque, lunettes) lors de la manipulation et l\'application. Respecter les zones non traitées à proximité des points d\'eau.'),
(23, 'Bouillie bordelaise', '10 à 20 g/L d\'eau (soit 1 à 2 kg/ha)', 'Tous les 10 à 14 jours, renouveler après la pluie', 'À base de sulfate de cuivre. Délai avant récolte : 21 jours. Éviter l\'application par temps chaud (>25°C). Port de gants et lunettes recommandé. Ne pas contaminer les cours d\'eau. Produit utilisable en agriculture biologique.'),
(24, 'Oidium-Fix', '2,5 à 5 mL/L d\'eau (soit 250 à 500 mL/ha)', 'Tous les 10 à 14 jours dès les premiers symptômes', 'Fongicide anti-oïdium à base de soufre mouillable. Délai avant récolte : 5 jours. Ne pas appliquer par temps chaud (>28°C) pour éviter les brûlures. Port de masque respiratoire conseillé.');

-- --------------------------------------------------------

--
-- Table structure for table `produit_phytosanitaire`
--

CREATE TABLE `produit_phytosanitaire` (
  `id` int(11) NOT NULL,
  `nom_produit` varchar(100) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequence_application` varchar(100) DEFAULT NULL,
  `remarques` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reclamations`
--

CREATE TABLE `reclamations` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `categorie` varchar(255) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(30) NOT NULL DEFAULT 'EN_ATTENTE',
  `reponse` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reclamations`
--

INSERT INTO `reclamations` (`id`, `utilisateur_id`, `categorie`, `titre`, `description`, `date_creation`, `statut`, `reponse`) VALUES
(24, 63, 'PAIMENT', 'problème de paiment', 'problème de paiment', '2026-04-05 03:35:35', 'TRAITE', 'maatoug ayoub (ADMIN) : je vais vérifier'),
(28, 101, 'ACCESS', 'problème d\'acces', 'Problème d\'accès à la plateforme agricole\n\nJe soussigné, Ayoub, utilisateur de la plateforme agricole [nom de la plateforme], me permets de vous signaler un problème d\'accès à mon compte. Depuis [date], j\'ai rencontré des difficultés pour me connecter à la plateforme en raison d\'un message d\'erreur indiquant que mon mot de passe est incorrect. Malgré plusieurs tentatives de réinitialisation, le problème persiste.\n\nJ\'ai vérifié que mon adresse e-mail et mon mot de passe sont corrects, mais sans succès. Je me suis également assuré que mon navigateur et mon système d\'exploitation sont à jour. J\'ai également essayé d\'utiliser un autre appareil et un autre navigateur, mais le problème persiste.\n\nJe vous demande de m\'aider à résoudre ce problème d\'accès à la plateforme agricole afin de pouvoir continuer à utiliser les services proposés. Je serais reconnaissant de votre prompte attention à ce problème.', '2026-04-14 15:02:37', 'EN_ATTENTE', NULL),
(29, 101, 'TECHNIQUE', 'technical problems', 'Titre de reclamation : Problèmes techniques\n\nDescription de la reclamation :\n\nJe me permets de vous signaler des problèmes techniques rencontrés lors de l\'utilisation de votre plateforme agricole. Je suis abonné depuis quelques mois et j\'ai remarqué que le système de gestion des données a commencé à présenter des erreurs répétitives. En effet, lors de la saisie de mes données de production, le système se bloque régulièrement et me demande de réinitialiser la session. De plus, je n\'ai pas la possibilité de consulter mes données de production précédentes, ce qui me rend difficile la prise de décision pour mes futures cultures.\n\nJe vous remercie de votre attention à ce problème et je vous serais reconnaissant de me fournir une solution pour résoudre ces problèmes techniques.', '2026-04-14 15:10:36', 'EN_ATTENTE', NULL),
(30, 78, 'DELIVERY', 'Problème paiment', 'Objet : Problème de paiement sur ma commande n°XXXXX\n\nJe me permets de vous signaler un problème de paiement sur ma commande n°XXXXX datant du 10 mars 2024. J\'ai effectué une commande de 500 euros pour l\'achat de semences et d\'équipements agricoles. Cependant, malgré les paiements réguliers, je n\'ai pas reçu de confirmation de réception de ma commande et les produits n\'ont pas été livrés.\n\nJe vous demande de m\'indiquer la raison de ce retard et de me fournir une estimation de la date de livraison. Je serais également reconnaissant si vous pouviez me rembourser les frais de commande, soit 20 euros, étant donné que la commande n\'a pas été livrée.\n\nJe vous remercie de votre promptitude et de votre attention à ce problème. Je suis à votre disposition pour tout complément d\'information.\n\nCordialement,\n[Votre nom]', '2026-04-16 01:05:10', 'TRAITE', 'maatoug ayoub (ADMIN) : io'),
(31, 78, 'DELIVERY', 'Retard', 'Objet : Retard dans la livraison de produits agricoles\n\nJe me permets de vous contacter pour exprimer ma déception concernant le retard subi dans la livraison de mes produits agricoles commandés sur votre plateforme. Le 10 février dernier, j\'ai passé commande pour un lot de semences de maïs et de fèves, avec une date de livraison prévue pour le 25 février. Malheureusement, après avoir vérifié mon compte, j\'ai constaté que la livraison n\'a pas été effectuée à la date prévue.\n\nJe suis préoccupé par ce retard, car cela affecte ma planification agricole et risque de compromettre la qualité de mes récoltes. Je vous demande de prendre les mesures nécessaires pour résoudre cette situation et me livrer les produits commandés dans les plus brefs délais. Je serais reconnaissant de recevoir une réponse et une explication quant aux causes de ce retard.\n\nJe vous remercie de votre attention à cette affaire et je reste à votre disposition pour tout complément d\'information.', '2026-04-19 13:33:04', 'TRAITE', 'maatoug ayoub (ADMIN) : je vais vérifier yesyesyes\nmaatoug ayoub (ADMIN) : oui'),
(34, 106, 'PAIMENT', 'problème de paiment', 'Objet : Problème de paiement sur ma plateforme agricole\n\nJe me permets de vous signaler un problème de paiement sur ma plateforme agricole. Le 10 mars dernier, j\'ai effectué une commande de semences de maïs pour un montant de 500 euros. Cependant, lors de la réception de ma commande, j\'ai constaté que le montant n\'avait pas été débité de ma carte bancaire.\n\nJe me suis connecté à mon compte pour vérifier les détails de ma commande et j\'ai constaté que le statut était indiqué comme \"en attente de paiement\". Malgré mes tentatives de réinitialisation du paiement, le problème persiste. J\'ai besoin de votre aide pour résoudre ce problème et pour que le paiement soit effectué.\n\nJe vous remercie d\'avance pour votre attention à ce problème et pour les efforts que vous déploierez pour résoudre cette situation. Je suis à votre disposition pour tout complément d\'information nécessaire.', '2026-04-20 11:32:51', 'TRAITE', 'maatoug ayoub (ADMIN) : on a corrigé le problème');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `demandeur_id` int(11) NOT NULL,
  `proprietaire_id` int(11) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `quantite` int(11) DEFAULT 1,
  `prix_total` decimal(10,2) NOT NULL,
  `caution` decimal(10,2) DEFAULT 0.00,
  `statut` enum('EN_ATTENTE','ACCEPTEE','REFUSEE','EN_COURS','TERMINEE','ANNULEE') DEFAULT 'EN_ATTENTE',
  `message_demande` text DEFAULT NULL,
  `reponse_proprietaire` text DEFAULT NULL,
  `date_demande` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_reponse` timestamp NULL DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `contrat_url` varchar(500) DEFAULT NULL,
  `contrat_signe` tinyint(1) DEFAULT 0,
  `date_signature_contrat` timestamp NULL DEFAULT NULL,
  `paiement_effectue` tinyint(1) DEFAULT 0,
  `date_paiement` timestamp NULL DEFAULT NULL,
  `mode_paiement` varchar(50) DEFAULT NULL,
  `commission` decimal(10,2) NOT NULL DEFAULT 0.00,
  `message` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `annonce_id`, `demandeur_id`, `proprietaire_id`, `date_debut`, `date_fin`, `quantite`, `prix_total`, `caution`, `statut`, `message_demande`, `reponse_proprietaire`, `date_demande`, `date_reponse`, `date_creation`, `contrat_url`, `contrat_signe`, `date_signature_contrat`, `paiement_effectue`, `date_paiement`, `mode_paiement`, `commission`, `message`) VALUES
(2, 4, 36, 39, '2026-02-15', '2026-02-16', 1, 275.00, 0.00, 'REFUSEE', 'hey', 'noob', '2026-02-15 22:52:58', '2026-02-15 23:04:37', '2026-02-15 22:52:58', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(3, 1, 39, 36, '2026-02-15', '2026-02-16', 1, 550.00, 1000.00, 'EN_ATTENTE', 'salut', NULL, '2026-02-15 23:06:47', NULL, '2026-02-15 23:06:47', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(4, 4, 41, 39, '2026-02-15', '2026-02-16', 1, 275.00, 0.00, 'EN_ATTENTE', 'interessé', NULL, '2026-02-15 23:15:01', NULL, '2026-02-15 23:15:01', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(5, 1, 39, 36, '2026-02-16', '2026-02-17', 1, 550.00, 1000.00, 'EN_ATTENTE', 'salut', NULL, '2026-02-16 00:06:34', NULL, '2026-02-16 00:06:34', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(6, 2, 39, 36, '2026-02-16', '2026-02-17', 1, 1760.00, 3000.00, 'EN_ATTENTE', 'aaaaaaaa', NULL, '2026-02-16 00:11:01', NULL, '2026-02-16 00:11:01', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(7, 19, 74, 39, '2026-02-25', '2026-02-26', 1, 550.00, 500.00, 'EN_ATTENTE', 'aaaaaaaa', NULL, '2026-02-25 15:55:14', NULL, '2026-02-25 15:55:14', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(8, 19, 74, 39, '2026-02-25', '2026-02-26', 1, 550.00, 500.00, 'ACCEPTEE', 'aaaaaaa', 'Demande acceptée. Bienvenue !', '2026-02-25 15:56:46', '2026-02-25 15:57:30', '2026-02-25 15:56:46', NULL, 0, NULL, 1, '2026-02-25 15:58:14', 'Carte bancaire (Stripe)', 0.00, NULL),
(9, 27, 63, 44, '2026-03-01', '2026-03-02', 1, 27.50, 0.00, 'EN_ATTENTE', 'aaaaaaaaa', NULL, '2026-03-01 14:33:01', NULL, '2026-03-01 14:33:01', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(10, 28, 70, 78, '2026-03-01', '2026-03-03', 1, 1485.00, 0.00, 'ACCEPTEE', 'Salut', 'Demande acceptée. Bienvenue !', '2026-03-01 22:48:06', '2026-03-01 22:49:54', '2026-03-01 22:48:06', NULL, 0, NULL, 1, '2026-03-01 22:53:33', 'Carte bancaire (Stripe)', 0.00, NULL),
(11, 29, 39, 70, '2026-03-02', '2026-03-04', 3, 495.00, 0.00, 'ACCEPTEE', 'je suis interessé', 'Demande acceptée. Bienvenue !', '2026-03-02 09:21:07', '2026-03-02 09:23:36', '2026-03-02 09:21:07', NULL, 0, NULL, 1, '2026-03-02 09:25:53', 'Carte bancaire (Stripe)', 0.00, NULL),
(15, 32, 70, 39, '2026-03-02', '2026-03-15', 2, 2310.00, 0.00, 'EN_ATTENTE', '', NULL, '2026-03-02 09:39:31', NULL, '2026-03-02 09:39:31', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(16, 32, 63, 39, '2026-03-02', '2026-03-05', 2, 660.00, 0.00, 'ACCEPTEE', '', 'Demande acceptée. Bienvenue !', '2026-03-02 09:40:13', '2026-03-02 09:40:49', '2026-03-02 09:40:13', NULL, 0, NULL, 0, NULL, NULL, 0.00, NULL),
(17, 33, 63, 90, '2026-04-06', '2026-04-10', 1, 2100.00, 0.00, 'ACCEPTEE', NULL, NULL, '2026-04-05 21:31:43', NULL, '2026-04-05 22:31:43', NULL, 0, NULL, 0, NULL, NULL, 100.00, 'yes'),
(18, 34, 63, 78, '2026-04-17', '2026-04-25', 1, 945.00, 0.00, 'ACCEPTEE', NULL, NULL, '2026-04-15 22:55:55', NULL, '2026-04-15 23:55:55', NULL, 0, NULL, 0, NULL, NULL, 45.00, 'YES'),
(19, 34, 103, 78, '2026-04-18', '2026-04-19', 1, 210.00, 0.00, 'ACCEPTEE', NULL, NULL, '2026-04-15 23:09:13', NULL, '2026-04-16 00:09:13', NULL, 0, NULL, 0, NULL, NULL, 10.00, 'bonjour'),
(20, 36, 103, 78, '2026-04-19', '2026-04-22', 2, 21000.00, 0.00, 'ACCEPTEE', NULL, NULL, '2026-04-18 21:50:28', NULL, '2026-04-18 22:50:28', NULL, 0, NULL, 0, NULL, NULL, 1000.00, 'hello');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `cin` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `motDePasse` varchar(255) NOT NULL,
  `role` varchar(40) NOT NULL,
  `dateCreation` date NOT NULL,
  `signature` varchar(500) NOT NULL,
  `revenu` double DEFAULT NULL,
  `carte_pro` varchar(500) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `parcelles` varchar(255) DEFAULT NULL,
  `certification` varchar(500) DEFAULT NULL,
  `verification_status` varchar(20) NOT NULL DEFAULT 'APPROVED',
  `verification_reason` varchar(500) DEFAULT NULL,
  `verification_score` double DEFAULT NULL,
  `nom_ar` varchar(255) DEFAULT NULL,
  `prenom_ar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `cin`, `email`, `motDePasse`, `role`, `dateCreation`, `signature`, `revenu`, `carte_pro`, `adresse`, `parcelles`, `certification`, `verification_status`, `verification_reason`, `verification_score`, `nom_ar`, `prenom_ar`) VALUES
(39, 'maatoug', 'ayoub', 11429920, 'ayoub.maatoug@esprit.tn', '$2y$13$A59e94vam5W1RDUx3zrIW.UsW9LC4XwupKwORF6y9Peik.zU13RbO', 'ADMIN', '2026-02-16', 'C:\\xampp\\htdocs\\signatures\\signature_ayoub_69d97f6f6183d5.01464415.png', 100.5, NULL, NULL, NULL, NULL, 'APPROVED', NULL, NULL, NULL, NULL),
(63, 'SAMI', 'MAATOUG', 74100000, 'maatougsami25@gmail.com', '$2y$13$coURcHAgTEDjr2dRDCiKJehIvuNijff.OxqRhmOxDCa54XXdg8mby', 'AGRICULTEUR', '2026-02-22', 'C:\\xampp\\htdocs\\signatures\\signature_sami_69e4d6e91b7a62.69979233.png', NULL, 'C:\\xampp\\htdocs\\cartes\\sami74100000_69e4d6e91be986.10235449.png', 'Rue Ali Douagi - Ras Jebel', '', NULL, 'APPROVED', NULL, NULL, 'سامي', 'معتوق'),
(70, 'Jerbi', 'Amenallah', 54611328, 'amenallahjerbi@gmail.com', '$2y$13$hceeSNZPKf5x4a7MYrqwOeWunrvWxFnTvT.a21ERCKu87dqnrafca', 'AGRICULTEUR', '2026-02-22', 'C:\\xampp\\htdocs\\signatures\\signature_amen_69e4d5630c49a0.94833855.webp', NULL, 'C:\\xampp\\htdocs\\cartes\\carte_pro_amen_69e4d5630cbe33.12266599.png', 'NABEUL', '', NULL, 'APPROVED', NULL, NULL, 'أمان الله', 'جربي'),
(76, 'yakine', 'sahli', 77882020, 'yakinesahli48@gmail.com', '$2a$12$RscsfaflKeKNiwTGqruexeZL.DS8D6tmcxyFErpOeKUTIwNw8HX0m', 'AGRICULTEUR', '2026-02-27', 'C:\\xampp\\htdocs\\signatures\\signature_yakine_69e4d46a6b9148.78363294.png', NULL, 'C:\\xampp\\htdocs\\cartes\\Carte_pro_yakine_69e4d46a6beb87.59320748.png', 'Bizerte', '', NULL, 'APPROVED', '', 1, 'يقين', 'ساحلي'),
(77, 'sahli', 'yakine eddine', 98765432, 'yakineddine.sahli@isgb.ucar.tn', '$2a$12$qD6nzyC34wBASVrNEkDXUOqV2hnzqppcBdiYlm4H7HuLiDNviraBm', 'AGRICULTEUR', '2026-02-27', 'C:\\xampp\\htdocs\\signatures\\signature_yakine_69e4d3fd1a46d4.21341173.png', NULL, 'C:\\xampp\\htdocs\\cartes\\carte_pro_yakine22_69e4d3fd1b4014.61217921.png', 'ariana', '', NULL, 'APPROVED', '', 1, 'يقين', 'ساحلي'),
(78, 'MAATOUG', 'AYOUB', 25042000, 'maatougayoub7@gmail.com', '$2y$13$cdXqlSIMk0a60iWmwC7G8OO3V.kjouIlNmV4vZW0SYURbibobrIra', 'AGRICULTEUR', '2026-02-28', 'C:\\xampp\\htdocs\\signatures\\signature_ayoub22_69d9806b67c964.56313655.png', NULL, 'C:\\xampp\\htdocs\\cartes\\carte_pro_25042000_69d9806b692003.10457364.png', 'Ras Jebel', '', NULL, 'APPROVED', NULL, NULL, 'أيوب', 'معتوق'),
(79, 'Fattoumi', 'Oussama', 20252026, 'fattoumioussema8@gmail.com', '$2y$13$ca50JojngilWTY5afwp/weAevaOgkSpZR0iNBDZpuhEBMN6a4zhD6', 'EXPERT', '2026-03-01', 'C:\\xampp\\htdocs\\signatures\\signature_oussama_69d980c89a0840.31300547.png', NULL, NULL, NULL, NULL, 'C:\\xampp\\htdocs\\certifications\\diplome_expert_Oussama__69d980c89a4543.59813979.png', 'APPROVED', '', 1, NULL, NULL),
(96, 'Maatoug2', 'Ayoub2', 11228877, 'ayoub@agriflow.tn', '$2y$13$NuFK3exSUXbWRSrYQOq1cO9B7TkOIq0e9AKB7oE7az0vF8gPey8kK', 'AGRICULTEUR', '2026-04-06', 'C:\\xampp\\htdocs\\signatures\\signature_ayoub33_69e4d30f3b0645.62557185.png', NULL, 'C:\\xampp\\htdocs\\cartes\\11228877_69e4d30f3b6505.72172199.png', 'Ariana', '', NULL, 'APPROVED', NULL, 1, 'أيوب', 'معتوق'),
(103, 'MAATOUG4', 'ADAM4', 12032004, 'adammaatoug7@gmail.com', '$2y$13$ByD1cox0Ci9FdsrdyytWW.kssSZo6ceSHMrsl9o3Tj5FKHs0xC9IO', 'AGRICULTEUR', '2026-04-14', 'C:\\xampp\\htdocs\\signatures\\signature_adam_69de5983a8c201.87226069.webp', NULL, 'C:\\xampp\\htdocs\\cartes\\carte_pro_adem_69de5983a97ab7.64472303.png', 'rue ali douagi', '', NULL, 'APPROVED', NULL, 1, 'آدم', 'معتوق'),
(104, 'BAJI', 'BADIS', 77885522, 'badis@agriflow.tn', '$2y$13$p4WBNdszZvVeVpn7KR9oCuK61q/Yk87sDeOs3zxwyGIbjaxkGQhMm', 'AGRICULTEUR', '2026-04-19', 'C:\\xampp\\htdocs\\signatures\\signature_badis_69e4eacc8a3c06.87053903.png', NULL, 'C:\\xampp\\htdocs\\cartes\\77885522_69e4eacc8a76e5.97711975.png', 'El Menzah', '', NULL, 'APPROVED', NULL, 1, 'باجي', 'باديس'),
(106, 'MAATOUG3', 'AYOUB3', 11223344, 'ayoub.maatoug@ipeib.ucar.tn', '$2y$13$PgQwmMccnZfI30FrGI7ddOJ2gKj.Q.0WDzTp4C4EzkpQh1qA9G.gy', 'AGRICULTEUR', '2026-04-20', 'C:\\xampp\\htdocs\\signatures\\signature_ayoub_69e5f1c6b8db83.76054936.png', NULL, 'C:\\xampp\\htdocs\\cartes\\Carte_pro__ayoub__69e5f1c6b93e63.85071429.png', 'Ariana Soghra', '', NULL, 'APPROVED', NULL, 1, 'أيوب', 'معتوق');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_categorie` (`categorie`),
  ADD KEY `idx_prix` (`prix`),
  ADD KEY `idx_proprietaire` (`proprietaire_id`);

--
-- Indexes for table `annonce_photos`
--
ALTER TABLE `annonce_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_annonce` (`annonce_id`);

--
-- Indexes for table `collab_applications`
--
ALTER TABLE `collab_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`request_id`,`candidate_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_candidate` (`candidate_id`);

--
-- Indexes for table `collab_requests`
--
ALTER TABLE `collab_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_location` (`location`),
  ADD KEY `idx_dates` (`start_date`,`end_date`);

--
-- Indexes for table `culture`
--
ALTER TABLE `culture`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cultures`
--
ALTER TABLE `cultures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cultures_acheteur` (`id_acheteur`);

--
-- Indexes for table `culture_history`
--
ALTER TABLE `culture_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_3D4E689C45C4487A` (`culture_id`),
  ADD KEY `IDX_3D4E689CFB88E14F` (`utilisateur_id`);

--
-- Indexes for table `diagnosti`
--
ALTER TABLE `diagnosti`
  ADD PRIMARY KEY (`id_diagnostic`),
  ADD KEY `diag` (`id_agriculteur`);

--
-- Indexes for table `diagnostic`
--
ALTER TABLE `diagnostic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5A013C7A891E41E5` (`id_agriculteur`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_message_annonce` (`annonce_id`),
  ADD KEY `fk_message_reservation` (`reservation_id`),
  ADD KEY `idx_expediteur` (`expediteur_id`),
  ADD KEY `idx_destinataire` (`destinataire_id`),
  ADD KEY `idx_lu` (`lu`);

--
-- Indexes for table `parcelle`
--
ALTER TABLE `parcelle`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plans_irrigation`
--
ALTER TABLE `plans_irrigation`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `id_culture` (`id_culture`);

--
-- Indexes for table `plans_irrigation_jour`
--
ALTER TABLE `plans_irrigation_jour`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_plan_jour_date` (`plan_id`,`jour`,`semaine_debut`);

--
-- Indexes for table `plan_irrigation`
--
ALTER TABLE `plan_irrigation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_87A0A53DC50A5BBF` (`culture_id`);

--
-- Indexes for table `plan_irrigation_jour`
--
ALTER TABLE `plan_irrigation_jour`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_641958379E89928` (`plan_id`);

--
-- Indexes for table `produits_phytosanitaires`
--
ALTER TABLE `produits_phytosanitaires`
  ADD PRIMARY KEY (`id_produit`);

--
-- Indexes for table `produit_phytosanitaire`
--
ALTER TABLE `produit_phytosanitaire`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reclamations_utilisateur` (`utilisateur_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservation_annonce` (`annonce_id`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_demandeur` (`demandeur_id`),
  ADD KEY `idx_proprietaire` (`proprietaire_id`),
  ADD KEY `IDX_4DA2398805AB2F` (`annonce_id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cin` (`cin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `annonce_photos`
--
ALTER TABLE `annonce_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `collab_applications`
--
ALTER TABLE `collab_applications`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `collab_requests`
--
ALTER TABLE `collab_requests`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `culture`
--
ALTER TABLE `culture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cultures`
--
ALTER TABLE `cultures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `culture_history`
--
ALTER TABLE `culture_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `diagnosti`
--
ALTER TABLE `diagnosti`
  MODIFY `id_diagnostic` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `diagnostic`
--
ALTER TABLE `diagnostic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parcelle`
--
ALTER TABLE `parcelle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `plans_irrigation`
--
ALTER TABLE `plans_irrigation`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `plans_irrigation_jour`
--
ALTER TABLE `plans_irrigation_jour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=757;

--
-- AUTO_INCREMENT for table `plan_irrigation`
--
ALTER TABLE `plan_irrigation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plan_irrigation_jour`
--
ALTER TABLE `plan_irrigation_jour`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produits_phytosanitaires`
--
ALTER TABLE `produits_phytosanitaires`
  MODIFY `id_produit` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `produit_phytosanitaire`
--
ALTER TABLE `produit_phytosanitaire`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reclamations`
--
ALTER TABLE `reclamations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cultures`
--
ALTER TABLE `cultures`
  ADD CONSTRAINT `fk_cultures_acheteur` FOREIGN KEY (`id_acheteur`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `culture_history`
--
ALTER TABLE `culture_history`
  ADD CONSTRAINT `FK_3D4E689C45C4487A` FOREIGN KEY (`culture_id`) REFERENCES `cultures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_3D4E689CFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `diagnosti`
--
ALTER TABLE `diagnosti`
  ADD CONSTRAINT `diag` FOREIGN KEY (`id_agriculteur`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `plan_irrigation`
--
ALTER TABLE `plan_irrigation`
  ADD CONSTRAINT `FK_87A0A53DC50A5BBF` FOREIGN KEY (`culture_id`) REFERENCES `culture` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `plan_irrigation_jour`
--
ALTER TABLE `plan_irrigation_jour`
  ADD CONSTRAINT `FK_641958379E89928` FOREIGN KEY (`plan_id`) REFERENCES `plan_irrigation` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
