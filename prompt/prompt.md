**Prompt para Agente de IA: Implementação de API REST para CRUD de Clientes em Laravel**

---

### Objetivo

Implemente uma API REST completa utilizando o framework Laravel para realizar operações de CRUD (Create, Read, Update, Delete) para a entidade Cliente.

---

### Requisitos da Tabela Clients

Crie a migration com os seguintes campos:

* `id`: primary key
* `name`: string, obrigatório
* `email`: string, obrigatório, único
* `phone`: string, opcional
* `address`: string, opcional
* `timestamps`

---

### Modelo Client

Crie a model `Client` com os seguintes campos preenchíveis:

```php
protected $fillable = [
    'name',
    'email',
    'phone',
    'address',
];
```

---

### Controller

Crie o controller `ClientController` dentro do namespace `App\Http\Controllers\Api` com os seguintes métodos:

* `index()`: retorna todos os clientes
* `store(Request $request)`: cria um cliente com validação de dados
* `show(Client $client)`: retorna um cliente específico
* `update(Request $request, Client $client)`: atualiza um cliente com validação
* `destroy(Client $client)`: deleta um cliente

Valide os dados conforme as regras abaixo:

```php
'name' => 'required|string|max:255',
'email' => 'required|email|unique:clients,email',
'phone' => 'nullable|string|max:20',
'address' => 'nullable|string|max:255',
```

Para o método `update`, o email deve ser único exceto para o próprio cliente:

```php
'email' => 'sometimes|required|email|unique:clients,email,' . $client->id
```

---

### Rotas da API

Adicione no arquivo `routes/api.php` a rota do resource:

```php
use App\Http\Controllers\Api\ClientController;

Route::apiResource('clients', ClientController::class);
```

---

### Testes Esperados

A API deve responder aos seguintes endpoints com comportamento correto:

* `GET /api/clients` → Lista todos os clientes
* `POST /api/clients` → Cria um novo cliente
* `GET /api/clients/{id}` → Retorna os dados de um cliente
* `PUT /api/clients/{id}` → Atualiza um cliente
* `DELETE /api/clients/{id}` → Remove um cliente

---

### (Opcional) Testes Automatizados

Crie testes para os métodos acima garantindo validações e retornos esperados.

---

### (Opcional) Autenticação

Caso deseje proteger as rotas, utilize Laravel Sanctum ou Passport.

---

**Instrução Final:**
Implemente todos os arquivos e configurações necessárias para que a API REST de clientes esteja funcional e pronta para consumo via ferramentas como Postman ou integração frontend.
