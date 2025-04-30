<template>
  <AppLayout title="Obras">
    <div class="bg-white shadow-md rounded-lg">
      <!-- Cabeçalho com filtros e botão de adicionar -->
      <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex flex-col gap-4 w-full">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <!-- Filtros de texto -->
            <AppInput
              v-model="filters.nome"
              placeholder="Nome da obra"
              class="w-full"
              icon="search"
            />
            <AppInput
              v-model="filters.endereco"
              placeholder="Endereço"
              class="w-full"
              icon="search"
            />
            
            <!-- Filtro de data de início (range) -->
            <div class="w-full">
              <label class="block text-sm font-medium text-gray-700 mb-1">Data de início</label>
              <div class="flex gap-2">
                <div class="w-1/2">
                  <label class="block text-xs text-gray-500 mb-1">De</label>
                  <input 
                    type="date" 
                    v-model="filters.data_inicio_min"
                    class="w-full rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                  />
                </div>
                <div class="w-1/2">
                  <label class="block text-xs text-gray-500 mb-1">Até</label>
                  <input 
                    type="date" 
                    v-model="filters.data_inicio_max"
                    class="w-full rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                  />
                </div>
              </div>
            </div>
            
            <!-- Filtros numéricos com min/max -->
            <div class="w-full">
              <label class="block text-sm font-medium text-gray-700 mb-1">Prazo estimado (dias)</label>
              <div class="flex gap-2">
                <input 
                  type="number" 
                  v-model="filters.prazo_estimado_min"
                  placeholder="Mínimo" 
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
                <input 
                  type="number" 
                  v-model="filters.prazo_estimado_max"
                  placeholder="Máximo" 
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
              </div>
            </div>
            
            <div class="w-full">
              <label class="block text-sm font-medium text-gray-700 mb-1">Valor estimado (R$)</label>
              <div class="flex gap-2">
                <input 
                  type="number" 
                  v-model="filters.valor_estimado_min"
                  placeholder="Mínimo" 
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
                <input 
                  type="number" 
                  v-model="filters.valor_estimado_max"
                  placeholder="Máximo" 
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
              </div>
            </div>
            
            <div class="w-full">
              <label class="block text-sm font-medium text-gray-700 mb-1">Taxa de administração (%)</label>
              <div class="flex gap-2">
                <input 
                  type="number" 
                  v-model="filters.taxa_administracao_min"
                  placeholder="Mínimo" 
                  step="0.01"
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
                <input 
                  type="number" 
                  v-model="filters.taxa_administracao_max"
                  placeholder="Máximo" 
                  step="0.01"
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
              </div>
            </div>
            
            <div class="w-full">
              <label class="block text-sm font-medium text-gray-700 mb-1">Área (m²)</label>
              <div class="flex gap-2">
                <input 
                  type="number" 
                  v-model="filters.area_m2_min"
                  placeholder="Mínimo" 
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
                <input 
                  type="number" 
                  v-model="filters.area_m2_max"
                  placeholder="Máximo" 
                  class="w-1/2 rounded-lg border border-gray-300 shadow-sm h-11 px-4 focus:outline-none focus:ring-4 focus:border-blue-500 focus:ring-blue-200"
                />
              </div>
            </div>
          </div>
          
          <div class="flex gap-2 w-full sm:w-auto">
            <button 
              @click="applyFilters" 
              class="h-11 px-4 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center w-full sm:w-32 shadow-sm"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
              </svg>
              Filtrar
            </button>
            <button 
              @click="clearFilters" 
              class="h-11 px-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors flex items-center justify-center w-full sm:w-32 shadow-sm"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
              Limpar
            </button>
          </div>
        </div>
        <router-link to="/obras/criar" class="w-full sm:w-auto mt-4 md:mt-0">
          <AppButton type="primary" class="whitespace-nowrap w-full sm:w-auto">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nova Obra
          </AppButton>
        </router-link>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-6 flex justify-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
      </div>

      <!-- Contador de total de itens -->
      <div v-if="!loading && obras.length > 0" class="px-6 py-3 bg-gray-50 border-b border-gray-200">
        <p class="text-sm text-gray-700">
          <span class="font-medium">{{ pagination.total || 0 }}</span> obras encontradas
        </p>
      </div>

      <!-- Tabela de obras (visível apenas em telas maiores) -->
      <div v-if="!loading && obras.length > 0" class="hidden md:block">
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
                      <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 011.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
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
            <tr v-for="obra in obras" :key="obra.id" class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4">
                <div class="text-sm font-medium text-gray-900 break-words max-w-[200px]">{{ obra.nome }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-500 break-words max-w-[200px]">{{ obra.endereco }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ formatDate(obra.data_inicio) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ obra.prazo_estimado }} dia(s)</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ obra.area_m2 }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ formatCurrency(obra.valor_estimado) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ obra.taxa_administracao }}%</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="relative inline-block text-left dropdown-container">
                  <button 
                    @click.stop="toggleDropdown(obra)" 
                    class="dropdown-toggle inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Ações
                    <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                      <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                  </button>
                  <div 
                    v-if="dropdownOpen[obra.id]" 
                    class="dropdown-menu origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                  >
                    <div class="py-1">
                      <router-link :to="`/obras/${obra.id}`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Visualizar
                      </router-link>
                      <router-link :to="`/obras/${obra.id}/editar`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Editar
                      </router-link>
                      <button 
                        @click="confirmDelete(obra)" 
                        class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100"
                      >
                        Excluir
                      </button>
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Cards para visualização mobile -->
      <div v-if="!loading && obras.length > 0" class="md:hidden">
        <div v-for="obra in obras" :key="obra.id" class="border-b border-gray-200 p-4">
          <div class="flex justify-between items-start">
            <div class="break-words max-w-[200px]">
              <h3 class="text-lg font-medium text-gray-900">{{ obra.nome }}</h3>
              <p class="text-sm text-gray-500 mt-1 break-words">{{ obra.endereco }}</p>
            </div>
            <div class="relative">
              <button 
                type="button" 
                class="dropdown-toggle inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-3 py-1 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                @click="toggleDropdown(obra)"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                </svg>
              </button>
              <div 
                v-if="dropdownOpen[obra.id]" 
                class="dropdown-menu origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
              >
                <div class="py-1">
                  <router-link :to="`/obras/${obra.id}`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Visualizar
                  </router-link>
                  <router-link :to="`/obras/${obra.id}/editar`" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Editar
                  </router-link>
                  <button 
                    @click="confirmDelete(obra)" 
                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-gray-100"
                  >
                    Excluir
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="grid grid-cols-1 gap-2 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-500">Data Início:</span>
              <span class="text-gray-900">{{ formatDate(obra.data_inicio) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Prazo Estimado:</span>
              <span class="text-gray-900">{{ obra.prazo_estimado }} dia(s)</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Área (m²):</span>
              <span class="text-gray-900">{{ obra.area_m2 }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Valor Estimado:</span>
              <span class="text-gray-900">{{ formatCurrency(obra.valor_estimado) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-500">Taxa Adm.:</span>
              <span class="text-gray-900">{{ obra.taxa_administracao }}%</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Sem resultados -->
      <div v-if="!loading && obras.length === 0" class="p-6 text-center">
        <p class="text-gray-500">Nenhuma obra encontrada.</p>
      </div>

      <!-- Paginação -->
      <div class="px-6 py-4 border-t border-gray-200 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-4">
          <div class="text-sm text-gray-500 text-center sm:text-left">
            Mostrando <span class="font-medium">{{ pagination.from || 0 }}</span> a <span class="font-medium">{{ pagination.to || 0 }}</span> de <span class="font-medium">{{ pagination.total || 0 }}</span> resultados
          </div>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Itens por página:</span>
            <select 
              v-model="filters.per_page" 
              @change="changeItemsPerPage"
              class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
        <div class="flex space-x-2 items-center">
          <AppButton 
            type="outline" 
            size="sm" 
            :disabled="pagination.current_page <= 1" 
            @click="goToPage(pagination.current_page - 1)"
          >
            Anterior
          </AppButton>
          <div class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded">
            Página {{ pagination.current_page }} de {{ pagination.last_page }}
          </div>
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
          Tem certeza que deseja excluir a obra <span class="font-medium">{{ obraToDelete?.nome }}</span>? Esta ação não pode ser desfeita.
        </p>
        <div class="flex justify-end space-x-3">
          <AppButton type="outline" @click="showDeleteModal = false">
            Cancelar
          </AppButton>
          <AppButton type="danger" @click="deleteObra">
            Excluir
          </AppButton>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/AppLayout.vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import obraService from '@/services/obraService';
import { useToast } from '@/services/toastService';

const router = useRouter();
const toast = useToast();

// Estado
const obras = ref([]);
const loading = ref(true);
const showDeleteModal = ref(false);
const obraToDelete = ref(null);
const dropdownOpen = ref({});
const pagination = ref({
  current_page: 1,
  from: 0,
  to: 0,
  total: 0,
  last_page: 1,
  per_page: 10
});

// Filtros e ordenação
const filters = reactive({
  nome: '',
  endereco: '',
  data_inicio_min: '',
  data_inicio_max: '',
  prazo_estimado_min: '',
  prazo_estimado_max: '',
  valor_estimado_min: '',
  valor_estimado_max: '',
  taxa_administracao_min: '',
  taxa_administracao_max: '',
  area_m2_min: '',
  area_m2_max: '',
  page: 1,
  per_page: 10,
  sort_by: '',
  sort_direction: 'asc'
});

// Colunas da tabela
const columns = reactive([
  { key: 'nome', label: 'Nome', sortable: true },
  { key: 'endereco', label: 'Endereço', sortable: true },
  { key: 'data_inicio', label: 'Data Início', sortable: true },
  { key: 'prazo_estimado', label: 'Prazo Estimado', sortable: true },
  { key: 'area_m2', label: 'Área (m²)', sortable: true },
  { key: 'valor_estimado', label: 'Valor Estimado', sortable: true },
  { key: 'taxa_administracao', label: 'Taxa Adm.', sortable: true }
]);

// Ordenação
const sortBy = (column) => {
  // Verifica se a coluna existe e se é ordenável
  const columnObj = columns.find(col => col.key === column);
  if (!column || !columnObj || !columnObj.sortable) return;
  
  if (filters.sort_by === column) {
    filters.sort_direction = filters.sort_direction === 'asc' ? 'desc' : 'asc';
  } else {
    filters.sort_by = column;
    filters.sort_direction = 'asc';
  }
  
  fetchObras();
};

// Buscar obras
const fetchObras = async () => {
  loading.value = true;
  try {
    const response = await obraService.getObras(filters);
    obras.value = response.data;
    pagination.value = {
      current_page: response.meta.current_page,
      from: response.meta.from,
      to: response.meta.to,
      total: response.meta.total,
      last_page: response.meta.last_page,
      per_page: response.meta.per_page
    };
  } catch (error) {
    toast.error('Erro ao carregar obras');
  } finally {
    loading.value = false;
  }
};

// Aplicar filtros
const applyFilters = () => {
  filters.page = 1;
  fetchObras();
};

// Limpar filtros
const clearFilters = () => {
  filters.nome = '';
  filters.endereco = '';
  filters.data_inicio_min = '';
  filters.data_inicio_max = '';
  filters.prazo_estimado_min = '';
  filters.prazo_estimado_max = '';
  filters.valor_estimado_min = '';
  filters.valor_estimado_max = '';
  filters.taxa_administracao_min = '';
  filters.taxa_administracao_max = '';
  filters.area_m2_min = '';
  filters.area_m2_max = '';
  filters.page = 1;
  filters.per_page = 10;
  filters.sort_by = '';
  filters.sort_direction = 'asc';
  fetchObras();
};

// Paginação
const goToPage = (page) => {
  filters.page = page;
  fetchObras();
};

// Toggle dropdown
const toggleDropdown = (obra) => {
  // Fechar todos os outros dropdowns
  Object.keys(dropdownOpen.value).forEach(key => {
    if (parseInt(key) !== obra.id) {
      dropdownOpen.value[parseInt(key)] = false;
    }
  });
  
  // Toggle o dropdown atual
  dropdownOpen.value[obra.id] = !dropdownOpen.value[obra.id];
};

// Fechar dropdown ao clicar fora
const closeDropdowns = (event) => {
  const target = event.target;
  if (!target.closest('.dropdown-menu') && !target.closest('.dropdown-toggle')) {
    Object.keys(dropdownOpen.value).forEach(key => {
      dropdownOpen.value[parseInt(key)] = false;
    });
  }
};

// Confirmar exclusão
const confirmDelete = (obra) => {
  obraToDelete.value = obra;
  showDeleteModal.value = true;
};

// Excluir obra
const deleteObra = async () => {
  if (!obraToDelete.value) return;
  
  try {
    await obraService.deleteObra(obraToDelete.value.id);
    toast.success('Obra excluída com sucesso');
    fetchObras();
  } catch (error) {
    toast.error('Erro ao excluir obra');
  } finally {
    showDeleteModal.value = false;
    obraToDelete.value = null;
  }
};

// Formatar data
const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('pt-BR');
};

// Formatar moeda
const formatCurrency = (value) => {
  if (value === undefined || value === null) return '-';
  return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
};

// Alterar itens por página
const changeItemsPerPage = () => {
  filters.page = 1;
  fetchObras();
};

// Eventos
onMounted(() => {
  fetchObras();
  document.addEventListener('click', closeDropdowns);
});

// Limpar event listeners
onUnmounted(() => {
  document.removeEventListener('click', closeDropdowns);
});
</script>
