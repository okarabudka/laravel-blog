FROM php:8.5-fpm-alpine

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions pdo_mysql pdo_sqlite intl zip bcmath opcache pcntl

# Match the host user so files written to the mounted project stay editable.
ARG UID=1000
ARG GID=1000
RUN addgroup -g "${GID}" app && adduser -D -u "${UID}" -G app app

WORKDIR /var/www
USER app
