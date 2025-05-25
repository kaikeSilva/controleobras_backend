
Implemente o CRUD da model Fonte Pagadora.
Representa as origens dos recursos financeiros utilizados nos gastos de uma obra. clientes podem criar fontes pagadoras para utilizar nas suas obras.

**Atributos:**
- `id` (PK): Identificador único
- `nome`: Nome da fonte pagadora
- `descricao`: Descrição detalhada
- `ativo`: Status da fonte (ativa/inativa)
- `data_cadastro`: Data de criação do registro
- `cliente_id` (FK): Referência ao cliente proprietário
- timestamps (created_at, updated_at)
- soft delete (deleted_at)
- status (ativo/inativo)

# tarefa criar crud completo tendo como exemplo o CRUD de clientes:
 - Resource
 - Controller
 - Model
 - Migration
 - Factory
 - Seeder

- deve ser possivel filtrar as fontes pagadoras por cliente, search por nome/descricao/status