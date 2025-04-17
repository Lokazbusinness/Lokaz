FROM php:8.2-apache

COPY ./frontend/ /var/www/html/

EXPOSE 8080

# On change la config Apache pour écouter le port 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
