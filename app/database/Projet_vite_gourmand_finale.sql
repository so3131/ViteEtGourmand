-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 31 août 2026 à 18:36
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `test_transit_ecf`
--
CREATE DATABASE IF NOT EXISTS `test_transit_ecf` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `test_transit_ecf`;
-- --------------------------------------------------------

--
-- Structure de la table `vg_allergene`
--

CREATE TABLE `vg_allergene` (
  `allergene_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_allergene`
--

INSERT INTO `vg_allergene` (`allergene_id`, `libelle`) VALUES
(1, 'Gluten'),
(2, 'Crustacés'),
(3, 'Œufs'),
(4, 'Poissons'),
(5, 'Arachides'),
(6, 'Soja'),
(7, 'Lait'),
(8, 'Fruits à coque'),
(9, 'Céleri'),
(10, 'Moutarde'),
(11, 'Graines de sésame'),
(12, 'Anhydride sulfureux et sulfites'),
(13, 'Lupin'),
(14, 'Mollusques');

-- --------------------------------------------------------

--
-- Structure de la table `vg_allergene_plat`
--

CREATE TABLE `vg_allergene_plat` (
  `plat_id` int(11) NOT NULL,
  `allergene_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_allergene_plat`
--

INSERT INTO `vg_allergene_plat` (`plat_id`, `allergene_id`) VALUES
(2, 7),
(3, 4),
(5, 1),
(6, 7),
(7, 2),
(7, 14),
(8, 1),
(8, 7),
(9, 7),
(10, 4),
(10, 7),
(11, 3),
(11, 7),
(12, 3),
(12, 7),
(13, 1),
(13, 3),
(13, 7),
(13, 8),
(14, 1),
(14, 7),
(19, 1),
(19, 3),
(19, 7),
(20, 4),
(21, 3),
(21, 7),
(23, 10),
(24, 1),
(24, 3),
(24, 7),
(25, 7);

-- --------------------------------------------------------

--
-- Structure de la table `vg_avis`
--

CREATE TABLE `vg_avis` (
  `avis_id` int(11) NOT NULL,
  `note` varchar(50) NOT NULL,
  `description` varchar(50) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vg_commande`
--

CREATE TABLE `vg_commande` (
  `commande_id` int(11) NOT NULL,
  `numero_commande` varchar(50) NOT NULL,
  `date_commande` date NOT NULL,
  `date_prestation` date NOT NULL,
  `heure_livraison` varchar(50) NOT NULL,
  `prix_menu` double NOT NULL,
  `nombre_personne` int(11) NOT NULL,
  `prix_livraison` double NOT NULL,
  `statut` enum('en_attente','acceptee','en_preparation','en_cours_livraison','livree','en_attente_retour_materiel','terminee','annulee') DEFAULT 'en_attente',
  `pret_materiel` tinyint(1) NOT NULL DEFAULT 0,
  `restitution_materiel` tinyint(1) NOT NULL DEFAULT 0,
  `utilisateur_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `lieu_prestation_id` int(11) NOT NULL,
  `motif_annulation` text DEFAULT NULL,
  `mode_contact` varchar(50) DEFAULT NULL,
  `prix_total` decimal(10,2) DEFAULT NULL,
  `depot_garantie` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_commande`
--

INSERT INTO `vg_commande` (`commande_id`, `numero_commande`, `date_commande`, `date_prestation`, `heure_livraison`, `prix_menu`, `nombre_personne`, `prix_livraison`, `statut`, `pret_materiel`, `restitution_materiel`, `utilisateur_id`, `menu_id`, `lieu_prestation_id`, `motif_annulation`, `mode_contact`, `prix_total`, `depot_garantie`) VALUES
(1, 'CMD_6a2828d57ded3', '2026-06-09', '2026-06-10', '12:00', 2871, 58, 9.72, 'terminee', 0, 0, 11, 5, 1, NULL, NULL, NULL, 0.00),
(2, 'CMD_6a283624b12ab', '2026-06-09', '2026-06-13', '12:00', 1470, 42, 9.72, 'en_preparation', 1, 0, 11, 1, 1, NULL, NULL, NULL, 0.00),
(3, 'CMD_6a2836fc1f661', '2026-06-09', '2026-06-12', '12:00', 1470, 42, 10.605, 'terminee', 1, 0, 11, 1, 1, NULL, NULL, NULL, 0.00),
(4, 'CMD_6a283809efa29', '2026-06-09', '2026-06-11', '12:00', 675, 27, 9.72, 'terminee', 1, 0, 11, 4, 1, NULL, NULL, NULL, 0.00),
(5, 'CMD_6a28385799dfd', '2026-06-09', '2026-06-19', '12:00', 675, 27, 9.425, 'terminee', 0, 0, 11, 4, 1, NULL, NULL, NULL, 0.00),
(6, 'CMD_6a283909cfbe7', '2026-06-09', '2026-06-12', '12:00', 2420, 44, 9.72, 'en_attente', 1, 0, 11, 5, 1, NULL, NULL, NULL, 0.00),
(7, 'CMD_6a28421a3f3c3', '2026-06-09', '2026-06-19', '12:00', 1512, 48, 9.425, 'en_attente_retour_materiel', 1, 0, 11, 1, 1, NULL, NULL, NULL, 0.00),
(8, 'CMD_6a294148b9576', '2026-06-10', '2026-06-13', '12:00', 1470, 42, 9.72, 'en_attente_retour_materiel', 1, 0, 12, 1, 1, 'fqsf', 'mail', NULL, 0.00),
(9, 'CMD_6a29640eaceb5', '2026-06-10', '2026-06-20', '12:00', 1575, 50, 10.9, 'livree', 0, 0, 12, 1, 8, 'cxcv', 'mail', NULL, 0.00),
(10, 'CMD_6a845e950ceb6', '2026-08-18', '2026-08-23', '19:30', 108, 3, 10.605, 'annulee', 1, 0, 9, 1, 7, '414', 'tel', 718.61, 600.00),
(11, 'CMD_6a88354af03df', '2026-08-21', '2026-08-27', '17:23', 108, 3, 9.425, 'en_attente', 0, 0, 9, 1, 3, 'ukutr', 'mail', 153.43, 0.00),
(12, 'CMD_6a8835be5809a', '2026-08-21', '2026-08-29', '13:28', 675, 27, 9.425, 'terminee', 1, 0, 9, 4, 3, NULL, NULL, 1284.43, 600.00),
(13, 'CMD_6a88381c4dbc0', '2026-08-21', '2026-08-29', '17:35', 180, 5, 7.95, 'terminee', 0, 0, 9, 1, 5, NULL, NULL, 187.95, 0.00),
(14, 'CMD_6a883ae44cf60', '2026-08-21', '2026-08-26', '16:47', 216, 6, 10.605, 'terminee', 1, 0, 9, 1, 7, NULL, NULL, 826.61, 600.00),
(15, 'CMD_6a883b933e5e0', '2026-08-21', '2026-08-27', '16:50', 108, 3, 8.835, 'en_attente', 1, 0, 9, 1, 6, NULL, NULL, 716.84, 600.00),
(16, 'CMD_6a8840651138f', '2026-08-21', '2026-08-23', '18:11', 675, 27, 9.72, 'en_attente', 1, 0, 9, 4, 4, NULL, NULL, 1284.72, 600.00),
(19, 'CMD_6a8da7203e132', '2026-08-25', '2026-08-28', '20:30', 675, 27, 9.425, 'annulee', 1, 0, 19, 4, 3, NULL, NULL, 1284.43, 600.00),
(20, 'CMD_6a8dc7d506dd1', '2026-08-25', '2026-08-26', '19:49', 750, 30, 9.72, 'annulee', 1, 0, 19, 4, 4, NULL, NULL, 1359.72, 600.00),
(22, 'CMD_6a8dc87e887ea', '2026-08-25', '2026-08-30', '19:49', 108, 3, 9.72, 'en_attente_retour_materiel', 1, 0, 19, 1, 4, NULL, NULL, 717.72, 600.00),
(23, 'CMD_6a8dc8a5ce5aa', '2026-08-25', '2026-08-30', '19:49', 108, 3, 9.72, 'terminee', 0, 0, 19, 1, 4, NULL, NULL, 117.72, 0.00),
(24, 'CMD_6a8dc8cf88494', '2026-08-25', '2026-09-02', '21:54', 108, 3, 8.835, 'en_attente_retour_materiel', 1, 0, 19, 1, 6, NULL, NULL, 716.84, 600.00),
(25, 'CMD_6a8fedbaeb92a', '2026-08-27', '2026-09-04', '13:59', 108, 3, 7.655, 'livree', 1, 0, 19, 1, 51, '12345', 'tel', 947.45, 600.00),
(26, 'CMD-20260827-2077', '2026-08-27', '2026-09-01', '13:00', 108, 3, 10.605, 'acceptee', 0, 0, 19, 1, 7, NULL, NULL, 154.61, 0.00),
(27, 'CMD-20260827-9888', '2026-08-27', '2026-09-05', '13:58', 108, 3, 0, 'annulee', 0, 0, 19, 1, 50, NULL, NULL, 227.04, 0.00),
(28, 'CMD-20260827-5501', '2026-08-27', '2026-09-04', '14:00', 108, 3, 7.95, 'terminee', 0, 0, 19, 1, 5, NULL, NULL, 115.95, 0.00),
(30, 'CMD-20260827-2408', '2026-08-27', '2026-09-03', '18:00', 175, 7, 11.25, 'acceptee', 0, 0, 19, 4, 9, NULL, NULL, 186.25, 0.00),
(31, 'CMD-20260827-2095', '2026-08-27', '2026-09-04', '10:00', 108, 3, 480.91, 'terminee', 1, 0, 19, 1, 10, NULL, NULL, 1188.91, 600.00),
(32, 'CMD-20260827-9120', '2026-08-27', '2026-08-29', '17:30', 2420, 44, 348.69, 'en_attente', 0, 0, 19, 5, 11, NULL, NULL, 2768.69, 0.00),
(33, 'CMD-20260827-5661', '2026-08-27', '2026-09-04', '12:30', 108, 3, 312.3, 'annulee', 0, 0, 19, 1, 52, NULL, NULL, 420.30, 0.00),
(34, 'CMD-20260827-5088', '2026-08-27', '2026-09-05', '18:00', 108, 3, 424.48, 'annulee', 0, 0, 19, 1, 53, NULL, NULL, 532.48, 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `vg_commande_statut_historique`
--

CREATE TABLE `vg_commande_statut_historique` (
  `historique_id` int(11) NOT NULL,
  `commande_id` int(11) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `date_changement` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_commande_statut_historique`
--

INSERT INTO `vg_commande_statut_historique` (`historique_id`, `commande_id`, `statut`, `date_changement`) VALUES
(1, 25, 'en_cours_livraison', '2026-08-31 17:49:46'),
(2, 25, 'livree', '2026-08-31 17:50:18'),
(3, 30, 'acceptee', '2026-08-31 18:02:10');

-- --------------------------------------------------------

--
-- Structure de la table `vg_horaire`
--

CREATE TABLE `vg_horaire` (
  `horaire_id` int(11) NOT NULL,
  `jour` varchar(50) NOT NULL,
  `heure_ouverture` varchar(50) DEFAULT NULL,
  `heure_fermeture` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_horaire`
--

INSERT INTO `vg_horaire` (`horaire_id`, `jour`, `heure_ouverture`, `heure_fermeture`) VALUES
(1, 'Lundi', '09:00', '19:00'),
(2, 'Mardi', '08H00', '19H00'),
(3, 'Mercredi', '08H00', '19H00'),
(4, 'Jeudi', '08H00', '19H00'),
(5, 'Vendredi', '08H00', '19H00'),
(6, 'Samedi', '08H00', '19H00'),
(7, 'Dimanche', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `vg_lieu_prestation`
--

CREATE TABLE `vg_lieu_prestation` (
  `id` int(11) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(100) NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `distance_bordeaux` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vg_lieu_prestation`
--

INSERT INTO `vg_lieu_prestation` (`id`, `adresse`, `ville`, `code_postal`, `latitude`, `longitude`, `distance_bordeaux`) VALUES
(1, 'Centre-ville', 'Bordeaux', '', NULL, NULL, 0.00),
(2, 'Rue Calixte Camelle', 'Bègles', '', NULL, NULL, 4.50),
(3, 'Avenue Pasteur', 'Pessac', '', NULL, NULL, 7.50),
(4, 'Avenue du Château d\'Eau', 'Mérignac', '', NULL, NULL, 8.00),
(5, 'Cours de la Libération', 'Talence', '', NULL, NULL, 5.00),
(6, 'Avenue Carnot', 'Cenon', '', NULL, NULL, 6.50),
(7, 'Avenue de la Libération', 'Eysines', '', NULL, NULL, 9.50),
(8, 'Route de Léognan', 'Villenave-d\'Ornon', '', NULL, NULL, 10.00),
(9, '', 'Pessac', '', 44.78746900, -0.67593300, 0.00),
(10, '', 'Lille', '', 50.64788000, 3.08691900, 0.00),
(11, '10 Rue Lecourbe 75015 Paris', 'Paris', '', 48.84489800, 2.31005500, 0.00),
(12, 't', '', '', NULL, NULL, 0.00),
(13, 'to', '', '', NULL, NULL, 0.00),
(14, 'tou', '', '', NULL, NULL, 0.00),
(15, 'Tout-Vent', 'Morteau', '25500', 47.05899300, 6.58566900, 0.00),
(16, '', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(17, 'Toul Benal', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(18, 't', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(19, 'to', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(20, 'tou', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(21, 'toul', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(22, 'toulo', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(23, 'toulou', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(24, 'toulous', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(25, 'toulouse', 'Pluvigner', '56330', 47.74238600, -2.98167400, 0.00),
(26, 'b', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(27, 'bo', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(28, 'bor', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(29, 'bord', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(30, 'borde', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(31, 'bordea', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(32, 'bordeau', 'Toulouse', '31000', 43.60408200, 1.43380500, 0.00),
(33, '', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(34, 't', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(35, 'to', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(36, 'tou', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(37, 't1', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(38, 't10', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(39, 't10 r', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(40, 't10 ru', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(41, 't10 rue', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(42, 't10 rue d', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(43, 't10 rue du', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(44, 't10 rue du t', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(45, '10 rue du t', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(46, '10 rue du ta', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(47, '10 rue du tau', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(48, '10 rue du taur', 'Bordeaux', '33000', 44.85189500, -0.58787700, 0.00),
(49, '10 Rue du Taur', 'Toulouse', '31000', 43.60544700, 1.44273900, 0.00),
(50, 'Bordeaux', 'Champsac', '87230', 45.70204400, 0.96268100, 0.00),
(51, 'Rue Calixte Camelle', 'Narbonne', '11100', 43.18948100, 3.01316100, 0.00),
(52, '10 Rue 53210', 'Argentré', '', 48.08823300, -0.63745600, 0.00),
(53, 'Taurelle 83340', 'Le Cannet-des-Maures', '', 43.39260900, 6.33013500, 0.00);

-- --------------------------------------------------------

--
-- Structure de la table `vg_membres_equipe`
--

CREATE TABLE `vg_membres_equipe` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `fonction` varchar(255) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vg_menu`
--

CREATE TABLE `vg_menu` (
  `menu_id` int(11) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `nombre_personne_minimum` int(11) DEFAULT 15,
  `prix_par_personne` double NOT NULL,
  `description_menu` varchar(50) NOT NULL,
  `quantite_restante` int(11) NOT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `regime_id` int(11) DEFAULT NULL,
  `delai_commande` int(11) DEFAULT 0,
  `conditions_stockage` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_menu`
--

INSERT INTO `vg_menu` (`menu_id`, `titre`, `nombre_personne_minimum`, `prix_par_personne`, `description_menu`, `quantite_restante`, `theme_id`, `regime_id`, `delai_commande`, `conditions_stockage`, `is_active`) VALUES
(1, 'Menu Découverte', 3, 36, 'Un assortiment raffiné', 70, 1, 3, 3, NULL, 1),
(2, 'Menu Végé-Gourmand', 100, 28, 'Toute la fraîcheur des légumes', 100, 2, 1, 0, NULL, 1),
(4, 'Menu Fraîcheur', 7, 25, 'Léger et estival', 9, 2, 1, 0, NULL, 1),
(5, 'Menu Prestige', 44, 55, 'Une expérience gastronomique inoubliable', 56, 1, 3, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `vg_menu_plat`
--

CREATE TABLE `vg_menu_plat` (
  `menu_id` int(11) NOT NULL,
  `plat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_menu_plat`
--

INSERT INTO `vg_menu_plat` (`menu_id`, `plat_id`) VALUES
(1, 3),
(1, 7),
(1, 11),
(2, 1),
(2, 8),
(2, 14),
(4, 19),
(4, 21),
(4, 22),
(5, 3),
(5, 6),
(5, 20),
(5, 24),
(5, 25);

-- --------------------------------------------------------

--
-- Structure de la table `vg_password_resets`
--

CREATE TABLE `vg_password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vg_password_resets`
--

INSERT INTO `vg_password_resets` (`id`, `email`, `token`, `created_at`, `expires_at`) VALUES
(1, 'test12000@test.com', '45ca03939a73324f62a381f709c54fab2f0554d882d812d0402a756da01cf14e', '2026-08-24 11:16:14', '2026-08-24 13:46:14'),
(2, 'test12000@test.com', '8997f38c4290118bf4200fedfff40ddbd5b3cc7e9159e74eb19a76ff06c4f2f9', '2026-08-24 11:16:51', '2026-08-24 13:46:51'),
(5, 'sofiene31@hotmail.com', '3cdebf4d7d6ae753689514eec6784ad90ed3de6cbe72cc3c2c03217a27e8fd82', '2026-08-25 15:26:19', '2026-08-25 17:56:19');

-- --------------------------------------------------------

--
-- Structure de la table `vg_plat`
--

CREATE TABLE `vg_plat` (
  `plat_id` int(11) NOT NULL,
  `titre_plat` varchar(50) NOT NULL,
  `description_plat` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `categorie` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_plat`
--

INSERT INTO `vg_plat` (`plat_id`, `titre_plat`, `description_plat`, `photo`, `categorie`) VALUES
(1, 'Salade de saison', 'Fraîche avec vinaigrette maison', 'assets/img/plats/salade-saison.webp', 'Entree'),
(2, 'Velouté de potiron', 'Onctueux avec éclats de châtaigne', 'assets/img/plats/veloute-potiron.webp', 'Entree'),
(3, 'Saumon fumé', 'Saumon d\'Ecosse, crème citronnée', 'assets/img/plats/saumon-fume.webp', 'Entree'),
(4, 'Tartare de tomates', 'Basilic frais et huile d\'olive', 'assets/img/plats/tartare-tomate.webp', 'Entree'),
(5, 'Terrine de campagne', 'Façon grand-mère, cornichons', 'assets/img/plats/terrine-campagne.webp', 'Entree'),
(6, 'Filet de bœuf', 'Sauce échalotes, purée maison', 'assets/img/plats/filet-boeuf.webp', 'Plat'),
(7, 'Gambas sautées', 'Ail, persil, riz basmati', 'assets/img/plats/gambas.webp', 'Plat'),
(8, 'Risotto aux champignons', 'Crémeux, copeaux de parmesan', 'assets/img/plats/risotto-champignon.webp', 'Plat'),
(9, 'Suprême de poulet', 'Crème forestière, légumes croquants et pommes grenailles', 'assets/img/plats/supreme-volaille.webp', 'Plat'),
(10, 'Cabillaud rôti', 'Beurre blanc, fondue de poireaux', 'assets/img/plats/cabillaud-poireaux.webp', 'Plat'),
(11, 'Mousse au chocolat', 'Chocolat noir intense', NULL, 'Dessert'),
(12, 'Crème brûlée', 'Vanille bourbon, cassonade caramélisée', 'assets/img/plats/creme-brulee.webp', 'Dessert'),
(13, 'Fondant aux noix', 'Cœur coulant, noix de Grenoble', 'assets/img/plats/fondant-noix.webp', 'Dessert'),
(14, 'Tarte aux pommes', 'Pâte sablée pur beurre', 'assets/img/plats/tarte-pommes.webp', 'Dessert'),
(15, 'Salade de fruits', 'Menthe fraîche, sirop léger', 'assets/img/plats/salade-fruits.webp', 'Dessert'),
(19, 'Quiche aux poireaux', 'Pâte brisée maison, crème légère', 'assets/img/plats/quiche-poireaux.webp', 'Entree'),
(20, 'Daurade royale', 'Cuite au four, tombée de tomates', 'assets/img/plats/daurade-royale.webp', 'Plat'),
(21, 'Pavlova aux fruits rouges', 'Meringue croustillante, chantilly', 'assets/img/plats/pavlova-rouge.webp', 'Dessert'),
(22, 'Gaspacho andalou', 'Tomates fraîches, poivrons, concombre', 'assets/img/plats/gaspacho.webp', 'Entree'),
(23, 'Filet de poulet rôti aux herbes', 'Pommes de terre grenailles', 'assets/img/plats/filet-poulet.webp', 'Plat'),
(24, 'Tartelette au citron', 'Crème onctueuse, meringue italienne', 'assets/img/plats/tarte-citron.webp', 'Dessert'),
(25, 'Assiette de fromages', 'Sélection de nos producteurs locaux', 'assets/img/plats/fromage.webp', 'Dessert');

-- --------------------------------------------------------

--
-- Structure de la table `vg_regime`
--

CREATE TABLE `vg_regime` (
  `regime_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_regime`
--

INSERT INTO `vg_regime` (`regime_id`, `libelle`) VALUES
(1, 'Végétarien'),
(2, 'Vegan'),
(3, 'Classique'),
(4, 'Sans Lactose'),
(5, 'Halal');

-- --------------------------------------------------------

--
-- Structure de la table `vg_role`
--

CREATE TABLE `vg_role` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vg_role`
--

INSERT INTO `vg_role` (`role_id`, `libelle`) VALUES
(1, 'Admin'),
(2, 'Employe'),
(3, 'Utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `vg_theme`
--

CREATE TABLE `vg_theme` (
  `theme_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_theme`
--

INSERT INTO `vg_theme` (`theme_id`, `libelle`) VALUES
(1, 'Gastronomique'),
(2, 'Traditionnel'),
(3, 'Saint Valentin'),
(4, 'Noël'),
(5, 'Pâques'),
(6, 'Anniversaire'),
(7, 'Halloween'),
(8, 'Nouvel An'),
(9, 'Mariage');

-- --------------------------------------------------------

--
-- Structure de la table `vg_utilisateur`
--

CREATE TABLE `vg_utilisateur` (
  `utilisateur_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `pays` varchar(50) NOT NULL,
  `adresse_postale` varchar(50) NOT NULL,
  `role_id` int(11) UNSIGNED DEFAULT 3,
  `est_actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_utilisateur`
--

INSERT INTO `vg_utilisateur` (`utilisateur_id`, `email`, `password`, `prenom`, `nom`, `telephone`, `ville`, `pays`, `adresse_postale`, `role_id`, `est_actif`, `created_at`) VALUES
(9, 'test12000@test.com', '$2y$10$T5VvyiQNjCo04gv1mdM.R.PxJvm2Wl4EE3Wudr9JqJ6BRiKZNCeaO', 'SOFIENE', 'GUERNANE', '0606060606', 'Toulouse', 'France', '8 Allee Michel Ange', 3, 1, '2026-08-23 10:30:45'),
(10, 'test13000@test.com', '$2y$10$LF3ymO0.BwZYq0RPubgive2YkAUNuc2l3k4zEkqcf2Lsi64BVZ.7e', 'SOFIENE', 'GUERNANE', '0620202020', 'Toulouse', 'France', '8 Allee Michel Ange', 3, 1, '2026-08-23 10:30:45'),
(11, 'test14000@test.com', '$2y$10$sUj6E7NfOZrIHV0ULdD3GO7uXLqkbhTUhVN8Iw9o3aNfOD8yJYZO.', 'SOFIENE', 'GUERNANE', '0630303030', 'Toulouse', 'France', '8 Allee Michel Ange', 3, 1, '2026-08-23 10:30:45'),
(12, 'test15000@test.com', '$2y$10$xVsHO/daj4l3e8tfBExBwOqMp0.n0coyAAMwAC9Cf59UuOykhmpWe', 'SOFIENE', 'GUERNANE', '0101010101', 'Toulouse', 'France', '8 Allee Michel', 3, 1, '2026-08-23 10:30:45'),
(13, 'admin@vite-gourmand.fr', '$2y$10$uUy.l5kaOZhTu5GH91yCeusMC4GNGbmnJQjIwoXa3JaDAL1FSXEre', 'Admin', 'Admin', '0600000000', 'Toulouse', 'France', '1 rue de la Paix', 1, 1, '2026-08-23 10:30:45'),
(15, 'employe2@vitegourmand.fr', '$2y$10$Wot3zfeO0Z5Zo1Pa3JRMPunUa6yU9uJTKwzpDhtj.MS09.H1Ne57W', '', '', '', '', '', '', 2, 1, '2026-08-23 10:47:51'),
(19, 'sofiene31@hotmail.com', '$2y$10$PNelfKYavYaHWUxXwM46aemj2Bw0S.vwhxtQncY9q3QP2Xs4lqLyq', 'SOFIENE', 'GUERNANE', '4545454512', 'Toulouse', 'France', '8 Allee Michel Ange', 3, 1, '2026-08-25 14:30:04'),
(20, 'test16000@gmail.fr', '$2y$10$Dk7t5pHwCHV17hQkXMCeHOnCfPZFHobAUnJvrtEMFkF6cXRpxBHkq', 'Emma', 'SABATIER', '0741424345', 'Toulouse', 'France', '8 Allee Michel Ange', 3, 1, '2026-08-25 16:58:57'),
(21, 'employe3@vite-gourmand.fr', '$2y$10$4woCShMBmY9rQf0zKOxo1O0b0x4nDlI1VSQuw81MVliDKQx.CPuSi', '', '', '', '', '', '', 2, 1, '2026-08-27 18:13:07'),
(22, 'employe4@vite-gourmand.fr', '$2y$10$cT6Z6.ianbP3I6ra0bkW9.4imUYJhXKohBBIkcAx.8kJ4lQk95e9i', '', '', '', '', '', '', 2, 0, '2026-08-27 18:13:16'),
(23, 'employe5@vite-gourmand.fr', '$2y$10$O5k8ROEWdoXrTv/doHahGuNS8g18hiFFIqiB3RE8lv1qA4W78fnnm', '', '', '', '', '', '', 2, 1, '2026-08-27 18:13:23'),
(24, 'titaniummultiservice@gmail.com', '$2y$10$El0ItTT.Lmn065mbi.2fYOI5oZmYOyq4fvgjTOwFvPV9ejsxM.dXa', '', '', '', '', '', '', 2, 1, '2026-08-31 14:54:20');

-- --------------------------------------------------------

--
-- Structure de la table `vg_ville`
--

CREATE TABLE `vg_ville` (
  `id` int(11) NOT NULL,
  `nom_ville` varchar(100) NOT NULL,
  `distance_bordeaux` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vg_ville`
--

INSERT INTO `vg_ville` (`id`, `nom_ville`, `distance_bordeaux`) VALUES
(1, 'Bordeaux', 0.00);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `vg_allergene`
--
ALTER TABLE `vg_allergene`
  ADD PRIMARY KEY (`allergene_id`);

--
-- Index pour la table `vg_allergene_plat`
--
ALTER TABLE `vg_allergene_plat`
  ADD PRIMARY KEY (`plat_id`,`allergene_id`),
  ADD KEY `allergene_id` (`allergene_id`);

--
-- Index pour la table `vg_avis`
--
ALTER TABLE `vg_avis`
  ADD PRIMARY KEY (`avis_id`),
  ADD KEY `fk_avis_utilisateur` (`utilisateur_id`),
  ADD KEY `fk_avis_menu` (`menu_id`);

--
-- Index pour la table `vg_commande`
--
ALTER TABLE `vg_commande`
  ADD PRIMARY KEY (`commande_id`),
  ADD KEY `fk_commande_utilisateur` (`utilisateur_id`),
  ADD KEY `fk_commande_menu` (`menu_id`),
  ADD KEY `fk_commande_lieu_prestation` (`lieu_prestation_id`);

--
-- Index pour la table `vg_commande_statut_historique`
--
ALTER TABLE `vg_commande_statut_historique`
  ADD PRIMARY KEY (`historique_id`),
  ADD KEY `commande_id` (`commande_id`);

--
-- Index pour la table `vg_horaire`
--
ALTER TABLE `vg_horaire`
  ADD PRIMARY KEY (`horaire_id`);

--
-- Index pour la table `vg_lieu_prestation`
--
ALTER TABLE `vg_lieu_prestation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vg_membres_equipe`
--
ALTER TABLE `vg_membres_equipe`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vg_menu`
--
ALTER TABLE `vg_menu`
  ADD PRIMARY KEY (`menu_id`),
  ADD KEY `fk_menu_theme` (`theme_id`),
  ADD KEY `fk_menu_regime` (`regime_id`);

--
-- Index pour la table `vg_menu_plat`
--
ALTER TABLE `vg_menu_plat`
  ADD PRIMARY KEY (`menu_id`,`plat_id`),
  ADD KEY `plat_id` (`plat_id`);

--
-- Index pour la table `vg_password_resets`
--
ALTER TABLE `vg_password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vg_plat`
--
ALTER TABLE `vg_plat`
  ADD PRIMARY KEY (`plat_id`);

--
-- Index pour la table `vg_regime`
--
ALTER TABLE `vg_regime`
  ADD PRIMARY KEY (`regime_id`);

--
-- Index pour la table `vg_role`
--
ALTER TABLE `vg_role`
  ADD PRIMARY KEY (`role_id`);

--
-- Index pour la table `vg_theme`
--
ALTER TABLE `vg_theme`
  ADD PRIMARY KEY (`theme_id`);

--
-- Index pour la table `vg_utilisateur`
--
ALTER TABLE `vg_utilisateur`
  ADD PRIMARY KEY (`utilisateur_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_vg_utilisateur_role` (`role_id`);

--
-- Index pour la table `vg_ville`
--
ALTER TABLE `vg_ville`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `vg_allergene`
--
ALTER TABLE `vg_allergene`
  MODIFY `allergene_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `vg_avis`
--
ALTER TABLE `vg_avis`
  MODIFY `avis_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vg_commande`
--
ALTER TABLE `vg_commande`
  MODIFY `commande_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT pour la table `vg_commande_statut_historique`
--
ALTER TABLE `vg_commande_statut_historique`
  MODIFY `historique_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `vg_horaire`
--
ALTER TABLE `vg_horaire`
  MODIFY `horaire_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `vg_lieu_prestation`
--
ALTER TABLE `vg_lieu_prestation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `vg_membres_equipe`
--
ALTER TABLE `vg_membres_equipe`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `vg_menu`
--
ALTER TABLE `vg_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `vg_password_resets`
--
ALTER TABLE `vg_password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `vg_plat`
--
ALTER TABLE `vg_plat`
  MODIFY `plat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `vg_regime`
--
ALTER TABLE `vg_regime`
  MODIFY `regime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `vg_role`
--
ALTER TABLE `vg_role`
  MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `vg_theme`
--
ALTER TABLE `vg_theme`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `vg_utilisateur`
--
ALTER TABLE `vg_utilisateur`
  MODIFY `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `vg_ville`
--
ALTER TABLE `vg_ville`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `vg_allergene_plat`
--
ALTER TABLE `vg_allergene_plat`
  ADD CONSTRAINT `allergene_plat_ibfk_1` FOREIGN KEY (`plat_id`) REFERENCES `vg_plat` (`plat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `allergene_plat_ibfk_2` FOREIGN KEY (`allergene_id`) REFERENCES `vg_allergene` (`allergene_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_avis`
--
ALTER TABLE `vg_avis`
  ADD CONSTRAINT `fk_avis_menu` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avis_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `vg_utilisateur` (`utilisateur_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_commande`
--
ALTER TABLE `vg_commande`
  ADD CONSTRAINT `fk_commande_lieu_prestation` FOREIGN KEY (`lieu_prestation_id`) REFERENCES `vg_lieu_prestation` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_commande_menu` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_commande_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `vg_utilisateur` (`utilisateur_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_commande_statut_historique`
--
ALTER TABLE `vg_commande_statut_historique`
  ADD CONSTRAINT `vg_commande_statut_historique_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `vg_commande` (`commande_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_menu`
--
ALTER TABLE `vg_menu`
  ADD CONSTRAINT `fk_menu_regime` FOREIGN KEY (`regime_id`) REFERENCES `vg_regime` (`regime_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_menu_theme` FOREIGN KEY (`theme_id`) REFERENCES `vg_theme` (`theme_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `vg_menu_plat`
--
ALTER TABLE `vg_menu_plat`
  ADD CONSTRAINT `menu_plat_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_plat_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `vg_plat` (`plat_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_utilisateur`
--
ALTER TABLE `vg_utilisateur`
  ADD CONSTRAINT `fk_vg_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `vg_role` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
