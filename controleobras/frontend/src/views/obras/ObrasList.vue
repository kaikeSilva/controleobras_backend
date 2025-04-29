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
      <div class="relative">
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
                  <div class="relative inline-block text-left action-menu-container">
                    <button 
                      @click.stop="toggleActionMenu(obra.id, $event)" 
                      class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                      Ações
                      <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Dropdowns (positioned outside the scrollable area) -->
        <div id="dropdown-container" class="dropdown-container">
          <div v-for="obra in obras" :key="`dropdown-${obra.id}`">
            <div 
              v-if="activeActionMenu === obra.id" 
              :id="`dropdown-${obra.id}`"
              class="fixed z-50 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none dropdown-menu"
              @click.stop
            >
              <div class="py-1">
                <router-link 
                  :to="`/obras/${obra.id}`" 
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200"
                >
                  <div class="flex items-center">
                    <svg class="mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                      <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                    </svg>
                    Visualizar
                  </div>
                </router-link>
                
                <router-link 
                  :to="`/obras/${obra.id}/editar`" 
                  class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200"
                >
                  <div class="flex items-center">
                    <svg class="mr-3 h-5 w-5 text-purple-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    Editar
                  </div>
                </router-link>
                
                <button 
                  @click="confirmDelete(obra)" 
                  class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition-colors duration-200"
                >
                  <div class="flex items-center">
                    <svg class="mr-3 h-5 w-5 text-red-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Excluir
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Paginação -->
      <div class="bg-white px-6 py-6 border-t border-gray-200">
        <div class="flex flex-col space-y-4">
          <!-- Informação de paginação -->
          <div class="text-sm text-gray-700 text-center sm:text-left">
            Mostrando
            <span class="font-medium">{{ pagination.from || 0 }}</span>
            a
            <span class="font-medium">{{ pagination.to || 0 }}</span>
            de
            <span class="font-medium">{{ pagination.total }}</span>
            resultados
          </div>
          
          <!-- Controles de paginação -->
          <div class="flex flex-col sm:flex-row justify-between items-center space-y-4 sm:space-y-0">
            <!-- Seletor de itens por página -->
            <div class="w-full sm:w-48">
              <label for="perPage" class="block text-sm font-medium text-gray-700 mb-2">Itens por Página</label>
              <div class="relative">
                <select
                  id="perPage"
                  v-model="filters.per_page"
                  @change="handlePerPageChange"
                  class="w-full rounded-lg border shadow-sm transition-all duration-200 h-11 bg-white pl-4 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 focus:outline-none"
                >
                  <option v-for="option in [1, 5, 10, 50, 100]" :key="option" :value="option">
                    {{ option }}
                  </option>
                </select>
              </div>
            </div>
            
            <!-- Versão mobile dos controles de paginação - visível apenas em telas pequenas -->
            <div class="flex space-x-2 sm:hidden">
              <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === 1 }"
              >
                Anterior
              </button>
              <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page }"
              >
                Próxima
              </button>
            </div>
            
            <!-- Versão desktop dos controles de paginação - visível apenas em telas médias e grandes -->
            <div class="hidden sm:flex items-center space-x-1">
              <button
                @click="goToPage(pagination.current_page - 1)"
                :disabled="pagination.current_page === 1"
                class="relative inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === 1 }"
              >
                <span class="sr-only">Anterior</span>
                &laquo;
              </button>
              <button
                v-for="page in paginationRange"
                :key="page"
                @click="goToPage(page)"
                class="relative inline-flex items-center justify-center min-w-9 h-9 rounded-full border transition-colors duration-200 px-3"
                :class="page === pagination.current_page 
                  ? 'bg-blue-600 border-blue-600 text-white' 
                  : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'"
              >
                {{ page }}
              </button>
              <button
                @click="goToPage(pagination.current_page + 1)"
                :disabled="pagination.current_page === pagination.last_page"
                class="relative inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-200"
                :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page === pagination.last_page }"
              >
                <span class="sr-only">Próxima</span>
                &raquo;
              </button>
            </div>
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
import { ref, reactive, computed, onMounted, onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '../../components/AppLayout.vue';
import obraService, { Obra, ObraFilters, PaginatedResponse } from '../../services/obraService';
import toastService from '../../services/toastService';

const router = useRouter();
const obras = ref<Obra[]>([]);
const loading = ref(false);
const showDeleteModal = ref(false);
const obraToDelete = ref<Obra | null>(null);
const activeActionMenu = ref<number | null>(null);

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
    // Garantir que a página seja um número
    filters.page = Number(filters.page) || 1;
    filters.per_page = Number(filters.per_page) || 10;
    
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

// Handle per page change
const handlePerPageChange = () => {
  filters.page = 1;
  loadObras();
};

// Toggle action menu
const toggleActionMenu = (obraId: number, event?: MouseEvent) => {
  if (activeActionMenu.value === obraId) {
    activeActionMenu.value = null;
  } else {
    activeActionMenu.value = obraId;
    
    // Wait for the DOM to update with the new dropdown
    setTimeout(() => {
      // Find the button that was clicked
      const button = event?.target as HTMLElement;
      const buttonContainer = button?.closest('.action-menu-container');
      const buttonRect = buttonContainer?.getBoundingClientRect();
      
      // Find the dropdown element
      const dropdown = document.getElementById(`dropdown-${obraId}`) as HTMLElement;
      
      if (dropdown && buttonRect) {
        // Position the dropdown relative to the button
        dropdown.style.top = `${buttonRect.bottom + window.scrollY}px`;
        dropdown.style.left = `${buttonRect.right - dropdown.offsetWidth + window.scrollX}px`;
        
        // Check if dropdown would extend beyond viewport
        const dropdownRect = dropdown.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        
        if (dropdownRect.bottom > viewportHeight - 20) { // 20px buffer
          // Position above the button if it would go off-screen
          dropdown.style.top = `${buttonRect.top - dropdown.offsetHeight + window.scrollY}px`;
        }
      }
    }, 0);
  }
};

// Close action menu when clicking outside
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement;
  if (!target.closest('.action-menu-container') && activeActionMenu.value !== null) {
    activeActionMenu.value = null;
  }
};

// Add event listeners and load initial data
onMounted(() => {
  loadObras();
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
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
