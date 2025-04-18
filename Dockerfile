FROM php:8.2-apache

# Installer les extensions PHP
RUN docker-php-ext-install pdo pdo_mysql

# Corriger le warning ServerName
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Ajouter un script d’entrée pour que Apache écoute sur le bon port dynamique
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copier les fichiers du projet
COPY . /var/www/html/

# Apache doit écouter sur ce port (Render le fournit dynamiquement)
EXPOSE 10000

CMD ["docker-entrypoint.sh"]



