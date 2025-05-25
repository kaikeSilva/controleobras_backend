
Implemente o CRUD da model Obra.
Representa cada projeto de construção gerenciado pelo cliente.

**Atributos:**
- `id` (PK): Identificador único
- `cliente_id` (FK): Referência ao cliente proprietário
- `nome`: Nome da obra
- `descricao`: Descrição detalhada
- `endereco`: Localização da obra
- `area_m2`: Área em metros quadrados
- `data_inicio`: Data de início da obra
- `prazo_estimado`: Prazo estimado de conclusão
- `valor_estimado`: Valor estimado total de execução
- `taxa_administracao`: Percentual da taxa de administração
- `status`: Status da obra (Em andamento, Concluída, Pausada)
- `data_cadastro`: Data de criação do registro


# tarefa criar crud completo tendo como exemplo o CRUD de clientes:
 - Resource
 - Controller
 - Model
 - Migration
 - Factory
 - Seeder
 - documentacao no na pasta do swagger e na view do swagger
 - Rota no autocomplete para obras com filtragem por cliente

- deve ser possivel filtrar as obras por cliente, search por nome/descricao/status