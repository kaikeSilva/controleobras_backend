# Executar migrações e otimizações (apenas no container app)
setup_laravel_app() {
    log "Configurando aplicação Laravel..."
    
    # Primeiro, limpar todos os caches
    log "Limpando todos os caches..."
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    
    # Verificar quais migrações existem
    log "Verificando migrações disponíveis..."
    php artisan migrate:status || log "AVISO: Não foi possível verificar status das migrações"
    
    # Detectar se há migrações pendentes com conflitos
    pending_migrations=$(php artisan migrate:status --pending 2>/dev/null | grep -c "Pending" || echo "0")
    log "Migrações pendentes: $pending_migrations"
    
    if [ "$pending_migrations" -gt "0" ]; then
        log "Há migrações pendentes. Verificando se há conflitos..."
        
        # Verificar se tabelas já existem (problema de estado inconsistente)
        existing_tables=$(php -r "
            try {
                \$pdo = new PDO('mysql:host=${DB_HOST};port=${DB_PORT:-3306};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');
                \$result = \$pdo->query('SHOW TABLES');
                \$count = 0;
                while (\$result->fetch()) { \$count++; }
                echo \$count;
            } catch (Exception \$e) {
                echo '0';
            }
        ")
        
        log "Tabelas existentes no banco: $existing_tables"
        
        if [ "$existing_tables" -gt "4" ]; then
            log "⚠️  Estado inconsistente detectado - tabelas existem mas migrações pendentes"
            log "Forçando reset completo das migrações..."
            
            # Reset#!/bin/bash

# Função para aguardar serviços usando PHP puro (EXATAMENTE como no dev)
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

# Instalar dependências do Composer se necessário (como no dev)
if [ ! -f "vendor/autoload.php" ]; then
    echo "Instalando dependências do Composer..."
    composer install --no-progress --no-interaction --optimize-autoloader
    echo "Composer instalado com sucesso."
else
    echo "Composer já está instalado."
fi

# Criar arquivo .env se não existir (como no dev)
if [ ! -f ".env" ]; then
    echo "Criando arquivo .env para ambiente $APP_ENV"
    cp .env.example .env
else
    echo "Arquivo .env já existe."
fi

# Aguardar serviços dependentes apenas se não for o serviço node (como no dev)
role=${CONTAINER_ROLE:-app}
if [ "$role" != "node" ]; then
    wait_for_service db 3306 "MySQL"
    wait_for_service redis 6379 "Redis"
fi

if [ "$role" = "app" ]; then
    echo "Iniciando aplicação Laravel..."
    
    # Executar migrações e configurações (como no dev, mas seguro para prod)
    php artisan key:generate --no-interaction || true
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Criar link simbólico do storage
    php artisan storage:link --force || true

    # Criar diretórios exigidos pelo Chrome Headless (Browsershot)
    mkdir -p /home/appuser/.config \
            /home/appuser/.cache \
            /home/appuser/.local/share

    # Garantir permissões corretas para o Chrome funcionar como appuser
    chown -R appuser:appuser /home/appuser/.config \
                            /home/appuser/.cache \
                            /home/appuser/.local/share
    chmod -R 700 /home/appuser/.config /home/appuser/.cache /home/appuser/.local/share

    
    # Ajustar permissões como no dev
    sudo chown -R appuser:appuser /home || true
    mkdir -p /home/appuser/.local/share
    chown -R appuser:appuser /home/appuser/.local/share || true
    
    # Iniciar PHP-FPM para produção (ao invés do servidor artisan)
    echo "Iniciando PHP-FPM para produção..."
    exec php-fpm --nodaemonize --force-stderr
    
elif [ "$role" = "queue" ]; then
    echo "Iniciando worker de filas..."
    
    # Mostrar informações de memória (como no dev)
    if command -v free &> /dev/null; then
        echo "Memória disponível:"
        free -h
    else
        echo "Comando 'free' não disponível, pulando verificação de memória."
    fi
    
    # Limpar caches antes de iniciar (como no dev)
    php artisan config:cache
    php artisan queue:clear
    
    echo "Iniciando queue worker..."
    echo "✅ Queue worker pronto para processar jobs!"
    
    # Iniciar worker (como no dev)
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
    
    # Instalar broadcasting se necessário (como no dev)
    php artisan install:broadcasting --reverb --no-interaction || true
    
    # Iniciar Reverb (como no dev)
    echo "Iniciando Reverb WebSocket server..."
    exec php artisan reverb:start --host=0.0.0.0 --port=6001 -vvv
    
elif [ "$role" = "scheduler" ]; then
    echo "Iniciando scheduler..."
    
    # Loop simples para scheduler
    while true; do
        php artisan schedule:run >> /var/www/storage/logs/scheduler.log 2>&1
        sleep 60
    done
    
else
    echo "Papel do container não reconhecido: $role"
    exit 1
fi
    esac
    
    sleep 5
    log "Container finalizado"
    exit 0
}

# Capturar sinais para graceful shutdown
trap cleanup SIGTERM SIGINT SIGQUIT

# Executar função principal
main "$@"esac
    
    sleep 5
    log "Container finalizado"
    exit 0
}

# Capturar sinais para graceful shutdown
trap cleanup SIGTERM SIGINT SIGQUIT

# Executar função principal
main "$@"