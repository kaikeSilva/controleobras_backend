
Implemente o CRUD da model Categoria de Gasto.

Define os tipos de classificação para os gastos da obra.

**Atributos:**
- `id` (PK): Identificador único
- `nome`: Nome da categoria
- `slug`: Slug da categoria
- `status`: Status da categoria (ativa/inativa)
- `cliente_id`: Referência ao cliente proprietário / nullable
- `descricao`: Descrição detalhada da categoria
- `cor`: Cor para identificação visual (opcional)
- `timestamps`: created_at, updated_at
- `soft delete`: deleted_at

**Exemplos de Categorias: para o seeder**
- Mão de obra
- Material de construção
- Alimentação
- Transporte
- Equipamentos
- Serviços terceirizados


# tarefa criar crud completo tendo como exemplo o CRUD de clientes:
 - Resource
 - Controller
 - Model
 - Migration
 - Factory
 - Seeder
 - documentacao no na pasta do swagger e na view do swagger
 - Rota no autocomplete

- deve ser possivel filtrar as categorias de gasto por cliente, search por nome/descricao/status
- Regra de negocio: 
- ao criar uma categoria de gasto, nao pode ter nome duplicado nem slug duplicado
