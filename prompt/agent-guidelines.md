# AGENT SAFETY GUIDELINES - Laravel 12 Docker Development

## CRITICAL DECISION MAKING HIERARCHY

### 1. ALWAYS TRY FIRST - Agent Capabilities
**YOU MUST ATTEMPT THESE ACTIONS BEFORE REQUESTING DEVELOPER HELP:**

- **File Creation/Editing**: Try to create/edit files directly
- **Directory Creation**: Try `mkdir` or file creation in new directories
- **Code Writing**: Write complete Laravel 12 classes and views
- **Route Addition**: Add routes to existing route files
- **Configuration Reading**: Read existing files to understand structure

### 2. PREFER LARAVEL 12 COMMANDS - Framework First Approach
**WHEN POSSIBLE, USE LARAVEL ARTISAN COMMANDS OVER MANUAL CREATION:**

```bash
# PREFERRED: Use Laravel 12 artisan commands
php artisan make:controller ControllerName
php artisan make:mail MailName  
php artisan make:job JobName
php artisan make:model ModelName
php artisan make:middleware MiddlewareName
php artisan make:request RequestName
php artisan make:resource ResourceName

# FALLBACK: Only use manual creation if artisan commands fail
mkdir app/Http/Controllers
touch app/Http/Controllers/ControllerName.php
```

### 3. DOCKER COMMAND EXECUTION
**ALWAYS ATTEMPT DOCKER COMMANDS DIRECTLY:**

```bash
# TRY THESE FIRST
docker compose exec php php artisan make:controller TestController
docker compose exec php php artisan config:clear
docker compose exec php php artisan route:list
docker compose ps
docker compose logs queue
```

### 4. ONLY REQUEST HELP WHEN EXPLICITLY BLOCKED
**REQUEST DEVELOPER ACTION ONLY WHEN YOU RECEIVE EXPLICIT ERROR MESSAGES:**

- ✅ "Permission denied"
- ✅ "Cannot edit file in .gitignore"
- ✅ "Access denied to container"
- ✅ "Command not found"
- ✅ "File is read-only"

**DO NOT REQUEST HELP FOR:**
- ❌ Files you haven't tried to create/edit
- ❌ Commands you haven't attempted to run
- ❌ Directories you haven't tried to create
- ❌ Standard Laravel operations

## LARAVEL 12 SPECIFIC GUIDELINES

### Framework Knowledge (Laravel 12.x)
**USE LARAVEL 12 CONVENTIONS AND FEATURES:**

- **Mail Classes**: Use `Mailable` with `ShouldQueue` interface
- **Job Classes**: Implement `ShouldQueue` interface
- **Route Definition**: Use array syntax `[Controller::class, 'method']`
- **View Structure**: Use blade templating with `@` directives
- **Validation**: Use `$request->validate()` method
- **Queue Configuration**: Use Redis driver with proper configuration
- **Mail Configuration**: Use SMTP with MailHog settings

### Directory Structure (Laravel 12)
**FOLLOW STANDARD LARAVEL 12 DIRECTORY CONVENTIONS:**

```
app/
├── Http/Controllers/
├── Jobs/
├── Mail/
├── Models/
└── ...
resources/
├── views/
│   ├── emails/
│   └── ...
routes/
├── web.php
└── ...
```

### Artisan Command Priority
**ALWAYS PREFER LARAVEL ARTISAN OVER MANUAL CREATION:**

```bash
# CORRECT APPROACH
1. Try: docker compose exec php php artisan make:controller TestController
2. If fails: Then create file manually with proper Laravel 12 structure

# WRONG APPROACH  
1. Immediately ask developer to run artisan command
2. Create file manually without trying artisan first
```

## CONTAINER-SPECIFIC EXECUTION RULES

### PHP Container Commands
**EXECUTE DIRECTLY IN PHP CONTAINER:**

```bash
# YOU SHOULD RUN THESE YOURSELF
docker compose exec php php artisan make:*
docker compose exec php php artisan config:clear
docker compose exec php php artisan route:clear
docker compose exec php php artisan view:clear
docker compose exec php composer install
```

### Service Container Commands
**EXECUTE DIRECTLY IN RESPECTIVE CONTAINERS:**

```bash
# YOU SHOULD RUN THESE YOURSELF
docker compose exec redis redis-cli ping
docker compose logs queue
docker compose logs mailhog
docker compose ps
```

### System Commands
**EXECUTE DIRECTLY:**

```bash
# YOU SHOULD RUN THESE YOURSELF
docker compose up -d
docker compose down
docker compose restart service_name
```

## WHEN TO REQUEST DEVELOPER HELP

### File Access Issues
**ONLY REQUEST HELP WHEN YOU GET EXPLICIT ERRORS:**

```
✅ REQUEST HELP FOR:
- "edit_file tool cannot modify files in .gitignore"
- "Permission denied when writing to file"
- "Cannot create directory: permission denied"

❌ DON'T REQUEST HELP FOR:
- Files you haven't tried to create
- Directories you haven't attempted to make
- Commands you haven't tried to execute
```

### Environment Configuration
**TRY TO READ/MODIFY .env FIRST:**

```bash
# TRY FIRST
1. Read current .env file
2. Attempt to modify .env file
3. Only if blocked, then request developer help
```

### Command Execution
**ATTEMPT DOCKER COMMANDS FIRST:**

```bash
# TRY FIRST
1. docker compose exec php php artisan make:controller TestController
2. docker compose exec php php artisan config:clear
3. Only if command fails, then request developer help
```

## LARAVEL 12 CODE STANDARDS

### Controller Structure
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Laravel 12 imports

class TestController extends Controller
{
    // Laravel 12 methods
}
```

### Job Structure
```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    // Laravel 12 job implementation
}
```

### Mail Structure
```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    
    // Laravel 12 mail implementation
}
```

## TESTING AND VERIFICATION APPROACH

### Self-Verification First
**VERIFY YOUR OWN WORK BEFORE REQUESTING HELP:**

1. ✅ Check if files were created successfully
2. ✅ Verify file contents are correct
3. ✅ Test route accessibility
4. ✅ Check container status
5. ❌ Don't immediately ask developer to verify

### Error Handling
**HANDLE ERRORS APPROPRIATELY:**

```bash
# IF COMMAND FAILS
1. Note the specific error message
2. Try alternative approach (manual file creation)
3. Only request help if all approaches fail
```

## FINAL EXECUTION PRINCIPLES

### Independence First
- **TRY**: Always attempt the action yourself first
- **VERIFY**: Check if your action succeeded
- **ADAPT**: Use alternative approaches if primary fails
- **REQUEST**: Only ask for help when truly blocked

### Framework Preference
- **ARTISAN FIRST**: Use Laravel 12 artisan commands when possible
- **MANUAL SECOND**: Create files manually if artisan fails
- **PROPER STRUCTURE**: Follow Laravel 12 conventions always

### Docker Awareness
- **CONTAINER CONTEXT**: Know which container to use for each command
- **DIRECT EXECUTION**: Execute docker commands directly
- **SERVICE INTERACTION**: Understand container relationships

### Communication Clarity
- **SPECIFIC ERRORS**: Only report actual error messages
- **ATTEMPTED ACTIONS**: List what you've tried before requesting help
- **EXACT REQUIREMENTS**: Be precise about what you need from developer

**REMEMBER**: Your goal is to be as independent as possible while following Laravel 12 and Docker best practices. Request developer help only when you encounter actual technical barriers, not as a first resort.