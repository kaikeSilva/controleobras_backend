<template>
  <AppLayout title="Categorias de Gasto">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
      <!-- Cabeçalho com filtros e botão de adicionar -->
      <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
          <AppInput
            v-model="filters.nome"
            placeholder="Buscar por nome"
            @input="debouncedFetchCategorias"
            class="w-full sm:w-64"
          />
        </div>
        <router-link to="/categorias-gasto/criar">
          <AppButton type="primary" class="whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nova Categoria
          </AppButton>
        </router-link>
      </div>

      <!-- Tabela de categorias -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th 
                v-for="column in columns" 
                :key="column.key"
                scope="col" 
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                @click="sortBy(column.key)"
              >
                <div class="flex items-center">
                  {{ column.label }}
                  <span v-if="filters.sort_by === column.key" class="ml-1">
                    <svg v-if="filters.sort_direction === 'asc'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </span>
                </div>
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">Ações</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading" class="animate-pulse">
              <td :colspan="columns.length + 1" class="px-6 py-4">
                <div class="flex justify-center">
                  <svg class="animate-spin h-6 w-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
              </td>
            </tr>
            <tr v-else-if="categorias.length === 0" class="hover:bg-gray-50">
              <td :colspan="columns.length + 1" class="px-6 py-4 text-center text-gray-500">
                Nenhuma categoria de gasto encontrada.
              </td>
            </tr>
            <tr v-for="categoria in categorias" :key="categoria.id" class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ categoria.nome }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ categoria.descricao }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end space-x-2">
                  <router-link :to="`/categorias-gasto/${categoria.id}/editar`" class="text-accent hover:text-accent-dark transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                  </router-link>
                  <button 
                    @click="confirmDelete(categoria)" 
                    class="text-danger hover:text-danger-dark transition-colors duration-200"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-500">
          Mostrando <span class="font-medium">{{ pagination.from || 0 }}</span> a <span class="font-medium">{{ pagination.to || 0 }}</span> de <span class="font-medium">{{ pagination.total || 0 }}</span> resultados
        </div>
        <div class="flex space-x-2">
          <AppButton 
            type="outline" 
            size="sm" 
            :disabled="pagination.current_page <= 1" 
            @click="goToPage(pagination.current_page - 1)"
          >
            Anterior
          </AppButton>
          <AppButton 
            type="outline" 
            size="sm" 
            :disabled="pagination.current_page >= pagination.last_page" 
            @click="goToPage(pagination.current_page + 1)"
          >
            Próxima
          </AppButton>
        </div>
      </div>
    </div>

    <!-- Modal de confirmação de exclusão -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar exclusão</h3>
        <p class="text-gray-600 mb-6">
          Tem certeza que deseja excluir a categoria <span class="font-medium">{{ categoriaToDelete?.nome }}</span>? Esta ação não pode ser desfeita.
        </p>
        <div class="flex justify-end space-x-3">
          <AppButton type="outline" @click="showDeleteModal = false">
            Cancelar
          </AppButton>
          <AppButton type="danger" @click="deleteCategoria">
            Excluir
          </AppButton>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/AppLayout.vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import categoriaGastoService, { CategoriaGasto, CategoriaGastoFilters, PaginatedResponse } from '@/services/categoriaGastoService';
import { useToast } from '@/services/toastService';

const router = useRouter();
const toast = useToast();

// Estado
const categorias = ref<CategoriaGasto[]>([]);
const loading = ref(true);
const showDeleteModal = ref(false);
const categoriaToDelete = ref<CategoriaGasto | null>(null);

// Filtros e paginação
const filters = reactive<CategoriaGastoFilters>({
  nome: '',
  sort_by: 'nome',
  sort_direction: 'asc',
  page: 1,
  per_page: 10
});

const pagination = reactive({
  current_page: 1,
  from: 0,
  last_page: 1,
  path: '',
  per_page: 10,
  to: 0,
  total: 0
});

// Colunas da tabela
const columns = [
  { key: 'nome', label: 'Nome' },
  { key: 'descricao', label: 'Descrição' }
];

// Métodos
const fetchCategorias = async () => {
  loading.value = true;
  try {
    const response: PaginatedResponse<CategoriaGasto> = await categoriaGastoService.getCategorias(filters);
    categorias.value = response.data;
    
    // Atualizar paginação
    Object.assign(pagination, response.meta);
  } catch (error) {
    toast.error('Erro ao carregar categorias de gasto');
    console.error('Erro ao carregar categorias de gasto:', error);
  } finally {
    loading.value = false;
  }
};

// Debounce para busca
const debouncedFetchCategorias = (() => {
  let timeout: ReturnType<typeof setTimeout>;
  return () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      filters.page = 1; // Resetar paginação ao filtrar
      fetchCategorias();
    }, 300);
  };
})();

// Ordenação
const sortBy = (column: string) => {
  if (filters.sort_by === column) {
    filters.sort_direction = filters.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.sort_by = column;
    filters.sort_direction = 'asc';
  }
  fetchCategorias();
};

// Paginação
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.last_page) return;
  filters.page = page;
  fetchCategorias();
};

// Exclusão
const confirmDelete = (categoria: CategoriaGasto) => {
  categoriaToDelete.value = categoria;
  showDeleteModal.value = true;
};

const deleteCategoria = async () => {
  if (!categoriaToDelete.value?.id) return;
  
  try {
    await categoriaGastoService.deleteCategoria(categoriaToDelete.value.id);
    toast.success('Categoria de gasto excluída com sucesso');
    fetchCategorias();
  } catch (error) {
    toast.error('Erro ao excluir categoria de gasto');
    console.error('Erro ao excluir categoria de gasto:', error);
  } finally {
    showDeleteModal.value = false;
    categoriaToDelete.value = null;
  }
};

// Inicialização
onMounted(() => {
  fetchCategorias();
});
</script>
