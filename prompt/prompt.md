
Implemente o CRUD model (Entrada de Recurso)  EntradaRecurso

Representa os aportes financeiros recebidos para as obras.

**Atributos:**
- `id` (PK): Identificador único
- `obra_id` (FK): Referência à obra
- `fonte_pagadora_id` (FK): Referência à fonte pagadora
- `valor`: Valor do aporte
- `data_entrada`: Data do recebimento
- `descricao`: Descrição do aporte
- `comprovante_url`: Link para comprovante (opcional)
- `tipo_entrada`: Tipo (Aporte inicial, Aporte adicional, Reembolso, regular) o tipo regular eh o default
- `timestamps`: created_at, updated_at
- `soft delete`: deleted_at

# tarefa criar crud completo tendo como exemplo o CRUD de clientes:
 - Resource
 - Controller
 - Model
 - Migration
 - Factory
 - Seeder
 - documentacao no na pasta do swagger e na view do swagger
 - Rota no autocomplete

- deve ser possivel filtrar as gastos por search por nome do cliente/descricao/string da data de compra/string da data de pagamento/string do numero do documento/string da data de criacao/string do valor/ nome da obra/ nome da categoria/ nome da fonte pagadora