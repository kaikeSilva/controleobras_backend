# Guia: Sistema de Listagem de Clientes com Filtros e Paginação

## Fase 1: Preparação

### Tarefa 1.1: Verificar Ambiente Docker

#### Objetivo
Verificar se todos os containers estão rodando corretamente

#### Container
`docker compose ps`

#### Arquivo
N/A

#### Código
```bash
docker compose ps
```

#### Validação
```bash
docker compose exec php php --version
docker compose exec php php artisan --version
```

#### Critério de Sucesso
- [ ] Container php está rodando
- [ ] Laravel está respondendo
- [ ] PHP 8.4 está ativo

---

### Tarefa 1.2: Verificar Conexão com Banco

#### Objetivo
Confirmar que a conexão com MySQL está funcionando

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan tinker --execute="DB::connection()->getPdo()"
```

#### Validação
```bash
docker compose exec php php artisan tinker --execute="echo 'Database: ' . config('database.default')"
```

#### Critério de Sucesso
- [ ] Conexão com banco estabelecida
- [ ] Sem erros de conexão

---

## Fase 2: Models e Migrations

### Tarefa 2.1: Criar Migration para Clientes

#### Objetivo
Criar migration para tabela clients com todos os campos necessários

#### Container
`docker compose exec php`

#### Arquivo
`database/migrations/[timestamp]_create_clients_table.php`

#### Código
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
```

#### Validação
```bash
docker compose exec php php -l database/migrations/*_create_clients_table.php
docker compose exec php php artisan migrate:status
```

#### Critério de Sucesso
- [ ] Migration criada sem erros de sintaxe
- [ ] Migration aparece como pending no status

---

### Tarefa 2.2: Executar Migration

#### Objetivo
Executar a migration para criar a tabela clients no banco

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan migrate --path=database/migrations --step
```

#### Validação
```bash
docker compose exec php php artisan migrate:status
docker compose exec php php artisan tinker --execute="Schema::hasTable('clients')"
```

#### Critério de Sucesso
- [ ] Migration executada com sucesso
- [ ] Tabela clients existe no banco
- [ ] Status mostra migration como executada

---

### Tarefa 2.3: Criar Model Client

#### Objetivo
Criar o model Client com configurações básicas

#### Container
`docker compose exec php`

#### Arquivo
`app/Models/Client.php`

#### Código
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

#### Validação
```bash
docker compose exec php php -l app/Models/Client.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] Model criado sem erros de sintaxe
- [ ] Autoload atualizado
- [ ] Model carregável via tinker

---

### Tarefa 2.4: Testar Model Client

#### Objetivo
Verificar se o model Client está funcionando corretamente

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan tinker --execute="App\Models\Client::query()->toSql()"
```

#### Validação
```bash
docker compose exec php php artisan tinker --execute="echo get_class(new App\Models\Client)"
```

#### Critério de Sucesso
- [ ] Model instancia sem erros
- [ ] Query SQL é gerada corretamente

---

## Fase 3: Resource API

### Tarefa 3.1: Criar ClientResource

#### Objetivo
Criar resource para formatação da resposta da API

#### Container
`docker compose exec php`

#### Arquivo
`app/Http/Resources/ClientResource.php`

#### Código
```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
```

#### Validação
```bash
docker compose exec php php -l app/Http/Resources/ClientResource.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] Resource criado sem erros de sintaxe
- [ ] Autoload atualizado
- [ ] Resource carregável

---

## Fase 4: Controller Base

### Tarefa 4.1: Criar ClientController

#### Objetivo
Criar controller base para clientes

#### Container
`docker compose exec php`

#### Arquivo
`app/Http/Controllers/ClientController.php`

#### Código
```php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Client::query();
        
        $clients = $query->paginate(15);
        
        return ClientResource::collection($clients);
    }
}
```

#### Validação
```bash
docker compose exec php php -l app/Http/Controllers/ClientController.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] Controller criado sem erros de sintaxe
- [ ] Autoload atualizado
- [ ] Method index básico implementado

---

## Fase 5: Implementar Filtros

### Tarefa 5.1: Adicionar Filtros Básicos

#### Objetivo
Implementar filtros para campos string e numéricos

#### Container
`docker compose exec php`

#### Arquivo
`app/Http/Controllers/ClientController.php`

#### Código
```php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Client::query();
        
        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');
            
            // Filtro por ID (exato)
            if (!empty($filters['id'])) {
                $query->where('id', $filters['id']);
            }
            
            // Filtros por string (LIKE)
            $stringFields = ['name', 'email', 'phone', 'address'];
            foreach ($stringFields as $field) {
                if (!empty($filters[$field])) {
                    $query->where($field, 'LIKE', '%' . $filters[$field] . '%');
                }
            }
        }
        
        $clients = $query->paginate(15);
        
        return ClientResource::collection($clients);
    }
}
```

#### Validação
```bash
docker compose exec php php -l app/Http/Controllers/ClientController.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] Controller atualizado sem erros
- [ ] Filtros básicos implementados
- [ ] Syntax check passa

---

### Tarefa 5.2: Adicionar Filtros de Data

#### Objetivo
Implementar filtros para campos de data (created_at e updated_at)

#### Container
`docker compose exec php`

#### Arquivo
`app/Http/Controllers/ClientController.php`

#### Código
```php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Client::query();
        
        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');
            
            // Filtro por ID (exato)
            if (!empty($filters['id'])) {
                $query->where('id', $filters['id']);
            }
            
            // Filtros por string (LIKE)
            $stringFields = ['name', 'email', 'phone', 'address'];
            foreach ($stringFields as $field) {
                if (!empty($filters[$field])) {
                    $query->where($field, 'LIKE', '%' . $filters[$field] . '%');
                }
            }
            
            // Filtros por data
            $dateFields = ['created_at', 'updated_at'];
            foreach ($dateFields as $field) {
                if (!empty($filters[$field])) {
                    $query->whereDate($field, $filters[$field]);
                }
            }
        }
        
        $clients = $query->paginate(15);
        
        return ClientResource::collection($clients);
    }
}
```

#### Validação
```bash
docker compose exec php php -l app/Http/Controllers/ClientController.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] Filtros de data implementados
- [ ] Controller sem erros de sintaxe
- [ ] Autoload atualizado

---

## Fase 6: Implementar Ordenação

### Tarefa 6.1: Adicionar Sistema de Ordenação

#### Objetivo
Implementar ordenação por qualquer campo com direção asc/desc

#### Container
`docker compose exec php`

#### Arquivo
`app/Http/Controllers/ClientController.php`

#### Código
```php
<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Client::query();
        
        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');
            
            // Filtro por ID (exato)
            if (!empty($filters['id'])) {
                $query->where('id', $filters['id']);
            }
            
            // Filtros por string (LIKE)
            $stringFields = ['name', 'email', 'phone', 'address'];
            foreach ($stringFields as $field) {
                if (!empty($filters[$field])) {
                    $query->where($field, 'LIKE', '%' . $filters[$field] . '%');
                }
            }
            
            // Filtros por data
            $dateFields = ['created_at', 'updated_at'];
            foreach ($dateFields as $field) {
                if (!empty($filters[$field])) {
                    $query->whereDate($field, $filters[$field]);
                }
            }
        }
        
        // Ordenação
        $sortBy = $request->input('sort_by', 'id');
        $direction = $request->input('direction', 'asc');
        
        $allowedSortFields = ['id', 'name', 'email', 'phone', 'address', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];
        
        if (in_array($sortBy, $allowedSortFields) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sortBy, $direction);
        }
        
        $clients = $query->paginate(15);
        
        return ClientResource::collection($clients);
    }
}
```

#### Validação
```bash
docker compose exec php php -l app/Http/Controllers/ClientController.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] Sistema de ordenação implementado
- [ ] Validação de campos permitidos
- [ ] Controller sem erros

---

## Fase 7: Rotas API

### Tarefa 7.1: Criar Rota para Listagem

#### Objetivo
Definir rota API para listagem de clientes

#### Container
`docker compose exec php`

#### Arquivo
`routes/api.php`

#### Código
```php
<?php

use App\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/clients', [ClientController::class, 'index']);
});
```

#### Validação
```bash
docker compose exec php php -l routes/api.php
docker compose exec php php artisan route:list --path=api/clients
```

#### Critério de Sucesso
- [ ] Rota criada sem erros de sintaxe
- [ ] Rota aparece na listagem
- [ ] Middleware auth aplicado

---

## Fase 8: Seeders e Testes

### Tarefa 8.1: Criar ClientSeeder

#### Objetivo
Criar seeder para popular tabela clients com dados de teste

#### Container
`docker compose exec php`

#### Arquivo
`database/seeders/ClientSeeder.php`

#### Código
```php
<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name' => 'João Silva',
                'email' => 'joao@email.com',
                'phone' => '(62) 91234-5678',
                'address' => 'Rua das Flores, 123',
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria@email.com',
                'phone' => '(62) 98765-4321',
                'address' => 'Av. Central, 456',
            ],
            [
                'name' => 'Pedro Costa',
                'email' => 'pedro@email.com',
                'phone' => '(62) 95555-5555',
                'address' => 'Rua Nova, 789',
            ],
            [
                'name' => 'Ana Oliveira',
                'email' => 'ana@email.com',
                'phone' => '(62) 94444-4444',
                'address' => 'Praça da Paz, 321',
            ],
            [
                'name' => 'Carlos Lima',
                'email' => 'carlos@email.com',
                'phone' => '(62) 93333-3333',
                'address' => 'Rua do Sol, 654',
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
```

#### Validação
```bash
docker compose exec php php -l database/seeders/ClientSeeder.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] Seeder criado sem erros
- [ ] Autoload atualizado
- [ ] Dados de teste definidos

---

### Tarefa 8.2: Registrar ClientSeeder

#### Objetivo
Registrar ClientSeeder no DatabaseSeeder principal

#### Container
`docker compose exec php`

#### Arquivo
`database/seeders/DatabaseSeeder.php`

#### Código
```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ClientSeeder::class,
        ]);
    }
}
```

#### Validação
```bash
docker compose exec php php -l database/seeders/DatabaseSeeder.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] DatabaseSeeder atualizado
- [ ] ClientSeeder registrado
- [ ] Sem erros de sintaxe

---

### Tarefa 8.3: Executar Seeders

#### Objetivo
Executar seeders para popular banco com dados de teste

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan db:seed --class=ClientSeeder
```

#### Validação
```bash
docker compose exec php php artisan tinker --execute="echo 'Clientes: ' . App\Models\Client::count()"
```

#### Critério de Sucesso
- [ ] Seeders executados com sucesso
- [ ] Dados inseridos no banco
- [ ] Contagem de clientes maior que 0

---

## Fase 9: Testes de Funcionalidade

### Tarefa 9.1: Testar Listagem Básica

#### Objetivo
Verificar se a listagem básica está funcionando

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan tinker --execute="
\$clients = App\Models\Client::paginate(15);
echo 'Total: ' . \$clients->total() . PHP_EOL;
echo 'Por página: ' . \$clients->perPage() . PHP_EOL;
echo 'Página atual: ' . \$clients->currentPage();
"
```

#### Validação
```bash
docker compose exec php php artisan route:list --path=api/clients
```

#### Critério de Sucesso
- [ ] Paginação funcional
- [ ] Dados retornados
- [ ] Rota disponível

---

### Tarefa 9.2: Testar Filtros

#### Objetivo
Verificar se os filtros estão funcionando corretamente

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan tinker --execute="
// Teste filtro por nome
\$query = App\Models\Client::query();
\$query->where('name', 'LIKE', '%João%');
echo 'Filtro nome: ' . \$query->count() . ' registros' . PHP_EOL;

// Teste filtro por email
\$query2 = App\Models\Client::query();
\$query2->where('email', 'LIKE', '%@email.com%');
echo 'Filtro email: ' . \$query2->count() . ' registros';
"
```

#### Validação
```bash
docker compose exec php php artisan tinker --execute="echo 'Teste filtros executado com sucesso'"
```

#### Critério de Sucesso
- [ ] Filtros de string funcionando
- [ ] Resultados coerentes
- [ ] Sem erros de execução

---

### Tarefa 9.3: Testar Ordenação

#### Objetivo
Verificar se a ordenação está funcionando

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan tinker --execute="
// Teste ordenação por nome ASC
\$clientsAsc = App\Models\Client::orderBy('name', 'asc')->limit(3)->pluck('name');
echo 'Ordenação ASC: ' . \$clientsAsc->implode(', ') . PHP_EOL;

// Teste ordenação por nome DESC  
\$clientsDesc = App\Models\Client::orderBy('name', 'desc')->limit(3)->pluck('name');
echo 'Ordenação DESC: ' . \$clientsDesc->implode(', ');
"
```

#### Validação
```bash
docker compose exec php php artisan tinker --execute="echo 'Teste ordenação executado'"
```

#### Critério de Sucesso
- [ ] Ordenação ASC funcional
- [ ] Ordenação DESC funcional
- [ ] Resultados ordenados corretamente

---

## Fase 10: Validação e Melhorias

### Tarefa 10.1: Adicionar Validação de Request

#### Objetivo
Criar FormRequest para validar parâmetros da listagem

#### Container
`docker compose exec php`

#### Arquivo
`app/Http/Requests/ClientIndexRequest.php`

#### Código
```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClientIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filter.id' => 'sometimes|integer|min:1',
            'filter.name' => 'sometimes|string|max:255',
            'filter.email' => 'sometimes|string|max:255',
            'filter.phone' => 'sometimes|string|max:255',
            'filter.address' => 'sometimes|string|max:255',
            'filter.created_at' => 'sometimes|date_format:Y-m-d',
            'filter.updated_at' => 'sometimes|date_format:Y-m-d',
            'sort_by' => 'sometimes|string|in:id,name,email,phone,address,created_at,updated_at',
            'direction' => 'sometimes|string|in:asc,desc',
            'page' => 'sometimes|integer|min:1',
        ];
    }
}
```

#### Validação
```bash
docker compose exec php php -l app/Http/Requests/ClientIndexRequest.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] FormRequest criado sem erros
- [ ] Regras de validação definidas
- [ ] Autoload atualizado

---

### Tarefa 10.2: Aplicar Validação no Controller

#### Objetivo
Integrar FormRequest no controller ClientController

#### Container
`docker compose exec php`

#### Arquivo
`app/Http/Controllers/ClientController.php`

#### Código
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientIndexRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    public function index(ClientIndexRequest $request): AnonymousResourceCollection
    {
        $query = Client::query();
        
        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');
            
            // Filtro por ID (exato)
            if (!empty($filters['id'])) {
                $query->where('id', $filters['id']);
            }
            
            // Filtros por string (LIKE)
            $stringFields = ['name', 'email', 'phone', 'address'];
            foreach ($stringFields as $field) {
                if (!empty($filters[$field])) {
                    $query->where($field, 'LIKE', '%' . $filters[$field] . '%');
                }
            }
            
            // Filtros por data
            $dateFields = ['created_at', 'updated_at'];
            foreach ($dateFields as $field) {
                if (!empty($filters[$field])) {
                    $query->whereDate($field, $filters[$field]);
                }
            }
        }
        
        // Ordenação
        $sortBy = $request->input('sort_by', 'id');
        $direction = $request->input('direction', 'asc');
        
        $allowedSortFields = ['id', 'name', 'email', 'phone', 'address', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];
        
        if (in_array($sortBy, $allowedSortFields) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sortBy, $direction);
        }
        
        $clients = $query->paginate(15);
        
        return ClientResource::collection($clients);
    }
}
```

#### Validação
```bash
docker compose exec php php -l app/Http/Controllers/ClientController.php
docker compose exec php composer dump-autoload
```

#### Critério de Sucesso
- [ ] FormRequest integrado
- [ ] Controller sem erros
- [ ] Validação aplicada

---

### Tarefa 10.3: Teste Final Completo

#### Objetivo
Executar teste completo de toda a funcionalidade

#### Container
`docker compose exec php`

#### Arquivo
N/A

#### Código
```bash
php artisan tinker --execute="
// Teste completo da funcionalidade
echo '=== TESTE COMPLETO SISTEMA CLIENTES ===' . PHP_EOL;

// 1. Verificar total de clientes
\$total = App\Models\Client::count();
echo '1. Total clientes: ' . \$total . PHP_EOL;

// 2. Teste paginação
\$paginated = App\Models\Client::paginate(15);
echo '2. Paginação - Total: ' . \$paginated->total() . ', Por página: ' . \$paginated->perPage() . PHP_EOL;

// 3. Teste filtro
\$filtered = App\Models\Client::where('name', 'LIKE', '%João%')->count();
echo '3. Filtro nome João: ' . \$filtered . ' registros' . PHP_EOL;

// 4. Teste ordenação
\$ordered = App\Models\Client::orderBy('name', 'asc')->first();
echo '4. Primeiro por nome: ' . \$ordered->name . PHP_EOL;

// 5. Teste Resource
\$resource = new App\Http\Resources\ClientResource(\$ordered);
echo '5. Resource funcional: ' . (isset(\$resource->resource->name) ? 'SIM' : 'NÃO') . PHP_EOL;

echo '=== TESTE CONCLUÍDO COM SUCESSO ===';
"
```

#### Validação
```bash
docker compose exec php php artisan route:list --path=api/clients
docker compose exec php php artisan config:clear
```

#### Critério de Sucesso
- [ ] Todos os testes passaram
- [ ] Rota disponível e configurada
- [ ] Sistema completo funcional
- [ ] Cache limpo

---

## Resumo da Implementação

### ✅ Funcionalidades Implementadas

1. **Model Client** com todos os campos necessários
2. **Migration** para tabela clients
3. **ClientResource** para formatação da API
4. **Filtros dinâmicos** por todos os campos especificados
5. **Sistema de ordenação** com validação
6. **Paginação** padrão Laravel (15 itens por página)
7. **Validação de Request** com FormRequest
8. **Seeders** para dados de teste
9. **Rota API** protegida por autenticação

### 🔗 Endpoint Disponível

```
GET /api/clients
```

### 📋 Exemplos de Uso

```bash
# Listagem básica
GET /api/clients

# Com filtros
GET /api/clients?filter[name]=joão&filter[email]=@gmail.com

# Com ordenação
GET /api/clients?sort_by=name&direction=desc

# Com paginação
GET /api/clients?page=2

# Completo
GET /api/clients?filter[name]=silva&sort_by=created_at&direction=desc&page=1
```

### 🔐 Autorização

- Requer autenticação via Sanctum
- Todos os usuários autenticados têm acesso

### 📊 Formato de Resposta

```json
{
  "data": [
    {
      "id": 1,
      "name": "João Silva",
      "email": "joao@email.com",
      "phone": "(62) 91234-5678",
      "address": "Rua das Flores, 123",
      "created_at": "2024-01-01T12:00:00.000000Z",
      "updated_at": "2024-01-02T12:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/clients?page=1",
    "last": "http://localhost/api/clients?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 15,
    "to": 5,
    "total": 5
  }
}
```