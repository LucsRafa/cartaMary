#!/usr/bin/env bash
set -e

PORT="${PORT:-8080}"

# Nginx conf usando a porta do Render
cat > /etc/nginx/conf.d/default.conf <<NGINX
server {
    listen ${PORT};
    server_name _;

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_index index.php;
    }

    client_max_body_size 10M;
}
NGINX

# APP_KEY (melhor setar no Render; se faltar, gera temporário)
if [ -z "$APP_KEY" ]; then
  [ -f .env ] || cp .env.example .env || true
  php artisan key:generate || true
else
  [ -f .env ] || cp .env.example .env || true
  sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|g" .env
fi

# APP_URL padrão (ajuste nas env vars depois)
grep -q "^APP_URL=" .env || echo "APP_URL=http://localhost" >> .env

# Caches do Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Permissões
chown -R www-data:www-data storage bootstrap/cache

# Sobe PHP-FPM e Nginx
php-fpm -D
nginx -g 'daemon off;'
