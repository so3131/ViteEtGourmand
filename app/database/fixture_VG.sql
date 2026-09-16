USE `test_transit_ecf`;

--
-- Déchargement des données de la table `vg_allergene`
--
INSERT INTO
    `vg_allergene` (`allergene_id`, `libelle`)
VALUES
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

--
-- Déchargement des données de la table `vg_plat`
--
INSERT INTO
    `vg_plat` (
        `plat_id`,
        `titre_plat`,
        `description_plat`,
        `photo`,
        `categorie`
    )
VALUES
    (
        1,
        'Salade de saison',
        'Fraîche avec vinaigrette maison',
        'assets/img/plats/salade-saison.webp',
        'Entree'
    ),
    (
        2,
        'Velouté de potiron',
        'Onctueux avec éclats de châtaigne',
        'assets/img/plats/veloute-potiron.webp',
        'Entree'
    ),
    (
        3,
        'Saumon fumé',
        'Saumon d\'Ecosse, crème citronnée',
        'assets/img/plats/saumon-fume.webp',
        'Entree'
    ),
    (
        4,
        'Tartare de tomates',
        'Basilic frais et huile d\'olive',
        'assets/img/plats/tartare-tomate.webp',
        'Entree'
    ),
    (
        5,
        'Terrine de campagne',
        'Façon grand-mère, cornichons',
        'assets/img/plats/terrine-campagne.webp',
        'Entree'
    ),
    (
        6,
        'Filet de bœuf',
        'Sauce échalotes, purée maison',
        'assets/img/plats/filet-boeuf.webp',
        'Plat'
    ),
    (
        7,
        'Gambas sautées',
        'Ail, persil, riz basmati',
        'assets/img/plats/gambas.webp',
        'Plat'
    ),
    (
        8,
        'Risotto aux champignons',
        'Crémeux, copeaux de parmesan',
        'assets/img/plats/risotto-champignon.webp',
        'Plat'
    ),
    (
        9,
        'Suprême de poulet',
        'Crème forestière, légumes croquants et pommes grenailles',
        'assets/img/plats/supreme-volaille.webp',
        'Plat'
    ),
    (
        10,
        'Cabillaud rôti',
        'Beurre blanc, fondue de poireaux',
        'assets/img/plats/cabillaud-poireaux.webp',
        'Plat'
    ),
    (
        11,
        'Mousse au chocolat',
        'Chocolat noir intense',
        NULL,
        'Dessert'
    ),
    (
        12,
        'Crème brûlée',
        'Vanille bourbon, cassonade caramélisée',
        'assets/img/plats/creme-brulee.webp',
        'Dessert'
    ),
    (
        13,
        'Fondant aux noix',
        'Cœur coulant, noix de Grenoble',
        'assets/img/plats/fondant-noix.webp',
        'Dessert'
    ),
    (
        14,
        'Tarte aux pommes',
        'Pâte sablée pur beurre',
        'assets/img/plats/tarte-pommes.webp',
        'Dessert'
    ),
    (
        15,
        'Salade de fruits',
        'Menthe fraîche, sirop léger',
        'assets/img/plats/salade-fruits.webp',
        'Dessert'
    ),
    (
        19,
        'Quiche aux poireaux',
        'Pâte brisée maison, crème légère',
        'assets/img/plats/quiche-poireaux.webp',
        'Entree'
    ),
    (
        20,
        'Daurade royale',
        'Cuite au four, tombée de tomates',
        'assets/img/plats/daurade-royale.webp',
        'Plat'
    ),
    (
        21,
        'Pavlova aux fruits rouges',
        'Meringue croustillante, chantilly',
        'assets/img/plats/pavlova-rouge.webp',
        'Dessert'
    ),
    (
        22,
        'Gaspacho andalou',
        'Tomates fraîches, poivrons, concombre',
        'assets/img/plats/gaspacho.webp',
        'Entree'
    ),
    (
        23,
        'Filet de poulet rôti aux herbes',
        'Pommes de terre grenailles',
        'assets/img/plats/filet-poulet.webp',
        'Plat'
    ),
    (
        24,
        'Tartelette au citron',
        'Crème onctueuse, meringue italienne',
        'assets/img/plats/tarte-citron.webp',
        'Dessert'
    ),
    (
        25,
        'Assiette de fromages',
        'Sélection de nos producteurs locaux',
        'assets/img/plats/fromage.webp',
        'Dessert'
    );

--
-- Déchargement des données de la table `vg_theme`
--
INSERT INTO
    `vg_theme` (`theme_id`, `libelle`)
VALUES
    (1, 'Gastronomique'),
    (2, 'Traditionnel'),
    (3, 'Saint Valentin'),
    (4, 'Noël'),
    (5, 'Pâques'),
    (6, 'Anniversaire'),
    (7, 'Halloween'),
    (8, 'Nouvel An'),
    (9, 'Mariage');

--
-- Déchargement des données de la table `vg_regime`
--
INSERT INTO
    `vg_regime` (`regime_id`, `libelle`)
VALUES
    (1, 'Végétarien'),
    (2, 'Vegan'),
    (3, 'Classique'),
    (4, 'Sans Lactose'),
    (5, 'Halal');

--
-- Déchargement des données de la table `vg_role`
--
INSERT INTO
    `vg_role` (`role_id`, `libelle`)
VALUES
    (1, 'Admin'),
    (2, 'Employe'),
    (3, 'Utilisateur');

--
-- Déchargement des données de la table `vg_horaire`
--
INSERT INTO
    `vg_horaire` (
        `horaire_id`,
        `jour`,
        `heure_ouverture`,
        `heure_fermeture`
    )
VALUES
    (1, 'Lundi', '09:00', '19:00'),
    (2, 'Mardi', '08H00', '19H00'),
    (3, 'Mercredi', '08H00', '19H00'),
    (4, 'Jeudi', '08H00', '19H00'),
    (5, 'Vendredi', '08H00', '19H00'),
    (6, 'Samedi', '08H00', '19H00'),
    (7, 'Dimanche', '', '');



--
-- Déchargement des données de la table `vg_lieu_prestation`
--
INSERT INTO
    `vg_lieu_prestation` (
        `id`,
        `adresse`,
        `ville`,
        `code_postal`,
        `latitude`,
        `longitude`,
        `distance_bordeaux`
    )
VALUES
    (
        1,
        'Centre-ville',
        'Bordeaux',
        '',
        NULL,
        NULL,
        0.00
    );

--
-- Déchargement des données de la table `vg_allergene_plat`
--
INSERT INTO
    `vg_allergene_plat` (`plat_id`, `allergene_id`)
VALUES
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

--
-- Déchargement des données de la table `vg_menu`
--
INSERT INTO
    `vg_menu` (
        `menu_id`,
        `titre`,
        `nombre_personne_minimum`,
        `prix_par_personne`,
        `description_menu`,
        `quantite_restante`,
        `theme_id`,
        `regime_id`,
        `delai_commande`,
        `conditions_stockage`,
        `is_active`
    )
VALUES
    (
        1,
        'Menu Découverte',
        3,
        36,
        'Un assortiment raffiné',
        70,
        1,
        3,
        3,
        NULL,
        1
    ),
    (
        2,
        'Menu Végé-Gourmand',
        100,
        28,
        'Toute la fraîcheur des légumes',
        100,
        2,
        1,
        0,
        NULL,
        1
    ),
    (
        4,
        'Menu Fraîcheur',
        7,
        25,
        'Léger et estival',
        9,
        2,
        1,
        0,
        NULL,
        1
    ),
    (
        5,
        'Menu Prestige',
        44,
        55,
        'Une expérience gastronomique inoubliable',
        56,
        1,
        3,
        0,
        NULL,
        1
    );

--
-- Déchargement des données de la table `vg_menu_plat`
--
INSERT INTO
    `vg_menu_plat` (`menu_id`, `plat_id`)
VALUES
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

--
-- Déchargement des données de la table `vg_utilisateur`
--
INSERT INTO
    `vg_utilisateur` (
        `utilisateur_id`,
        `email`,
        `password`,
        `prenom`,
        `nom`,
        `telephone`,
        `ville`,
        `pays`,
        `adresse_postale`,
        `role_id`,
        `est_actif`,
        `created_at`
    )
VALUES
    (
        1,
        'admin@vite-gourmand.fr',
        '$2y$10$uUy.l5kaOZhTu5GH91yCeusMC4GNGbmnJQjIwoXa3JaDAL1FSXEre',
        'Admin',
        'Admin',
        '0600000000',
        'Toulouse',
        'France',
        '1 rue de la Paix',
        1,
        1,
        '2026-08-23 10:30:45'
    );