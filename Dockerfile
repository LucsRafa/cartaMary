# PHP-FPM em Alpine (leve e com extensões do Laravel)
FROM php:8.2-fpm-alpine

# Dependências do sistema e do PHP
RUN apk add --no-cache \
    nginx curl bash icu-dev libzip-dev oniguruma-dev \
    libpng-dev freetype-dev libjpeg-turbo-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql mbstring zip intl gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Código da aplicação
WORKDIR /var/www/html
COPY . .

# Dependências PHP (produção)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissões de runtime
RUN chown -R www-data:www-data storage bootstrap/cache \
 && mkdir -p /run/nginx

# Script de inicialização (gera conf do Nginx com a $PORT do Render)
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080
CMD ["/start.sh"]
