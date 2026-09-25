# Commit 46 : Dockerisation

À cette étape :
- `Dockerfile` (php:8.2-apache, racine `/public`, extensions pdo/mbstring/mongodb)
- `docker-compose.yml` (services `app`:8080 + `db` mariadb:10.6:3307, volumes persistants)
- `.dockerignore`, `.env.docker`, `.gitignore` mis à jour
- `Constants.php`/`Database.php`/`env.php` : gestion propre des variables selon l'environnement (local/Docker/prod)