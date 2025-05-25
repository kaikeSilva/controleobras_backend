# Template de Prompt para Chat Criar Instruções para Agente - Laravel 12

## [PROMPT BASE PARA GUIAR O CHAT]:

Você deve criar um guia de desenvolvimento passo-a-passo para um agente de AI que executará as tarefas em um projeto Laravel 12 rodando em Docker. O agente tem capacidades limitadas e se perde com tarefas complexas, então siga estas diretrizes rigorosamente:

### REGRAS FUNDAMENTAIS:

1. **Decomposição Extrema**: Divida CADA funcionalidade em tarefas de no máximo 3-5 ações
2. **Uma Coisa Por Vez**: Cada tarefa deve fazer APENAS uma coisa específica
3. **Validação Constante**: Após cada tarefa, inclua um comando de verificação
4. **Sem Dependências Complexas**: Evite tarefas que dependam de múltiplos arquivos simultaneamente

### FORMATO OBRIGATÓRIO PARA CADA TAREFA:

```markdown
## Tarefa X.Y: [Nome Descritivo Curto]

### Objetivo
[Uma única frase clara do que será feito]

### Container
`docker compose exec php` (ou websocket/queue/node conforme necessário)

### Arquivo
`app/path/to/file.php` (ou resources/views/, routes/, config/)

### Código
```[linguagem]
[Código COMPLETO do arquivo - não use "..." ou comentários como "resto do código"]
```

### Validação
```bash
docker compose exec php php -l app/path/to/file.php
docker compose exec php composer dump-autoload
```

### Critério de Sucesso
- [ ] Arquivo criado/modificado com sucesso
- [ ] Syntax check passa sem erros
- [ ] Autoload atualizado
- [ ] [Outro critério específico e verificável]
```

### ESTRUTURA DO GUIA:

1. **Fase de Preparação** (1-2 tarefas)
   - Verificar ambiente Docker
   - Verificar estrutura existente

2. **Fase de Models/Migrations** (2-4 tarefas)
   - Criar migration
   - Criar model
   - Definir relacionamentos
   - Executar migration

3. **Fase de Controllers** (2-3 tarefas)
   - Criar controller base
   - Adicionar methods CRUD
   - Adicionar validação

4. **Fase de Routes** (1-2 tarefas)
   - Definir rotas
   - Testar rotas

5. **Fase de Events/Jobs** (2-4 tarefas conforme necessário)
   - Criar event classes
   - Criar job classes
   - Configurar broadcasting

6. **Fase de Views/Emails** (2-3 tarefas conforme necessário)
   - Criar templates Blade
   - Configurar mail classes

### EXEMPLO DE TAREFA BEM DECOMPOSTA:

```markdown
## Tarefa 2.1: Criar Migration para Usuários

### Objetivo
Criar migration para tabela users com campos básicos

### Container
`docker compose exec php`

### Arquivo
`database/migrations/[timestamp]_create_users_table.php`

### Código
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

### Validação
```bash
docker compose exec php php artisan migrate:status
docker compose exec php php -l database/migrations/*_create_users_table.php
```

### Critério de Sucesso
- [ ] Migration criada sem erros de sintaxe
- [ ] Migration aparece em migrate:status como pending
```

### EVITE:

1. **Tarefas com múltiplas responsabilidades**
   - ❌ "Crie um controller com CRUD completo, validação e broadcasting"
   - ✅ "Crie controller base" → "Adicione method index" → "Adicione validação"

2. **Configurações complexas de uma vez**
   - ❌ "Configure broadcasting, queue e mail simultaneamente"
   - ✅ "Configure broadcasting" → "Teste broadcasting" → "Configure queue"

3. **Dependências entre arquivos na mesma tarefa**
   - ❌ "Crie o model e já adicione no controller"
   - ✅ "Crie o model" → "Importe o model no controller"

4. **Instruções vagas**
   - ❌ "Adicione validação adequada"
   - ✅ "Adicione validação: name required|string|max:255"

### TECNOLOGIAS DO PROJETO:
- Laravel 12.x com PHP 8.4
- MySQL 5.7 como banco de dados
- Redis para cache, sessões e filas
- Laravel Reverb para WebSocket/Broadcasting
- MailHog para desenvolvimento de emails
- Docker Compose com containers específicos:
  - `php`: Aplicação Laravel principal
  - `websocket`: Servidor Reverb
  - `queue`: Worker de filas
  - `node`: Assets frontend
  - `db`: MySQL database
  - `redis`: Cache e filas
  - `mailhog`: Email testing

### COMANDOS DISPONÍVEIS:
```bash
# LARAVEL ARTISAN:
docker compose exec php php artisan make:controller ControllerName
docker compose exec php php artisan make:model ModelName
docker compose exec php php artisan make:migration create_table_name
docker compose exec php php artisan make:event EventName
docker compose exec php php artisan make:job JobName
docker compose exec php php artisan make:mail MailName

# DATABASE:
docker compose exec php php artisan migrate
docker compose exec php php artisan migrate:status
docker compose exec php php artisan migrate:rollback

# VALIDATION:
docker compose exec php php -l app/path/to/file.php
docker compose exec php composer dump-autoload
docker compose exec php php artisan config:clear
docker compose exec php php artisan route:list

# TESTING:
docker compose exec test-services /tests/run-tests.sh
docker compose exec test-services /tests/test-connectivity.sh
docker compose exec test-services /tests/test-laravel.sh

# TINKER TESTING:
docker compose exec php php artisan tinker --execute="DB::connection()->getPdo()"
docker compose exec php php artisan tinker --execute="Redis::ping()"
```

### ARQUIVOS EXISTENTES (NÃO MODIFICAR SEM NECESSIDADE):
- `app/Http/Controllers/TestController.php` - Controller com métodos WebSocket
- `app/Events/` - 4 eventos WebSocket já existem
- `app/Jobs/` - 3 jobs de exemplo já existem
- `app/Mail/TestMail.php` - Classe mail de teste
- `routes/web.php` - Rotas WebSocket já configuradas
- `config/broadcasting.php` - Reverb já configurado
- `docker-compose.yaml` - Não modificar

### ESTRUTURA DE ARQUIVOS LARAVEL:
```
app/
├── Http/Controllers/     # Controllers da aplicação
├── Models/              # Models Eloquent
├── Events/              # Event classes para broadcasting
├── Jobs/                # Job classes para queue
├── Mail/                # Mailable classes
├── Listeners/           # Event listeners
└── Services/            # Service classes (criar se necessário)

database/
├── migrations/          # Database migrations
└── seeders/             # Database seeders

resources/
├── views/               # Templates Blade
└── views/emails/        # Email templates

routes/
├── web.php              # Web routes
├── api.php              # API routes
└── channels.php         # Broadcasting channels
```

Agora, com base nessas diretrizes, crie um guia passo-a-passo para:

## [MINHA DESCRIÇÃO DO QUE QUERO QUE SEJA CONSTRUÍDO]

[Aqui você descreve sua funcionalidade backend]

---

# Exemplo de Uso:

## [PROMPT BASE PARA GUIAR O CHAT]:
[Cole todo o conteúdo acima]

## [MINHA DESCRIÇÃO DO QUE QUERO QUE SEJA CONSTRUÍDO]:

Preciso criar um sistema de gerenciamento de usuários com:
- CRUD completo de usuários (create, read, update, delete)
- Campos: name, email, password, active (boolean), timestamps
- Validação adequada para cada campo
- API RESTful com responses em JSON
- Envio de email de boas-vindas quando criar usuário
- Broadcasting de evento quando usuário é criado/atualizado
- Job em fila para envio do email
- Testes básicos de funcionalidade

---

# Resultado Esperado:

O chat criará um artefato com 15-25 tarefas pequenas e específicas que o agente conseguirá executar sequencialmente sem se perder, cada uma validável e independente, seguindo as melhores práticas do Laravel 12 em ambiente Docker.