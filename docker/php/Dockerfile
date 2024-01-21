FROM php:8.1-fpm-alpine

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo pdo_mysql gettext xdebug
RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

WORKDIR /srv/app/
