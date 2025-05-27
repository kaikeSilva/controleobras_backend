#!/bin/bash

# Função para aguardar serviços usando PHP puro
wait_for_service() {
    local host=$1
    local port=$2
    local service_name=$3
    
    echo "Aguardando $service_name em $host:$port..."
    
    while true; do
        if php -r "
            \$connection = @fsockopen('$host', $port, \$errno, \$errstr, 5);
            if (\$connection) {
                fclose(\$connection);
                exit(0);
            } else {
                exit(1);
            }
        "; then
            echo "$service_name está disponível!"
            break
        else
            echo "Aguardando $service_name..."
            sleep 2
        fi
    done
}

# Instalar dependências do Composer se necessário
if [ ! -f "vendor/autoload.php" ]; then
    echo "Instalando dependências do Composer..."
    composer install --no-progress --no-interaction --optimize-autoloader
    echo "Composer instalado com sucesso."
else
    echo "Composer já está instalado."
fi

# Criar arquivo .env se não existir
if [ ! -f ".env" ]; then
    echo "Criando arquivo .env para ambiente $APP_ENV"
    cp .env.example .env
else
    echo "Arquivo .env já existe."
fi

# Aguardar serviços dependentes apenas se não for o serviço node
role=${CONTAINER_ROLE:-app}
if [ "$role" != "node" ]; then
    wait_for_service db 3306 "MySQL"
    wait_for_service redis 6379 "Redis"
fi

if [ "$role" = "app" ]; then
    echo "Iniciando aplicação Laravel..."
    
    # Executar migrações e configurações
    php artisan key:generate --no-interaction || true
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Iniciar servidor
    echo "Iniciando servidor na porta $PORT"
    exec php artisan serve --port=$PORT --host=0.0.0.0
    sudo chown -R appuser:appuser /home
    mkdir -p /home/appuser/.local/share
    chown -R appuser:appuser /home/appuser/.local/share

elif [ "$role" = "queue" ]; then
    echo "Iniciando worker de filas..."
    
    # Mostrar informações de memória (se o comando existir)
    if command -v free &> /dev/null; then
        echo "Memória disponível:"
        free -h
    else
        echo "Comando 'free' não disponível, pulando verificação de memória."
    fi
    
    # Limpar caches antes de iniciar
    php artisan config:cache
    php artisan queue:clear
    
    echo "Iniciando queue worker..."
    echo "✅ Queue worker pronto para processar jobs!"
    
    # Iniciar worker
    exec php artisan queue:work \
        --verbose \
        --tries=3 \
        --timeout=60 \
        --memory=128 \
        --sleep=3 \
        --max-jobs=100 \
        --max-time=3600

elif [ "$role" = "websocket" ]; then
    echo "Configurando e iniciando WebSocket (Reverb)..."
    
    # Instalar broadcasting se necessário
    php artisan install:broadcasting --reverb --no-interaction || true
    
    # Iniciar Reverb
    echo "Iniciando Reverb WebSocket server..."
    exec php artisan reverb:start --host=0.0.0.0 --port=6001 -vvv

else
    echo "Papel do container não reconhecido: $role"
    exit 1
fi