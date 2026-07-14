INSERT INTO vg_plat (plat_id, titre_plat, description_plat) VALUES 
(19, 'Quiche aux poireaux', 'Pâte brisée maison, crème légère'),
(20, 'Daurade royale', 'Cuite au four, tombée de tomates'),
(21, 'Pavlova aux fruits rouges', 'Meringue croustillante, chantilly'),
(22, 'Gaspacho andalou', 'Tomates fraîches, poivrons, concombre'),
(23, 'Poulet rôti aux herbes', 'Pommes de terre grenailles'),
(24, 'Tartelette au citron', 'Crème onctueuse, meringue italienne'),
(25, 'Assiette de fromages', 'Sélection de nos producteurs locaux');
INSERT INTO vg_allergene_plat(plat_id, allergene_id) VALUES 
(19, 1), (19, 3), (19, 7),
(20, 4),
(21, 3), (21, 7),
(23, 10),
(24, 1), (24, 3), (24, 7),
(25, 7);
INSERT INTO vg_menu (menu_id, titre, description_menu, prix_par_personne, nombre_personne_minimum, quantite_restante, theme_id, regime_id) VALUES 
(4, 'Menu Fraîcheur', 'Léger et estival', 25.00, 2, 30, 2, 1);

INSERT INTO vg_menu (menu_id, titre, description_menu, prix_par_personne, nombre_personne_minimum, quantite_restante, theme_id, regime_id) VALUES 
(5, 'Menu Prestige', 'Une expérience gastronomique inoubliable', 55.00, 4, 10, 1, 3);
INSERT INTO vg_menu_plat (menu_id, plat_id) VALUES 
(4, 22), (4, 19), (4, 21),
(5, 3), (5, 6), (5, 20), (5, 24), (5, 25);