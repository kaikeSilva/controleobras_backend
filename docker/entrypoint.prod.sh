#!/bin/bash
set -e

echo "=== ENTRYPOINT DEBUG ==="
echo "CONTAINER_ROLE: $CONTAINER_ROLE"
echo "USER: $(whoami)"
echo "PWD: $(pwd)"
echo "PHP VERSION: $(php --version)"
echo "========================"

# Aguardar dependências se necessário
if [ "$CONTAINER_ROLE" = "app" ] || [ "$CONTAINER_ROLE" = "websocket" ] || [ "$CONTAINER_ROLE" = "queue" ] || [ "$CONTAINER_ROLE" = "scheduler" ]; then
    echo "Aguardando banco de dados..."
    while ! nc -z db 3306; do
        echo "DB ainda não está pronto..."
        sleep 2
    done
    echo "Banco disponível!"

    echo "Aguardando Redis..."
    while ! nc -z redis 6379; do
        echo "Redis ainda não está pronto..."
        sleep 2
    done
    echo "Redis disponível!"
fi

# Executar comando baseado na role
case "$CONTAINER_ROLE" in
    "app")
        echo "Criando banco, se necessário..."
        mariadb --ssl=OFF \
                -h "$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
                -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` \
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        echo "Recriando caches Laravel..."
        php artisan route:clear   || true
        php artisan config:clear  || true
        php artisan view:clear    || true

        php artisan config:cache
        php artisan route:cache
        php artisan view:cache

        echo "Rodando migrações..."
        php artisan migrate --force

        echo "Iniciando PHP-FPM..."
        exec php-fpm -F
        ;;
    "websocket")
        echo "Iniciando Laravel Reverb..."
        exec php artisan reverb:start --host=0.0.0.0 --port=6001
        ;;
    "queue")
        echo "Iniciando Queue Worker..."
        # usa valor da env, cai para 'default' se não existir
        QUEUE_NAMES="${QUEUE_NAMES:-default}"

        exec php artisan queue:work redis \
            --queue="$QUEUE_NAMES" \
            --verbose --tries=3 --timeout=300
        ;;
    "scheduler")
        echo "Iniciando Laravel schedule:work…"
        # opcional: alinhar com o início do minuto
        sleep $((60 - $(date +%S)))

        # ➜ --sleep não existe mais
        exec php artisan schedule:work --verbose --no-interaction
        ;;
    *)
        echo "CONTAINER_ROLE não definido ou inválido. Iniciando PHP-FPM..."
        exec php-fpm -F
        ;;
esac