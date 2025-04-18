# Utiliser l'image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires (PDO + MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Supprimer le message d'avertissement "ServerName"
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Apache écoute sur le port fourni par Render (variable d'environnement $PORT)
# Attention : on ne met pas EXPOSE ici, Render détecte le port automatiquement

# Modifier la config d'Apache pour écouter sur ce port dynamique
RUN sed -i 's/80/${PORT}/g' /etc/apache2/ports.conf && \
    sed -i 's/80/${PORT}/g' /etc/apache2/sites-enabled/000-default.conf

# Copier les fichiers du projet dans le dossier d'Apache
COPY . /var/www/html/

# Donner les bons droits aux fichiers
RUN chown -R www-data:www-data /var/www/html

# Lancer Apache
CMD ["apache2-foreground"]



