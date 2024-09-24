FROM php:8.3-cli-alpine

ENV COMPOSER_HOME=/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

VOLUME ["/app"]

WORKDIR /app