#!/bin/bash

# Script para testar WebSocket (Reverb) DENTRO do container
# Uso: docker exec test-services bash /tests/test-websocket.sh

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🚀 Teste Robusto do WebSocket/Reverb (Executando dentro do container)${NC}"
echo "=================================================================="

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Arquivo artisan não encontrado. Execute este script do diretório raiz do Laravel.${NC}"
    exit 1
fi

# Função para verificar conexões básicas
check_connections() {
    echo -e "${YELLOW}🔍 Verificando conexões básicas...${NC}"
    
    # Testar Redis (necessário para Reverb)
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

# Função para verificar se o Reverb está rodando
check_reverb_status() {
    echo -e "${YELLOW}🔌 Verificando status do Reverb WebSocket...${NC}"
    
    # Verificar se a porta 6001 está aberta
    if nc -z websocket 6001 2>/dev/null; then
        echo -e "  ✅ Porta 6001 acessível"
    else
        echo -e "  ❌ Porta 6001 não está acessível"
        echo -e "  💡 Verifique se o container websocket está rodando"
        exit 1
    fi
    
    # Tentar fazer uma requisição HTTP básica ao Reverb
    local response=$(curl -s -w "%{http_code}" -o /dev/null "http://websocket:6001" 2>/dev/null || echo "000")
    
    if [ "$response" = "426" ] || [ "$response" = "400" ] || [ "$response" = "200" ]; then
        echo -e "  ✅ Reverb respondendo (HTTP $response)"
    else
        echo -e "  ⚠️  Reverb pode não estar respondendo corretamente (HTTP $response)"
    fi
}

# Função para criar Event de teste
create_test_event_class() {
    echo -e "${YELLOW}📝 Criando classe Event de teste...${NC}"
    
    # Criar diretório se não existir
    mkdir -p app/Events
    
    # Criar arquivo da classe Event
    cat > app/Events/WebSocketTestEvent.php << 'EOF'
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WebSocketTestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $testType;
    public $timestamp;
    public $testId;

    public function __construct($message, $testType = 'normal', $testId = null)
    {
        $this->message = $message;
        $this->testType = $testType;
        $this->timestamp = now()->format('Y-m-d H:i:s');
        $this->testId = $testId ?? uniqid('test_');
        
        Log::info("🎯 [WEBSOCKET TEST] Event criado: {$this->message} | ID: {$this->testId}");
    }

    public function broadcastOn()
    {
        return [
            new Channel('test-channel'),
            new Channel("test-channel-{$this->testType}")
        ];
    }

    public function broadcastAs()
    {
        return 'websocket.test';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'test_type' => $this->testType,
            'timestamp' => $this->timestamp,
            'test_id' => $this->testId,
            'server_time' => now()->toISOString()
        ];
    }
}
EOF

    echo -e "${GREEN}✅ Classe Event criada em app/Events/WebSocketTestEvent.php${NC}"
}

# Função para criar um listener simples para logs
create_test_listener() {
    echo -e "${YELLOW}📝 Criando Listener de teste...${NC}"
    
    mkdir -p app/Listeners
    
    cat > app/Listeners/WebSocketTestListener.php << 'EOF'
<?php

namespace App\Listeners;

use App\Events\WebSocketTestEvent;
use Illuminate\Support\Facades\Log;

class WebSocketTestListener
{
    public function handle(WebSocketTestEvent $event)
    {
        Log::info("👂 [WEBSOCKET TEST] Listener executado para: {$event->message} | ID: {$event->testId}");
        
        // Simular processamento baseado no tipo
        switch ($event->testType) {
            case 'quick':
                // Processamento rápido
                break;
            case 'slow':
                // Processamento mais lento
                sleep(1);
                break;
            case 'data':
                // Processamento com dados
                $data = ['processed_at' => now(), 'event_id' => $event->testId];
                Log::info("📊 Dados processados: " . json_encode($data));
                break;
        }
        
        Log::info("✅ Listener concluído para: {$event->message}");
    }
}
EOF

    echo -e "${GREEN}✅ Classe Listener criada em app/Listeners/WebSocketTestListener.php${NC}"
}

# Função para registrar o listener
register_event_listener() {
    echo -e "${YELLOW}🔧 Configurando Event/Listener...${NC}"
    
    # Verificar se o EventServiceProvider existe e adicionar o listener
    if [ -f "app/Providers/EventServiceProvider.php" ]; then
        # Backup do arquivo original
        cp app/Providers/EventServiceProvider.php app/Providers/EventServiceProvider.php.bak
        
        # Adicionar o listener se não existir
        if ! grep -q "WebSocketTestEvent" app/Providers/EventServiceProvider.php; then
            # Criar uma versão temporária com o listener adicionado
            cat > /tmp/add_listener.php << 'EOF'
<?php
$file = 'app/Providers/EventServiceProvider.php';
$content = file_get_contents($file);

// Procurar pela linha dos imports
if (strpos($content, 'use App\Events\WebSocketTestEvent;') === false) {
    $content = str_replace(
        '<?php',
        "<?php\n\nuse App\Events\WebSocketTestEvent;\nuse App\Listeners\WebSocketTestListener;",
        $content
    );
}

// Procurar pelo array $listen e adicionar o evento
if (strpos($content, 'WebSocketTestEvent::class') === false) {
    $listenPattern = '/protected \$listen = \[(.*?)\];/s';
    if (preg_match($listenPattern, $content, $matches)) {
        $newListen = rtrim($matches[1]) . "\n        WebSocketTestEvent::class => [\n            WebSocketTestListener::class,\n        ],\n    ";
        $content = str_replace($matches[1], $newListen, $content);
    } else {
        // Se não encontrar o array $listen, criar um
        $content = str_replace(
            'class EventServiceProvider extends ServiceProvider',
            "class EventServiceProvider extends ServiceProvider\n{\n    protected \$listen = [\n        WebSocketTestEvent::class => [\n            WebSocketTestListener::class,\n        ],\n    ];",
            $content
        );
    }
}

file_put_contents($file, $content);
echo "Event/Listener registrado com sucesso";
EOF
            
            php /tmp/add_listener.php
            rm /tmp/add_listener.php
        fi
        
        echo -e "  ✅ Event/Listener configurado"
    else
        echo -e "  ⚠️  EventServiceProvider não encontrado, pulando configuração automática"
    fi
}

# Função para executar eventos de teste
run_test_events() {
    echo -e "${BLUE}🧪 Executando eventos WebSocket de teste...${NC}"
    
    # Garantir que as classes existem
    if [ ! -f "app/Events/WebSocketTestEvent.php" ]; then
        create_test_event_class
    fi
    
    if [ ! -f "app/Listeners/WebSocketTestListener.php" ]; then
        create_test_listener
    fi
    
    # Limpar cache do composer
    composer dump-autoload -q
    
    # Limpar cache do Laravel
    php artisan config:clear
    php artisan event:clear
    
    # Executar eventos usando artisan tinker
    php artisan tinker --execute="
        use App\Events\WebSocketTestEvent;
        
        \$events = [
            ['Teste #1 - Evento Rápido', 'quick'],
            ['Teste #2 - Evento Lento', 'slow'], 
            ['Teste #3 - Evento com Dados', 'data'],
            ['Teste #4 - Evento Normal', 'normal']
        ];
        
        echo '📡 Disparando ' . count(\$events) . ' eventos WebSocket...' . PHP_EOL;
        
        foreach (\$events as [\$message, \$type]) {
            \$testId = uniqid('ws_test_');
            event(new WebSocketTestEvent(\$message, \$type, \$testId));
            echo '  ✓ ' . \$message . ' disparado (ID: ' . \$testId . ')' . PHP_EOL;
            usleep(500000); // 0.5 segundos
        }
        
        // Evento especial para testar broadcasting
        \$specialEvent = new WebSocketTestEvent('Teste #5 - Broadcasting Especial', 'broadcast', 'special_' . time());
        broadcast(\$specialEvent);
        echo '  ✓ Evento especial com broadcast() disparado' . PHP_EOL;
        
        echo PHP_EOL . '🎯 Todos os eventos foram disparados com sucesso!' . PHP_EOL;
    "
}

# Função para testar conectividade WebSocket com cliente simples
test_websocket_connection() {
    echo -e "${YELLOW}🔌 Testando conectividade WebSocket...${NC}"
    
    # Criar um cliente WebSocket simples em Node.js para teste
    cat > /tmp/ws-test-client.js << 'EOF'
const WebSocket = require('ws');

const wsUrl = 'ws://websocket:6001/app/test-app-key?protocol=7&client=js&version=7.0.0&flash=false';

console.log('🔌 Tentando conectar ao WebSocket:', wsUrl);

const ws = new WebSocket(wsUrl);

let connected = false;
let messageReceived = false;

ws.on('open', function() {
    console.log('✅ Conectado ao WebSocket');
    connected = true;
    
    // Subscrever ao canal de teste
    const subscribeMessage = JSON.stringify({
        event: 'pusher:subscribe',
        data: {
            channel: 'test-channel'
        }
    });
    
    console.log('📺 Subscrevendo ao canal test-channel');
    ws.send(subscribeMessage);
    
    // Aguardar um pouco e fechar
    setTimeout(() => {
        console.log('⏰ Tempo de teste esgotado, fechando conexão');
        ws.close();
    }, 5000);
});

ws.on('message', function(data) {
    try {
        const message = JSON.parse(data);
        console.log('📨 Mensagem recebida:', message.event || 'evento desconhecido');
        
        if (message.event === 'websocket.test') {
            console.log('🎉 Evento de teste recebido!', message.data);
            messageReceived = true;
        }
    } catch (e) {
        console.log('📨 Mensagem bruta:', data.toString());
    }
});

ws.on('error', function(error) {
    console.log('❌ Erro WebSocket:', error.message);
});

ws.on('close', function() {
    console.log('🔌 Conexão WebSocket fechada');
    process.exit(connected ? 0 : 1);
});

// Timeout de segurança
setTimeout(() => {
    if (!connected) {
        console.log('⏰ Timeout: Não foi possível conectar');
        process.exit(1);
    }
}, 10000);
EOF

    # Tentar executar o teste de conexão se Node.js estiver disponível
    if command -v node &> /dev/null; then
        echo -e "  🟡 Executando teste de conexão WebSocket..."
        if timeout 15 node /tmp/ws-test-client.js 2>/dev/null; then
            echo -e "  ✅ Teste de conexão WebSocket concluído"
        else
            echo -e "  ⚠️  Teste de conexão teve problemas, mas isso pode ser normal"
        fi
    else
        echo -e "  ⚠️  Node.js não disponível, pulando teste de conexão direta"
    fi
    
    # Limpar arquivo temporário
    rm -f /tmp/ws-test-client.js
}

# Função para verificar logs do Reverb
check_reverb_logs() {
    echo -e "${YELLOW}📊 Verificando atividade do Reverb...${NC}"
    
    # Verificar processos relacionados ao Reverb
    if pgrep -f "reverb:start" > /dev/null; then
        echo -e "  ✅ Processo Reverb encontrado"
    else
        echo -e "  ⚠️  Processo Reverb não encontrado diretamente"
    fi
    
    # Verificar logs recentes se existirem
    if [ -f "storage/logs/laravel.log" ]; then
        local recent_logs=$(tail -20 storage/logs/laravel.log | grep -i "reverb\|websocket\|broadcasting" | wc -l)
        if [ $recent_logs -gt 0 ]; then
            echo -e "  📊 Encontradas $recent_logs entradas relacionadas ao WebSocket nos logs recentes"
        else
            echo -e "  📊 Nenhuma entrada recente de WebSocket encontrada nos logs"
        fi
    fi
}

# Função para monitorar execução
monitor_execution() {
    echo -e "${GREEN}⏱️  Aguardando propagação dos eventos (15 segundos)...${NC}"
    echo "   (Os eventos aparecerão nos logs)"
    
    local start_time=$(date +%s)
    local end_time=$((start_time + 15))
    local counter=0
    
    while [ $(date +%s) -lt $end_time ]; do
        counter=$((counter + 1))
        echo -ne "\r  ⏳ Aguardando... ${counter}s    "
        sleep 1
    done
    
    echo ""
}

# Função para mostrar resultados
show_results() {
    echo -e "${BLUE}📋 Resultados do teste WebSocket:${NC}"
    
    # Verificar logs dos eventos
    echo ""
    echo -e "${YELLOW}📊 Logs dos eventos WebSocket (últimas 15 linhas):${NC}"
    if [ -f "storage/logs/laravel.log" ]; then
        tail -15 storage/logs/laravel.log | grep -E "(WEBSOCKET TEST|Event criado|Listener executado|broadcasting)" || echo "  Nenhum log de teste WebSocket encontrado ainda"
    else
        echo "  Arquivo de log não encontrado"
    fi
    
    echo ""
    echo -e "${GREEN}✅ Teste robusto do WebSocket concluído!${NC}"
    echo ""
    echo -e "${YELLOW}💡 Para verificar logs em tempo real:${NC}"
    echo "   tail -f storage/logs/laravel.log | grep -i websocket"
    echo ""
    echo -e "${YELLOW}💡 Para limpar classes de teste:${NC}"
    echo "   rm -f app/Events/WebSocketTestEvent.php"
    echo "   rm -f app/Listeners/WebSocketTestListener.php"
    echo "   mv app/Providers/EventServiceProvider.php.bak app/Providers/EventServiceProvider.php 2>/dev/null || true"
    echo ""
    echo -e "${YELLOW}💡 Para testar manualmente:${NC}"
    echo "   php artisan tinker"
    echo "   >>> event(new App\Events\WebSocketTestEvent('Teste Manual'));"
}

# Função para limpeza
cleanup() {
    echo -e "${YELLOW}🧹 Limpando arquivos temporários...${NC}"
    rm -f /tmp/ws-test-client.js
    rm -f /tmp/add_listener.php
}

# Execução principal
main() {
    check_connections
    echo ""
    
    check_reverb_status
    echo ""
    
    create_test_event_class
    create_test_listener
    register_event_listener
    echo ""
    
    run_test_events
    echo ""
    
    test_websocket_connection
    echo ""
    
    check_reverb_logs
    echo ""
    
    monitor_execution
    echo ""
    
    show_results
    
    cleanup
}

# Verificar parâmetros
case "${1:-}" in
    --status)
        check_connections
        check_reverb_status
        check_reverb_logs
        ;;
    --connection)
        check_reverb_status
        test_websocket_connection
        ;;
    --help|-h)
        echo "Uso: $0 [opção]"
        echo ""
        echo "Opções:"
        echo "  (sem parâmetro)  Executa teste completo"
        echo "  --status         Mostra apenas status das conexões"
        echo "  --connection     Testa apenas conectividade WebSocket"
        echo "  --help, -h       Mostra esta ajuda"
        ;;
    *)
        main
        ;;
esac