# Vite & Gourmand

Application web PHP MVC de présentation et de commande de menus pour événements.

## Prérequis

- Windows avec [XAMPP](https://www.apachefriends.org/) installé.
- Apache et MariaDB démarrés depuis le panneau de contrôle XAMPP.
- PHP 8.2 ou supérieur.
- Composer installé et disponible dans le terminal.
- MongoDB installé localement ou une instance MongoDB accessible.
- L'extension PHP `pdo_mysql` activée.
- L'extension PHP `mongodb` activée pour les statistiques et les fonctionnalités MongoDB.
https://account.heigit.org/manage/key?first_visit=true aussi

## Installation locale

### 1. Placer le projet dans XAMPP

Le projet doit être placé dans le dossier `htdocs` :

```text
C:\xampp\htdocs\Projet_Vite_Gourmand_Finale
```

Si le dossier ou le nom du projet change, adapter `BASE_URL` dans `app/Config/Constants.php`.

### 2. Installer les dépendances PHP

Depuis la racine du projet :

```bash
composer install
```

Cette commande installe notamment la bibliothèque PHP MongoDB dans `vendor/`.

### 3. Créer la base MariaDB

1. Ouvrir [phpMyAdmin](http://localhost/phpmyadmin/).
2. Créer une base nommée `test_transit_ecf`.
3. Importer uniquement `app/database/Projet_vite_gourmand_finale.sql`.
4. Vérifier que les tables importées utilisent bien le préfixe `vg_`.
5. Ne pas importer `data_vg.sql` ni `data_vg2.sql` après le script principal : ces fichiers contiennent des insertions complémentaires ou un nettoyage qui peuvent supprimer ou dupliquer des données déjà présentes.

La connexion actuellement utilisée par l'application est définie dans `app/Config/Database.php` :

```text
hôte     : localhost
base     : test_transit_ecf
utilisateur : root
mot de passe : vide
```

Si les identifiants MariaDB sont différents, modifier ce fichier avant de lancer l'application.

Le script `Projet_vite_gourmand_finale.sql` est actuellement le script complet de création et d'insertion. Il contient les tables des menus, plats, allergènes, utilisateurs, rôles, commandes, lieux, horaires, thèmes et régimes.

Les fichiers `data_vg.sql` et `data_vg2.sql` sont des jeux de données complémentaires ou de travail. Ils ne doivent pas être importés automatiquement lors d'une installation neuve.

### 4. Configurer MongoDB

Créer un fichier `.env` à la racine du projet. Ce fichier est ignoré par Git et ne doit pas contenir de secrets dans le dépôt.

Exemple de configuration locale :

```env
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=vite_et_gourmand
```

Démarrer MongoDB avant d'utiliser les statistiques administrateur. Le nom exact de la base peut être adapté à la configuration utilisée par `app/Managers/MongoStatsManager.php`.

### 5. Configurer les emails

En développement, l'application utilise la fonction PHP `mail()`. Pour tester les emails localement, configurer un outil comme MailHog, puis vérifier la configuration dans `app/Helpers/MailService.php`.

Avant un déploiement, remplacer la configuration locale par un service SMTP de production et ne pas publier les identifiants SMTP.

## Lancer l'application

Avec Apache démarré dans XAMPP, ouvrir :

```text
http://localhost/Projet_Vite_Gourmand_Finale/public/index.php?page=home
```

Les principales pages utilisent les paramètres suivants :

```text
?page=home                 Accueil
?page=search               Liste des menus
?page=contact              Contact
?page=login                Connexion
?page=signin               Création de compte
?page=mention              Mentions légales et CGV
```

Les espaces utilisateur, employé et administrateur nécessitent une session avec le rôle correspondant.



## Structure du projet

```text
app/
├── Config/          Configuration, constantes et accès base de données
├── Controllers/     Contrôleurs de l'application
├── Helpers/         Fonctions utilitaires et services
├── Managers/        Accès aux données et logique de gestion
├── models/          Modèles métier
└── views/           Vues HTML/PHP

public/
├── index.php        Point d'entrée et routeur
└── assets/          CSS, JavaScript et images

app/database/        Scripts SQL
vendor/               Dépendances Composer
```



















## Sécurité avant déploiement

Avant toute mise en ligne :

- Créer un vrai fichier `.env` hors du dépôt et remplacer les identifiants locaux.
- Désactiver `display_errors` en production.
- Ajouter une protection CSRF aux formulaires et actions sensibles.
- Utiliser HTTPS et des cookies de session sécurisés.
- Retirer les comptes, mots de passe et chaînes de connexion de test.
- Vérifier les droits d'accès pour chaque rôle.
- Tester les validations serveur et l'échappement des données affichées.
- Remplacer les informations fictives des mentions légales.

## État connu du projet

Le projet est en cours de finalisation ECF. Le schéma SQL principal est maintenant largement aligné avec les commandes et les menus utilisés par le code. Les points restant à stabiliser sont notamment la table de réinitialisation du mot de passe, le lien des avis avec les commandes, certaines routes d'actions, l'historique des statuts de commande, la sécurité CSRF, la conformité RGPD, l'accessibilité RGAA et la documentation de déploiement.

## Branches Git

Le développement courant se fait sur `dev`. Les fonctionnalités doivent être développées dans une branche dédiée, testées, puis fusionnées dans `dev`. Après validation complète, `dev` peut être fusionnée dans `main`.

## Feuille de route ECF

Cette checklist correspond à l'audit actuel du projet. Les cases cochées indiquent les éléments déjà présents dans le code, mais certains restent à tester ou à fiabiliser.

### Fonctionnalités déjà présentes

- [x] Architecture MVC PHP avec contrôleurs, modèles, managers et vues.
- [x] Page d'accueil avec présentation de Vite & Gourmand et de l'équipe.
- [x] Liste et détail des menus.
- [x] Filtres de menus en AJAX.
- [x] Affichage des plats, régimes, thèmes et allergènes.
- [x] Inscription, connexion, déconnexion et gestion des rôles.
- [x] Hachage des mots de passe.
- [x] Début de procédure de mot de passe oublié.
- [x] Tunnel de commande en plusieurs étapes.
- [x] Calcul des frais de livraison.
- [x] Réduction de 10 % à partir de cinq personnes supplémentaires.
- [x] Gestion partielle du stock.
- [x] Tableau de bord utilisateur.
- [x] Gestion partielle des commandes par l'employé.
- [x] Création et désactivation d'employés par l'administrateur.
- [x] Premières statistiques MongoDB et graphiques Chart.js.
- [x] Filtre du chiffre d'affaires par menu.
- [x] Filtre du chiffre d'affaires sur une période.
- [x] Affichage du détail du menu sélectionné sans réduire les graphiques comparatifs.
- [x] Formulaire de contact.
- [x] Mentions légales et CGV.

### Priorité 1 : rendre la base de données fiable

- [x] Choisir `Projet_vite_gourmand_finale.sql` comme script SQL officiel.
- [x] Supprimer les anciennes tables sans rapport avec Vite & Gourmand du script officiel.
- [x] Harmoniser les principaux noms de colonnes entre le SQL et le code PHP.
- [x] Ajouter aux commandes le lieu, le prix total, le dépôt de garantie et les informations d'annulation.
- [x] Ajouter au menu le délai de commande et les conditions de stockage.
- [x] Ajouter les tables des lieux de prestation et des villes.
- [ ] Ajouter la table des tokens de réinitialisation du mot de passe.
- [x] Ajouter les rôles et la relation entre utilisateur et rôle.
- [x] Ajouter les clés étrangères et les index principaux.
- [x] Ajouter des données de démonstration dans le script principal.
- [ ] Ajouter la commande concernée à la table `vg_avis`.
- [ ] Ajouter les dates de création et de modération des avis.
- [ ] Tester une installation neuve uniquement avec le script SQL officiel.

### Priorité 2 : corriger les routes et les actions

Vérifier dans `public/index.php` que chaque bouton et chaque formulaire possède une route réellement traitée.

- [ ] Ajouter ou corriger les routes d'ajout et de suppression de menu.
- [ ] Corriger les routes de modération des avis.
- [ ] Corriger les routes des conflits employés.
- [ ] Corriger ou retirer les routes de tickets non terminées.
- [ ] Corriger les redirections vers `dashboard-user`.
- [ ] Corriger les routes du mot de passe oublié.
- [ ] Ajouter la route des horaires si la page doit être accessible.
- [ ] Tester chaque action depuis l'interface, et pas uniquement l'affichage des pages.

### Priorité 3 : terminer les avis clients

Le parcours attendu est : `commande terminée -> avis utilisateur -> validation employé -> affichage sur l'accueil`.

- [ ] Réactiver et corriger le formulaire d'avis.
- [ ] Ajouter la route de soumission.
- [ ] Enregistrer les avis dans une base clairement choisie.
- [ ] Relier l'avis à l'utilisateur et à la commande.
- [ ] Autoriser un avis uniquement pour une commande terminée.
- [ ] Empêcher plusieurs avis pour une même commande.
- [ ] Permettre à l'employé de valider ou refuser un avis.
- [ ] Afficher uniquement les avis validés sur l'accueil.
- [ ] Corriger les namespaces et types MongoDB si MongoDB est utilisé pour les avis.

### Priorité 4 : terminer les commandes

- [ ] Implémenter les changements de statut par l'employé.
- [ ] Gérer les statuts : acceptée, en préparation, en livraison, livrée, retour matériel et terminée.
- [ ] Créer l'historique des statuts avec date, heure et auteur.
- [ ] Envoyer les emails liés aux changements importants.
- [ ] Envoyer la notification concernant les 600 euros de matériel non restitué.
- [ ] Interdire à l'utilisateur de modifier le menu choisi.
- [ ] Vérifier la propriété de la commande avant chaque action utilisateur.
- [ ] Restituer le stock lors d'une annulation si nécessaire.
- [ ] Harmoniser les libellés des statuts, notamment `annulee` et `annulée`.
- [ ] Décider si le stock représente des commandes ou des personnes et appliquer une seule règle.

### Priorité 5 : compléter les espaces employé et administrateur

- [ ] Permettre la création, modification et suppression complète des menus.
- [ ] Gérer les thèmes, régimes, plats, photos, allergènes et conditions du menu.
- [ ] Permettre la gestion complète des horaires.
- [ ] Finaliser la modération des avis.
- [ ] Finaliser la gestion des comptes utilisateurs et le bannissement.
- [ ] Protéger les actions de suppression, désactivation et modification.
- [x] Ajouter aux statistiques un filtre par menu.
- [x] Ajouter aux statistiques un filtre par période.
- [x] Afficher une vue globale adaptée à la période sélectionnée.
- [ ] Enregistrer dans MongoDB le véritable identifiant de la commande SQL.
- [ ] Corriger les types MongoDB signalés par l'analyse du code.
- [ ] Calculer le chiffre d'affaires à partir de commandes confirmées.

### Priorité 6 : sécurité

- [ ] Ajouter un token CSRF à tous les formulaires POST.
- [ ] Remplacer les actions destructives en GET par des actions POST protégées.
- [ ] Régénérer l'identifiant de session après connexion.
- [ ] Configurer les cookies avec `HttpOnly`, `Secure` et `SameSite`.
- [ ] Désactiver `display_errors` en production.
- [ ] Valider toutes les données côté serveur.
- [ ] Valider les adresses email avec `filter_var`.
- [ ] Appliquer la politique complète du mot de passe lors de sa réinitialisation.
- [ ] Limiter les tentatives de connexion.
- [ ] Échapper toutes les données affichées par PHP et JavaScript.
- [ ] Retirer les secrets et comptes de test des fichiers publics.
- [ ] Remplacer les URLs `localhost` et configurer un vrai service SMTP en production.

### Priorité 7 : RGPD et RGAA

- [ ] Ajouter une politique de confidentialité.
- [ ] Enregistrer le consentement utilisateur.
- [ ] Documenter les données collectées, les finalités et les durées de conservation.
- [ ] Permettre l'accès, la rectification et la suppression des données.
- [ ] Ajouter la suppression du compte utilisateur.
- [ ] Documenter les données envoyées par email et à MongoDB.
- [ ] Vérifier les textes alternatifs des images.
- [ ] Donner un nom accessible à chaque bouton et contrôle.
- [ ] Tester la navigation au clavier, les contrastes et le zoom à 200 %.
- [ ] Vérifier les formulaires avec un lecteur d'écran.
- [ ] Corriger les chemins d'images, CSS et JavaScript cassés.

### Tests à réaliser

- [ ] Parcours visiteur : accueil, menus, détail, contact et CGV.
- [ ] Inscription avec données valides et invalides.
- [ ] Connexion, déconnexion et compte désactivé.
- [ ] Mot de passe oublié avec adresse connue et inconnue.
- [ ] Filtres de menus sans rechargement.
- [ ] Commande avec le minimum de personnes.
- [ ] Réduction avec minimum + 5 personnes.
- [ ] Livraison à Bordeaux et dans une autre ville.
- [ ] Stock insuffisant.
- [ ] Modification et annulation d'une commande.
- [ ] Tous les changements de statut employé.
- [ ] Validation et refus d'un avis.
- [ ] Création, modification et désactivation d'un employé.
- [ ] Statistiques par menu et par période.
- [ ] Contrôle des droits pour chaque rôle.
- [ ] Tests CSRF, XSS et accès à des commandes appartenant à un autre utilisateur.

### Livrables ECF manquants

- [ ] Dépôt GitHub public.
- [ ] Application déployée avec URL fonctionnelle.
- [ ] Lien vers Jira, Trello, Notion ou autre outil de gestion de projet.
- [ ] Manuel utilisateur PDF avec identifiants de démonstration.
- [ ] Charte graphique PDF avec couleurs et polices.
- [ ] Trois maquettes desktop et trois maquettes mobiles.
- [ ] Documentation de gestion de projet.
- [ ] Documentation technique.
- [ ] MCD ou diagramme de classes.
- [ ] Diagramme de cas d'utilisation.
- [ ] Diagramme de séquence.
- [ ] Documentation du déploiement.
- [ ] Scripts SQL propres pour créer et alimenter la base.

## Ordre conseillé pour terminer

1. Harmoniser la base SQL et le code.
2. Corriger les routes et les redirections.
3. Terminer les statuts et l'historique des commandes.
4. Terminer les avis et leur modération.
5. Compléter les espaces employé et administrateur.
6. Ajouter CSRF et corriger la sécurité.
7. Finaliser les statistiques filtrées.
8. Corriger les assets et effectuer tous les tests.
9. Ajouter RGPD et accessibilité.
10. Produire les documents, déployer et faire la démonstration finale.
