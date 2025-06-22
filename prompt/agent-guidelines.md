# AGENT BEST PRACTICES - Laravel 12 Docker Development

## 1. CRITICAL DECISION HIERARCHY

### ALWAYS TRY FIRST (Agent Capabilities)
**Execute estas ações ANTES de solicitar ajuda:**

#### Verification & File Management
```bash
# OBRIGATÓRIO: Verificar ANTES de criar
ls -la app/Events/                    # Check directory exists
ls -la app/Events/*WebSocket*         # Check similar files
grep -r "WebSocketTestEvent" app/     # Check class conflicts

# AGENT FUNCTIONS (preferred):
- Create/edit PHP/Blade/JS files via agent
- Modify existing files preserving structure
- Create directory structures
- Read files for analysis
```

### PREFER LARAVEL COMMANDS
```bash
# VIA DOCKER (preferred):
docker compose exec app php artisan make:controller ControllerName
docker compose exec app php artisan make:event EventName
docker compose exec app php artisan make:job JobName
docker compose exec app php artisan make:mail MailName
docker compose exec app php artisan make:service ServiceName --invokable  # Para serviços

# CONFIGURATION:
docker compose exec app php artisan config:clear
docker compose exec app composer dump-autoload
```

### DOCKER EXECUTION RULES
```bash
# CONTAINER-SPECIFIC COMMANDS:
docker compose exec app php [laravel-commands]      # Main app
docker compose exec queue php [queue-commands]      # Worker
```

### REQUEST HELP ONLY WHEN BLOCKED
**✅ Valid reasons:** "Permission denied", "Access denied", "File is read-only", build failures
**❌ Invalid reasons:** Files not attempted, standard Laravel operations, syntax errors

---

## 2. PROJECT-SPECIFIC STANDARDS

### Container Architecture
```bash
# CONTAINER ROLES:
Ler o docker-compose.yml para entender os containers
```

### Error Monitoring
```bash
# REAL-TIME MONITORING:
docker compose logs -f app | grep -i error
```

### WebSockets & Broadcasting
```bash
# INICIAR SERVIDOR WEBSOCKET:
docker compose exec app php artisan reverb:start

# CONFIGURAR CANAIS PRIVADOS:
# Sempre use canais privados com prefixo para garantir segurança
# Exemplo: private-pdf.{userId}

# TESTAR WEBSOCKETS:
docker compose exec app php artisan reverb:status
```

### Boas Práticas para Implementação de WebSockets

1. **Estrutura de Eventos**
   - Implemente a interface `ShouldBroadcast` em todos os eventos WebSocket
   - Use métodos `broadcastOn()`, `broadcastAs()` e `broadcastWith()` em cada evento
   - Mantenha a estrutura de payload consistente entre eventos relacionados

2. **Segurança de Canais**
   - Use canais privados com escopo de usuário (`private-pdf.{userId}`)
   - Configure autorização em `routes/channels.php`
   - Valide o usuário antes de autorizar o canal

3. **Serviços Centralizados**
   - Crie serviços dedicados para lógica de notificação
   - Use IDs únicos para jobs e processos
   - Implemente métodos para cada tipo de notificação

4. **Jobs Assíncronos**
   - Adicione parâmetros para jobId e userId em jobs que usam WebSockets
   - Notifique o início, progresso, conclusão e falhas
   - Use try/catch para capturar e notificar erros

5. **Controllers & Rotas**
   - Retorne informações sobre o canal WebSocket nas respostas API
   - Implemente endpoints para cancelamento e verificação de status
   - Use grupos de rotas com prefixos para organizar endpoints relacionados

6. **Documentação**
   - Documente todos os canais WebSocket e seus formatos de payload
   - Inclua instruções de teste para WebSockets
   - Liste todos os arquivos envolvidos na implementação

---

