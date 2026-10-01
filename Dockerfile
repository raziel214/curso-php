# syntax=docker/dockerfile:1

# ---------- Etapa 1: dependencias PHP ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock* ./
# Si no existe composer.lock, composer lo resuelve en el build.
RUN composer install --no-dev --no-interaction --no-progress --no-scripts --prefer-dist --optimize-autoloader \
    || composer update --no-dev --no-interaction --no-progress --no-scripts --prefer-dist --optimize-autoloader

# ---------- Etapa 2: runtime ----------
FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql \
    && a2enmod rewrite

COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/app
COPY --from=vendor /app/vendor ./vendor
COPY . .

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
