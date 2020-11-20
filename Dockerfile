FROM dangdungcntt/php:7.4-cli-composer

WORKDIR /home/app

COPY composer.* ./

RUN composer install --prefer-dist --no-progress --no-scripts --no-autoloader \
    && rm -rf /root/.composer

COPY . /home/app

RUN cp .env.production .env \
    && composer dump-autoload --no-scripts --optimize

VOLUME ["/home/app/cache"]

EXPOSE 3408

ENTRYPOINT php clear-cache.php \
        && php index.php
