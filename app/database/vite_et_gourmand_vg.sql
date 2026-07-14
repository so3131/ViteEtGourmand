-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 22 mai 2026 à 12:35
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
-- Base de données : `vite_et_gourmand`
--

-- --------------------------------------------------------

--
-- Structure de la table `allergene`
--

CREATE TABLE `vg_allergene` (
  `allergene_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `allergene_plat`
--

CREATE TABLE `vg_allergene_plat` (
  `plat_id` int(11) NOT NULL,
  `allergene_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avis`
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
-- Structure de la table `commande`
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
  `statut` varchar(50) NOT NULL,
  `pret_materiel` tinyint(1) NOT NULL DEFAULT 0,
  `restitution_materiel` tinyint(1) NOT NULL DEFAULT 0,
  `utilisateur_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `horaire`
--

CREATE TABLE `vg_horaire` (
  `horaire_id` int(11) NOT NULL,
  `jour` varchar(50) NOT NULL,
  `heure_ouverture` varchar(50) NOT NULL,
  `heure_fermeture` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu`
--

CREATE TABLE `vg_menu` (
  `menu_id` int(11) NOT NULL,
  `titre` varchar(50) NOT NULL,
  `nombre_personne_minimum` int(11) NOT NULL,
  `prix_par_personne` double NOT NULL,
  `description` varchar(50) NOT NULL,
  `quantite_restante` int(11) NOT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `regime_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `menu_plat`
--

CREATE TABLE `vg_menu_plat` (
  `menu_id` int(11) NOT NULL,
  `plat_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `vg_plat`
--

CREATE TABLE `vg_plat` (
  `plat_id` int(11) NOT NULL,
  `titre_plat` varchar(50) NOT NULL,
  `photo` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `regime`
--

CREATE TABLE `vg_regime` (
  `regime_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_regime`
--

INSERT INTO `vg_regime` (`regime_id`, `libelle`) VALUES
(1, 'Classique'),
(2, 'Végétarien'),
(3, 'Vegan');

-- --------------------------------------------------------

--
-- Structure de la table `theme`
--

CREATE TABLE `vg_theme` (
  `theme_id` int(11) NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vg_theme`
--

INSERT INTO `vg_theme` (`theme_id`, `libelle`) VALUES
(1, 'Noël'),
(2, 'Pâques'),
(3, 'Saint-Valentin');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `vg_utilisateur` (
  `utilisateur_id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `telephone` varchar(50) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `pays` varchar(50) NOT NULL,
  `adresse_postale` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  ADD KEY `fk_commande_menu` (`menu_id`);

--
-- Index pour la table `vg_horaire`
--
ALTER TABLE `vg_horaire`
  ADD PRIMARY KEY (`horaire_id`);

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
-- Index pour la table `vg_theme`
--
ALTER TABLE `vg_theme`
  ADD PRIMARY KEY (`theme_id`);

--
-- Index pour la table `vg_utilisateur`
--
ALTER TABLE `vg_utilisateur`
  ADD PRIMARY KEY (`utilisateur_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `allergene`
--
ALTER TABLE `vg_allergene`
  MODIFY `allergene_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `vg_avis`
  MODIFY `avis_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `commande`
--
ALTER TABLE `vg_commande`
  MODIFY `commande_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `horaire`
--
ALTER TABLE `vg_horaire`
  MODIFY `horaire_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `menu`
--
ALTER TABLE `vg_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `plat`
--
ALTER TABLE `vg_plat`
  MODIFY `plat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `regime`
--
ALTER TABLE `vg_regime`
  MODIFY `regime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `theme`
--
ALTER TABLE `vg_theme`
  MODIFY `theme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `vg_utilisateur`
  MODIFY `utilisateur_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `allergene_plat`
--
ALTER TABLE `vg_allergene_plat`
  ADD CONSTRAINT `allergene_plat_ibfk_1` FOREIGN KEY (`plat_id`) REFERENCES `vg_plat` (`plat_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `allergene_plat_ibfk_2` FOREIGN KEY (`allergene_id`) REFERENCES `vg_allergene` (`allergene_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `vg_avis`
  ADD CONSTRAINT `fk_avis_menu` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_avis_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `vg_utilisateur` (`utilisateur_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commande`
--
ALTER TABLE `vg_commande`
  ADD CONSTRAINT `fk_commande_menu` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_commande_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `vg_utilisateur` (`utilisateur_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `menu`
--
ALTER TABLE `vg_menu`
  ADD CONSTRAINT `fk_menu_regime` FOREIGN KEY (`regime_id`) REFERENCES `vg_regime` (`regime_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_menu_theme` FOREIGN KEY (`theme_id`) REFERENCES `vg_theme` (`theme_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `menu_plat`
--
ALTER TABLE `vg_menu_plat`
  ADD CONSTRAINT `menu_plat_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_plat_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `vg_plat` (`plat_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 22 mai 2026 à 12:36
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
-- Base de données : `ecoride_panel_client`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `avis_id` int(11) NOT NULL,
  `utilisateur_id` int(10) UNSIGNED NOT NULL,
  `covoiturage_id` int(10) UNSIGNED NOT NULL,
  `commentaire` text DEFAULT NULL,
  `note` tinyint(1) DEFAULT NULL,
  `statut` enum('approuve','attente','rejete') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `configuration`
--

CREATE TABLE `configuration` (
  `id_configuration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `covoiturage`
--

CREATE TABLE `covoiturage` (
  `covoiturage_id` int(10) UNSIGNED NOT NULL,
  `voiture_id` int(11) UNSIGNED NOT NULL,
  `date_depart` date NOT NULL,
  `heure_depart` time NOT NULL,
  `lieu_depart` varchar(255) NOT NULL,
  `date_arrivee` date NOT NULL,
  `heure_arrivee` time NOT NULL,
  `lieu_arrivee` varchar(255) NOT NULL,
  `statut` enum('publie','en_cours','termine','annule') DEFAULT 'publie',
  `nb_place` int(11) NOT NULL,
  `prix_personne` float NOT NULL,
  `is_eco` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `covoiturage`
--

INSERT INTO `covoiturage` (`covoiturage_id`, `voiture_id`, `date_depart`, `heure_depart`, `lieu_depart`, `date_arrivee`, `heure_arrivee`, `lieu_arrivee`, `statut`, `nb_place`, `prix_personne`, `is_eco`, `created_at`) VALUES
(1, 1, '2026-06-15', '08:00:00', 'Toulouse', '2026-06-15', '10:30:00', 'Bordeaux', 'publie', 3, 22, 0, '2026-05-15 21:25:55'),
(2, 2, '2026-06-15', '09:00:00', 'Toulouse', '2026-06-15', '11:45:00', 'Bordeaux', 'publie', 2, 25, 1, '2026-05-15 21:25:55'),
(3, 3, '2026-06-16', '07:30:00', 'Paris', '2026-06-16', '12:00:00', 'Lyon', 'publie', 4, 35, 1, '2026-05-15 21:25:55'),
(4, 4, '2026-06-16', '10:00:00', 'Paris', '2026-06-16', '15:00:00', 'Lyon', 'publie', 3, 30, 0, '2026-05-15 21:25:55'),
(5, 5, '2026-06-17', '14:00:00', 'Montpellier', '2026-06-17', '16:00:00', 'Marseille', 'publie', 2, 15, 1, '2026-05-15 21:25:55'),
(6, 6, '2026-06-18', '08:00:00', 'Nantes', '2026-06-18', '09:30:00', 'Rennes', 'publie', 1, 12, 0, '2026-05-15 21:25:55'),
(7, 7, '2026-06-19', '17:00:00', 'Lille', '2026-06-19', '19:30:00', 'Paris', 'publie', 3, 20, 1, '2026-05-15 21:25:55'),
(8, 8, '2026-06-20', '06:00:00', 'Nice', '2026-06-20', '08:30:00', 'Marseille', 'publie', 4, 18, 0, '2026-05-15 21:25:55'),
(9, 1, '2026-06-21', '05:00:00', 'Toulouse', '2026-06-21', '13:00:00', 'Paris', 'publie', 2, 45, 0, '2026-05-15 21:25:55'),
(10, 3, '2026-06-22', '18:30:00', 'Lyon', '2026-06-22', '20:00:00', 'Grenoble', 'publie', 3, 10, 1, '2026-05-15 21:25:55'),
(11, 10, '2026-05-21', '18:24:00', 'tourcoing', '2026-05-22', '18:24:00', 'mondonville', 'publie', 12, 1000, 0, '2026-05-19 16:23:20'),
(12, 10, '2026-05-20', '20:26:00', 'tourcoing', '2026-05-21', '01:29:00', 'mondonville', 'publie', 12, 1500, 0, '2026-05-19 16:27:26'),
(13, 22, '2026-05-21', '01:15:00', 'tourcoing', '2026-05-21', '07:21:00', 'mondonville', 'publie', 4, 4000, 0, '2026-05-19 23:15:27');

-- --------------------------------------------------------

--
-- Structure de la table `marque`
--

CREATE TABLE `marque` (
  `marque_id` int(10) UNSIGNED NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `marque`
--

INSERT INTO `marque` (`marque_id`, `libelle`) VALUES
(1, 'Peugeot'),
(2, 'Tesla'),
(3, 'Renault'),
(4, 'Volkswagen'),
(5, 'Toyota'),
(6, 'BMW'),
(7, 'Nissan'),
(8, 'Citro├½n');

-- --------------------------------------------------------

--
-- Structure de la table `parametre`
--

CREATE TABLE `parametre` (
  `parametre_id` int(11) NOT NULL,
  `propriete` varchar(50) NOT NULL,
  `valeur` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `preferences_conducteur`
--

CREATE TABLE `preferences_conducteur` (
  `preference_id` int(11) NOT NULL,
  `covoiturage_id` int(10) UNSIGNED NOT NULL,
  `bagages` enum('petit','moyen','grands') DEFAULT NULL,
  `non_fumeur` tinyint(1) DEFAULT NULL,
  `animaux` tinyint(1) DEFAULT NULL,
  `note` decimal(2,1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `musique` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `preferences_conducteur`
--

INSERT INTO `preferences_conducteur` (`preference_id`, `covoiturage_id`, `bagages`, `non_fumeur`, `animaux`, `note`, `created_at`, `musique`) VALUES
(9, 1, 'petit', 1, 0, 4.5, '2026-05-15 21:38:28', 0),
(10, 2, 'grands', 1, 1, 5.0, '2026-05-15 21:38:28', 0),
(11, 3, 'moyen', 0, 0, 3.8, '2026-05-15 21:38:28', 0),
(12, 4, 'petit', 1, 0, 4.2, '2026-05-15 21:38:28', 0),
(13, 5, 'grands', 1, 1, 4.7, '2026-05-15 21:38:28', 0),
(14, 6, 'moyen', 1, 0, 2.5, '2026-05-15 21:38:28', 0),
(15, 7, 'petit', 1, 0, 4.9, '2026-05-15 21:38:28', 0),
(16, 8, 'grands', 1, 1, 4.0, '2026-05-15 21:38:28', 0),
(17, 9, 'moyen', 1, 0, 4.3, '2026-05-15 21:38:28', 0),
(18, 10, 'petit', 1, 1, 5.0, '2026-05-15 21:38:28', 0),
(19, 12, 'petit', 0, 0, NULL, '2026-05-19 16:27:26', 0),
(20, 13, 'petit', 0, 0, NULL, '2026-05-19 23:15:27', 0);

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id` int(10) UNSIGNED NOT NULL,
  `passager_id` int(10) UNSIGNED NOT NULL,
  `covoiturage_id` int(10) UNSIGNED NOT NULL,
  `places_reservees` tinyint(2) UNSIGNED NOT NULL DEFAULT 1,
  `reservation_number` varchar(25) DEFAULT NULL,
  `date_reservation` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `role_id` int(11) UNSIGNED NOT NULL,
  `libelle` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`role_id`, `libelle`) VALUES
(1, 'Administrateur'),
(2, 'Employé'),
(3, 'Utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `signalements`
--

CREATE TABLE `signalements` (
  `id` int(11) NOT NULL,
  `covoiturage_id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `motif` text NOT NULL,
  `statut` enum('en attente','traité','rejeté') DEFAULT 'en attente',
  `date_signalement` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(10) UNSIGNED DEFAULT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `statut` enum('en_attente','en_cours','resolu') DEFAULT 'en_attente',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tickets`
--

INSERT INTO `tickets` (`id`, `utilisateur_id`, `nom`, `email`, `sujet`, `message`, `statut`, `date_creation`) VALUES
(1, NULL, 'SOFIENE GUERNANE', 'test3@test.com', 'technique', 'blablabla', 'en_attente', '2026-05-18 09:13:27'),
(2, NULL, 'SOFIENE GUERNANE', 'test3@test.com', 'technique', 'blablabla', 'en_attente', '2026-05-18 09:16:22'),
(3, NULL, 'SOFIENE GUERNANE', 'test3@test.com', 'technique', 'blablabla', 'en_attente', '2026-05-18 09:16:26'),
(9, NULL, 'SOFIENE GUERNANE', 'sofiene31@hotmail.com', 'technique', 'bfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbghhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhtgedbgbfdtbhhtgedbgdnbfdtbhhdnbfdtbhhtgedbgbfdtbhhtgedbgdn', 'en_attente', '2026-05-18 09:24:08'),
(10, NULL, 'SOFIENE GUERNANE', 'sofiene31@hotmail.com', 'technique', 'lhjblj', 'en_attente', '2026-05-18 09:26:54'),
(11, NULL, 'SOFIENE GUERNANE', 'sofiene31@hotmail.com', 'technique', 'lhjblj', 'en_attente', '2026-05-18 09:32:33');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `utilisateur_id` int(10) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL DEFAULT 3,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(5) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL,
  `date_naissance` date NOT NULL,
  `photo` varchar(255) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `solde_credits` decimal(3,0) UNSIGNED NOT NULL DEFAULT 20,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_naiss_modifiee` tinyint(1) DEFAULT 0,
  `nom_modifie` tinyint(4) DEFAULT 0,
  `prenom_modifie` tinyint(4) DEFAULT 0,
  `pseudo_modifie` tinyint(4) DEFAULT 0,
  `est_actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`utilisateur_id`, `role_id`, `nom`, `prenom`, `email`, `telephone`, `password`, `adresse`, `ville`, `code_postal`, `pays`, `date_naissance`, `photo`, `pseudo`, `solde_credits`, `created_at`, `updated_at`, `date_naiss_modifiee`, `nom_modifie`, `prenom_modifie`, `pseudo_modifie`, `est_actif`) VALUES
(1, 3, 'Thomas', 'Clemence', 'clemence.thomas@example.com', '0612345678', 'password123', '12 Rue des Fleurs', 'Toulouse', '31000', 'France', '1992-05-14', '../images/pictureprofil1.svg', 'ClemenceT92', 20, '2025-12-01 09:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(2, 3, 'Rousseau', 'Adam', 'adam.rousseau@example.com', '0698765432', 'securepass456', '45 Avenue de la Liberté', 'Toulouse', '31000', 'France', '1985-11-23', '../images/pictureprofil2.svg', 'AdamR85', 20, '2025-11-20 08:30:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(3, 3, 'Bertrand', 'Zoé', 'zoe.bertrand@example.com', '0678901234', 'mypassword789', '78 Boulevard Victor Hugo', 'Toulouse', '31000', 'France', '1977-03-09', '../images/pictureprofil3.svg', 'ZoeB77', 20, '2025-12-05 13:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(4, 3, 'Simon', 'Martin', 'martin.simon@example.com', '0654321098', 'passmartin321', '23 Place du Capitole', 'Toulouse', '31000', 'France', '1990-08-17', '../images/pictureprofil4.svg', 'MartinS90', 20, '2025-12-01 09:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(5, 3, 'Masson', 'Lilou', 'lilou.masson@example.com', '0687654321', 'liloupass654', '56 Rue Saint-Rome', 'Toulouse', '31000', 'France', '1995-12-05', '../images/pictureprofil5.svg', 'LilouM95', 20, '2025-12-01 09:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(6, 3, 'Laurent', 'Sophie', 'sophie.laurent@example.com', '0745678901', 'password999', '89 Rue de la Paix', 'Toulouse', '31000', 'France', '1988-07-22', '../images/pictureprofil6.png', 'SophieL88', 20, '2025-12-01 09:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(7, 3, 'Dupont', 'Pierre', 'pierre.dupont@example.com', '0756789012', 'password888', '34 Chemin des Roses', 'Toulouse', '31000', 'France', '1993-03-11', '../images/pictureprofil7.png', 'PierreD93', 20, '2025-12-01 09:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(8, 3, 'Moreau', 'Isabelle', 'isabelle.moreau@example.com', '0767890123', 'password777', '67 Avenue des Champs', 'Toulouse', '31000', 'France', '1987-09-30', '../images/pictureprofil8.png', 'IsabelleM87', 20, '2025-12-01 09:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(9, 1, 'Guernane', 'Sofiene', 'Sofiene31@hotmail.com', '0769478041', '$2y$10$zRf54m1swQSLjTNgYqMVSuyx.DaEv1ykGa0vXKyZSLiKEJJZ2vr.K', '1 Rue du test', 'Toulouse', '31000', 'France', '1990-03-26', '', 'So31Admin', 20, '2025-01-01 09:00:00', '2026-05-17 13:34:45', 0, 0, 0, 0, 1),
(10, 2, 'Sofiene', 'Guernane', 'test31@hotmail.com', '0769478041', '4321', '1 Rue du test', 'Toulouse', '31000', 'France', '1990-03-26', '', 'So31Employee', 20, '2025-01-01 09:00:00', '2026-05-15 15:41:13', 0, 0, 0, 0, 1),
(11, 3, 'Testeur', 'Deteste', 'test@test.com', '0769478041', '$2y$10$bBcmqulQSW/5CjJerrqZvuerK6JifpzWCnSpEC1YGiv1KxUMbKDMq', '30 rue du test', 'Toulouse', '31000', 'France', '1990-03-26', '', 'testuser', 150, '2026-05-15 12:03:37', '2026-05-15 18:17:12', 1, 1, 1, 0, 1),
(13, 3, 'chips', '', 'test2@test.com', '', '$2y$10$WpqbYoOI0LnH180.06LvAOUu6wuvDlDsUk0kYUNtAa91yuGBTPTNu', '', NULL, NULL, NULL, '0000-00-00', '', 'testuser2', 20, '2026-05-15 21:58:40', '2026-05-15 21:58:56', 0, 1, 0, 0, 1),
(15, 2, '', '', 'employe@ecoride.fr', '', '$2y$10$9F1ib83b9VqqFEwU7c6RLeLWaStwsyeSdkh2EuSG2Up7EnpR4lspS', '', NULL, NULL, NULL, '0000-00-00', '', '', 20, '2026-05-17 17:12:10', '2026-05-17 17:15:07', 0, 0, 0, 0, 1),
(16, 3, '', '', 'test2hkjjJ@test.com', '', '$2y$10$d0HbekEIIxZkXY2VHcbbt.8djqJKIX97iZy4KgDU83szBdHsXiRSy', '', NULL, NULL, NULL, '0000-00-00', '', 'slipkh', 20, '2026-05-18 07:31:47', '2026-05-20 18:01:25', 0, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

CREATE TABLE `voiture` (
  `voiture_id` int(11) UNSIGNED NOT NULL,
  `marque_id` int(10) UNSIGNED DEFAULT NULL,
  `proprietaire_id` int(10) UNSIGNED NOT NULL COMMENT 'cle etrangere',
  `modele` varchar(50) NOT NULL,
  `immatriculation` varchar(20) DEFAULT NULL,
  `energie` enum('essence','diesel','electrique','hybride') DEFAULT NULL,
  `is_eco` tinyint(1) DEFAULT 0,
  `couleur` varchar(50) NOT NULL,
  `date_premiere_immatriculation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`voiture_id`, `marque_id`, `proprietaire_id`, `modele`, `immatriculation`, `energie`, `is_eco`, `couleur`, `date_premiere_immatriculation`) VALUES
(1, 1, 1, '308', 'AA-001-BB', 'essence', 0, 'Rouge', '2019-03-15'),
(2, 2, 2, 'model 3', 'AA-002-BB', 'electrique', 1, 'Blanc', '2021-06-20'),
(3, 3, 3, 'Megane E-Tech', 'AA-003-BB', 'electrique', 1, 'Gris', '2022-01-10'),
(4, 4, 4, 'Touran', 'AA-004-BB', 'essence', 0, 'Noir', '2018-11-25'),
(5, 5, 5, 'Rav4 Hybride', 'AA-005-BB', 'hybride', 0, 'Noir', '2020-05-12'),
(6, 6, 6, '330i', 'AA-006-BB', 'essence', 0, 'Bleu', '2019-08-18'),
(7, 7, 7, 'Leaf', 'AA-007-BB', 'electrique', 1, 'Blanc', '2022-02-14'),
(8, 8, 8, 'C5 Aircross', 'AA-008-BB', 'diesel', 0, 'Argent', '2021-09-22'),
(10, 7, 13, 'yaris', 'AB-123-CD', 'essence', 0, 'blanc', '2026-05-12'),
(22, 7, 13, 'nissan', 'NS-111-NS', 'essence', 0, 'vert', '2026-05-13');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`avis_id`),
  ADD KEY `fk_avis_utilisateurID` (`utilisateur_id`),
  ADD KEY `covoiturage_id` (`covoiturage_id`);

--
-- Index pour la table `configuration`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`id_configuration`);

--
-- Index pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD PRIMARY KEY (`covoiturage_id`),
  ADD KEY `fk_trajetID_voitureID` (`voiture_id`);

--
-- Index pour la table `marque`
--
ALTER TABLE `marque`
  ADD PRIMARY KEY (`marque_id`);

--
-- Index pour la table `parametre`
--
ALTER TABLE `parametre`
  ADD PRIMARY KEY (`parametre_id`);

--
-- Index pour la table `preferences_conducteur`
--
ALTER TABLE `preferences_conducteur`
  ADD PRIMARY KEY (`preference_id`),
  ADD KEY `fk_preferences_covoiturage` (`covoiturage_id`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_number` (`reservation_number`),
  ADD KEY `fk_passagerID_utilisateurID` (`passager_id`),
  ADD KEY `fk_covoiturage` (`covoiturage_id`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`role_id`);

--
-- Index pour la table `signalements`
--
ALTER TABLE `signalements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tickets_user` (`utilisateur_id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`utilisateur_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_utilisateurID_roleID` (`role_id`);

--
-- Index pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`voiture_id`),
  ADD UNIQUE KEY `immatriculation` (`immatriculation`),
  ADD KEY `fk_utilisateurID_proprietaireID` (`proprietaire_id`),
  ADD KEY `fk_voiture_marque` (`marque_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `avis_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `configuration`
--
ALTER TABLE `configuration`
  MODIFY `id_configuration` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  MODIFY `covoiturage_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `marque`
--
ALTER TABLE `marque`
  MODIFY `marque_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `parametre`
--
ALTER TABLE `parametre`
  MODIFY `parametre_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `preferences_conducteur`
--
ALTER TABLE `preferences_conducteur`
  MODIFY `preference_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `role_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `signalements`
--
ALTER TABLE `signalements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `utilisateur_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `voiture`
--
ALTER TABLE `voiture`
  MODIFY `voiture_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `fk_avis_utilisateurID` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_covoiturage_utilisateurID` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD CONSTRAINT `fk_trajetID_voitureID` FOREIGN KEY (`voiture_id`) REFERENCES `voiture` (`voiture_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `preferences_conducteur`
--
ALTER TABLE `preferences_conducteur`
  ADD CONSTRAINT `fk_preferences_covoiturage` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_covoiturage` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`covoiturage_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_passagerID_utilisateurID` FOREIGN KEY (`passager_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_user` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_utilisateurID_roleID` FOREIGN KEY (`role_id`) REFERENCES `role` (`role_id`);

--
-- Contraintes pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD CONSTRAINT `fk_utilisateurID_proprietaireID` FOREIGN KEY (`proprietaire_id`) REFERENCES `utilisateurs` (`utilisateur_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_voiture_marque` FOREIGN KEY (`marque_id`) REFERENCES `marque` (`marque_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
