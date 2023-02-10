ARG VER_PHP=8.2
ARG VER_COMPOSER=2.5

FROM php:${VER_PHP}-cli AS base

RUN apt-get update && apt-get install -y zip git

RUN mkdir /app

WORKDIR /app

ADD ./app /app

# It would be nice if we could pull through specific VER_COMPOSER but i cant get ARG to work with COPY --from
COPY --from=composer:2.5 /usr/bin/composer /composer

RUN COMPOSER_ALLOW_SUPERUSER=1 /composer install --no-dev \
    && /composer dump-autoload \
    && rm -rf /composer

COPY ./entrypoint.sh /

RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

FROM base AS dev
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

RUN mkdir -p /tmp/xdebug \
    && touch /tmp/xdebug/xdebug.log

COPY ./entrypoint-debug.sh /

RUN chmod +x /entrypoint-debug.sh

ENTRYPOINT ["/entrypoint-debug.sh"]
