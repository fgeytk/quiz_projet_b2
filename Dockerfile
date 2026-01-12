# Image PHP + Apache
FROM php:8.1-apache

# Installer mysqli et pdo_mysql
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copier le code dans le serveur
COPY . /var/www/html/

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html

# Exposer le port HTTP
EXPOSE 80
