-- Fixtures Vite & Gourmand : catalogue + comptes de démo + jeu de données transactionnelles (commandes/avis)
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

INSERT INTO
    `vg_avis` (
        `avis_id`,
        `note`,
        `description`,
        `statut`,
        `utilisateur_id`,
        `commande_id`,
        `created_at`,
        `validated_by`,
        `validated_by_name`,
        `validated_at`
    )
VALUES
    (
        1,
        5,
        'Une prestation parfaite du début à la fin, je recommande vivement !',
        'pending',
        3,
        7,
        '2026-09-16 13:00:00',
        NULL,
        NULL,
        NULL
    );

INSERT INTO
    `vg_commande` (
        `commande_id`,
        `numero_commande`,
        `date_commande`,
        `date_prestation`,
        `heure_livraison`,
        `prix_menu`,
        `nombre_personne`,
        `prix_livraison`,
        `statut`,
        `pret_materiel`,
        `restitution_materiel`,
        `utilisateur_id`,
        `menu_id`,
        `lieu_prestation_id`,
        `motif_annulation`,
        `mode_contact`,
        `prix_total`,
        `depot_garantie`
    )
VALUES
    (
        1,
        'CMD-20260910-0001',
        '2026-09-10',
        '2026-09-25',
        '12:00',
        180,
        5,
        0,
        'en_attente',
        0,
        0,
        3,
        1,
        1,
        NULL,
        'email',
        180.00,
        0.00
    ),
    (
        2,
        'CMD-20260910-0002',
        '2026-09-10',
        '2026-09-24',
        '19:00',
        168,
        6,
        5,
        'acceptee',
        0,
        0,
        3,
        4,
        2,
        NULL,
        'email',
        173.00,
        0.00
    ),
    (
        3,
        'CMD-20260910-0003',
        '2026-09-10',
        '2026-09-23',
        '12:30',
        220,
        4,
        0,
        'en_preparation',
        1,
        0,
        3,
        5,
        1,
        NULL,
        'telephone',
        820.00,
        600.00
    ),
    (
        4,
        'CMD-20260910-0004',
        '2026-09-10',
        '2026-09-22',
        '18:00',
        220,
        4,
        5,
        'en_cours_livraison',
        0,
        0,
        3,
        5,
        2,
        NULL,
        'email',
        225.00,
        0.00
    ),
    (
        5,
        'CMD-20260910-0005',
        '2026-09-10',
        '2026-09-21',
        '12:00',
        180,
        5,
        0,
        'livree',
        1,
        0,
        3,
        1,
        1,
        NULL,
        'email',
        780.00,
        600.00
    ),
    (
        6,
        'CMD-20260910-0006',
        '2026-09-10',
        '2026-09-19',
        '19:30',
        232,
        8,
        5,
        'en_attente_retour_materiel',
        1,
        0,
        3,
        6,
        2,
        NULL,
        'telephone',
        837.00,
        600.00
    ),
    (
        7,
        'CMD-20260910-0007',
        '2026-09-10',
        '2026-09-15',
        '12:00',
        108,
        3,
        0,
        'terminee',
        0,
        0,
        3,
        1,
        1,
        NULL,
        'email',
        108.00,
        0.00
    ),
    (
        8,
        'CMD-20260910-0008',
        '2026-09-10',
        '2026-09-18',
        '19:00',
        168,
        6,
        0,
        'annulee',
        0,
        0,
        3,
        4,
        1,
        'Changement de date du côté client',
        'email',
        168.00,
        0.00
    );

INSERT INTO
    `vg_commande_statut_historique` (
        `historique_id`,
        `commande_id`,
        `statut`,
        `date_changement`
    )
VALUES
    (1, 1, 'en_attente', '2026-09-10 10:00:00'),
    (2, 2, 'en_attente', '2026-09-10 10:05:00'),
    (3, 2, 'acceptee', '2026-09-11 09:00:00'),
    (4, 3, 'en_attente', '2026-09-10 10:10:00'),
    (5, 3, 'acceptee', '2026-09-11 09:10:00'),
    (6, 3, 'en_preparation', '2026-09-20 08:00:00'),
    (7, 4, 'en_attente', '2026-09-10 10:15:00'),
    (8, 4, 'en_cours_livraison', '2026-09-22 10:00:00'),
    (9, 5, 'en_attente', '2026-09-10 10:20:00'),
    (10, 5, 'livree', '2026-09-21 13:00:00'),
    (11, 6, 'en_attente', '2026-09-10 10:25:00'),
    (
        12,
        6,
        'en_attente_retour_materiel',
        '2026-09-19 20:00:00'
    ),
    (13, 7, 'en_attente', '2026-09-10 10:30:00'),
    (14, 7, 'terminee', '2026-09-16 09:00:00'),
    (15, 8, 'en_attente', '2026-09-10 10:35:00'),
    (16, 8, 'annulee', '2026-09-12 11:00:00');

INSERT INTO
    `vg_horaire` (
        `horaire_id`,
        `jour`,
        `heure_ouverture`,
        `heure_fermeture`
    )
VALUES
    (1, 'Lundi', '09h00', '19h00'),
    (2, 'Mardi', '09:00', '19:00'),
    (3, 'Mercredi', '08H00', '19H00'),
    (4, 'Jeudi', '08H00', '19H00'),
    (5, 'Vendredi', '08H00', '19H00'),
    (6, 'Samedi', '08H00', '19H00'),
    (7, 'Dimanche', '', '');

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
        '15 rue Sainte-Catherine',
        'Bordeaux',
        '33000',
        44.83790000,
        -0.57500000,
        0.00
    ),
    (
        2,
        '8 avenue de la Libération',
        'Mérignac',
        '33700',
        44.84140000,
        -0.65200000,
        8.50
    );

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
        100,
        8,
        5,
        3,
        'Aucune précaution particulière',
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
        3,
        'Menu Halloween',
        12,
        29,
        'Ambiance spooky et gourmande',
        100,
        7,
        3,
        2,
        'À conserver au frais, consommer sous 24h',
        1
    ),
    (
        4,
        'Menu Fraîcheur',
        7,
        25,
        'Léger et estival',
        100,
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
        100,
        1,
        3,
        0,
        NULL,
        1
    ),
    (
        6,
        'Menu Anniversaire',
        10,
        32,
        'Un instant de douceur à partager',
        100,
        6,
        3,
        2,
        'Dessert à conserver au réfrigérateur',
        1
    ),
    (
        7,
        'Menu Saint Valentin',
        2,
        45,
        'Duo gourmand pour une soirée romantique',
        100,
        3,
        3,
        2,
        'Plateau de fruits de mer à consommer le jour même',
        1
    ),
    (
        8,
        'Menu Mariage',
        30,
        58,
        'Raffinement absolu pour le grand jour',
        100,
        9,
        3,
        5,
        'Chaîne du froid à respecter jusqu\'au service',
        1
    );

INSERT INTO
    `vg_menu_plat` (`menu_id`, `plat_id`)
VALUES
    (1, 3),
    (1, 7),
    (1, 11),
    (2, 1),
    (2, 8),
    (2, 14),
    (3, 5),
    (3, 10),
    (3, 13),
    (4, 19),
    (4, 21),
    (4, 22),
    (5, 3),
    (5, 6),
    (5, 20),
    (5, 24),
    (5, 25),
    (6, 2),
    (6, 9),
    (6, 12),
    (7, 4),
    (7, 7),
    (7, 11),
    (8, 3),
    (8, 6),
    (8, 24),
    (8, 25);

INSERT INTO
    `vg_plat` (
        `plat_id`,
        `titre_plat`,
        `description_plat`,
        `photo`,
        `categorie`,
        `is_active`
    )
VALUES
    (
        1,
        'Salade de saison',
        'Fraîche avec vinaigrette maison',
        'assets/img/plats/salade-saison.webp',
        'Entree',
        1
    ),
    (
        2,
        'Velouté de potiron',
        'Onctueux avec éclats de châtaigne',
        'assets/img/plats/veloute-potiron.webp',
        'Entree',
        1
    ),
    (
        3,
        'Saumon fumé',
        'Saumon d\'Ecosse, crème citronnée',
        'assets/img/plats/saumon-fume.webp',
        'Entree',
        1
    ),
    (
        4,
        'Tartare de tomates',
        'Basilic frais et huile d\'olive',
        'assets/img/plats/tartare-tomate.webp',
        'Entree',
        1
    ),
    (
        5,
        'Terrine de campagne',
        'Façon grand-mère, cornichons',
        'assets/img/plats/terrine-campagne.webp',
        'Entree',
        1
    ),
    (
        6,
        'Filet de bœuf',
        'Sauce échalotes, purée maison',
        'assets/img/plats/filet-boeuf.webp',
        'Plat',
        1
    ),
    (
        7,
        'Gambas sautées',
        'Ail, persil, riz basmati',
        'assets/img/plats/gambas.webp',
        'Plat',
        1
    ),
    (
        8,
        'Risotto aux champignons',
        'Crémeux, copeaux de parmesan',
        'assets/img/plats/risotto-champignon.webp',
        'Plat',
        1
    ),
    (
        9,
        'Suprême de poulet',
        'Crème forestière, légumes croquants et pommes grenailles',
        'assets/img/plats/supreme-volaille.webp',
        'Plat',
        1
    ),
    (
        10,
        'Cabillaud rôti',
        'Beurre blanc, fondue de poireaux',
        'assets/img/plats/cabillaud-poireaux.webp',
        'Plat',
        1
    ),
    (
        11,
        'Mousse au chocolat',
        'Chocolat noir intense',
        'assets/img/plats/mousse-chocolat.webp',
        'Dessert',
        1
    ),
    (
        12,
        'Crème brûlée',
        'Vanille bourbon, cassonade caramélisée',
        'assets/img/plats/creme-brulee.webp',
        'Dessert',
        1
    ),
    (
        13,
        'Fondant aux noix',
        'Cœur coulant, noix de Grenoble',
        'assets/img/plats/fondant-noix.webp',
        'Dessert',
        1
    ),
    (
        14,
        'Tarte aux pommes',
        'Pâte sablée pur beurre',
        'assets/img/plats/tarte-pommes.webp',
        'Dessert',
        1
    ),
    (
        15,
        'Salade de fruits',
        'Menthe fraîche, sirop léger',
        'assets/img/plats/salade-fruits.webp',
        'Dessert',
        1
    ),
    (
        19,
        'Quiche aux poireaux',
        'Pâte brisée maison, crème légère',
        'assets/img/plats/quiche-poireaux.webp',
        'Entree',
        1
    ),
    (
        20,
        'Daurade royale',
        'Cuite au four, tombée de tomates',
        'assets/img/plats/daurade-royale.webp',
        'Plat',
        1
    ),
    (
        21,
        'Pavlova aux fruits rouges',
        'Meringue croustillante, chantilly',
        'assets/img/plats/pavlova-rouge.webp',
        'Dessert',
        1
    ),
    (
        22,
        'Gaspacho andalou',
        'Tomates fraîches, poivrons, concombre',
        'assets/img/plats/gaspacho.webp',
        'Entree',
        1
    ),
    (
        23,
        'Filet de poulet rôti aux herbes',
        'Pommes de terre grenailles',
        'assets/img/plats/filet-poulet.webp',
        'Plat',
        1
    ),
    (
        24,
        'Tartelette au citron',
        'Crème onctueuse, meringue italienne',
        'assets/img/plats/tarte-citron.webp',
        'Dessert',
        1
    ),
    (
        25,
        'Assiette de fromages',
        'Sélection de nos producteurs locaux',
        'assets/img/plats/fromage.webp',
        'Dessert',
        1
    );

INSERT INTO
    `vg_regime` (`regime_id`, `libelle`)
VALUES
    (1, 'Végétarien'),
    (2, 'Vegan'),
    (3, 'Classique'),
    (4, 'Sans Lactose'),
    (5, 'Halal');

INSERT INTO
    `vg_role` (`role_id`, `libelle`)
VALUES
    (1, 'Admin'),
    (2, 'Employe'),
    (3, 'Utilisateur');

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
        '$2y$10$fkCnCWA4VLtqixIS5gau9e.iICLbEISJ05ZzlDIZl7nKFIo0ofqE.',
        'Admin',
        'Admin',
        '0600000000',
        'Toulouse',
        'France',
        '1 rue de la Paix',
        1,
        1,
        '2026-08-23 10:30:45'
    ),
    (
        2,
        'employe@vite-gourmand.fr',
        '$2y$10$WHKrD0rsbY6j5uGHOy/.6uTI4f5swAeNe/fd4E2nFYtqMYMKHc0OK',
        'Julie',
        'Martin',
        '0611111111',
        'Bordeaux',
        'France',
        '5 rue du Commerce',
        2,
        1,
        '2026-08-23 08:30:45'
    ),
    (
        3,
        'client@vite-gourmand.fr',
        '$2y$10$Rf23yVSbNfTZIhxTWWmIqeSa/jW2nignNSoZZk5U.Fn1cdbgw34yK',
        'Jean',
        'Dupont',
        '0622222222',
        'Bordeaux',
        'France',
        '12 rue de la Victoire',
        3,
        1,
        '2026-08-23 08:30:45'
    );