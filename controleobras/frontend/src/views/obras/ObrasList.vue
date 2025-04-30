<template>
  <AppLayout title="Obras">
    <div class="bg-white shadow-md rounded-lg">
      <!-- Cabeçalho com filtros e botão de adicionar -->
      <div class="p-6 border-b border-gray-200 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
          <AppInput
            v-model="filters.nome"
            placeholder="Buscar por nome"
            @input="debouncedFetchObras"
            class="w-full sm:w-64"
          />
          <AppInput
            v-model="filters.endereco"
            placeholder="Buscar por endereço"
            @input="debouncedFetchObras"
            class="w-full sm:w-64"
          />
        </div>
        <router-link to="/obras/criar">
          <AppButton type="primary" class="whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Nova Obra
          </AppButton>
        </router-link>
      </div>

      <!-- Tabela de obras -->
      <div class="">
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
            <tr v-else-if="obras.length === 0" class="hover:bg-gray-50">
              <td :colspan="columns.length + 1" class="px-6 py-4 text-center text-gray-500">
                Nenhuma obra encontrada.
              </td>
            </tr>
            <tr v-for="obra in obras" :key="obra.id" class="hover:bg-gray-50 transition-colors duration-200">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ obra.nome }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ obra.endereco }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ formatDate(obra.data_inicio) }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ obra.prazo_estimado }} meses</div>
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
                    class="text-gray-500 hover:text-gray-900 transition-colors duration-200"
                  >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                      <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                    </svg>
                  </button>
                  <div 
                    v-if="dropdownOpen[obra.id]" 
                    class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                  >
                    <div class="py-1">
                      <router-link 
                        :to="`/obras/${obra.id}`" 
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                      >
                        <div class="flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-primary" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                          </svg>
                          Visualizar
                        </div>
                      </router-link>
                      <router-link 
                        :to="`/obras/${obra.id}/editar`" 
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                      >
                        <div class="flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-accent" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                          </svg>
                          Editar
                        </div>
                      </router-link>
                      <button 
                        @click.stop="confirmDelete(obra)" 
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                      >
                        <div class="flex items-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-danger" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                          </svg>
                          Excluir
                        </div>
                      </button>
                    </div>
                  </div>
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
import obraService, { Obra, ObraFilters, PaginatedResponse } from '@/services/obraService';
import { useToast } from '@/services/toastService';

const router = useRouter();
const toast = useToast();

// Estado
const obras = ref<Obra[]>([]);
const loading = ref(true);
const showDeleteModal = ref(false);
const obraToDelete = ref<Obra | null>(null);

// Filtros e paginação
const filters = reactive<ObraFilters>({
  nome: '',
  endereco: '',
  sort_by: 'created_at',
  sort_direction: 'desc',
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
  { key: 'endereco', label: 'Endereço' },
  { key: 'data_inicio', label: 'Data de Início' },
  { key: 'prazo_estimado', label: 'Prazo (meses)' },
  { key: 'valor_estimado', label: 'Valor Estimado' },
  { key: 'taxa_administracao', label: 'Taxa Adm. (%)' }
];

// Dropdown
const dropdownOpen = ref<Record<number, boolean>>({});

// Fechar dropdown quando clicar fora
const closeAllDropdowns = () => {
  Object.keys(dropdownOpen.value).forEach(key => {
    dropdownOpen.value[Number(key)] = false;
  });
};

// Métodos
const fetchObras = async () => {
  loading.value = true;
  try {
    const response: PaginatedResponse<Obra> = await obraService.getObras(filters);
    obras.value = response.data;
    
    // Atualizar paginação
    Object.assign(pagination, response.meta);
  } catch (error) {
    toast.error('Erro ao carregar obras');
    console.error('Erro ao carregar obras:', error);
  } finally {
    loading.value = false;
  }
};

// Debounce para busca
const debouncedFetchObras = (() => {
  let timeout: ReturnType<typeof setTimeout>;
  return () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      filters.page = 1; // Resetar paginação ao filtrar
      fetchObras();
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
  fetchObras();
};

// Paginação
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.last_page) return;
  filters.page = page;
  fetchObras();
};

// Exclusão
const confirmDelete = (obra: Obra) => {
  obraToDelete.value = obra;
  showDeleteModal.value = true;
};

const deleteObra = async () => {
  if (!obraToDelete.value?.id) return;
  
  try {
    await obraService.deleteObra(obraToDelete.value.id);
    toast.success('Obra excluída com sucesso');
    fetchObras();
  } catch (error) {
    toast.error('Erro ao excluir obra');
    console.error('Erro ao excluir obra:', error);
  } finally {
    showDeleteModal.value = false;
    obraToDelete.value = null;
  }
};

// Dropdown
const toggleDropdown = (obra: Obra) => {
  // Fechar todos os outros dropdowns primeiro
  Object.keys(dropdownOpen.value).forEach(key => {
    if (Number(key) !== obra.id) {
      dropdownOpen.value[Number(key)] = false;
    }
  });
  // Alternar o dropdown atual
  dropdownOpen.value[obra.id!] = !dropdownOpen.value[obra.id!];
};

// Formatação
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('pt-BR').format(date);
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

// Inicialização
onMounted(() => {
  fetchObras();
  document.addEventListener('click', (e) => {
    const target = e.target as HTMLElement;
    if (!target.closest('.dropdown-container')) {
      closeAllDropdowns();
    }
  });
});

// Remover event listener ao desmontar o componente
onUnmounted(() => {
  document.removeEventListener('click', closeAllDropdowns);
});
</script>
