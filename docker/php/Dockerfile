ARG PHP_VERSION=8.3
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-fpm-alpine


RUN apk add --no-cache \
bash shadow git curl unzip libzip-dev icu-dev oniguruma-dev \
libpng libpng-dev libjpeg-turbo libjpeg-turbo-dev freetype freetype-dev \
postgresql-dev build-base autoconf linux-headers


RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
&& docker-php-ext-install -j$(nproc) \
intl \
pdo_pgsql \
zip \
gd \
bcmath \
opcache


RUN pecl install xdebug \
&& docker-php-ext-enable xdebug \
&& echo "xdebug.mode=\${XDEBUG_MODE}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
&& echo "xdebug.client_host=\${XDEBUG_CLIENT_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY php.ini /usr/local/etc/php/php.ini

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www/html/api

CMD ["php-fpm", "-F"]