FROM php:7.4-cli-alpine

RUN apk add --no-cache composer

WORKDIR /usr/src/app

COPY composer.* ./

RUN composer install --prefer-dist --no-progress --no-scripts --no-autoloader \
    && rm -rf /root/.composer

COPY . /usr/src/app

RUN cp .env.production .env \
    && composer dump-autoload --no-scripts --optimize

VOLUME ["/usr/src/app/cache"]

EXPOSE 3408

ENTRYPOINT php clear-cache.php \
        && php index.php
