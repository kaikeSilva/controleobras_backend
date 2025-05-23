# Modelo de Dados - Sistema Financeiro de Obras

## 1. Visão Geral

O modelo de dados do sistema de gestão financeira de obras é estruturado de forma hierárquica, onde um **Cliente** possui múltiplas **Obras**, cada obra contém diversos **Gastos**, e cada gasto é classificado por uma **Categoria** e financiado por uma **Fonte Pagadora**.

### Hierarquia Principal
```
Cliente → Obra → Gasto → [Categoria + Fonte Pagadora]
```

---

## 2. Entidades do Sistema

### 2.1 Cliente

Representa os empreiteiros/contratantes que utilizam o sistema para gerenciar suas obras.

**Atributos:**
- `id` (PK): Identificador único
- `nome`: Nome completo ou razão social
- `tipo_pessoa`: Física ou Jurídica
- `cpf_cnpj`: Documento de identificação
- `email`: Email principal
- `telefone`: Telefone de contato
- `endereco`: Endereço completo
- `data_cadastro`: Data de criação do registro
- `ativo`: Status do cliente (ativo/inativo)

**Relacionamentos:**
- Um Cliente possui várias Obras (1:N)

---

### 2.2 Obra

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

**Relacionamentos:**
- Uma Obra pertence a um Cliente (N:1)
- Uma Obra possui vários Gastos (1:N)
- Uma Obra possui várias Entradas de Recurso (1:N)

---

### 2.3 Categoria de Gasto

Define os tipos de classificação para os gastos da obra.

**Atributos:**
- `id` (PK): Identificador único
- `nome`: Nome da categoria
- `descricao`: Descrição detalhada da categoria
- `cor`: Cor para identificação visual (opcional)
- `ativo`: Status da categoria (ativa/inativa)
- `data_cadastro`: Data de criação do registro

**Exemplos de Categorias:**
- Mão de obra
- Material de construção
- Alimentação
- Transporte
- Equipamentos
- Serviços terceirizados

**Relacionamentos:**
- Uma Categoria classifica vários Gastos (1:N)

---

### 2.4 Fonte Pagadora

Representa as origens dos recursos financeiros utilizados nos gastos.

**Atributos:**
- `id` (PK): Identificador único
- `nome`: Nome da fonte pagadora
- `descricao`: Descrição detalhada
- `tipo`: Tipo da fonte (Conta bancária, Cartão, Dinheiro, PIX, etc.)
- `dados_bancarios`: Informações bancárias (quando aplicável)
- `limite_credito`: Limite disponível (quando aplicável)
- `ativo`: Status da fonte (ativa/inativa)
- `data_cadastro`: Data de criação do registro

**Relacionamentos:**
- Uma Fonte Pagadora financia vários Gastos (1:N)
- Uma Fonte Pagadora recebe várias Entradas de Recurso (1:N)

---

### 2.5 Gasto (Item de Gasto)

Representa cada despesa individual registrada em uma obra.

**Atributos:**
- `id` (PK): Identificador único
- `obra_id` (FK): Referência à obra
- `categoria_id` (FK): Referência à categoria de gasto
- `fonte_pagadora_id` (FK): Referência à fonte pagadora
- `descricao`: Descrição detalhada do gasto
- `valor`: Valor do gasto
- `data_compra`: Data da compra/aquisição
- `data_pagamento`: Data efetiva do pagamento
- `numero_documento`: Número da nota fiscal ou recibo
- `comprovante_url`: Link/caminho para o arquivo do comprovante
- `observacoes`: Observações adicionais
- `data_cadastro`: Data de criação do registro

**Relacionamentos:**
- Um Gasto pertence a uma Obra (N:1)
- Um Gasto é classificado por uma Categoria (N:1)
- Um Gasto é pago por uma Fonte Pagadora (N:1)

---

### 2.6 Entrada de Recurso

Representa os aportes financeiros recebidos para as obras.

**Atributos:**
- `id` (PK): Identificador único
- `obra_id` (FK): Referência à obra
- `fonte_pagadora_id` (FK): Referência à fonte pagadora
- `valor`: Valor do aporte
- `data_entrada`: Data do recebimento
- `descricao`: Descrição do aporte
- `comprovante_url`: Link para comprovante (opcional)
- `tipo_entrada`: Tipo (Aporte inicial, Aporte adicional, Reembolso)
- `data_cadastro`: Data de criação do registro

**Relacionamentos:**
- Uma Entrada pertence a uma Obra (N:1)
- Uma Entrada é creditada por uma Fonte Pagadora (N:1)

---

## 3. Relacionamentos Detalhados

### 3.1 Diagrama de Relacionamentos

```
Cliente (1) -----> (N) Obra (1) -----> (N) Gasto (N) -----> (1) Categoria
                     |                    |
                     |                    |
                     v                    v
                (N) Entrada_Recurso   (1) Fonte_Pagadora
                     |                    ^
                     |                    |
                     +--------------------+
```

### 3.2 Cardinalidades

- **Cliente → Obra**: 1:N (Um cliente pode ter várias obras)
- **Obra → Gasto**: 1:N (Uma obra pode ter vários gastos)
- **Obra → Entrada_Recurso**: 1:N (Uma obra pode receber várias entradas)
- **Categoria → Gasto**: 1:N (Uma categoria pode classificar vários gastos)
- **Fonte_Pagadora → Gasto**: 1:N (Uma fonte pode pagar vários gastos)
- **Fonte_Pagadora → Entrada_Recurso**: 1:N (Uma fonte pode fazer várias entradas)

---

## 4. Regras de Negócio

### 4.1 Validações Principais

**Cliente:**
- CPF/CNPJ deve ser único no sistema
- Email deve ser único no sistema
- Tipo de pessoa deve ser consistente com o documento

**Obra:**
- Deve ter pelo menos um cliente associado
- Taxa de administração deve ser entre 0% e 100%
- Data de início não pode ser futura
- Valor estimado deve ser positivo

**Gasto:**
- Valor deve ser positivo
- Data de pagamento não pode ser anterior à data de compra
- Deve ter comprovante obrigatório para valores acima de um limite
- Categoria e Fonte Pagadora são obrigatórias

**Entrada de Recurso:**
- Valor deve ser positivo
- Data de entrada não pode ser futura

### 4.2 Cálculos Automáticos

**Saldo da Obra:**
```
Saldo = Σ(Entradas de Recurso) - Σ(Gastos)
```

**Taxa de Administração a Faturar:**
```
Taxa a Faturar = Σ(Gastos) × (Taxa de Administração / 100)
```

**Total por Categoria:**
```
Total Categoria = Σ(Gastos WHERE categoria_id = X)
```

---

## 5. Índices Recomendados

### 5.1 Índices de Performance

- `obras.cliente_id` - Para consultas por cliente
- `gastos.obra_id` - Para consultas de gastos por obra
- `gastos.categoria_id` - Para relatórios por categoria
- `gastos.fonte_pagadora_id` - Para consultas por fonte
- `gastos.data_pagamento` - Para filtros temporais
- `entradas_recurso.obra_id` - Para cálculo de saldo

### 5.2 Índices Compostos

- `gastos(obra_id, data_pagamento)` - Para relatórios mensais
- `gastos(categoria_id, data_pagamento)` - Para análises categóricas temporais

---

## 6. Considerações Técnicas

### 6.1 Auditoria

Todas as entidades devem incluir campos de auditoria:
- `created_at`: Data/hora de criação
- `updated_at`: Data/hora da última atualização
- `created_by`: Usuário que criou (quando aplicável)
- `updated_by`: Usuário que atualizou (quando aplicável)

### 6.2 Soft Delete

Para entidades principais (Cliente, Obra, Categoria, Fonte Pagadora):
- `deleted_at`: Campo para implementar exclusão lógica
- Permite manter histórico mesmo após "exclusão"

### 6.3 Arquivos

Para campos de comprovantes (`comprovante_url`):
- Implementar upload seguro
- Validar tipos de arquivo permitidos
- Implementar backup dos arquivos
- Considerar versionamento de documentos

---

## 7. Queries Comuns

### 7.1 Saldo Atual de uma Obra

```sql
SELECT 
    o.nome as obra,
    COALESCE(entradas.total, 0) - COALESCE(gastos.total, 0) as saldo
FROM obras o
LEFT JOIN (
    SELECT obra_id, SUM(valor) as total 
    FROM entradas_recurso 
    GROUP BY obra_id
) entradas ON o.id = entradas.obra_id
LEFT JOIN (
    SELECT obra_id, SUM(valor) as total 
    FROM gastos 
    GROUP BY obra_id
) gastos ON o.id = gastos.obra_id
WHERE o.id = ?
```

### 7.2 Gastos por Categoria em um Período

```sql
SELECT 
    c.nome as categoria,
    SUM(g.valor) as total,
    COUNT(g.id) as quantidade
FROM gastos g
JOIN categorias c ON g.categoria_id = c.id
WHERE g.obra_id = ? 
    AND g.data_pagamento BETWEEN ? AND ?
GROUP BY c.id, c.nome
ORDER BY total DESC
```

### 7.3 Taxa de Administração a Faturar

```sql
SELECT 
    o.nome as obra,
    SUM(g.valor) as total_gasto,
    o.taxa_administracao,
    (SUM(g.valor) * o.taxa_administracao / 100) as taxa_a_faturar
FROM obras o
JOIN gastos g ON o.id = g.obra_id
WHERE o.cliente_id = ?
GROUP BY o.id, o.nome, o.taxa_administracao
```