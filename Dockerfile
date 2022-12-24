FROM php:8-cli

RUN apt-get update && \
    apt-get install --no-install-recommends -y libzip-dev zip unzip git && \
    docker-php-ext-install gettext


RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /app
