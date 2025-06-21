#######################################################################
# 1 ▸ BUILDER – dependências PHP (Composer)
#######################################################################
FROM composer:2.7 AS composer-deps
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader \
    --no-progress --no-scripts


#######################################################################
# 2 ▸ BUILDER – front-end (Vite)
#######################################################################
FROM node:22-alpine AS node-build
WORKDIR /front
COPY package*.json ./
RUN npm ci
COPY resources/ resources/
COPY vite.config.js .
RUN npm run build      # gera public/build
RUN rm -rf node_modules


#######################################################################
# 3 ▸ RUNTIME – PHP-FPM 8.4
#######################################################################
FROM php:8.4-fpm-alpine3.22 AS php

RUN apk add --no-cache \
      icu-data icu-libs libzip libzip-dev zlib-dev \
      tzdata curl su-exec mariadb-client \
      bash git make g++ pkgconfig autoconf

# ➜ extensões
RUN docker-php-ext-install \
        pdo pdo_mysql bcmath pcntl zip \
 && pecl install redis \
 && docker-php-ext-enable redis

# 3.3 usuário não-root
ARG USER_ID=1000
ARG GROUP_ID=1000
RUN addgroup -g $GROUP_ID appgroup \
 && adduser  -D -u $USER_ID -G appgroup appuser

#######################################################################
# 3.4 aplicação Laravel
#######################################################################
WORKDIR /var/www

# assets Vite → public/build
COPY --from=node-build      /front/public/build   ./public/build
# Composer binário + vendor de produção
COPY --from=composer-deps   /usr/bin/composer     /usr/local/bin/composer
COPY --from=composer-deps   /app/vendor           ./vendor
# resto do código
COPY . .

RUN composer dump-autoload --optimize --apcu

#######################################################################
# 3.5 entrypoint & finalização
#######################################################################
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
 && chown -R appuser:appgroup /var/www

USER appuser
EXPOSE 9000
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
