ARG PHP_VERSION=8.1-fpm-alpine3.18
ARG PHP_BASE=php-base
FROM php:${PHP_VERSION} as php-base

# postgresql
RUN set -ex && apk --no-cache add postgresql-dev
RUN docker-php-ext-install pdo_pgsql
RUN apk add --no-cache freetype-dev libjpeg-turbo-dev libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

RUN apk add --no-cache libzip-dev
RUN docker-php-ext-install zip

# install xdebug
RUN apk add --no-cache $PHPIZE_DEPS && pecl install xdebug && docker-php-ext-enable xdebug

#pecl and redis
RUN pecl install redis && docker-php-ext-enable redis

# composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Supervisor
RUN apk add --no-cache supervisor

# Timezone
RUN apk add --no-cache tzdata
RUN cp /usr/share/zoneinfo/${SERVER_TZ} /etc/localtime
RUN echo "${SERVER_TZ}" > /etc/timezone
ENV TZ=${SERVER_TZ}

WORKDIR /var/www/project

FROM ${PHP_BASE} AS base

COPY docker/images/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/images/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/images/php/supervisor-laravel.conf /etc/supervisor.d/laravel.conf

FROM base as code
WORKDIR /var/www/project
COPY --chown=www-data:www-data . .
RUN rm -rf docker

FROM code AS app
WORKDIR /var/www/project
RUN chmod +x startup.sh
RUN cp .env.example .env
RUN php artisan key:generate

ENTRYPOINT [ ]
CMD /bin/sh /var/www/project/startup.sh && exec php-fpm
