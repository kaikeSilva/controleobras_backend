# 🚀 Plano de Ação: Modernização da Dockerização

## **📊 Diagnóstico dos Arquivos Atuais**

### **✅ Arquivos Base Identificados:**
- `Dockerfile.production` → será base para novo `Dockerfile`
- `docker/entrypoint.prod.sh` → será base para novo `docker/entrypoint.sh`
- `docker-compose.prod.yaml` → será base para nova estrutura modular
- `.env` → será reorganizado em múltiplos arquivos de ambiente

---

## **📁 Nova Estrutura de Pastas**

```
controleobras/
├── docker/
│   ├── entrypoint.sh                 # ← Baseado em entrypoint.prod.sh
│   ├── nginx/
│   │   ├── default.conf              # ← Manter como está
│   │   └── entrypoint.sh             # ← Manter como está
│   ├── compose/
│   │   ├── docker-compose.base.yml   # ← Extraído de docker-compose.prod.yaml
│   │   ├── docker-compose.dev.yml    # ← Novo (overrides para dev)
│   │   └── docker-compose.prod.yml   # ← Extraído de docker-compose.prod.yaml
│   └── env/
│       ├── .env.development          # ← Baseado no .env atual
│       └── .env.production           # ← Baseado no .env atual
├── pdf-service/
│   └── Dockerfile                    # ← Manter inalterado (já está correto)
├── Dockerfile                        # ← Baseado em Dockerfile.production
└── .env                             # ← Manter como está (para compatibilidade)
```

---

## **🔧 Transformações Arquivo por Arquivo**

### **1. Dockerfile (Raiz)**

**📋 Base:** Copie `Dockerfile.production` para `Dockerfile`

**🔄 Mudanças Necessárias:**

```dockerfile
# COPIAR TODO O CONTEÚDO de Dockerfile.production

# ❌ REMOVER esta linha:
# COPY docker/entrypoint.prod.sh /usr/local/bin/entrypoint.sh

# ✅ SUBSTITUIR por:
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# ✅ ADICIONAR após a linha ENV PUPPETEER_EXECUTABLE_PATH=...
# Configuração flexível de ambiente
ARG APP_ENV=production
ENV APP_ENV=${APP_ENV} \
    INSTALL_DEV_DEPS=false \
    LOG_LEVEL=info

# ✅ MANTER todo o resto inalterado
```

**💡 Resultado:** Dockerfile único que detecta ambiente via `APP_ENV` mas mantém toda funcionalidade existente.

---

### **2. docker/entrypoint.sh**

**📋 Base:** Copie `docker/entrypoint.prod.sh` para `docker/entrypoint.sh`

**🔄 Mudanças Necessárias:**

```bash
#!/bin/bash
set -e

# ✅ MANTER todo o debug inicial
echo "=== ENTRYPOINT DEBUG ==="
echo "CONTAINER_ROLE: $CONTAINER_ROLE"
echo "USER: $(whoami)"
echo "PWD: $(pwd)"
echo "PHP VERSION: $(php --version | head -n1)"
echo "========================"

# ✅ ADICIONAR NOVA SEÇÃO antes do "Aguardar dependências"
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

# ✅ EXECUTAR configuração
configure_environment

# ✅ MANTER TODA a seção "Aguardar dependências" igual
if [ "$CONTAINER_ROLE" = "app" ] || [ "$CONTAINER_ROLE" = "websocket" ] || [ "$CONTAINER_ROLE" = "queue" ] || [ "$CONTAINER_ROLE" = "scheduler" ]; then
    # ... (copiar exatamente como está)
fi

# ✅ MODIFICAR apenas a seção "app" no case:
case "$CONTAINER_ROLE" in
    "app")
        # ✅ MANTER: criação do banco
        echo "Criando banco, se necessário..."
        mariadb --ssl=OFF \
                -h "$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
                -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` \
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

        # ✅ SUBSTITUIR a seção de cache por esta lógica condicional:
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

        # ✅ ADICIONAR antes das migrações:
        if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
            # Storage link para desenvolvimento
            php artisan storage:link || true
            
            # Seeders apenas em desenvolvimento
            if [ "$RUN_SEEDERS" = "true" ]; then
                echo "🌱 Executando seeders..."
                php artisan db:seed --force || true
            fi
        fi

        # ✅ MANTER: migrações e inicio do PHP-FPM exatamente igual
        echo "Rodando migrações..."
        php artisan migrate --force

        echo "Iniciando PHP-FPM..."
        exec php-fpm -F
        ;;

    # ✅ MANTER todos os outros cases (websocket, queue, scheduler, *) EXATAMENTE iguais
    "websocket")
        # ... (copiar exatamente)
    "queue")
        # ... (copiar exatamente)
    "scheduler")
        # ... (copiar exatamente)
    *)
        # ... (copiar exatamente)
esac
```

---

### **3. docker/compose/docker-compose.base.yml**

**📋 Base:** Extrair de `docker-compose.prod.yaml`

**🔄 Processo:**

```yaml
# ✅ COPIAR a estrutura básica de docker-compose.prod.yaml

version: '3.8'

services:
  # ✅ MODIFICAR apenas estas linhas no serviço php:
  php:
    build:
      context: .
      dockerfile: Dockerfile  # ← REMOVER .production
      target: php
      args:
        - USER_ID=${USER_ID:-1000}
        - GROUP_ID=${GROUP_ID:-1000}
        - APP_ENV=${APP_ENV:-production}  # ← ADICIONAR
    image: ${APP_NAME:-controleobras}-app:latest
    container_name: ${APP_NAME:-controleobras}-php
    restart: always
    env_file:
      - .env  # ← MANTER compatibilidade
    environment:
      - CONTAINER_ROLE=app
      - APP_ENV=${APP_ENV:-production}  # ← ADICIONAR
    # ✅ MANTER todo o resto (volumes, depends_on, networks, etc.) IGUAL

  # ✅ APLICAR o mesmo padrão para websocket, queue, scheduler:
  websocket:
    build:
      context: .
      dockerfile: Dockerfile  # ← REMOVER .production
      target: php
      args:
        - USER_ID=${USER_ID:-1000}
        - GROUP_ID=${GROUP_ID:-1000}
        - APP_ENV=${APP_ENV:-production}  # ← ADICIONAR
    # ✅ MANTER todo o resto igual, apenas adicionar:
    environment:
      - CONTAINER_ROLE=websocket
      - APP_ENV=${APP_ENV:-production}  # ← ADICIONAR

  # ✅ MANTER TODOS os outros serviços (db, redis, nginx, pdf-service) EXATAMENTE iguais
  db:
    # ... (copiar exatamente de docker-compose.prod.yaml)
  
  redis:
    # ... (copiar exatamente de docker-compose.prod.yaml)
    
  nginx:
    # ... (copiar exatamente de docker-compose.prod.yaml)
    
  pdf-service:
    # ... (copiar exatamente de docker-compose.prod.yaml)

# ✅ MANTER volumes e networks EXATAMENTE iguais
volumes:
  # ... (copiar de docker-compose.prod.yaml)

networks:
  # ... (copiar de docker-compose.prod.yaml)
```

---

### **4. docker/compose/docker-compose.dev.yml**

**📋 Base:** Novo arquivo (override para desenvolvimento)

**🔄 Criação:**

```yaml
version: '3.8'

# ✅ CRIAR do zero - overrides para desenvolvimento
services:
  php:
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - INSTALL_DEV_DEPS=true
      - RUN_SEEDERS=true
    volumes:
      - .:/var/www  # ← Hot reload para desenvolvimento
    ports:
      - "9000:9000"  # ← Expor PHP-FPM para debug

  websocket:
    environment:
      - APP_ENV=local
      - APP_DEBUG=true

  queue:
    environment:
      - APP_ENV=local
      - APP_DEBUG=true
      - QUEUE_NAMES=default  # ← Filas mais simples em dev

  scheduler:
    environment:
      - APP_ENV=local
      - APP_DEBUG=true

  # ✅ NGINX com configuração para desenvolvimento
  nginx:
    ports:
      - "8080:80"  # ← Porta padrão para dev

  # ✅ REMOVER deploy constraints para desenvolvimento
  # (não adicionar seções deploy nos overrides)
```

---

### **5. docker/compose/docker-compose.prod.yml**

**📋 Base:** Extrair configurações de produção de `docker-compose.prod.yaml`

**🔄 Processo:**

```yaml
version: '3.8'

# ✅ EXTRAIR apenas as configurações específicas de produção
services:
  php:
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    # ✅ COPIAR as seções deploy, security_opt, healthcheck de docker-compose.prod.yaml
    deploy:
      # ... (copiar exatamente)
    security_opt:
      # ... (copiar exatamente)
    healthcheck:
      # ... (copiar exatamente)

  websocket:
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
    deploy:
      # ... (copiar de docker-compose.prod.yaml)
    security_opt:
      # ... (copiar de docker-compose.prod.yaml)
    healthcheck:
      # ... (copiar de docker-compose.prod.yaml)

  # ✅ REPETIR padrão para queue e scheduler
  # ✅ MANTER todas as configurações de produção (deploy, security, health)
```

---

### **6. docker/env/.env.development**

**📋 Base:** Copiar `.env` atual

**🔄 Mudanças:**

```bash
# ✅ COPIAR TODO o conteúdo do .env atual

# ✅ MODIFICAR apenas estas linhas:
APP_ENV=local  # ← em vez de production
APP_DEBUG=true  # ← em vez de false

# ✅ ADICIONAR estas novas variáveis:
INSTALL_DEV_DEPS=true
RUN_SEEDERS=true
LOG_LEVEL=debug

# ✅ MANTER todas as outras configurações iguais
```

---

### **7. docker/env/.env.production**

**📋 Base:** Copiar `.env` atual

**🔄 Mudanças:**

```bash
# ✅ COPIAR TODO o conteúdo do .env atual

# ✅ MANTER APP_ENV=production (já está correto)

# ✅ ADICIONAR estas novas variáveis:
INSTALL_DEV_DEPS=false
RUN_SEEDERS=false

# ✅ MANTER todas as outras configurações iguais
```

---

---

## **📋 Checklist de Migração**

### **Fase 1: Preparação**
- [ ] Criar pasta `docker/compose/`
- [ ] Criar pasta `docker/env/`
- [ ] Copiar `Dockerfile.production` → `Dockerfile` com modificações
- [ ] Copiar `entrypoint.prod.sh` → `docker/entrypoint.sh` com modificações
- [ ] Criar `docker-compose.base.yml` baseado no atual
- [ ] Criar `docker-compose.dev.yml` e `docker-compose.prod.yml`
- [ ] Criar arquivos de environment

### **Fase 2: Teste**
- [ ] Testar build: `docker build -t test .`
- [ ] Testar desenvolvimento: 
  ```bash
  docker-compose -f docker/compose/docker-compose.base.yml \
                 -f docker/compose/docker-compose.dev.yml \
                 --env-file docker/env/.env.development up -d
  ```
- [ ] Validar todas as funcionalidades
- [ ] Testar produção:
  ```bash
  docker-compose -f docker/compose/docker-compose.base.yml \
                 -f docker/compose/docker-compose.prod.yml \
                 --env-file docker/env/.env.production up -d
  ```

### **Fase 3: Cleanup**
- [ ] Remover `Dockerfile.production`
- [ ] Remover `docker/entrypoint.prod.sh`
- [ ] Remover `docker-compose.prod.yaml`
- [ ] Atualizar documentação

## **🎯 Comandos de Uso Final**

### **Para Desenvolvimento:**
```bash
docker-compose -f docker/compose/docker-compose.base.yml \
               -f docker/compose/docker-compose.dev.yml \
               --env-file docker/env/.env.development \
               up -d --build
```

### **Para Produção:**
```bash
docker-compose -f docker/compose/docker-compose.base.yml \
               -f docker/compose/docker-compose.prod.yml \
               --env-file docker/env/.env.production \
               up -d --build
```

**🎯 Resultado:** Estrutura moderna mantendo 100% da funcionalidade atual!

### **💡 Como usar após a migração:**

**Desenvolvimento:**
```bash
docker-compose -f docker/compose/docker-compose.base.yml \
               -f docker/compose/docker-compose.dev.yml \
               --env-file docker/env/.env.development up -d
```

**Produção:**
```bash
docker-compose -f docker/compose/docker-compose.base.yml \
               -f docker/compose/docker-compose.prod.yml \
               --env-file docker/env/.env.production up -d
```