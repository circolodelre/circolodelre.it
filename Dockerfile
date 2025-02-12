FROM php:8.0-cli

RUN apt-get update && \
    apt-get install --no-install-recommends -y locales libzip-dev zip unzip git && \
    docker-php-ext-install gettext

RUN echo 'it_IT.UTF-8 UTF-8' >> /etc/locale.gen && locale-gen

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /app
