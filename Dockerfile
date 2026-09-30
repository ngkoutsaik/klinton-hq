FROM serversideup/php:8.3-fpm-nginx AS base

USER root
RUN install-php-extensions intl
USER www-data

FROM base AS vendor

COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

FROM node:24-alpine AS assets

WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY --from=vendor /var/www/html/vendor ./vendor
RUN npm run build


FROM base

ENV PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=true

COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /var/www/html/vendor ./vendor
COPY --from=assets --chown=www-data:www-data /app/public/build ./public/build

# Runs package:discover and filament:upgrade (publishes Filament's assets).
RUN composer dump-autoload --optimize --no-dev --no-interaction \
    && php artisan filament:optimize
