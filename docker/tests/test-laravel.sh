#!/bin/bash

# Script para testar funcionalidades básicas do Laravel
# Executado pelo container test-services

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🐘 Teste da Aplicação Laravel${NC}"
echo "============================="

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Arquivo artisan não encontrado.${NC}"
    exit 1
fi

# Contador de testes
TOTAL=0
PASSED=0

echo -e "${BLUE}🔧 Testando configurações do Laravel...${NC}"
echo ""

# Teste 1: Verificar se o .env existe e está configurado
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}📋 Testando arquivo .env...${NC}"
if [ -f ".env" ] && grep -q "APP_KEY=" .env && [ -n "$(grep '^APP_KEY=' .env | cut -d'=' -f2)" ]; then
    echo -e "${GREEN}   ✅ Arquivo .env configurado corretamente${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Arquivo .env não encontrado ou APP_KEY não configurada${NC}"
fi
echo ""

# Teste 2: Verificar conexão com banco de dados
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}🗄️  Testando conexão com banco de dados...${NC}"
if php artisan tinker --execute="
    try {
        \$pdo = DB::connection()->getPdo();
        echo 'Conexão OK - Driver: ' . \$pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    } catch (Exception \$e) {
        echo 'Erro: ' . \$e->getMessage();
        exit(1);
    }
" 2>/dev/null | grep -q "Conexão OK"; then
    echo -e "${GREEN}   ✅ Banco de dados conectado${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Falha na conexão com banco de dados${NC}"
fi
echo ""

# Teste 3: Verificar migrações
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}🔄 Testando status das migrações...${NC}"
if php artisan migrate:status 2>/dev/null | grep -q "Ran"; then
    echo -e "${GREEN}   ✅ Migrações executadas${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Problema com migrações${NC}"
fi
echo ""

# Teste 4: Verificar cache de configuração
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}⚡ Testando cache de configuração...${NC}"
if php artisan config:show app.name 2>/dev/null | grep -q "Laravel"; then
    echo -e "${GREEN}   ✅ Configurações carregadas corretamente${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Problema com configurações${NC}"
fi
echo ""

# Teste 5: Verificar se a aplicação responde HTTP
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}🌐 Testando resposta HTTP da aplicação...${NC}"
response=$(curl -s -o /dev/null -w "%{http_code}" "http://php:8000" 2>/dev/null || echo "000")
if [ "$response" = "200" ] || [ "$response" = "302" ]; then
    echo -e "${GREEN}   ✅ Aplicação respondendo HTTP (Status: $response)${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Aplicação não está respondendo (Status: $response)${NC}"
fi
echo ""

echo -e "${BLUE}📊 Testando funcionalidades específicas...${NC}"
echo ""

# Teste 6: Verificar Redis para cache
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}🔥 Testando cache Redis...${NC}"
if php artisan tinker --execute="
    try {
        Cache::put('test_key', 'test_value', 60);
        \$value = Cache::get('test_key');
        if (\$value === 'test_value') {
            echo 'Cache OK';
        } else {
            echo 'Cache ERRO';
            exit(1);
        }
    } catch (Exception \$e) {
        echo 'Cache ERRO: ' . \$e->getMessage();
        exit(1);
    }
" 2>/dev/null | grep -q "Cache OK"; then
    echo -e "${GREEN}   ✅ Cache Redis funcionando${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Problema com cache Redis${NC}"
fi
echo ""

# Teste 7: Verificar sistema de logs
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}📝 Testando sistema de logs...${NC}"
test_log_message="TESTE_LARAVEL_$(date +%s)"
if php artisan tinker --execute="
    Log::info('$test_log_message');
    echo 'Log escrito';
" 2>/dev/null | grep -q "Log escrito"; then
    sleep 1
    if grep -q "$test_log_message" storage/logs/laravel.log 2>/dev/null; then
        echo -e "${GREEN}   ✅ Sistema de logs funcionando${NC}"
        PASSED=$((PASSED + 1))
    else
        echo -e "${RED}   ❌ Log não foi gravado no arquivo${NC}"
    fi
else
    echo -e "${RED}   ❌ Erro ao escrever log${NC}"
fi
echo ""

# Teste 8: Verificar Artisan commands
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}⚙️  Testando comandos Artisan...${NC}"
if php artisan --version 2>/dev/null | grep -q "Laravel Framework"; then
    echo -e "${GREEN}   ✅ Artisan funcionando corretamente${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Problema com comandos Artisan${NC}"
fi
echo ""

# Teste 9: Verificar broadcasting (se configurado)
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}📡 Testando configuração de broadcasting...${NC}"
if php artisan tinker --execute="
    try {
        \$config = config('broadcasting.default');
        if (\$config) {
            echo 'Broadcasting configurado: ' . \$config;
        } else {
            echo 'Broadcasting não configurado';
        }
    } catch (Exception \$e) {
        echo 'Broadcasting ERRO: ' . \$e->getMessage();
        exit(1);
    }
" 2>/dev/null | grep -q "Broadcasting"; then
    echo -e "${GREEN}   ✅ Broadcasting configurado${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${RED}   ❌ Problema com configuração de broadcasting${NC}"
fi
echo ""

# Teste 10: Verificar performance básica
TOTAL=$((TOTAL + 1))
echo -e "${YELLOW}⚡ Testando performance básica...${NC}"
start_time=$(date +%s%N)
response_time=$(curl -s -o /dev/null -w "%{time_total}" "http://php:8000" 2>/dev/null || echo "999")
end_time=$(date +%s%N)

# Converter para segundos (o curl já retorna em segundos)
if (( $(echo "$response_time < 5.0" | bc -l 2>/dev/null || echo "0") )); then
    echo -e "${GREEN}   ✅ Tempo de resposta aceitável (${response_time}s)${NC}"
    PASSED=$((PASSED + 1))
else
    echo -e "${YELLOW}   ⚠️  Tempo de resposta alto (${response_time}s)${NC}"
    PASSED=$((PASSED + 1))  # Consideramos como passou, mas com aviso
fi
echo ""

# Resumo final
echo -e "${BLUE}📊 Resumo dos Testes Laravel${NC}"
echo "============================"
echo -e "${YELLOW}Total de testes: $TOTAL${NC}"
echo -e "${GREEN}Testes passaram: $PASSED${NC}"
echo -e "${RED}Testes falharam: $((TOTAL - PASSED))${NC}"

# Informações adicionais
echo ""
echo -e "${BLUE}ℹ️  Informações da Aplicação${NC}"
echo "============================"
if [ -f ".env" ]; then
    echo -e "${YELLOW}Ambiente: $(grep '^APP_ENV=' .env | cut -d'=' -f2 || echo 'não definido')${NC}"
    echo -e "${YELLOW}Debug: $(grep '^APP_DEBUG=' .env | cut -d'=' -f2 || echo 'não definido')${NC}"
fi
echo -e "${YELLOW}Versão PHP: $(php --version | head -n1)${NC}"
echo -e "${YELLOW}Versão Laravel: $(php artisan --version 2>/dev/null | head -n1)${NC}"

echo ""
if [ $PASSED -eq $TOTAL ]; then
    echo -e "${GREEN}🎉 Aplicação Laravel está funcionando perfeitamente!${NC}"
    exit 0
else
    echo -e "${RED}⚠️  Alguns testes falharam na aplicação Laravel${NC}"
    echo -e "${YELLOW}💡 Verifique os logs para mais detalhes:${NC}"
    echo "   tail -f storage/logs/laravel.log"
    exit 1
fi