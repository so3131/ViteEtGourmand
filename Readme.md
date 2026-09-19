![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php)
![Heroku](https://img.shields.io/badge/Deployed-Heroku-430098?logo=heroku)

# Guide d'installation et de déploiement local - Vite & Gourmand


Application web PHP MVC de traiteur événementiel — catalogue de menus, tunnel de commande, espaces client / employé / administrateur.

Ce document décrit la démarche pour installer et exécuter l'application en local (environnement XAMPP / MariaDB / PHP).

## Liens du projet
- 🚀 Application déployée : [https://vite-gourmand-student-a8b437ed0314.herokuapp.com/](https://vite-gourmand-student-a8b437ed0314.herokuapp.com/)
- 📋 Gestion de projet (Trello) : [https://trello.com/b/qGLPe8mZ](https://trello.com/b/qGLPe8mZ)

## Documentation

- 📘 [Documentation technique](docs/Documentations/Documentation_Technique.pdf)
- 📋 [Documentation gestion de projet](docs/Documentations/Documentation_Gestion_de_projet.pdf)
- 👤 [Manuel d'utilisation](docs/Documentations/Manuel_d'utilisation.pdf)
- 🎨 [Charte graphique, wireframes et mockups](docs/Chartes%20et%20mockups/Charte_Graphique.pdf)
- 📊 [Diagrammes et dictionnaire de données](docs/Diagrammes%20et%20Dictionnaire%20de%20donn%C3%A9es/) (MCD, cas d'utilisation, séquence)

## Prérequis

- **OS :** Windows avec [XAMPP](https://www.apachefriends.org/) (Apache & MariaDB)
- **PHP :** 8.2 ou supérieur
- **Composer**
- **Base NoSQL :** un cluster [MongoDB Atlas](https://www.mongodb.com/atlas) (le projet n'utilise pas de MongoDB local)
- **Extensions PHP requises :** `pdo_mysql`, `mongodb`, `mbstring`
- **Clé d'API** [OpenRouteService](https://account.heigit.org/manage/key?first_visit=true) (calcul d'itinéraire/frais de livraison)
- **Compte** [Brevo](https://www.brevo.com/) (envoi des emails transactionnels)

## Installation locale

### 1. Placer le projet dans XAMPP

```text
C:\xampp\htdocs\Projet_Vite_Gourmand_Finale
```

Si le dossier est renommé ou déplacé, adapter la constante `BASE_URL` dans `app/Config/Constants.php`.

### 2. Installer les dépendances PHP

Depuis la racine du projet :

```bash
composer install
```

(Installe notamment la bibliothèque MongoDB dans `vendor/`.)

### 3. Créer la base MariaDB

1. Démarrer Apache et MySQL depuis le panneau XAMPP.
2. Ouvrir [phpMyAdmin](http://localhost/phpmyadmin/).
3. Créer une base vide nommée `test_transit_ecf`.
4. Importer, **dans cet ordre**, les deux fichiers suivants (onglet **Importer**) :
   - `app/Database/Schema_VG.sql` (structure des tables)
   - `app/Database/fixture_VG.sql` (données de test)
5. Vérifier que les tables importées utilisent bien le préfixe `vg_`.

### 4. Créer l'utilisateur dédié

L'application se connecte via un utilisateur dédié plutôt que via `root` :

```sql
CREATE USER 'vg_creator'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON test_transit_ecf.* TO 'vg_creator'@'localhost';
FLUSH PRIVILEGES;
```
### 5. Configurer les variables d'environnement (`.env`)

Créer un fichier `.env` à la racine du projet (déjà présent dans `.gitignore` — ne jamais le committer)
<br>
Copier le contenu ci-dessous dedans, puis remplacer chaque valeur d'exemple par vos propres identifiants:

```env
# Base de données relationnelle
DB_HOST=localhost
DB_NAME=test_transit_ecf
DB_USER=vg_creator
DB_PASS=votre_mot_de_passe

# Base de données NoSQL (MongoDB Atlas)
MONGODB_URI=mongodb+srv://<utilisateur>:<mot_de_passe>@<cluster>.mongodb.net/vite_gourmand?appName=ProjetViteGourmand

# API externes & services
BREVO_API_KEY=votre_cle_brevo
MAIL_FROM_EMAIL=votre_email_expediteur
OPENROUTESERVICE_API_KEY=votre_cle_openroute

# URL absolue de l'application (utilisée dans les emails, ex. réinitialisation de mot de passe)
APP_URL=http://localhost/Projet_Vite_Gourmand_Finale/public
```

### 6. Emails

L'application utilise exclusivement l'API **Brevo** pour l'envoi d'emails transactionnels (confirmation de commande, réinitialisation de mot de passe, création de compte staff) — y compris en local. Aucune configuration SMTP locale n'est nécessaire ; il suffit de renseigner `BREVO_API_KEY`. Sans cette clé, `MailService::sendEmail()` échoue silencieusement et journalise l'erreur via `error_log()` sans bloquer le reste de l'application.

## Lancer l'application

Avec Apache et MySQL démarrés :

```text
http://localhost/Projet_Vite_Gourmand_Finale/public/index.php?page=home
```
Les principales pages sont accessibles via les liens du menu, ou directement en tapant le paramètre `?page=` correspondant dans l'URL :

| Paramètre | Page |
|---|---|
| `?page=home` | Accueil |
| `?page=search` | Liste des menus |
| `?page=contact` | Contact |
| `?page=login` | Connexion |
| `?page=signin` | Création de compte |
| `?page=mention` | Mentions légales et CGV |

### Comptes de test par défaut

| Rôle | Identifiant | Mot de passe |
|---|---|---|
| Administrateur | admin@vite-gourmand.fr | Vgadmin2026! |
| Employé | employe@vite-gourmand.fr | Vgemploye2026! |
| Client | client@vite-gourmand.fr | Vgclient2026! |

Ces trois comptes sont présents nativement dans `fixture_VG.sql` et fonctionnent aussi bien en local qu'en production. Le compte client dispose déjà de commandes de test couvrant tous les statuts possibles, ainsi que d'un avis en attente de modération.

## Structure du projet

```text
app/
├── Config/       Configuration, constantes, connexion base de données
├── Controllers/  Contrôleurs de l'application
├── Database/     Scripts SQL (Schema_VG.sql, fixture_VG.sql)
├── Helpers/      Fonctions utilitaires et services (mail, sécurité...)
├── Managers/     Accès aux données (requêtes SQL préparées) et orchestration
│                 (transactions, appels aux Models pour les calculs métier)
├── Models/       Logique métier pure (calculs, règles) — en principe sans accès
│                 direct à la base (exception connue : Timetable.php)
└── Views/        Vues HTML/PHP

public/
├── index.php     Point d'entrée et routeur
└── assets/       CSS, JavaScript, images

docs/
├── Documentations/                          Doc technique, gestion de projet, manuel utilisateur
├── Chartes et mockups/                      Charte graphique + wireframes/mockups
└── Diagrammes et Dictionnaire de données/   MCD, diagrammes UML, dictionnaire de données

vendor/           Dépendances Composer
Procfile          Configuration de déploiement Heroku
```

## Déploiement en production

L'application est déployée sur **Heroku** (add-on JawsDB pour la base MySQL). Le détail complet de la procédure — Config, add-ons, import de la base, vérifications post-déploiement — est documenté dans la **documentation technique** du projet.

## Branches Git

- `main` : branche de production, déployée sur Heroku.
- `dev` : branche de développement courant.
- Chaque fonctionnalité est développée dans une branche dédiée, testée, puis fusionnée dans `dev`. Après validation complète, `dev` est fusionnée dans `main`.