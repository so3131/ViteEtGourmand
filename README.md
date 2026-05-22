# Vite & Gourmand - Projet MVC PHP

Un système de gestion de menus pour événements.

## Architecture
- **MVC Pattern**: Controllers, Models, Views
- **PHP 8.2+** avec PDO
- **Bootstrap 5** pour le frontend
- **MariaDB** pour la base de données

## Installation
1. Cloner le projet
2. Configurer `app/config/Constants.php`
3. Importer les fichiers SQL
4. Lancer un serveur PHP

## Structure
```
app/
├── config/        # Configuration
├── controllers/   # Logique métier
├── models/        # Accès données
├── views/         # Templates HTML
└── helpers/       # Fonctions utilitaires

public/
├── index.php      # Point d'entrée
└── assets/        # CSS, JS, images
```
