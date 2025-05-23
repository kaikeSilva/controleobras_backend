#!/bin/bash

# Script principal para executar todos os testes automatizados
# Executado pelo container test-services

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m'

echo -e "${PURPLE}🚀 INICIANDO BATERIA DE TESTES AUTOMATIZADOS${NC}"
echo "=============================================="
echo -e "${CYAN}Data/Hora: $(date)${NC}"
echo -e "${CYAN}Ambiente: ${APP_ENV:-production}${NC}"
echo ""

# Contador de testes
TOTAL_TESTS=0
PASSED_TESTS=0
FAILED_TESTS=0

# Função para executar um teste
run_test() {
    local test_name="$1"
    local test_script="$2"
    local test_description="$3"
    
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
    
    echo -e "${BLUE}📋 TESTE ${TOTAL_TESTS}: ${test_name}${NC}"
    echo -e "${YELLOW}   ${test_description}${NC}"
    echo "   $(date '+%H:%M:%S') - Iniciando..."
    
    # Executar o teste e capturar o resultado
    if bash "$test_script"; then
        echo -e "${GREEN}   ✅ PASSOU - ${test_name}${NC}"
        PASSED_TESTS=$((PASSED_TESTS + 1))
        return 0
    else
        echo -e "${RED}   ❌ FALHOU - ${test_name}${NC}"
        FAILED_TESTS=$((FAILED_TESTS + 1))
        return 1
    fi
}

# Função para mostrar resumo final
show_summary() {
    echo ""
    echo -e "${PURPLE}📊 RESUMO DOS TESTES${NC}"
    echo "===================="
    echo -e "${CYAN}Total de testes executados: ${TOTAL_TESTS}${NC}"
    echo -e "${GREEN}Testes que passaram: ${PASSED_TESTS}${NC}"
    echo -e "${RED}Testes que falharam: ${FAILED_TESTS}${NC}"
    
    if [ $FAILED_TESTS -eq 0 ]; then
        echo ""
        echo -e "${GREEN}🎉 TODOS OS TESTES PASSARAM!${NC}"
        echo -e "${GREEN}✅ Aplicação está funcionando corretamente${NC}"
        exit 0
    else
        echo ""
        echo -e "${RED}⚠️  ALGUNS TESTES FALHARAM!${NC}"
        echo -e "${RED}❌ Verifique os logs acima para mais detalhes${NC}"
        exit 1
    fi
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Arquivo artisan não encontrado.${NC}"
    echo -e "${RED}   Este script deve ser executado do diretório raiz do Laravel.${NC}"
    exit 1
fi

# Aguardar um pouco para garantir que todos os serviços estejam prontos
echo -e "${YELLOW}⏳ Aguardando estabilização dos serviços (10 segundos)...${NC}"
sleep 10

echo ""
echo -e "${PURPLE}🧪 EXECUTANDO TESTES INDIVIDUAIS${NC}"
echo "=================================="

# TESTE 1: Queue
echo ""
run_test "QUEUE" "/tests/test-queue.sh" "Verificar se o sistema de filas está funcionando"

# TESTE 2: WebSocket
echo ""
run_test "WEBSOCKET" "/tests/test-websocket.sh" "Verificar se o WebSocket/Reverb está funcionando"

# TESTE 3: Conectividade Básica (novo teste simples)
echo ""
run_test "CONECTIVIDADE" "/tests/test-connectivity.sh" "Verificar conectividade básica dos serviços"

# TESTE 4: Aplicação Laravel (novo teste simples)
echo ""
run_test "LARAVEL" "/tests/test-laravel.sh" "Verificar se a aplicação Laravel está respondendo"

echo ""
echo -e "${PURPLE}🏁 FINALIZANDO TESTES${NC}"
echo "====================="

# Mostrar resumo final
show_summary