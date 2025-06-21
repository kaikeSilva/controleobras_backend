#!/bin/bash
set -e

echo "=== ENTRYPOINT DEBUG ==="
echo "CONTAINER_ROLE: $CONTAINER_ROLE"
echo "USER: $(whoami)"
echo "PWD: $(pwd)"
echo "PHP VERSION: $(php --version | head -n1)"
echo "========================"

# Configuração por ambiente
APP_ENV="${APP_ENV:-production}"
echo "🚀 Ambiente detectado: $APP_ENV"

configure_environment() {
    case "$APP_ENV" in
        "local"|"development")
            echo "🔧 Configurando modo desenvolvimento..."
            # Instalar dependências de dev se solicitado
            if [ "$INSTALL_DEV_DEPS" = "true" ]; then
                echo "📦 Instalando dependências de desenvolvimento..."
                composer install --dev --no-interaction || true
            fi
            # Configurações mais permissivas para dev
            export DB_CONNECTION_TIMEOUT=30
            export QUEUE_RETRY_AFTER=30
            ;;
        "production")
            echo "🚀 Configurando modo produção..."
            # Configurações otimizadas para produção
            export DB_CONNECTION_TIMEOUT=10
            export QUEUE_RETRY_AFTER=300
            ;;
    esac
}

# Executar configuração
configure_environment

# Aguardar dependências se necessário
if [ "$CONTAINER_ROLE" = "app" ] || [ "$CONTAINER_ROLE" = "websocket" ] || [ "$CONTAINER_ROLE" = "queue" ] || [ "$CONTAINER_ROLE" = "scheduler" ]; then
    echo "Aguardando banco de dados..."
    while ! nc -z mysql_db 3306; do
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

        # Configurando caches Laravel
        echo "Configurando caches Laravel..."
        php artisan route:clear   || true
        php artisan config:clear  || true
        php artisan view:clear    || true
        
        if [ "$APP_ENV" = "production" ]; then
            echo "🚀 Aplicando caches de produção..."
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
        else
            echo "🔧 Modo desenvolvimento - sem cache agressivo"
            # Executar discovery para dev
            php artisan package:discover --ansi || true
        fi

        # Storage link e seeders para desenvolvimento
        if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
            # Storage link para desenvolvimento
            php artisan storage:link || true
            
            # Seeders apenas em desenvolvimento
            if [ "$RUN_SEEDERS" = "true" ]; then
                echo "🌱 Executando seeders..."
                php artisan db:seed --force || true
            fi
        fi

        echo "Rodando migrações..."
        php artisan migrate --force

        echo "Iniciando PHP-FPM..."
        exec php-fpm -F
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