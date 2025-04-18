FROM php:8.2-apache

# Installer les extensions nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Copier les fichiers du projet
COPY . /var/www/html/

# Configurer Apache pour qu’il écoute sur le port attribué par Render
RUN sed -i 's/Listen 80/Listen ${PORT}/' /etc/apache2/ports.conf && \
    sed -i 's/:80/:${PORT}/' /etc/apache2/sites-available/000-default.conf

# Dossier de travail
WORKDIR /var/www/html/

# Lancer Apache
CMD ["apache2-foreground"]

