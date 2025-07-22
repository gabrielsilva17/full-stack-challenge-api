#!/bin/bash
set -e

. /usr/local/bin/porcentage.sh

echo "[ ${PORCENTAGE_0} ] Iniciando setup do container Laravel"

# 1) Cache e permissões iniciais
echo "[  5% ] Garantindo bootstrap/cache"
mkdir -p bootstrap/cache
chown -R www-data:www-data bootstrap/cache

# 2) .env
echo "[ ${PORCENTAGE_10} ] Verificando existência do .env"
if [ ! -f .env ]; then
  echo "[ ${PORCENTAGE_10} ] Copiando .env.example para .env"
  cp .env.example .env
  NEW_ENV=true
fi

# 3) Composer (somente se vendor/autoload.php não existir)
if [ ! -f vendor/autoload.php ]; then
  echo "[ ${PORCENTAGE_20} ] Instalando dependências PHP via Composer"
  composer install --no-interaction --prefer-dist --optimize-autoloader
  echo "[ ${PORCENTAGE_30} ] Dump autoload otimizado"
  composer dump-autoload -o
  echo "[ ${PORCENTAGE_40} ] Gerando documentação Swagger"
  php artisan l5-swagger:generate || true
else
  echo "[ ${PORCENTAGE_20} ] Dependências já instaladas (vendor/autoload.php encontrado)"
fi

# 4) Geração da chave da aplicação (somente no 1º run)
if [ "${NEW_ENV}" = true ] || grep -q '^APP_KEY=$' .env; then
  echo "[ ${PORCENTAGE_50} ] Gerando APP_KEY"
  php artisan key:generate --force
fi

# 5) Migrations & Seeds
echo "[ ${PORCENTAGE_65} ] Executando migrations e seeders"
php artisan migrate:fresh --force --seed

# 6) Storage link
echo "[ ${PORCENTAGE_70} ] Criando diretórios de storage e arquivo de log"
php artisan storage:link
chown -R www-data:www-data storage
chmod -R 775 storage
mkdir -p storage/framework/views
chown -R www-data:www-data storage/framework/views
chmod -R 775 storage/framework/views

# 7) Limpeza e rebuild de cache
echo "[ ${PORCENTAGE_80} ] Limpando caches"
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "[ ${PORCENTAGE_85} ] Recriando caches"
php artisan config:cache
php artisan route:cache
php artisan optimize

# 8) Ajuste de permissões finais
echo "[ ${PORCENTAGE_90} ] Ajustando permissões finais"
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
chmod -R 775 app/Docs

# 9) Finalização
echo "[ ${PORCENTAGE_100} ] Setup finalizado – iniciando Apache"
exec apache2-foreground
