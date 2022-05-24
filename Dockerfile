FROM php:8.0.18-apache

ENV TZ=America/Bogota
# EXPOSE 443

WORKDIR /var/www/html
COPY ./docker.ini /etc/php/php.ini
COPY ./prueba.conf /etc/apache2/sites-available/000-default.conf
# COPY ./docker/ssl /etc/apache2/ssl

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime  \
    && a2enmod rewrite  \
    && echo $TZ > /etc/timezone \
    && apt-get update && apt install -y \
    curl \
    libcap2-bin \
    nano \
    wget \
    zlib1g-dev \
    libicu-dev \
    libpng-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    &&docker-php-ext-install pdo pdo_pgsql \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip \
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer 
COPY . /var/www/html
COPY ./docker.env .env
RUN  composer install --ignore-platform-reqs\
    && chmod -R ug+rwx storage bootstrap/cache \
    && chgrp -R www-data storage bootstrap/cache \
    && php artisan key:generate \
    && php artisan jwt:secret \
    && php artisan storage:link