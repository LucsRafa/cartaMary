#!/usr/bin/env sh
set -e

PORT="${PORT:-8080}"

# Garante diretórios do Nginx no Alpine
mkdir -p /run/nginx /etc/nginx/http.d

# Cria o virtual host usando a porta do Render
cat > /etc/nginx/http.d/default.conf <<EOF
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
EOF

# APP_KEY (recomendado definir no Render; senão gera temporário)
if [ -z "$APP_KEY" ]; then
  [ -f .env ] || cp .env.example .env || true
  php artisan key:generate || true
else
  [ -f .env ] || cp .env.example .env || true
  sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|g" .env
fi

# APP_URL padrão, se faltar
grep -q "^APP_URL=" .env || echo "APP_URL=http://localhost" >> .env

# Caches do Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Permissões necessárias
chown -R www-data:www-data storage bootstrap/cache

# Sobe PHP-FPM e Nginx
php-fpm -D
nginx -g 'daemon off;'
