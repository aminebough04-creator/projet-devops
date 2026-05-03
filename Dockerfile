# Utilise une image PHP officielle avec Apache
FROM php:8.2-apache

# Copie tous vos fichiers (.php, dossiers assets, config...) dans le serveur web
COPY . /var/www/html/

# Donne les permissions nécessaires
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 80
