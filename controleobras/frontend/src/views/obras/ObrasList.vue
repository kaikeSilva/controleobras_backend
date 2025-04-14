<template>
  <AppLayout title="Gerenciamento de Obras">
    <div class="bg-white shadow overflow-hidden rounded-lg">
      <!-- Filtros e Ações -->
      <div class="px-6 py-6 border-b border-gray-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-6 md:space-y-0">
          <div class="flex flex-col sm:flex-row sm:space-x-4 space-y-6 sm:space-y-0 w-full md:w-2/3">
            <div class="w-full sm:w-1/2">
              <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome da Obra</label>
              <div class="relative">
                <input
                  id="nome"
                  v-model="filters.nome"
                  type="text"
                  placeholder="Filtrar por nome"
                  class="w-full rounded-lg border shadow-sm transition-all duration-200 h-11 bg-white pl-4 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 focus:outline-none"
                  @keyup.enter="loadObras"
                />
              </div>
            </div>
            <div class="w-full sm:w-1/2">
              <label for="endereco" class="block text-sm font-medium text-gray-700 mb-2">Endereço</label>
              <div class="relative">
                <input
                  id="endereco"
                  v-model="filters.endereco"
                  type="text"
                  placeholder="Filtrar por endereço"
                  class="w-full rounded-lg border shadow-sm transition-all duration-200 h-11 bg-white pl-4 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 focus:outline-none"
                  @keyup.enter="loadObras"
                />
              </div>
            </div>
            <div class="w-full sm:w-1/2">
              <label for="perPage" class="block text-sm font-medium text-gray-700 mb-2">Itens por Página</label>
              <div class="relative">
                <select
                  id="perPage"
                  v-model="filters.per_page"
                  @change="loadObras"
                  class="w-full rounded-lg border shadow-sm transition-all duration-200 h-11 bg-white pl-4 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 focus:outline-none"
                >
                  <option v-for="option in [1, 5, 10, 50, 100]" :key="option" :value="option">
                    {{ option }}
                  </option>
                </select>
              </div>
            </div>
          </div>
          <div class="flex space-x-3">
            <button 
              @click="loadObras" 
              class="rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden text-base px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500 hover:shadow-md hover:-translate-y-0.5"
            >
              <span class="relative z-10 flex items-center">Filtrar</span>
            </button>
            <button 
              @click="resetFilters" 
              class="rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden text-base px-4 py-2.5 bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-300"
            >
              <span class="relative z-10 flex items-center">Limpar</span>
            </button>
            <router-link to="/obras/criar">
              <button 
                class="rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden text-base px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white focus:ring-green-500 hover:shadow-md hover:-translate-y-0.5"
              >
                <span class="relative z-10 flex items-center">Nova Obra</span>
              </button>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Tabela de Obras -->
      <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border-spacing-0 w-full">
          <thead>
            <tr>
              <th 
                scope="col" 
                class="bg-gray-50 text-left text-sm font-medium text-gray-600 uppercase px-4 py-3 cursor-pointer transition-colors duration-150"
                @click="sortBy('nome')"
              >
                Nome
                <span v-if="filters.sort_by === 'nome'" class="ml-1">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </span>
              </th>
              <th scope="col" class="bg-gray-50 text-left text-sm font-medium text-gray-600 uppercase px-4 py-3">
                Endereço
              </th>
              <th 
                scope="col" 
                class="bg-gray-50 text-left text-sm font-medium text-gray-600 uppercase px-4 py-3 cursor-pointer transition-colors duration-150"
                @click="sortBy('data_inicio')"
              >
                Data Início
                <span v-if="filters.sort_by === 'data_inicio'" class="ml-1">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </span>
              </th>
              <th 
                scope="col" 
                class="bg-gray-50 text-left text-sm font-medium text-gray-600 uppercase px-4 py-3 cursor-pointer transition-colors duration-150"
                @click="sortBy('valor_estimado')"
              >
                Valor Estimado
                <span v-if="filters.sort_by === 'valor_estimado'" class="ml-1">
                  {{ filters.sort_direction === 'asc' ? '↑' : '↓' }}
                </span>
              </th>
              <th scope="col" class="bg-gray-50 text-right text-sm font-medium text-gray-600 uppercase px-4 py-3">
                Ações
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="5" class="px-4 py-6 text-center">
                <div class="flex flex-col items-center justify-center space-y-3">
                  <div class="skeleton h-6 w-32 rounded"></div>
                </div>
              </td>
            </tr>
            <tr v-else-if="obras.length === 0">
              <td colspan="5" class="px-4 py-6 text-center">
                <div class="flex flex-col items-center justify-center">
                  <p class="text-gray-500">Nenhuma obra encontrada</p>
                </div>
              </td>
            </tr>
            <tr v-for="obra in obras" :key="obra.id" class="transition-colors duration-150 hover:bg-gray-50 border-b border-gray-100">
              <td class="px-4 py-4">
                <div class="text-sm font-medium text-gray-900">{{ obra.nome }}</div>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm text-gray-600">{{ obra.endereco }}</div>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm text-gray-600">{{ formatDate(obra.data_inicio) }}</div>
              </td>
              <td class="px-4 py-4">
                <div class="text-sm text-gray-600">{{ formatCurrency(obra.valor_estimado) }}</div>
              </td>
              <td class="px-4 py-4 text-right">
                <div class="flex items-center justify-end space-x-3">
                  <router-link :to="`/obras/${obra.id}`" class="text-blue-600 hover:text-blue-800 transition-colors duration-150 font-medium">
                    Visualizar
                  </router-link>
                  <router-link :to="`/obras/${obra.id}/editar`" class="text-purple-600 hover:text-purple-800 transition-colors duration-150 font-medium">
                    Editar
                  </router-link>
                  <button 
                    @click="confirmDelete(obra)" 
                    class="text-red-600 hover:text-red-800 transition-colors duration-150 font-medium"
                  >
                    Excluir
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Paginação -->
      <div class="bg-white px-6 py-4 flex items-center justify-between border-t border-gray-200">
        <div class="flex-1 flex justify-between sm:hidden">
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150"
            :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === 1 }"
          >
            Anterior
          </button>
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-150"
            :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page }"
          >
            Próxima
          </button>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Mostrando
              <span class="font-medium">{{ pagination.from || 0 }}</span>
              a
              <span class="font-medium">{{ pagination.to || 0 }}</span>
              de
              <span class="font-medium">{{ pagination.total }}</span>
              resultados
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex space-x-1" aria-label="Pagination">
              <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-150"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === 1 }"
              >
                <span class="sr-only">Anterior</span>
                &laquo;
              </button>
              <button
                v-for="page in paginationRange"
                :key="page"
                @click="goToPage(page)"
                class="min-w-36 h-36 border-radius-50% relative inline-flex items-center justify-center min-w-9 h-9 rounded-full border transition-colors duration-150"
                :class="page === pagination.current_page 
                  ? 'bg-blue-600 border-blue-600 text-white' 
                  : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'"
              >
                {{ page }}
              </button>
              <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-150"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page }"
              >
                <span class="sr-only">Próxima</span>
                &raquo;
              </button>
            </nav>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteModal" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-[4px] transition-opacity" aria-hidden="true" @click="showDeleteModal = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-8 pt-8 pb-6">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                  Confirmar Exclusão
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Tem certeza que deseja excluir a obra "{{ obraToDelete?.nome }}"? Esta ação não pode ser desfeita.
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-8 py-4 sm:flex sm:flex-row-reverse">
            <button 
              type="button" 
              class="rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden text-base px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white focus:ring-red-500 hover:shadow-md hover:-translate-y-0.5 w-full sm:w-auto"
              @click="deleteObra"
            >
              <span class="relative z-10 flex items-center">Excluir</span>
            </button>
            <button 
              type="button" 
              class="mt-3 sm:mt-0 sm:mr-3 rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden text-base px-4 py-2.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-300 w-full sm:w-auto"
              @click="showDeleteModal = false"
            >
              <span class="relative z-10 flex items-center">Cancelar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '../../components/AppLayout.vue';
import obraService, { Obra, ObraFilters, PaginatedResponse } from '../../services/obraService';
import toastService from '../../services/toastService';

const router = useRouter();
const obras = ref<Obra[]>([]);
const loading = ref(false);
const showDeleteModal = ref(false);
const obraToDelete = ref<Obra | null>(null);

const pagination = reactive({
  current_page: 1,
  from: 0,
  last_page: 1,
  per_page: 10,
  to: 0,
  total: 0
});

const filters = reactive<ObraFilters>({
  nome: '',
  endereco: '',
  sort_by: 'created_at',
  sort_direction: 'desc',
  page: 1,
  per_page: 10
});

// Calcular range de páginas para paginação
const paginationRange = computed(() => {
  const range = [];
  const maxPages = 5; // Número máximo de páginas a mostrar
  
  let start = Math.max(1, pagination.current_page - Math.floor(maxPages / 2));
  let end = Math.min(pagination.last_page, start + maxPages - 1);
  
  if (end - start + 1 < maxPages) {
    start = Math.max(1, end - maxPages + 1);
  }
  
  for (let i = start; i <= end; i++) {
    range.push(i);
  }
  
  return range;
});

// Carregar obras com filtros e paginação
const loadObras = async () => {
  loading.value = true;
  try {
    const response = await obraService.getObras(filters);
    obras.value = response.data;
    
    // Atualizar dados de paginação
    pagination.current_page = response.meta.current_page;
    pagination.from = response.meta.from;
    pagination.last_page = response.meta.last_page;
    pagination.per_page = response.meta.per_page;
    pagination.to = response.meta.to;
    pagination.total = response.meta.total;
  } catch (error) {
    console.error('Erro ao carregar obras:', error);
    toastService.showErrorToast(`Erro ao carregar obras: ${error instanceof Error ? error.message : 'Erro desconhecido'}`);
    obras.value = [];
  } finally {
    loading.value = false;
  }
};

// Navegar para uma página específica
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.last_page) return;
  
  filters.page = page;
  loadObras();
};

// Ordenar por campo
const sortBy = (field: string) => {
  if (filters.sort_by === field) {
    // Inverter direção se já estiver ordenando por este campo
    filters.sort_direction = filters.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.sort_by = field;
    filters.sort_direction = 'asc';
  }
  
  loadObras();
};

// Resetar filtros
const resetFilters = () => {
  filters.nome = '';
  filters.endereco = '';
  filters.page = 1;
  filters.sort_by = 'created_at';
  filters.sort_direction = 'desc';
  
  loadObras();
};

// Confirmar exclusão
const confirmDelete = (obra: Obra) => {
  obraToDelete.value = obra;
  showDeleteModal.value = true;
};

// Excluir obra
const deleteObra = async () => {
  if (!obraToDelete.value?.id) return;
  
  try {
    await obraService.deleteObra(obraToDelete.value.id);
    showDeleteModal.value = false;
    loadObras();
    toastService.showSuccessToast(`Obra "${obraToDelete.value.nome}" excluída com sucesso.`);
  } catch (error) {
    console.error('Erro ao excluir obra:', error);
    toastService.showErrorToast(`Erro ao excluir obra: ${error instanceof Error ? error.message : 'Erro desconhecido'}`);
  }
};

// Formatar data
const formatDate = (dateString: string) => {
  if (!dateString) return '';
  
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('pt-BR').format(date);
};

// Formatar valor monetário
const formatCurrency = (value: number) => {
  if (value === undefined || value === null) return '';
  
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

// Carregar obras ao montar o componente
onMounted(() => {
  loadObras();
});
</script>

<style scoped>
.skeleton {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: skeleton-loading 1.5s infinite;
}

@keyframes skeleton-loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}
</style>
