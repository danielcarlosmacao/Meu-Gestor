#!/bin/bash

cd /var/www/gestor || exit

echo "➡️ Atualizando código..."
git pull origin main || exit

echo "📦 Atualizando dependências PHP..."
composer install --no-dev --optimize-autoloader || exit

echo "🧱 Rodando migrations..."
php artisan migrate --force || exit

echo "🚀 Limpando e atualizando cache do Laravel..."
php artisan config:clear
php artisan cache:clear
php artisan route:cache
php artisan view:cache



echo "✅ Update finalizado com sucesso!"
