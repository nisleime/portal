# Use uma imagem oficial do PHP com Apache
FROM php:7.4.33-apache

# Defina o diretório de trabalho
WORKDIR /var/www/html

# Instale as dependências do sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install zip

# Instale o Composer
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Copie os arquivos da aplicação para o contêiner
COPY . /var/www/html

# Configure o Apache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Instale as dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Gere a chave da aplicação Laravel
RUN php artisan key:generate

# Exponha a porta 80
EXPOSE 80

# Comando para iniciar o servidor Apache
CMD ["apache2-foreground"]
