FROM php:8.2-apache

# Installer PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copier les fichiers du projet dans le répertoire web
COPY . /var/www/html/

# Exposer le port sur lequel Apache écoutera (8080 si tu utilises ce port)
EXPOSE 8080

# Lancer Apache
CMD ["apache2-foreground"]

