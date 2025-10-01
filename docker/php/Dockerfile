# syntax=docker/dockerfile:1
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apk add --no-cache \
    bash git curl icu icu-libs libpng libjpeg-turbo freetype postgresql-libs

RUN apk add --no-cache --virtual .build-deps \
      $PHPIZE_DEPS \
      icu-dev libzip-dev postgresql-dev \
      libpng-dev libjpeg-turbo-dev freetype-dev \
      build-base linux-headers \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" intl pdo_pgsql zip gd bcmath opcache \
 && pecl install xdebug-3.4.5 \
 && docker-php-ext-enable xdebug \
 && apk del .build-deps

COPY --from=composer:2.7 /usr/bin/composer /usr/local/bin/composer

RUN echo "xdebug.start_with_request=trigger" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=9000" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_connect_back=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \

COPY php.ini /usr/local/etc/php/php.ini

RUN addgroup -g 1000 -S www-data || true \
 && adduser  -u 1000 -S -D -G www-data www-data || true \
 && chown -R www-data:www-data /var/www

WORKDIR /var/www/html/api
USER www-data

CMD ["php-fpm", "-F"]
