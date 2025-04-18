FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Ajouter ServerName pour éviter les warnings d'Apache
RUN echo "ServerName lokaz.onrender.com" >> /etc/apache2/apache2.conf

# Ajouter le script d’entrée personnalisé
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copier tous les fichiers du projet dans le répertoire web
COPY . /var/www/html/

# Exposer le port par défaut d’Apache (80) — Render s’occupe de la redirection
EXPOSE 80

# Définir le point d’entrée pour démarrer Apache
ENTRYPOINT ["docker-entrypoint.sh"]

