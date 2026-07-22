FROM php:8.4-fpm-alpine AS php

RUN apk add -U --no-cache curl-dev
RUN docker-php-ext-install curl
RUN docker-php-ext-install mysqli
RUN docker-php-ext-install exif
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install apcu \
    && docker-php-ext-enable apcu
RUN apk add --no-cache libpng-dev \
    && docker-php-ext-install gd
RUN docker-php-ext-install pdo_mysql
FROM php:8.4-fpm-alpine AS php
RUN docker-php-ext-install pdo_mysql
RUN install -o www-data -g www-data -d /var/www/upload/image/
COPY php.ini /usr/local/etc/php/conf.d/uploads.ini/
