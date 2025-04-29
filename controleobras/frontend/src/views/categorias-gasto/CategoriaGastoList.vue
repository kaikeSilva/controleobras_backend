<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import categoriaGastoService, { CategoriaGasto, CategoriaGastoFilters, PaginatedResponse } from '@/services/categoriaGastoService';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import { useToast } from '@/services/toastService';

const router = useRouter();
const { toast } = useToast();

// Estado
const categorias = ref<CategoriaGasto[]>([]);
const loading = ref(false);
const error = ref<string | null>(null);
const searchTerm = ref('');
const currentPage = ref(1);
const totalPages = ref(1);
const itemsPerPage = ref(10);
const totalItems = ref(0);

// Filtros
const filters = computed<CategoriaGastoFilters>(() => ({
  nome: searchTerm.value || undefined,
  page: currentPage.value,
  per_page: itemsPerPage.value,
  sort_by: 'nome',
  sort_direction: 'asc'
}));

// Carregar categorias
const loadCategorias = async () => {
  loading.value = true;
  error.value = null;
  
  try {
    const response: PaginatedResponse<CategoriaGasto> = await categoriaGastoService.getCategorias(filters.value);
    categorias.value = response.data;
    currentPage.value = response.meta.current_page;
    totalPages.value = response.meta.last_page;
    totalItems.value = response.meta.total;
  } catch (err) {
    error.value = 'Erro ao carregar categorias de gasto';
    toast.error('Erro ao carregar categorias de gasto');
    console.error(err);
  } finally {
    loading.value = false;
  }
};

// Navegar para página de criação
const goToCreate = () => {
  router.push({ name: 'categoria-gasto-create' });
};

// Navegar para página de edição
const goToEdit = (id: number) => {
  router.push({ name: 'categoria-gasto-edit', params: { id } });
};

// Excluir categoria
const deleteCategoria = async (id: number) => {
  if (!confirm('Tem certeza que deseja excluir esta categoria de gasto?')) {
    return;
  }
  
  loading.value = true;
  
  try {
    await categoriaGastoService.deleteCategoria(id);
    toast.success('Categoria de gasto excluída com sucesso');
    loadCategorias(); // Recarregar a lista
  } catch (err) {
    toast.error('Erro ao excluir categoria de gasto');
    console.error(err);
  } finally {
    loading.value = false;
  }
};

// Paginação
const goToPage = (page: number) => {
  currentPage.value = page;
  loadCategorias();
};

// Pesquisar
const handleSearch = () => {
  currentPage.value = 1; // Resetar para primeira página ao pesquisar
  loadCategorias();
};

// Limpar pesquisa
const clearSearch = () => {
  searchTerm.value = '';
  handleSearch();
};

// Carregar dados iniciais
onMounted(() => {
  loadCategorias();
});
</script>

<template>
  <div class="categorias-gasto-list">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">Categorias de Gasto</h1>
      <AppButton 
        variant="primary" 
        @click="goToCreate"
        class="flex items-center"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        Nova Categoria
      </AppButton>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
      <div class="flex gap-4 mb-6">
        <div class="flex-1">
          <AppInput
            v-model="searchTerm"
            placeholder="Buscar por nome..."
            @keyup.enter="handleSearch"
          />
        </div>
        <AppButton 
          variant="secondary" 
          @click="handleSearch"
          class="flex items-center"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
          </svg>
          Buscar
        </AppButton>
        <AppButton 
          v-if="searchTerm" 
          variant="tertiary" 
          @click="clearSearch"
        >
          Limpar
        </AppButton>
      </div>

      <div v-if="loading" class="flex justify-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
      </div>

      <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-md">
        {{ error }}
      </div>

      <div v-else-if="categorias.length === 0" class="text-center py-8 text-gray-500">
        Nenhuma categoria de gasto encontrada.
      </div>

      <div v-else>
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Nome
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Descrição
              </th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                Ações
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="categoria in categorias" :key="categoria.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ categoria.nome }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-500 line-clamp-2">{{ categoria.descricao || '-' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">
                  <AppButton 
                    variant="tertiary" 
                    size="sm" 
                    @click="goToEdit(categoria.id!)"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    Editar
                  </AppButton>
                  <AppButton 
                    variant="tertiary" 
                    size="sm" 
                    @click="deleteCategoria(categoria.id!)"
                    class="text-red-600 hover:text-red-800"
                  >
                    Excluir
                  </AppButton>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Paginação -->
        <div v-if="totalPages > 1" class="flex justify-between items-center mt-6">
          <div class="text-sm text-gray-500">
            Mostrando {{ (currentPage - 1) * itemsPerPage + 1 }} a {{ Math.min(currentPage * itemsPerPage, totalItems) }} de {{ totalItems }} resultados
          </div>
          <div class="flex gap-2">
            <AppButton 
              variant="tertiary" 
              size="sm" 
              :disabled="currentPage === 1"
              @click="goToPage(currentPage - 1)"
            >
              Anterior
            </AppButton>
            <AppButton 
              v-for="page in totalPages" 
              :key="page" 
              variant="tertiary" 
              size="sm" 
              :class="{ 'bg-blue-50 text-blue-600': page === currentPage }"
              @click="goToPage(page)"
            >
              {{ page }}
            </AppButton>
            <AppButton 
              variant="tertiary" 
              size="sm" 
              :disabled="currentPage === totalPages"
              @click="goToPage(currentPage + 1)"
            >
              Próxima
            </AppButton>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-clamp: 2;
}
</style>
