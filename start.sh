#!/usr/bin/env bash
set -e

# Render injeta $PORT. Se não houver, usa 8080.
PORT="${PORT:-8080}"

# Gera configurações do Nginx usando a porta do Render
cat > /etc/nginx/sites-available/default <<NGINX
server {
    listen ${PORT};
    server_name _;

    root /var/www/html/public;
    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    }

    client_max_body_size 10M;
}
NGINX

# Gera APP_KEY se não estiver definido (melhor: defina APP_KEY como env no Render)
if [ -z "$APP_KEY" ]; then
  echo ">> APP_KEY não definido; gerando um temporário (recomendado definir no Render)"
  php -r "copy('.env.example', '.env');" 2>/dev/null || true
  php artisan key:generate || true
else
  # Se há .env.example, copia e injeta APP_KEY
  if [ ! -f .env ]; then
    cp .env.example .env || true
  fi
  sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|g" .env
fi

# APP_URL default se não vier do Render
if ! grep -q "^APP_URL=" .env; then
  echo "APP_URL=http://localhost" >> .env
fi

# Caches do Laravel
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Ajusta permissões de runtime
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Sobe PHP-FPM e Nginx
service php8.2-fpm start
nginx -g "daemon off;"
