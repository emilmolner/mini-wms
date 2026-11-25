# Containerul cu aplicatia: PHP + Composer + extensia pentru MySQL.
FROM php:8.5-cli

RUN apt-get update && apt-get install -y --no-install-recommends git unzip libicu-dev libzip-dev \
    && docker-php-ext-install pdo_mysql intl zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY php.ini /usr/local/etc/php/conf.d/wms.ini
RUN git config --global --add safe.directory '*'

WORKDIR /app

# serverul web inclus in PHP: suficient pentru dezvoltare, fara Nginx sau Apache
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
