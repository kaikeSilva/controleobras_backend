**Documento de Escopo – Módulo Financeiro**  

**1. Introdução**  
O **Módulo Financeiro** é uma parte essencial do sistema de gestão de obras por administração, responsável pelo controle de custos, fluxo de caixa e geração de relatórios financeiros. Ele substituirá as planilhas manuais atualmente utilizadas, automatizando processos e garantindo maior precisão nos dados.  

---  

### **2. Objetivos**  
- Registrar todas as **entradas e saídas financeiras** da obra.  
- Categorizar os gastos em categorias pre definidas pelo usuario.  
- Calcular a **taxa de administração** sobre o valor total gasto.  
- Gerar **relatórios mensais e consolidados** para análise do cliente.  
- Fornecer **alertas** sobre saldo disponível e necessidades de aporte.  

---  

### **3. Requisitos Funcionais**  
#### **3.1 Cadastro de Categoria de gastos**  
- Permitir o cadastro de obras com os seguintes dados:  
  - Nome da obra, endereço, área (m²), prazo estimado.  
- Valor estimado de execução e taxa de administração (%).

#### **3.1 Cadastro de Obras**  
- Permitir o cadastro de obras com os seguintes dados:  
  - Nome da obra, endereço, área (m²), prazo estimado.  
- Valor estimado de execução e taxa de administração (%).
- cadastro de fontes pagadoras

#### **3.2 Gestão de Pagamentos**  
- **Entradas (Depósitos):**  
  - Registrar depósitos do cliente (comprovante, valor, data).  
  - Vincular a uma **fonte pagadora** (conta específica da obra).  
- **Saídas (Gastos):**  
  - Registrar notas fiscais, recibos e comprovantes (upload de arquivos).  
  - Campos obrigatórios:  
    - Data da compra vs. data do pagamento.  
    - Descrição do gasto, valor, número do documento.  

#### **3.3 Fluxo de Caixa**  
- Exibir saldo atualizado em tempo real (**entradas – saídas**).  
- Filtrar por período (mensal, anual) ou categoria (material, mão de obra).  
- Alertar quando o saldo estiver abaixo de um limite definido.  

#### **3.4 Relatórios Financeiros**  
- **Resumo Mensal:**  
  - Total gasto por categoria.  
  - Gráficos comparativos (ex.: % de gastos com mão de obra vs. material).  
- **Cálculo da Taxa de Administração:**  
  - Aplicar automaticamente a taxa (ex.: 12%) sobre o total gasto.  
  - Exibir valor a ser faturado para a administradora.  
- **Relatório Anual:** Consolidado de todas as obras.   

---  

### **4. Requisitos Não Funcionais**  
- **Usabilidade:** Interface simples, focada em clientes não técnicos.  
- **Usabilidade:** Deve funcionar nos navegadores com layout responsivo.  
---  

### **5. Restrições**  
- **Tecnológicas:** Funcionar em navegadores modernos (Chrome, Edge).
 - **Stack:** laravel no backende vue js no frontend.
---  

### **6. Partes Interessadas**  
- **Cliente:** Empreiteiros/contratantes que precisam acompanhar gastos.  
- **Administradores:** Equipe que gerencia as obras e taxas.  
- **Contabilidade:** Para conciliação de notas fiscais e recibos.  

---  

### **7. Entregáveis**  
- Sistema web com todas as funcionalidades descritas.  
- Painel de visualização simplificada para o cliente.  
- Manual de uso e treinamento para a equipe.

Classes do sistema: 🔧 Entidades Principais e Relacionamentos
1. Obra

    Atributos:

        Nome da obra

        Descrição

        Endereço

        Data de início

        Prazo estimado

        Área em m²

        Valor estimado de execução

        Taxa de administração (%)

    Relacionamentos:

        Muitas para muitas com Categorias de Gasto

        Muitas para muitas com Fontes Pagadoras

        Um para muitos com Itens de Gasto

        Um para muitos com Entradas de Recursos

2. Categoria de Gasto

    Exemplos: Mão de obra, Material, Alimentação

    Atributos:

        Nome

        Descrição

    Relacionamentos:

        Muitas para muitas com Obras

        Um para muitos com Itens de Gasto

3. Fonte Pagadora

    Atributos:

        Nome

        Descrição

        Dados bancários ou forma de pagamento

    Relacionamentos:

        Muitas para muitas com Obras

        Um para muitos com Entradas de Recursos

4. Item de Gasto

    Representa um gasto específico em uma obra.

    Atributos:

        Obra (FK)

        Categoria de Gasto (FK)

        Descrição

        Valor

        Data da compra

        Data do pagamento

        Número da nota fiscal ou recibo

        Comprovante (arquivo ou link)

    Observações:

        Relaciona-se com Obra e Categoria de Gasto

5. Entrada de Recurso

    Representa um aporte de valor na obra por uma fonte pagadora.

    Atributos:

        Obra (FK)

        Fonte Pagadora (FK)

        Data da entrada

        Valor

        Descrição

        Comprovante (opcional)

Entidades e Relacionamentos
Cliente

Possui várias Obras (1:N)

Obra

Pertence a um Cliente (N:1)
Possui vários Gastos (1:N)

Gasto (Item de Gasto)

Pertence a uma Obra (N:1)
É classificado em uma Categoria de Gasto (N:1)
Tem uma Fonte Pagadora (N:1)

Categoria de Gasto

Classifica vários Gastos (1:N)
Exemplos: Mão de obra, Material, Alimentação

Fonte Pagadora

Financia vários Gastos (1:N)
Representa de onde vem o dinheiro para pagar cada gasto específico

Fluxo de Dados

Cliente tem múltiplas Obras
Cada Obra acumula Gastos ao longo do tempo
Cada Gasto é categorizado (tipo de despesa) e tem origem definida (fonte do pagamento)
Sistema calcula totais por obra, por categoria, por fonte pagadora
Aplica taxa de administração sobre gastos totais da obra
Gera relatórios consolidados para o cliente

Essa estrutura permite controle granular: o cliente vê todas suas obras, cada obra mostra seus gastos detalhados, e cada gasto tem sua classificação e origem de financiamento bem definidas.