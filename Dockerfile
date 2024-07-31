# Use the official PHP image as base
FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install necessary PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Install Git
RUN apt-get update && apt-get install -y git

# Clone the repository
RUN git clone https://github.com/nisleime/portal.git /var/www/html

# Set permissions for the Apache document root
RUN chown -R www-data:www-data /var/www/html
