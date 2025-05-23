#!/bin/bash

# Script para testar conectividade básica entre serviços
# Executado pelo container test-services

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔌 Teste de Conectividade dos Serviços${NC}"
echo "====================================="

# Função para testar conectividade
test_service() {
    local service_name="$1"
    local host="$2"
    local port="$3"
    local description="$4"
    
    echo -e "${YELLOW}🔍 Testando ${service_name}...${NC}"
    echo "   Host: $host | Porta: $port"
    echo "   Descrição: $description"
    
    if nc -z "$host" "$port" 2>/dev/null; then
        echo -e "${GREEN}   ✅ $service_name está acessível${NC}"
        return 0
    else
        echo -e "${RED}   ❌ $service_name não está acessível${NC}"
        return 1
    fi
}

# Função para testar HTTP
test_http_service() {
    local service_name="$1"
    local url="$2"
    local expected_status="$3"
    local description="$4"
    
    echo -e "${YELLOW}🌐 Testando ${service_name} via HTTP...${NC}"
    echo "   URL: $url"
    echo "   Status esperado: $expected_status"
    echo "   Descrição: $description"
    
    local status=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null || echo "000")
    
    if [ "$status" = "$expected_status" ]; then
        echo -e "${GREEN}   ✅ $service_name respondeu corretamente (HTTP $status)${NC}"
        return 0
    else
        echo -e "${RED}   ❌ $service_name não respondeu como esperado (HTTP $status)${NC}"
        return 1
    fi
}

# Contador de testes
TOTAL=0
PASSED=0

echo -e "${BLUE}📋 Testando conectividade de rede...${NC}"
echo ""

# Teste 1: MySQL
TOTAL=$((TOTAL + 1))
if test_service "MySQL" "db" "3306" "Banco de dados principal"; then
    PASSED=$((PASSED + 1))
fi
echo ""

# Teste 2: Redis
TOTAL=$((TOTAL + 1))
if test_service "Redis" "redis" "6379" "Cache e sessões"; then
    PASSED=$((PASSED + 1))
fi
echo ""

# Teste 3: WebSocket
TOTAL=$((TOTAL + 1))
if test_service "WebSocket" "websocket" "6001" "Servidor WebSocket/Reverb"; then
    PASSED=$((PASSED + 1))
fi
echo ""

# Teste 4: Aplicação PHP
TOTAL=$((TOTAL + 1))
if test_service "Aplicação PHP" "php" "8000" "Servidor web principal"; then
    PASSED=$((PASSED + 1))
fi
echo ""

echo -e "${BLUE}🌐 Testando serviços HTTP...${NC}"
echo ""

# Teste 5: Laravel HTTP
TOTAL=$((TOTAL + 1))
if test_http_service "Laravel App" "http://php:8000" "200" "Aplicação Laravel principal"; then
    PASSED=$((PASSED + 1))
fi
echo ""

# Teste adicional: Verificar se o Reverb responde
TOTAL=$((TOTAL + 1))
if test_http_service "Reverb WebSocket" "http://websocket:6001" "426" "Upgrade WebSocket esperado"; then
    PASSED=$((PASSED + 1))
fi
echo ""

echo -e "${BLUE}🔧 Testando conectividade interna...${NC}"
echo ""

# Teste 6: Redis com autenticação
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}🔍 Testando Redis com autenticação...${NC}"
if redis-cli -h redis -p 6379 -a "${DB_PASSWORD}" ping 2>/dev/null | grep -q "PONG"; then
    echo -e "${GREEN}   ✅ Redis autenticado corretamente${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Falha na autenticação Redis${NC}"
fi
echo ""

# Teste 7: MySQL com autenticação
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}🔍 Testando MySQL com autenticação...${NC}"
if mysql -h db -u "${DB_USERNAME}" -p"${DB_PASSWORD}" -e "SELECT 1" 2>/dev/null | grep -q "1"; then
    echo -e "${GREEN}   ✅ MySQL autenticado corretamente${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Falha na autenticação MySQL${NC}"
fi
echo ""

# Resumo final
echo -e "${BLUE}📊 Resumo da Conectividade${NC}"
echo "=========================="
echo -e "${YELLOW}Total de testes: $TOTAL${NC}"
echo -e "${GREEN}Testes passaram: $PASSED${NC}"
echo -e "${RED}Testes falharam: $((TOTAL - PASSED))${NC}"
echo ""

if [ $PASSED -eq $TOTAL ]; then
    echo -e "${GREEN}🎉 Todos os serviços estão conectados corretamente!${NC}"
    exit 0
else
    echo -e "${RED}⚠️  Alguns serviços não estão acessíveis${NC}"
    echo -e "${YELLOW}💡 Verifique se todos os containers estão rodando:${NC}"
    echo "   docker-compose ps"
    exit 1
fi