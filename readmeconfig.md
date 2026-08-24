# Guide d'installation et de déploiement local - Vite Gourmand

Ce document décrit les étapes nécessaires pour installer et faire fonctionner l'application **Vite Gourmand** en local sur votre machine (environnement XAMPP / MariaDB / PHP).

---

## A) Prérequis

Assurez-vous d'avoir installé les éléments suivants sur votre machine :

- **PHP** (version 8.2 ou supérieure recommandée)
- **MariaDB / MySQL** (via XAMPP, WampServer ou équivalent)
- **Composer**
- Un navigateur web

---

## B) Cloner ou placer le projet

Placez le dossier de votre projet dans le répertoire racine de votre serveur local (par exemple `C:\xampp\htdocs\Projet_Vite_Gourmand_Finale`)

---

## C) Configuration de la Base de Données

1. Lancez votre serveur **MySQL / MariaDB**.
2. Ouvrez **phpMyAdmin** (`http://localhost/phpmyadmin/`).
3. Créez une base de données vide nommée **`test_transit_ecf`** (ou importez directement le fichier fourni de création de la base de données et d'insertion des données qui se trouve dans app/database/Projet_vite_gourmand_finale.sql, le cas échéant passez directement à l'étape "Création de l'utilisateur dédié").
4. **Importation du fichier SQL :**
   - Rendez-vous dans l'onglet **Importer**.
   - Sélectionnez le fichier SQL situé dans le dossier du projet : `app/database/Projet_vite_gourmand_finale.sql`.
   - Cliquez sur **Exécuter** pour créer automatiquement toutes les tables et intégrer les données de test.

---

## D) Création de l'utilisateur dédié

Pour respecter les bonnes pratiques de développement, l'application se connecte via un utilisateur dédié et non par le compte `root`.

Exécutez cette requête dans l'onglet **SQL** de phpMyAdmin :

```sql
CREATE USER 'vg_creator'@'localhost' IDENTIFIED BY 'ton_mot_de_passe';
GRANT ALL PRIVILEGES ON test_transit_ecf.* TO 'vg_creator'@'localhost';
FLUSH PRIVILEGES;
```

## E) Configuration des variables d'environnement (`.env`)

1. À la racine du projet, créez ou vérifiez votre fichier **`.env`**.
2. Renseignez les accès à la base de données :

```env
DB_HOST=localhost
DB_NAME=test_transit_ecf
DB_USER=vg_creator
DB_PASS=ton_mot_de_passe
```
# Configuration MongoDB
MONGODB_URI=mongodb://localhost:27017
MONGODB_DATABASE=vite_et_gourmand

## F) Lancer l'application :
Assurez-vous que les services Apache et MySQL sont démarrés sur XAMPP.

Ouvrez votre navigateur et accédez à l'URL du projet :
http://localhost/Projet_Vite_Gourmand_Finale/public/ (adaptez le nom du dossier selon votre installation).

##  G) Comptes de test par défaut :

Administrateur : admin@vite-gourmand.fr

Employé : employe2@vitegourmand.fr

Utilisateur classique : test12000@test.com

