-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 31 août 2026 à 18:36
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

--
-- Structure de la table : `test_transit_ecf`
--
CREATE DATABASE IF NOT EXISTS `test_transit_ecf` DEFAULT CHARACTER
SET
  utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `test_transit_ecf`;

-- --------------------------------------------------------
--
-- Structure de la table `vg_allergene`
--
CREATE TABLE
  `vg_allergene` (
    `allergene_id` int (11) NOT NULL,
    `libelle` varchar(50) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_allergene_plat`
--
CREATE TABLE
  `vg_allergene_plat` (
    `plat_id` int (11) NOT NULL,
    `allergene_id` int (11) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
CREATE TABLE
  `vg_avis` (
    `avis_id` int (11) NOT NULL AUTO_INCREMENT,
    `note` int (11) NOT NULL,
    `description` varchar(255) NOT NULL,
    `statut` varchar(20) NOT NULL DEFAULT 'pending',
    `utilisateur_id` int (11) NOT NULL,
    `commande_id` int (11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `validated_by` int (11) DEFAULT NULL,
    `validated_by_name` varchar(100) DEFAULT NULL,
    `validated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`avis_id`),
    UNIQUE KEY `un_seule_avis_par_commande` (`commande_id`),
    KEY `fk_avis_utilisateur` (`utilisateur_id`),
    KEY `fk_avis_commande` (`commande_id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_commande`
--
CREATE TABLE
  `vg_commande` (
    `commande_id` int (11) NOT NULL,
    `numero_commande` varchar(50) NOT NULL,
    `date_commande` date NOT NULL,
    `date_prestation` date NOT NULL,
    `heure_livraison` varchar(50) NOT NULL,
    `prix_menu` double NOT NULL,
    `nombre_personne` int (11) NOT NULL,
    `prix_livraison` double NOT NULL,
    `statut` enum (
      'en_attente',
      'acceptee',
      'en_preparation',
      'en_cours_livraison',
      'livree',
      'en_attente_retour_materiel',
      'terminee',
      'annulee'
    ) DEFAULT 'en_attente',
    `pret_materiel` tinyint (1) NOT NULL DEFAULT 0,
    `restitution_materiel` tinyint (1) NOT NULL DEFAULT 0,
    `utilisateur_id` int (11) NOT NULL,
    `menu_id` int (11) NOT NULL,
    `lieu_prestation_id` int (11) NOT NULL,
    `motif_annulation` text DEFAULT NULL,
    `mode_contact` varchar(50) DEFAULT NULL,
    `prix_total` decimal(10, 2) DEFAULT NULL,
    `depot_garantie` decimal(10, 2) DEFAULT 0.00
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_commande_statut_historique`
--
CREATE TABLE
  `vg_commande_statut_historique` (
    `historique_id` int (11) NOT NULL,
    `commande_id` int (11) NOT NULL,
    `statut` varchar(50) NOT NULL,
    `date_changement` datetime DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_horaire`
--
CREATE TABLE
  `vg_horaire` (
    `horaire_id` int (11) NOT NULL,
    `jour` varchar(50) NOT NULL,
    `heure_ouverture` varchar(50) DEFAULT NULL,
    `heure_fermeture` varchar(50) DEFAULT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_lieu_prestation`
--
CREATE TABLE
  `vg_lieu_prestation` (
    `id` int (11) NOT NULL,
    `adresse` varchar(255) NOT NULL,
    `ville` varchar(100) NOT NULL,
    `code_postal` varchar(10) NOT NULL,
    `latitude` decimal(10, 8) DEFAULT NULL,
    `longitude` decimal(11, 8) DEFAULT NULL,
    `distance_bordeaux` decimal(5, 2) DEFAULT 0.00
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_menu`
--
CREATE TABLE
  `vg_menu` (
    `menu_id` int (11) NOT NULL,
    `titre` varchar(50) NOT NULL,
    `nombre_personne_minimum` int (11) DEFAULT 15,
    `prix_par_personne` double NOT NULL,
    `description_menu` varchar(50) NOT NULL,
    `quantite_restante` int (11) NOT NULL,
    `theme_id` int (11) DEFAULT NULL,
    `regime_id` int (11) DEFAULT NULL,
    `delai_commande` int (11) DEFAULT 0,
    `conditions_stockage` text DEFAULT NULL,
    `is_active` tinyint (1) NOT NULL DEFAULT 1
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_menu_plat`
--
CREATE TABLE
  `vg_menu_plat` (
    `menu_id` int (11) NOT NULL,
    `plat_id` int (11) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_password_resets`
--
CREATE TABLE
  `vg_password_resets` (
    `id` int (11) NOT NULL,
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `expires_at` datetime NOT NULL,
    UNIQUE KEY `uk_email` (`email`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_plat`
--
CREATE TABLE
  `vg_plat` (
    `plat_id` int (11) NOT NULL AUTO_INCREMENT,
    `titre_plat` varchar(50) NOT NULL,
    `description_plat` varchar(255) DEFAULT NULL,
    `photo` varchar(255) DEFAULT NULL,
    `categorie` varchar(50) DEFAULT NULL,
    `is_active` tinyint (1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`plat_id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_regime`
--
CREATE TABLE
  `vg_regime` (
    `regime_id` int (11) NOT NULL,
    `libelle` varchar(50) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_role`
--
CREATE TABLE
  `vg_role` (
    `role_id` int (11) UNSIGNED NOT NULL,
    `libelle` varchar(50) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_theme`
--
CREATE TABLE
  `vg_theme` (
    `theme_id` int (11) NOT NULL,
    `libelle` varchar(50) NOT NULL
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Structure de la table `vg_utilisateur`
--
CREATE TABLE
  `vg_utilisateur` (
    `utilisateur_id` int (11) NOT NULL,
    `email` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `prenom` varchar(50) NOT NULL,
    `nom` varchar(50) NOT NULL,
    `telephone` varchar(50) NOT NULL,
    `ville` varchar(50) NOT NULL,
    `pays` varchar(50) NOT NULL,
    `adresse_postale` varchar(50) NOT NULL,
    `role_id` int (11) UNSIGNED DEFAULT 3,
    `est_actif` tinyint (1) DEFAULT 1,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp()
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
--
-- Index pour les tables déchargées
--
--
-- Index pour la table `vg_allergene`
--
ALTER TABLE `vg_allergene` ADD PRIMARY KEY (`allergene_id`);

--
-- Index pour la table `vg_allergene_plat`
--
ALTER TABLE `vg_allergene_plat` ADD PRIMARY KEY (`plat_id`, `allergene_id`),
ADD KEY `allergene_id` (`allergene_id`);

--
-- Index pour la table `vg_commande`
--
ALTER TABLE `vg_commande` ADD PRIMARY KEY (`commande_id`),
ADD KEY `fk_commande_utilisateur` (`utilisateur_id`),
ADD KEY `fk_commande_menu` (`menu_id`),
ADD KEY `fk_commande_lieu_prestation` (`lieu_prestation_id`);

--
-- Index pour la table `vg_commande_statut_historique`
--
ALTER TABLE `vg_commande_statut_historique` ADD PRIMARY KEY (`historique_id`),
ADD KEY `commande_id` (`commande_id`);

--
-- Index pour la table `vg_horaire`
--
ALTER TABLE `vg_horaire` ADD PRIMARY KEY (`horaire_id`);

--
-- Index pour la table `vg_lieu_prestation`
--
ALTER TABLE `vg_lieu_prestation` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vg_menu`
--
ALTER TABLE `vg_menu` ADD PRIMARY KEY (`menu_id`),
ADD KEY `fk_menu_theme` (`theme_id`),
ADD KEY `fk_menu_regime` (`regime_id`);
--
-- Index pour la table `vg_menu`
--

ALTER TABLE `vg_menu` MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Index pour la table `vg_menu_plat`
--
ALTER TABLE `vg_menu_plat` ADD PRIMARY KEY (`menu_id`, `plat_id`),
ADD KEY `plat_id` (`plat_id`);

--
-- Index pour la table `vg_password_resets`
--
ALTER TABLE `vg_password_resets` ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vg_regime`
--
ALTER TABLE `vg_regime` ADD PRIMARY KEY (`regime_id`);

--
-- Index pour la table `vg_role`
--
ALTER TABLE `vg_role` ADD PRIMARY KEY (`role_id`);

--
-- Index pour la table `vg_theme`
--
ALTER TABLE `vg_theme` ADD PRIMARY KEY (`theme_id`);

--
-- Index pour la table `vg_utilisateur`
--
ALTER TABLE `vg_utilisateur` ADD PRIMARY KEY (`utilisateur_id`),
ADD UNIQUE KEY `email` (`email`),
ADD KEY `fk_vg_utilisateur_role` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--
--
-- AUTO_INCREMENT pour la table `vg_allergene`
--
ALTER TABLE `vg_allergene` MODIFY `allergene_id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 15;

--
--
-- AUTO_INCREMENT pour la table `vg_commande`
--
ALTER TABLE `vg_commande` MODIFY `commande_id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 35;

--
-- AUTO_INCREMENT pour la table `vg_commande_statut_historique`
--
ALTER TABLE `vg_commande_statut_historique` MODIFY `historique_id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT pour la table `vg_horaire`
--
ALTER TABLE `vg_horaire` MODIFY `horaire_id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 8;

--
-- AUTO_INCREMENT pour la table `vg_lieu_prestation`
--
ALTER TABLE `vg_lieu_prestation` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 54;

--
-- AUTO_INCREMENT pour la table `vg_password_resets`
--
ALTER TABLE `vg_password_resets` MODIFY `id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 7;

--
-- AUTO_INCREMENT pour la table `vg_regime`
--
ALTER TABLE `vg_regime` MODIFY `regime_id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

--
-- AUTO_INCREMENT pour la table `vg_role`
--
ALTER TABLE `vg_role` MODIFY `role_id` int (11) UNSIGNED NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT pour la table `vg_theme`
--
ALTER TABLE `vg_theme` MODIFY `theme_id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 10;

--
-- AUTO_INCREMENT pour la table `vg_utilisateur`
--
ALTER TABLE `vg_utilisateur` MODIFY `utilisateur_id` int (11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 25;

--
-- Contraintes pour les tables déchargées
--
--
-- Contraintes pour la table `vg_allergene_plat`
--
ALTER TABLE `vg_allergene_plat` ADD CONSTRAINT `allergene_plat_ibfk_1` FOREIGN KEY (`plat_id`) REFERENCES `vg_plat` (`plat_id`) ON DELETE CASCADE,
ADD CONSTRAINT `allergene_plat_ibfk_2` FOREIGN KEY (`allergene_id`) REFERENCES `vg_allergene` (`allergene_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_commande`
--
ALTER TABLE `vg_commande` ADD CONSTRAINT `fk_commande_lieu_prestation` FOREIGN KEY (`lieu_prestation_id`) REFERENCES `vg_lieu_prestation` (`id`) ON UPDATE CASCADE,
ADD CONSTRAINT `fk_commande_menu` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_commande_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `vg_utilisateur` (`utilisateur_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_avis`
--
ALTER TABLE `vg_avis` ADD CONSTRAINT `fk_avis_utilisateur` FOREIGN KEY (`utilisateur_id`) REFERENCES `vg_utilisateur` (`utilisateur_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_avis_commande` FOREIGN KEY (`commande_id`) REFERENCES `vg_commande` (`commande_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_commande_statut_historique`
--
ALTER TABLE `vg_commande_statut_historique` ADD CONSTRAINT `vg_commande_statut_historique_ibfk_1` FOREIGN KEY (`commande_id`) REFERENCES `vg_commande` (`commande_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_menu`
--
ALTER TABLE `vg_menu` ADD CONSTRAINT `fk_menu_regime` FOREIGN KEY (`regime_id`) REFERENCES `vg_regime` (`regime_id`) ON DELETE SET NULL,
ADD CONSTRAINT `fk_menu_theme` FOREIGN KEY (`theme_id`) REFERENCES `vg_theme` (`theme_id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `vg_menu_plat`
--
ALTER TABLE `vg_menu_plat` ADD CONSTRAINT `menu_plat_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `vg_menu` (`menu_id`) ON DELETE CASCADE,
ADD CONSTRAINT `menu_plat_ibfk_2` FOREIGN KEY (`plat_id`) REFERENCES `vg_plat` (`plat_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `vg_utilisateur`
--
ALTER TABLE `vg_utilisateur` ADD CONSTRAINT `fk_vg_utilisateur_role` FOREIGN KEY (`role_id`) REFERENCES `vg_role` (`role_id`) ON DELETE SET NULL ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;