FROM php:8.2-apache

# Installer mysqli
RUN docker-php-ext-install mysqli

# Copier tous les fichiers du projet dans le dossier web de Apache
COPY . /var/www/html/

# Changer le port d'écoute si nécessaire
EXPOSE 8080

# Lancer le serveur Apache
CMD ["apache2-foreground"]
