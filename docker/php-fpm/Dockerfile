FROM php:8.1-fpm

COPY ./conf.d /usr/local/etc/php/conf.d

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libpq-dev

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo_pgsql

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/bin --filename=composer && \
    rm -rf /root/.composer/cache

WORKDIR /app

RUN chmod -R 777 /app
