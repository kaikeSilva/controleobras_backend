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
docker compose exec controleobras-app-dev php artisan make:controller ControllerName
docker compose exec controleobras-app-dev php artisan make:event EventName
docker compose exec controleobras-app-dev php artisan make:job JobName
docker compose exec controleobras-app-dev php artisan make:mail MailName

# CONFIGURATION:
docker compose exec controleobras-app-dev php artisan config:clear
docker compose exec controleobras-app-dev composer dump-autoload
```

### DOCKER EXECUTION RULES
```bash
# CONTAINER-SPECIFIC COMMANDS:
docker compose exec controleobras-app-dev php [laravel-commands]      # Main app
docker compose exec controleobras-queue-dev php [queue-commands]      # Worker
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
docker compose logs -f controleobras-app-dev | grep -i error
```
---

