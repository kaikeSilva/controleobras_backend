<template>
  <AppLayout :title="obra ? obra.nome : 'Detalhes da Obra'">
    <div v-if="loading" class="flex justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <div v-else-if="!obra" class="bg-white shadow-md rounded-lg p-6">
      <p class="text-gray-600">Obra não encontrada.</p>
      <div class="mt-6">
        <router-link to="/obras">
          <AppButton type="primary">Voltar para lista</AppButton>
        </router-link>
      </div>
    </div>

    <div v-else>
      <!-- Ações -->
      <div class="mb-6 flex justify-end space-x-3">
        <router-link :to="`/obras/${id}/editar`">
          <AppButton type="primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
              <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
            </svg>
            Editar
          </AppButton>
        </router-link>
        <AppButton type="danger" @click="confirmDelete">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
          </svg>
          Excluir
        </AppButton>
      </div>

      <!-- Detalhes da obra -->
      <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Informações Gerais</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 class="text-sm font-medium text-gray-500">Nome</h3>
              <p class="mt-1 text-base text-gray-900">{{ obra.nome }}</p>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500">Endereço</h3>
              <p class="mt-1 text-base text-gray-900">{{ obra.endereco }}</p>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500">Data de Início</h3>
              <p class="mt-1 text-base text-gray-900">{{ formatDate(obra.data_inicio) }}</p>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500">Prazo Estimado</h3>
              <p class="mt-1 text-base text-gray-900">{{ obra.prazo_estimado }} meses</p>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500">Área (m²)</h3>
              <p class="mt-1 text-base text-gray-900">{{ formatNumber(obra.area_m2) }} m²</p>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500">Valor Estimado</h3>
              <p class="mt-1 text-base text-gray-900">{{ formatCurrency(obra.valor_estimado) }}</p>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500">Taxa de Administração</h3>
              <p class="mt-1 text-base text-gray-900">{{ obra.taxa_administracao }}%</p>
            </div>
            
            <div>
              <h3 class="text-sm font-medium text-gray-500">Cadastrado em</h3>
              <p class="mt-1 text-base text-gray-900">{{ formatDateTime(obra.created_at) }}</p>
            </div>
          </div>
        </div>
        
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Descrição</h2>
          <p class="text-gray-700 whitespace-pre-line">{{ obra.descricao || 'Nenhuma descrição fornecida.' }}</p>
        </div>
        
        <div class="p-6">
          <h2 class="text-xl font-semibold text-gray-800 mb-4">Categorias de Gasto</h2>
          <div v-if="obra.categorias_gasto && obra.categorias_gasto.length > 0" class="flex flex-wrap gap-2">
            <span 
              v-for="categoria in obra.categorias_gasto" 
              :key="categoria.id" 
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-light text-primary"
            >
              {{ categoria.nome }}
            </span>
          </div>
          <p v-else class="text-gray-500">Nenhuma categoria de gasto associada.</p>
        </div>
      </div>
    </div>

    <!-- Modal de confirmação de exclusão -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Confirmar exclusão</h3>
        <p class="text-gray-600 mb-6">
          Tem certeza que deseja excluir a obra <span class="font-medium">{{ obra?.nome }}</span>? Esta ação não pode ser desfeita.
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
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/AppLayout.vue';
import AppButton from '@/components/AppButton.vue';
import obraService, { Obra } from '@/services/obraService';
import { useToast } from '@/services/toastService';

const props = defineProps<{
  id: string;
}>();

const router = useRouter();
const toast = useToast();

// Estado
const obra = ref<Obra | null>(null);
const loading = ref(true);
const showDeleteModal = ref(false);

// Métodos
const fetchObra = async () => {
  loading.value = true;
  try {
    obra.value = await obraService.getObra(Number(props.id));
  } catch (error) {
    toast.error('Erro ao carregar detalhes da obra');
    console.error('Erro ao carregar detalhes da obra:', error);
  } finally {
    loading.value = false;
  }
};

const confirmDelete = () => {
  showDeleteModal.value = true;
};

const deleteObra = async () => {
  try {
    await obraService.deleteObra(Number(props.id));
    toast.success('Obra excluída com sucesso');
    router.push('/obras');
  } catch (error) {
    toast.error('Erro ao excluir obra');
    console.error('Erro ao excluir obra:', error);
  } finally {
    showDeleteModal.value = false;
  }
};

// Formatação
const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('pt-BR').format(date);
};

const formatDateTime = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('pt-BR', {
    dateStyle: 'long',
    timeStyle: 'short'
  }).format(date);
};

const formatNumber = (value: number) => {
  return new Intl.NumberFormat('pt-BR').format(value);
};

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('pt-BR', {
    style: 'currency',
    currency: 'BRL'
  }).format(value);
};

// Inicialização
onMounted(() => {
  fetchObra();
});
</script>
