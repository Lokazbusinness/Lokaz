FROM php:8.2-apache

# Installer les extensions PHP
RUN docker-php-ext-install pdo pdo_mysql

# Corriger le warning ServerName
RUN echo "https://lokaz.onrender.com" >> /etc/apache2/apache2.conf

# Ajouter un script d’entrée pour que Apache écoute sur le bon port dynamique
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copier les fichiers du projet
COPY . /var/www/html/
# Exposer le port dynamique
EXPOSE ${PORT}
# Démarrer Apache
CMD ["apache2-foreground"]



