# Arquitetura de Dockerização - Controle de Obras

Este documento descreve a arquitetura de dockerização do projeto Controle de Obras, explicando a estrutura de arquivos e suas funções.

## Estrutura de Arquivos

```
docker/
├── compose/
│   ├── docker-compose.base.yml    # Configurações base compartilhadas
│   ├── docker-compose.dev.yml     # Overrides para ambiente de desenvolvimento
│   └── docker-compose.prod.yml    # Overrides para ambiente de produção
├── env/
│   ├── .env.development           # Variáveis de ambiente para desenvolvimento
│   └── .env.production.example    # Exemplo de variáveis para produção
├── entrypoint.sh                  # Script de inicialização dos containers
└── README.md                      # Este arquivo
```

## Arquivos Principais

### Dockerfile

O `Dockerfile` na raiz do projeto utiliza uma abordagem multi-stage para otimizar o processo de build:

1. **Stage composer-deps**: Instala dependências PHP via Composer
2. **Stage node-build**: Compila assets frontend com Node.js
3. **Stage php**: Runtime final da aplicação PHP

Características principais:
- Baseado em PHP 8.3 Alpine
- Suporte a múltiplos ambientes via variáveis de ambiente
- Instalação condicional de dependências de desenvolvimento
- Otimização para produção (autoloader, assets)
- Usuário não-root para segurança

### docker/entrypoint.sh

Script de inicialização que gerencia o comportamento do container conforme o ambiente e função:

- Detecta automaticamente o ambiente (desenvolvimento/produção)
- Aguarda serviços dependentes (banco de dados, Redis)
- Executa tarefas específicas por papel do container:
  - **app**: Migrações, cache, seeders (dev), PHP-FPM
  - **websocket**: Laravel Reverb
  - **queue**: Workers de filas
  - **scheduler**: Agendador de tarefas

### docker/compose/

#### docker-compose.base.yml

Configurações compartilhadas entre todos os ambientes:
- Definição de serviços (php, websocket, queue, scheduler, nginx, db, redis, pdf-service)
- Configuração de redes e volumes
- Healthchecks para garantir disponibilidade

#### docker-compose.dev.yml

Overrides específicos para ambiente de desenvolvimento:
- Volumes para hot-reload de código
- Portas expostas para debugging
- Variáveis de ambiente para desenvolvimento
- Seeders habilitados

#### docker-compose.prod.yml

Overrides específicos para ambiente de produção:
- Limites de recursos (CPU, memória)
- Configurações de segurança
- Otimizações de performance
- Debug desabilitado

### docker/env/

#### .env.development

Variáveis de ambiente para desenvolvimento:
- URLs locais
- Configurações de debug
- Banco de dados de desenvolvimento
- Configurações para ferramentas de desenvolvimento

#### .env.production.example

Exemplo de variáveis para ambiente de produção:
- URLs de produção
- Configurações otimizadas
- Segurança reforçada
- Logs reduzidos

## Comandos de Uso

### Ambiente de Desenvolvimento

```bash
docker compose -f docker/compose/docker-compose.base.yml \
               -f docker/compose/docker-compose.dev.yml \
               up -d --build
```

### Ambiente de Produção

```bash
docker compose --env-file docker/env/.env.production \
               -f docker/compose/docker-compose.base.yml \
               -f docker/compose/docker-compose.prod.yml \
               up -d --build
```

## Benefícios da Nova Arquitetura

1. **Modularidade**: Separação clara entre configurações base e específicas de ambiente
2. **Manutenibilidade**: Arquivos menores e mais focados
3. **Flexibilidade**: Fácil adição de novos ambientes (staging, testing)
4. **Segurança**: Configurações específicas para produção
5. **Desenvolvimento**: Ambiente otimizado para desenvolvimento com hot-reload