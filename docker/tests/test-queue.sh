#!/bin/bash

# Script para testar queue DENTRO do container
# Uso: docker exec php bash /var/www/test-queue.sh

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Teste Robusto da Queue (Executando dentro do container)${NC}"
echo "============================================================"

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Arquivo artisan não encontrado. Execute este script do diretório raiz do Laravel.${NC}"
    exit 1
fi

# Função para verificar conexões
check_connections() {
    echo -e "${YELLOW}🔍 Verificando conexões...${NC}"
    
    # Testar Redis
    if php -r "
        require 'vendor/autoload.php';
        \$app = require_once 'bootstrap/app.php';
        \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
        \$kernel->bootstrap();
        try {
            \$redis = app('redis');
            \$redis->ping();
            echo 'Redis OK';
        } catch (Exception \$e) {
            echo 'Redis ERRO: ' . \$e->getMessage();
            exit(1);
        }
    "; then
        echo -e "  ✅ Redis conectado"
    else
        echo -e "  ❌ Erro na conexão Redis"
        exit 1
    fi
    
    # Testar Database
    if php artisan tinker --execute="
        try {
            DB::connection()->getPdo();
            echo 'DB OK';
        } catch (Exception \$e) {
            echo 'DB ERRO: ' . \$e->getMessage();
            exit(1);
        }
    " 2>/dev/null | grep -q "DB OK"; then
        echo -e "  ✅ Database conectado"
    else
        echo -e "  ❌ Erro na conexão Database"
        exit 1
    fi
}

# Função para verificar status da queue
check_queue_status() {
    echo -e "${YELLOW}📊 Status atual da queue:${NC}"
    
    php -r "
    require 'vendor/autoload.php';
    \$app = require_once 'bootstrap/app.php';
    \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
    \$kernel->bootstrap();
    
    try {
        \$redis = app('redis');
        \$queueSize = \$redis->llen('queues:default');
        \$delayedSize = \$redis->zcard('queues:default:delayed');
        \$reservedSize = \$redis->llen('queues:default:reserved');
        
        echo \"  📋 Jobs pendentes: \$queueSize\n\";
        echo \"  ⏰ Jobs com delay: \$delayedSize\n\";
        echo \"  🔒 Jobs reservados: \$reservedSize\n\";
        
    } catch (Exception \$e) {
        echo \"  ❌ Erro ao verificar status: \" . \$e->getMessage() . \"\n\";
    }
    "
}

# Função para criar job de teste real
create_test_job_class() {
    echo -e "${YELLOW}📝 Criando classe Job de teste...${NC}"
    
    # Criar diretório se não existir
    mkdir -p app/Jobs
    
    # Criar arquivo da classe Job
    cat > app/Jobs/QueueTestJob.php << 'EOF'
<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class QueueTestJob implements ShouldQueue
{
    use Queueable;
    
    public $tries = 3;
    public $timeout = 60;
    public $message;
    public $testType;
    
    public function __construct($message, $testType = 'normal')
    {
        $this->message = $message;
        $this->testType = $testType;
    }
    
    public function handle()
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $logMessage = "🎉 [TESTE ROBUSTO] {$this->message} | Tipo: {$this->testType} | Executado: {$timestamp}";
        
        Log::info($logMessage);
        
        // Simular diferentes tipos de processamento
        switch ($this->testType) {
            case 'quick':
                // Job rápido
                break;
            case 'slow':
                // Job mais lento
                sleep(2);
                break;
            case 'memory':
                // Job que usa memória
                $data = range(1, 1000);
                $sum = array_sum($data);
                Log::info("Soma calculada: $sum");
                break;
        }
        
        Log::info("✅ Job concluído: {$this->message}");
    }
    
    public function failed(\Throwable $exception)
    {
        Log::error("❌ Job falhou: {$this->message} | Erro: " . $exception->getMessage());
    }
}
EOF

    echo -e "${GREEN}✅ Classe Job criada em app/Jobs/QueueTestJob.php${NC}"
}

# Função para executar jobs de teste
run_test_jobs() {
    echo -e "${BLUE}🧪 Executando jobs de teste...${NC}"
    
    # Garantir que a classe existe
    if [ ! -f "app/Jobs/QueueTestJob.php" ]; then
        create_test_job_class
    fi
    
    # Limpar cache do composer para garantir que a classe seja encontrada
    composer dump-autoload -q
    
    # Executar jobs usando comando artisan
    php artisan tinker --execute="
        use App\Jobs\QueueTestJob;
        
        \$jobs = [
            ['Job #1 - Rápido', 'quick'],
            ['Job #2 - Lento', 'slow'], 
            ['Job #3 - Memória', 'memory'],
            ['Job #4 - Normal', 'normal']
        ];
        
        echo '📤 Despachando ' . count(\$jobs) . ' jobs de teste...' . PHP_EOL;
        
        foreach (\$jobs as [\$message, \$type]) {
            QueueTestJob::dispatch(\$message, \$type);
            echo '  ✓ ' . \$message . ' despachado' . PHP_EOL;
            usleep(300000); // 0.3 segundos
        }
        
        // Job com delay
        QueueTestJob::dispatch('Job #5 - Com Delay', 'quick')->delay(now()->addSeconds(10));
        echo '  ✓ Job com delay de 10 segundos despachado' . PHP_EOL;
        
        echo PHP_EOL . '🎯 Todos os jobs foram despachados com sucesso!' . PHP_EOL;
    "
}

# Função para monitorar execução
monitor_execution() {
    echo -e "${GREEN}⏱️  Monitorando execução por 30 segundos...${NC}"
    echo "   (Os jobs aparecerão nos logs do queue worker)"
    
    local start_time=$(date +%s)
    local end_time=$((start_time + 30))
    
    while [ $(date +%s) -lt $end_time ]; do
        local current_jobs=$(php -r "
        require 'vendor/autoload.php';
        \$app = require_once 'bootstrap/app.php';
        \$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
        \$kernel->bootstrap();
        try {
            \$redis = app('redis');
            echo \$redis->llen('queues:default');
        } catch (Exception \$e) {
            echo '0';
        }
        " 2>/dev/null)
        
        echo -ne "\r  📊 Jobs restantes na fila: $current_jobs    "
        sleep 2
    done
    
    echo ""
}

# Função para mostrar resultados
show_results() {
    echo -e "${BLUE}📋 Resultados do teste:${NC}"
    
    # Status final da queue
    check_queue_status
    
    echo ""
    echo -e "${YELLOW}📊 Últimos logs dos jobs (últimas 10 linhas):${NC}"
    if [ -f "storage/logs/laravel.log" ]; then
        tail -10 storage/logs/laravel.log | grep -E "(TESTE ROBUSTO|Job concluído|Job falhou)" || echo "  Nenhum log de teste encontrado ainda"
    else
        echo "  Arquivo de log não encontrado"
    fi
    
    echo ""
    echo -e "${GREEN}✅ Teste robusto concluído!${NC}"
    echo ""
    echo -e "${YELLOW}💡 Para ver logs em tempo real:${NC}"
    echo "   tail -f storage/logs/laravel.log | grep 'TESTE ROBUSTO'"
    echo ""
    echo -e "${YELLOW}💡 Para limpar a classe de teste:${NC}"
    echo "   rm -f app/Jobs/QueueTestJob.php"
}

# Execução principal
main() {
    check_connections
    echo ""
    
    check_queue_status
    echo ""
    
    run_test_jobs
    echo ""
    
    monitor_execution
    echo ""
    
    show_results
}

# Verificar parâmetros
case "${1:-}" in
    --status)
        check_connections
        check_queue_status
        ;;
    --help|-h)
        echo "Uso: $0 [opção]"
        echo ""
        echo "Opções:"
        echo "  (sem parâmetro)  Executa teste completo"
        echo "  --status         Mostra apenas status das conexões e queue"
        echo "  --help, -h       Mostra esta ajuda"
        ;;
    *)
        main
        ;;
esac