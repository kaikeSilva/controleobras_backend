# Boas Práticas para Construção de Views de Lista

Este documento apresenta diretrizes e boas práticas para a construção de views de lista no sistema de Controle de Obras, baseado na implementação da tela ObrasList. Estas práticas visam garantir consistência, usabilidade e manutenibilidade em todas as telas de listagem do sistema.

## Estrutura da View

### 1. Organização do Template

```
<template>
  <AppLayout>
    <!-- 1. Cabeçalho com filtros e ações principais -->
    <div class="header-section">
      <!-- Filtros -->
      <!-- Botões de ação (Adicionar, etc.) -->
    </div>
    
    <!-- 2. Seção de resumo/totais (quando aplicável) -->
    <div class="summary-section">
      <!-- Totais, estatísticas, etc. -->
    </div>
    
    <!-- 3. Tabela de dados (versão desktop) -->
    <div class="table-section">
      <!-- Cabeçalho da tabela com ordenação -->
      <!-- Linhas de dados -->
      <!-- Ações por item -->
    </div>
    
    <!-- 4. Cards (versão mobile) -->
    <div class="cards-section">
      <!-- Cards responsivos para visualização em dispositivos móveis -->
    </div>
    
    <!-- 5. Paginação -->
    <div class="pagination-section">
      <!-- Controles de paginação -->
      <!-- Seletor de itens por página -->
    </div>
    
    <!-- 6. Modais (confirmação, detalhes, etc.) -->
    <div class="modals">
      <!-- Modal de confirmação de exclusão -->
      <!-- Outros modais -->
    </div>
  </AppLayout>
</template>
```

### 2. Organização do Script

```javascript
<script setup>
// 1. Importações
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import componentes from '@/components';
import services from '@/services';

// 2. Inicialização de serviços e hooks
const router = useRouter();
const { toast } = useToast();

// 3. Estado
// - Dados principais
const items = ref([]);
const loading = ref(true);
// - Estado de UI
const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const dropdownOpen = ref({});
// - Paginação
const pagination = ref({...});
// - Filtros
const filters = reactive({...});
// - Configuração da tabela
const columns = reactive([...]);

// 4. Métodos de busca e manipulação de dados
// - Buscar dados
const fetchItems = async () => {...};
// - Aplicar filtros
const applyFilters = () => {...};
// - Limpar filtros
const clearFilters = () => {...};

// 5. Métodos de UI e interação
// - Ordenação
const sortBy = (column) => {...};
// - Paginação
const goToPage = (page) => {...};
// - Dropdowns e menus
const toggleDropdown = (item) => {...};
const closeDropdowns = (event) => {...};

// 6. Operações CRUD
// - Confirmar exclusão
const confirmDelete = (item) => {...};
// - Excluir item
const deleteItem = async () => {...};

// 7. Formatação e utilitários
// - Formatar data
const formatDate = (date) => {...};
// - Formatar moeda
const formatCurrency = (value) => {...};

// 8. Eventos de ciclo de vida
onMounted(() => {
  fetchItems();
  document.addEventListener('click', closeDropdowns);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns);
});
</script>
```

## Requisitos Funcionais

### 1. Listagem

- Implementar paginação do lado do servidor para otimizar o carregamento de dados
- Exibir indicadores de carregamento durante operações assíncronas
- Mostrar mensagem apropriada quando não houver dados
- Exibir contagem total de itens e intervalo atual (ex: "Mostrando 1 a 10 de 50 resultados")

### 2. Filtragem

- Agrupar filtros relacionados (ex: range de datas, valores mínimo/máximo)
- Implementar filtros específicos para cada tipo de dado:
  - Texto: campos de busca com ícone de lupa
  - Datas: seletores de data com opção de range (de/até)
  - Números: campos numéricos com opção de range (mínimo/máximo)
- Incluir botões para aplicar e limpar filtros
- Manter estado dos filtros entre navegações quando apropriado

### 3. Ordenação

- Permitir ordenação por colunas relevantes
- Indicar visualmente a coluna e direção de ordenação atual
- Alternar entre ordenação ascendente e descendente ao clicar na mesma coluna

### 4. Paginação

- Exibir controles de navegação entre páginas
- Permitir selecionar o número de itens por página
- Atualizar a URL para refletir a página atual (opcional)
- Manter a posição da página ao retornar à listagem

### 5. Operações CRUD

- Incluir botão de criação em posição de destaque
- Implementar ações por item (editar, excluir, visualizar) em menu dropdown
- Solicitar confirmação antes de operações destrutivas (ex: exclusão)
- Exibir feedback após operações (mensagens de sucesso/erro)
- Atualizar a lista após operações de criação, edição ou exclusão

### 6. Totalizadores (quando aplicável)

- Exibir totais e estatísticas relevantes em seção destacada
- Atualizar totais ao aplicar filtros
- Formatar valores numéricos e monetários adequadamente

## Requisitos Não-Funcionais

### 1. Responsividade

- Implementar visualização em tabela para desktop
- Implementar visualização em cards para dispositivos móveis
- Adaptar layout de filtros para diferentes tamanhos de tela
- Utilizar grid responsivo para organizar filtros e controles

### 2. Desempenho

- Implementar debounce em filtros de texto para evitar requisições excessivas
- Carregar dados de forma paginada
- Mostrar indicadores de carregamento durante operações assíncronas
- Otimizar a renderização de listas grandes

### 3. Acessibilidade

- Utilizar rótulos (labels) em todos os campos de formulário
- Garantir contraste adequado entre texto e fundo
- Implementar foco visual em elementos interativos
- Utilizar atributos ARIA quando necessário

### 4. Estilo e Design

- Seguir o sistema de design estabelecido
- Manter consistência visual com o restante da aplicação
- Utilizar cores para destacar informações importantes
- Implementar estados visuais para interações (hover, focus, active)
- Utilizar ícones para melhorar a compreensão visual

### 5. Usabilidade

- Posicionar ações frequentes em locais de fácil acesso
- Fornecer feedback visual para todas as interações
- Implementar tooltips para ações com ícones
- Manter consistência na posição e comportamento dos elementos de interface

## Implementação Técnica

### 1. Serviços e API

- Criar um serviço dedicado para cada tipo de entidade
- Implementar métodos para todas as operações CRUD
- Utilizar interfaces TypeScript para tipagem de dados
- Tratar erros adequadamente e exibir mensagens amigáveis

```typescript
// Exemplo de serviço
const itemService = {
  getItems: (filters = {}): Promise<PaginatedResponse<Item>> => {
    return api.get('/items', { params: filters })
      .then((response: any) => response.data);
  },
  
  deleteItem: (id: number): Promise<void> => {
    return api.delete(`/items/${id}`)
      .then((response: any) => response.data);
  },
  
  // Outros métodos...
};
```

### 2. Componentes Reutilizáveis

- Utilizar componentes base para elementos comuns:
  - AppLayout: Layout base da aplicação
  - AppButton: Botões estilizados
  - AppInput: Campos de entrada
  - AppModal: Modais de confirmação e formulários
- Implementar slots e props para personalização

### 3. Gestão de Estado

- Utilizar refs para valores reativos simples
- Utilizar reactive para objetos complexos
- Considerar Pinia/Vuex para estado compartilhado entre componentes
- Manter o estado local quando possível para simplicidade

### 4. Tratamento de Erros

- Implementar try/catch em todas as operações assíncronas
- Exibir mensagens de erro amigáveis usando o sistema de toast
- Registrar erros no console para depuração
- Implementar fallbacks para dados ausentes

```typescript
try {
  await itemService.deleteItem(itemToDelete.value.id);
  toast.success('Item excluído com sucesso');
  await fetchItems();
} catch (error) {
  console.error('Erro ao excluir item:', error);
  toast.error('Erro ao excluir item');
}
```

## Checklist de Implementação

Utilize esta checklist ao implementar novas views de lista:

### Estrutura Básica
- [ ] Layout base com AppLayout
- [ ] Seção de filtros
- [ ] Tabela/lista de dados
- [ ] Paginação
- [ ] Modais necessários

### Funcionalidades
- [ ] Listagem paginada
- [ ] Filtros relevantes
- [ ] Ordenação por colunas
- [ ] Ações CRUD por item
- [ ] Totalizadores (quando aplicável)

### Responsividade
- [ ] Layout adaptável para desktop
- [ ] Layout adaptável para tablets
- [ ] Layout adaptável para smartphones

### Experiência do Usuário
- [ ] Indicadores de carregamento
- [ ] Mensagens de feedback
- [ ] Estados vazios
- [ ] Confirmações para ações destrutivas

### Código e Manutenção
- [ ] Tipagem com TypeScript
- [ ] Serviço dedicado para operações de API
- [ ] Componentes reutilizáveis
- [ ] Tratamento de erros
- [ ] Comentários em seções complexas

## Conclusão

Seguir estas diretrizes garantirá consistência, usabilidade e manutenibilidade em todas as views de lista do sistema. Adapte conforme necessário para requisitos específicos de cada entidade, mantendo a coerência com o restante da aplicação.
