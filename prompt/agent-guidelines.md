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

#### Laravel 12 Code Creation
```php
// STANDARD EVENT STRUCTURE:
class WebSocketTestEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    
    public function broadcastOn(): array  // Laravel 12: array return
    {
        return [new Channel('test-channel')];
    }
}
```

### PREFER LARAVEL COMMANDS
```bash
# VIA DOCKER (preferred):
docker compose exec php php artisan make:controller ControllerName
docker compose exec php php artisan make:event EventName
docker compose exec php php artisan make:job JobName
docker compose exec php php artisan make:mail MailName

# CONFIGURATION:
docker compose exec php php artisan config:clear
docker compose exec php composer dump-autoload
```

### DOCKER EXECUTION RULES
```bash
# CONTAINER-SPECIFIC COMMANDS:
docker compose exec php [laravel-commands]      # Main app
docker compose exec websocket [reverb-commands] # WebSocket
docker compose exec queue [queue-commands]      # Worker
docker compose exec node [npm-commands]         # Assets
```

### REQUEST HELP ONLY WHEN BLOCKED
**✅ Valid reasons:** "Permission denied", "Access denied", "File is read-only", build failures
**❌ Invalid reasons:** Files not attempted, standard Laravel operations, syntax errors

---

## 2. PROJECT-SPECIFIC STANDARDS

### Container Architecture
```bash
# CONTAINER ROLES:
php:8000        -> Main Laravel app (db, redis dependent)
websocket:6001  -> Reverb server (db, redis, node dependent)  
queue           -> Worker process (db, redis dependent)
node:5173       -> Vite dev server (independent)
db:3306         -> MySQL database
redis:6379      -> Cache/Queue/Sessions
mailhog:8025    -> Email testing
```

### Environment Configuration (.env)
```bash
# CONTAINER COMMUNICATION (internal):
DB_HOST=db
REDIS_HOST=redis
MAIL_HOST=mailhog
REVERB_HOST=websocket

# EXTERNAL ACCESS (browser):
VITE_REVERB_HOST="localhost"
VITE_REVERB_PORT=6001
```

### Existing Project Structure
```
app/
├── Events/         # 4 WebSocket events exist
├── Jobs/           # 3 queue jobs exist  
├── Mail/           # 1 mail class exists
├── Http/Controllers/TestController.php  # WebSocket methods implemented
└── Listeners/      # 1 WebSocket listener exists

resources/views/
├── test-form.blade.php      # Redis/Queue/Mail tests exist
└── websocket-test.blade.php # WebSocket interface (to create)
```

---

## 3. FILE & DIRECTORY MANAGEMENT

### Pre-Creation Verification (MANDATORY)
```bash
# ALWAYS CHECK FIRST:
1. ls -la [target_directory]/
2. find . -name "*ClassName*" -type f
3. grep -r "class ClassName" app/
4. cat existing_file.php | head -10
```

### Prevent Duplication (Identified Issues)
```bash
# KNOWN CONFLICTS IN PROJECT:
app/Events/TestWebSocketEvent.php     # ✅ Correct
app/Events/WebSocketTestEvent.php     # ❌ Duplicate

resources/sass/_variables.scss        # ✅ Correct (with hyphen)
resources/sass/variables.scss         # ❌ Wrong convention

# PREVENTION:
grep -l "class.*WebSocket.*Event" app/Events/*.php
ls -la resources/sass/*variables*
```

### Agent vs Manual Commands
```bash
✅ USE AGENT FOR:
- Creating Laravel classes (Controllers, Events, Jobs)
- Editing configuration files
- Creating views and templates
- Complex code structures

❌ AVOID MANUAL COMMANDS:
- touch filename.php
- mkdir -p directory  
- echo "content" > file
```

---

## 4. VALIDATION WORKFLOW (MANDATORY)

### After Every Change
```bash
# EXECUTE IN SEQUENCE:
docker compose exec php php -l app/path/to/file.php
docker compose exec php composer dump-autoload
docker compose exec php php artisan config:clear
docker compose exec php php artisan route:list  # For routes
```

### Build Validation (CRITICAL)
```bash
# AFTER STRUCTURAL CHANGES:
docker compose exec php php artisan optimize
docker compose logs php --tail=20
docker compose exec node npm run build  # For frontend changes

# TEST FUNCTIONALITY:
docker compose exec php php artisan tinker --execute="event(new App\Events\TestEvent('test'))"
```

## 5. LARAVEL 12 CODE PATTERNS

### Events (ShouldBroadcast)
```php
class WebSocketTestEvent implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;
    
    public function broadcastOn(): array { return [new Channel('test-channel')]; }
    public function broadcastAs(): string { return 'websocket.test'; }
    public function broadcastWith(): array { return $this->data; }
}
```

### Jobs (ShouldQueue)
```php
class TestJob implements ShouldQueue
{
    use Queueable;
    
    public $tries = 3;
    public $timeout = 60;
    public $maxExceptions = 2;
    
    public function handle(): void { /* implementation */ }
    public function failed(\Throwable $exception): void { Log::error($exception->getMessage()); }
}
```

### Mail (Laravel 12)
```php
class TestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    public function envelope(): Envelope { return new Envelope(subject: 'Test'); }
    public function content(): Content { return new Content(view: 'emails.test'); }
}
```

### Controllers (Standard Response)
```php
public function method(Request $request)
{
    $request->validate(['field' => 'required|string|max:255']);
    
    try {
        // Business logic
        Log::info("✅ Action completed");
        return response()->json(['status' => 'success', 'data' => $data]);
    } catch (\Exception $e) {
        Log::error("❌ Action failed: " . $e->getMessage());
        return response()->json(['status' => 'error'], 500);
    }
}
```

---

## 6. TESTING & VERIFICATION

### Connectivity Tests
```bash
# CORE SERVICES:
docker compose exec php php artisan tinker --execute="Redis::ping()"
docker compose exec php php artisan tinker --execute="DB::connection()->getPdo()"
nc -z websocket 6001
curl -f http://mailhog:8025/api/v1/messages
```

### Service Validation Matrix
```bash
✅ REDIS: Ping responds "PONG", queue accessible
✅ DATABASE: Connection established, migrations OK
✅ WEBSOCKET: Port 6001 accessible, HTTP 426 response
✅ QUEUE: Worker processing jobs, no memory leaks
✅ MAIL: MailHog receiving emails
```

### Error Monitoring
```bash
# REAL-TIME MONITORING:
docker compose logs -f php | grep -i error
docker compose logs -f websocket | grep -i error
docker compose logs -f queue | grep -i error
```

---

## 7. ERROR PREVENTION & RECOVERY

### Common Issues & Solutions
```bash
# FONTAWESOME BUILD ERROR (identified issue):
# Prevention: ls -la node_modules/@fortawesome/ before importing
# Recovery: docker compose exec node npm install @fortawesome/fontawesome-free

# DIRECTORY NON-EXISTENCE (identified issue):
# Prevention: [[ -d "app/Events" ]] || mkdir -p app/Events
# Always check directory exists before creating files

# CLASS DUPLICATION (identified issue):
# Prevention: grep -r "class.*EventName" app/ before creating
```

### Recovery Procedures
```bash
# CONTAINER ROLLBACK:
docker compose down
docker compose up -d --no-deps php

# APPLICATION RECOVERY:
docker compose exec php php artisan config:clear
docker compose exec php php artisan optimize:clear
docker compose exec php composer dump-autoload
```

### Error Escalation
```bash
✅ SELF-RECOVERABLE:
- Syntax errors, config cache issues, autoload issues, missing directories

🚨 ESCALATE TO DEVELOPER:
- Docker daemon issues, file permission errors, container build failures
```

---

## 8. IMPLEMENTATION CHECKLISTS

### Controller Implementation
**Pre:** Check if exists, read structure, verify routes
**Standards:** Namespace, validation, try-catch, logging, response format
**Post:** Syntax check, route verification, functionality test

### Event Implementation  
**Pre:** Check directory, similar events, naming conflicts, broadcasting config
**Standards:** ShouldBroadcast, array return types, logging in constructor
**Post:** Syntax check, autoload refresh, event test, broadcasting verification

### Job Implementation
**Pre:** Check directory, similar jobs, queue config, Redis connection
**Standards:** ShouldQueue, tries/timeout/maxExceptions, handle/failed methods
**Post:** Syntax check, autoload refresh, dispatch test, queue monitoring

### View Implementation
**Pre:** Check directory, similar views, layout base, asset compilation
**Standards:** Extends layout, sections, CSRF, route helpers, proper escaping
**Post:** Blade syntax check, compilation test, asset verification, functionality test

---

## 9. COMPLETION CRITERIA

### Technical Gates (ALL must pass)
- [ ] Syntax checks pass: `find app/ -name "*.php" -exec php -l {} \;`
- [ ] No errors in logs: `docker compose logs --tail=50`
- [ ] Services responding: `docker compose ps`
- [ ] Functionality verified via browser/tinker

### Quality Gates
- [ ] **Verification:** All validation commands pass
- [ ] **Integration:** Feature works end-to-end  
- [ ] **Stability:** No new errors introduced
- [ ] **Performance:** Response times acceptable
- [ ] **Recovery:** System recoverable if issues occur

### Before Requesting Help
- [ ] Multiple approaches attempted
- [ ] Specific error messages documented
- [ ] System state clearly described
- [ ] Technical barrier identified

---

## 10. EMERGENCY PROCEDURES

### Critical Error Response
```bash
1. STOP: Don't continue with broken code
2. DOCUMENT: Capture exact error message
3. ROLLBACK: Container restart or file restore
4. VERIFY: Run test suite to confirm stability
5. REPORT: Clear description with attempted actions
```

### Quick Recovery Commands
```bash
# ONE-LINER VALIDATION:
docker compose exec php bash -c "php artisan config:clear && composer dump-autoload && echo 'OK'"

# COMPREHENSIVE RESET:
docker compose exec php php artisan optimize:clear
docker compose exec php php artisan optimize
docker compose restart php websocket queue
```

---

## FUNDAMENTAL PRINCIPLES

1. **Independence First:** Always try agent capabilities before requesting help
2. **Framework Preference:** Use Laravel commands via Docker containers
3. **Validation Mandatory:** Never skip syntax/functionality checks after changes
4. **Project Awareness:** Follow existing patterns and avoid known conflicts
5. **Quality Gates:** Complete technical verification before considering tasks done

**Remember:** Maximum independence with guaranteed quality. Request help only when truly blocked by technical barriers.