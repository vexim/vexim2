FROM php:8.2-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN addgroup -S -g 90 vexim && \
    adduser -S -u 90 -G vexim -h /var/vmail vexim && \
    cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini && \
    install-php-extensions pdo pdo_mysql gettext xdebug

WORKDIR /srv/app/
