FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Créer un fichier de configuration Apache avec la bonne écoute de port
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Changer la configuration par défaut pour écouter sur le bon port (Render -> $PORT)
RUN sed -i "s/80/\${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

# Copier tous les fichiers du projet dans le dossier racine d’Apache
COPY . /var/www/html/

# Apache doit écouter sur le port fourni par Render
EXPOSE $PORT

# Démarrer Apache
CMD ["apache2-foreground"]


