-- Base de données Vite & Gourmand

CREATE DATABASE IF NOT EXISTS vite_gourmand;
USE vite_gourmand;

-- Table des utilisateurs
CREATE TABLE vg_utilisateur (
    utilisateur_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse_postale VARCHAR(255),
    ville VARCHAR(100),
    pays VARCHAR(100),
    role_id INT NOT NULL DEFAULT 3,
    is_banned TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des thèmes
CREATE TABLE vg_theme (
    theme_id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des régimes
CREATE TABLE vg_regime (
    regime_id INT PRIMARY KEY AUTO_INCREMENT,
    libelle VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des menus
CREATE TABLE vg_menu (
    menu_id INT PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(150) NOT NULL,
    description_menu TEXT,
    prix_par_personne DECIMAL(10, 2) NOT NULL,
    nombre_personne_minimum INT DEFAULT 15,
    quantite_restante INT,
    theme_id INT,
    regime_id INT,
    delai_commande INT,
    conditions_stockage TEXT,
    categorie VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (theme_id) REFERENCES vg_theme(theme_id),
    FOREIGN KEY (regime_id) REFERENCES vg_regime(regime_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des commandes
CREATE TABLE vg_commande (
    commande_id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    menu_id INT NOT NULL,
    nombre_personne INT,
    statut VARCHAR(50) DEFAULT 'en_attente',
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES vg_utilisateur(utilisateur_id),
    FOREIGN KEY (menu_id) REFERENCES vg_menu(menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données initiales
INSERT INTO vg_theme (libelle) VALUES ('Noël'), ('Pâques'), ('Traditionnel');
INSERT INTO vg_regime (libelle) VALUES ('Vegan'), ('Végétarien'), ('Classique');

-- Utilisateurs de test
INSERT INTO vg_utilisateur (nom, prenom, email, password, role_id) VALUES
('Admin', 'Test', 'admin@test.com', '$2y$10$YourHashedPassword', 1),
('Employee', 'Test', 'employee@test.com', '$2y$10$YourHashedPassword', 2),
('User', 'Test', 'user@test.com', '$2y$10$YourHashedPassword', 3);

-- Menus de test
INSERT INTO vg_menu (titre, description_menu, prix_par_personne, nombre_personne_minimum, theme_id, regime_id, categorie) VALUES
('Menu Fraîcheur', 'Léger et estival', 25, 27, 2, 1, 'Entree'),
('Menu Traditionnel', 'Classique et savoureux', 30, 20, 1, 3, 'Plat'),
('Menu Festif', 'Pour vos grandes occasions', 45, 40, 1, 3, 'Complet');
