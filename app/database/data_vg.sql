-- 1. Nettoyage
DELETE FROM vg_allergene_plat;
DELETE FROM vg_menu_plat;
DELETE FROM vg_menu;
DELETE FROM vg_plat;
DELETE FROM vg_allergene;
DELETE FROM vg_theme;
DELETE FROM vg_regime;

-- 2. Référentiels
INSERT INTO vg_theme (theme_id, libelle) VALUES 
(1, 'Gastronomique'), (2, 'Traditionnel'), (3, 'Saint Valentin'), 
(4, 'Noël'), (5, 'Pâques'), (6, 'Anniversaire'), (7, 'Halloween'), 
(8, 'Nouvel An'), (9, 'Mariage');

INSERT INTO vg_regime (regime_id, libelle) VALUES 
(1, 'Végétarien'), (2, 'Vegan'), (3, 'Classique'), (4, 'Sans Lactose'), (5, 'Halal');

INSERT INTO vg_allergene (allergene_id, libelle) VALUES 
(1, 'Gluten'), (2, 'Crustacés'), (3, 'Œufs'), (4, 'Poissons'), (5, 'Arachides'),
(6, 'Soja'), (7, 'Lait'), (8, 'Fruits à coque'), (9, 'Céleri'), (10, 'Moutarde'),
(11, 'Graines de sésame'), (12, 'Anhydride sulfureux et sulfites'), (13, 'Lupin'), (14, 'Mollusques');

-- 3. Plats (avec description_plat intégrée)
INSERT INTO vg_plat (plat_id, titre_plat, description_plat) VALUES 
(1, 'Salade de saison', 'Fraîche avec vinaigrette maison'),
(2, 'Velouté de potiron', 'Onctueux avec éclats de châtaigne'),
(3, 'Saumon fumé', 'Saumon d''Ecosse, crème citronnée'),
(4, 'Tartare de tomates', 'Basilic frais et huile d''olive'),
(5, 'Terrine de campagne', 'Façon grand-mère, cornichons'),
(6, 'Filet de bœuf', 'Sauce échalotes, purée maison'),
(7, 'Gambas sautées', 'Ail, persil, riz basmati'),
(8, 'Risotto aux champignons', 'Crémeux, copeaux de parmesan'),
(9, 'Suprême de poulet', 'Crème forestière, légumes croquants'),
(10, 'Cabillaud rôti', 'Beurre blanc, fondue de poireaux'),
(11, 'Mousse au chocolat', 'Chocolat noir intense'),
(12, 'Crème brûlée', 'Vanille bourbon, cassonade caramélisée'),
(13, 'Fondant aux noix', 'Cœur coulant, noix de Grenoble'),
(14, 'Tarte aux pommes', 'Pâte sablée pur beurre'),
(15, 'Salade de fruits', 'Menthe fraîche, sirop léger');

-- 4. Liaisons plats <-> allergènes
INSERT INTO vg_allergene_plat(plat_id, allergene_id) VALUES 
(2, 7), (3, 4), (5, 1),
(6, 7), (7, 2), (7, 14), (8, 7), (8, 1), (9, 7), (10, 4), (10, 7),
(11, 3), (11, 7), (12, 3), (12, 7), (13, 1), (13, 3), (13, 7), (13, 8), (14, 1), (14, 7);

-- 5. Menus
INSERT INTO vg_menu (menu_id, titre, description_menu, prix_par_personne, nombre_personne_minimum, quantite_restante, theme_id, regime_id) VALUES 
(1, 'Menu Découverte', 'Un assortiment raffiné', 35.00, 2, 20, 1, 3),
(2, 'Menu Végé-Gourmand', 'Toute la fraîcheur des légumes', 28.00, 1, 15, 2, 1);

-- 6. Liaison menu <-> plats
INSERT INTO vg_menu_plat (menu_id, plat_id) VALUES 
(1, 1), (1, 6), (1, 11),
(2, 1), (2, 8);



-- UPDATE vg_utilisateur SET role_id = 2 WHERE utilisateur_id = X     brouillon pour aprees