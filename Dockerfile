# Utiliser une image PHP officielle avec Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires (ex: pdo_mysql pour la base de données)
RUN docker-php-ext-install pdo pdo_mysql

# Copier tous les fichiers de votre projet dans le dossier web du serveur
COPY . /var/www/html/

# Donner les permissions nécessaires au dossier
RUN chown -R www-data:www-data /var/www/html/

# Exposer le port 80 (standard pour le web)
EXPOSE 80