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
