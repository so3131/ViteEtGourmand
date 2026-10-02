# Commit 51 : Migration PHP 8.2 -> 8.4

À cette étape :

- `composer.json`/`composer.lock` : contrainte PHP mise a jour (~8.4.0), ajout
  declarations explicites ext-curl et ext-fileinfo
- `Dockerfile` : image de base php:8.4-apache
- `Order.php` : suppression de curl_close() (obsolete depuis PHP 8.0, deprecie
  en 8.5), ajout de error_log() explicite sur les deux chemins de repli
  Haversine (echec cURL / reponse API inattendue)
- Test en local (Docker PHP 8.4.26) sur l'ensemble des parcours : connexion,
  catalogue, tunnel de commande complet, upload/suppression de plat,
  dashboard admin, RH. Aucun warning/deprecated detecte dans les logs.