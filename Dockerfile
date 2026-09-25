# Part de PHP 8.2 + Apache ( VERIFIER SI ON PASSE EN 8.4 VOIR SI COMPATIBLE)
FROM php:8.2-apache
# Configure la racine web sur public/ (équivalent Docker du Procfile Heroku)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# remplace le chemin dans les fichiers de config
RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf

# installe les paquets nécessaires à la compilation (oniguruma) 
# (\ && rm -rf /var/lib/apt/lists/* nettoie le cache apt pour réduire la taille de l'image)
RUN apt-get update && apt-get install -y unzip libonig-dev libssl-dev \
&& rm -rf /var/lib/apt/lists/*

# Installe les extensions PHP nécessaires (pdo, pdo_mysql, mbstring, mongodb)
RUN docker-php-ext-install pdo pdo_mysql mbstring \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb

# Active les modules Apache nécessaires (expires, headers, rewrite)
RUN a2enmod expires headers rewrite
# Récupère Composer avant (optimisation du cache)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# definie le dossier travail dans le conteneur
WORKDIR /var/www/html
# Copier les fichiers composer.json et composer.lock dans le conteneur pour installer les dépendances
COPY composer.json composer.lock ./
# Installer les dépendances PHP avec Composer sans interaction, optimiser l'autoloader et ne pas exécuter les scripts définis dans composer.json
RUN composer install --no-interaction --optimize-autoloader --no-scripts

COPY . .

RUN chown -R www-data:www-data /var/www/html