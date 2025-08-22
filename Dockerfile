# Etapa base: PHP-FPM com extensões do Laravel e Composer
FROM php:8.2-fpm-bullseye AS base

# Dependências e extensões
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libzip-dev libicu-dev locales ca-certificates \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo pdo_mysql mbstring zip intl gd \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

# Instala dependências do PHP e otimiza autoload
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Permissões para cache e logs
RUN chown -R www-data:www-data storage bootstrap/cache

# Etapa final: Nginx + PHP-FPM
FROM debian:bullseye-slim

RUN apt-get update && apt-get install -y nginx php8.2-fpm ca-certificates curl \
 && rm -rf /var/lib/apt/lists/*

# Copia app da etapa base
COPY --from=base /var/www/html /var/www/html

# Copia script de inicialização
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Ajusta usuário/donos
RUN chown -R www-data:www-data /var/www/html /var/lib/nginx /var/log/nginx

# Variáveis úteis
ENV APP_ENV=production \
    APP_DEBUG=false

# Porta do Render (Render injeta $PORT)
EXPOSE 8080

WORKDIR /var/www/html
CMD ["/start.sh"]
