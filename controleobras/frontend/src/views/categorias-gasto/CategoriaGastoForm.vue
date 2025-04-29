<script setup lang="ts">
import { ref, onMounted, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import categoriaGastoService, { CategoriaGasto } from '@/services/categoriaGastoService';
import obraService, { Obra } from '@/services/obraService';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import AppTextarea from '@/components/AppTextarea.vue';
import { useToast } from '@/services/toastService';

const router = useRouter();
const route = useRoute();
const { toast } = useToast();

// Verificar se estamos editando ou criando
const isEditing = computed(() => !!route.params.id);
const pageTitle = computed(() => isEditing.value ? 'Editar Categoria de Gasto' : 'Nova Categoria de Gasto');

// Estado
const categoriaGasto = ref<CategoriaGasto>({
  nome: '',
  descricao: '',
  obras: []
});
const obras = ref<Obra[]>([]);
const selectedObras = ref<number[]>([]);
const loading = ref(false);
const submitting = ref(false);
const errors = ref<Record<string, string>>({});

// Carregar categoria para edição
const loadCategoria = async (id: number) => {
  loading.value = true;
  
  try {
    const data = await categoriaGastoService.getCategoria(id);
    categoriaGasto.value = data;
    
    // Extrair IDs das obras relacionadas
    if (data.obras) {
      selectedObras.value = data.obras.map((obra: any) => obra.id);
    }
  } catch (err) {
    toast.error('Erro ao carregar categoria de gasto');
    console.error(err);
    router.push({ name: 'categoria-gasto-list' });
  } finally {
    loading.value = false;
  }
};

// Carregar lista de obras para o select
const loadObras = async () => {
  try {
    const response = await obraService.getObras({ per_page: 100 });
    obras.value = response.data;
  } catch (err) {
    console.error('Erro ao carregar obras:', err);
  }
};

// Validar formulário
const validateForm = (): boolean => {
  errors.value = {};
  
  if (!categoriaGasto.value.nome?.trim()) {
    errors.value.nome = 'O nome da categoria é obrigatório';
  }
  
  return Object.keys(errors.value).length === 0;
};

// Salvar categoria
const saveCategoria = async () => {
  if (!validateForm()) {
    toast.error('Por favor, corrija os erros no formulário');
    return;
  }
  
  submitting.value = true;
  
  try {
    // Incluir obras selecionadas
    const dataToSend = {
      ...categoriaGasto.value,
      obras: selectedObras.value
    };
    
    if (isEditing.value) {
      await categoriaGastoService.updateCategoria(Number(route.params.id), dataToSend);
      toast.success('Categoria de gasto atualizada com sucesso');
    } else {
      await categoriaGastoService.createCategoria(dataToSend);
      toast.success('Categoria de gasto criada com sucesso');
    }
    
    router.push({ name: 'categoria-gasto-list' });
  } catch (err: any) {
    console.error(err);
    
    // Tratar erros de validação da API
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
      toast.error('Por favor, corrija os erros no formulário');
    } else {
      toast.error('Erro ao salvar categoria de gasto');
    }
  } finally {
    submitting.value = false;
  }
};

// Cancelar e voltar para a listagem
const cancelAndGoBack = () => {
  router.push({ name: 'categoria-gasto-list' });
};

// Inicialização
onMounted(async () => {
  await loadObras();
  
  if (isEditing.value && route.params.id) {
    await loadCategoria(Number(route.params.id));
  }
});
</script>

<template>
  <div class="categoria-gasto-form">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-semibold text-gray-800">{{ pageTitle }}</h1>
      <AppButton 
        variant="tertiary" 
        @click="cancelAndGoBack"
        class="flex items-center"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Voltar
      </AppButton>
    </div>

    <div v-if="loading" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-primary"></div>
    </div>

    <form v-else @submit.prevent="saveCategoria" class="bg-white rounded-lg shadow-sm p-6">
      <div class="space-y-6">
        <!-- Nome -->
        <div>
          <AppInput
            v-model="categoriaGasto.nome"
            label="Nome *"
            placeholder="Ex: Mão de obra, Material, Alimentação"
            :error="errors.nome"
          />
        </div>

        <!-- Descrição -->
        <div>
          <AppTextarea
            v-model="categoriaGasto.descricao"
            label="Descrição"
            placeholder="Descreva a categoria de gasto..."
            rows="4"
            :error="errors.descricao"
          />
        </div>

        <!-- Obras relacionadas -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Obras relacionadas
          </label>
          <div class="border border-gray-300 rounded-md p-4 max-h-60 overflow-y-auto">
            <div v-if="obras.length === 0" class="text-gray-500 text-sm">
              Nenhuma obra disponível
            </div>
            <div v-else class="space-y-2">
              <div v-for="obra in obras" :key="obra.id" class="flex items-center">
                <input
                  type="checkbox"
                  :id="`obra-${obra.id}`"
                  :value="obra.id"
                  v-model="selectedObras"
                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label :for="`obra-${obra.id}`" class="ml-2 block text-sm text-gray-900">
                  {{ obra.nome }}
                </label>
              </div>
            </div>
          </div>
          <p v-if="errors.obras" class="mt-1 text-sm text-red-600">{{ errors.obras }}</p>
        </div>

        <!-- Botões de ação -->
        <div class="flex justify-end gap-3 pt-4">
          <AppButton 
            type="button" 
            variant="secondary" 
            @click="cancelAndGoBack"
            :disabled="submitting"
          >
            Cancelar
          </AppButton>
          <AppButton 
            type="submit" 
            variant="primary" 
            :loading="submitting"
          >
            {{ isEditing ? 'Atualizar' : 'Salvar' }}
          </AppButton>
        </div>
      </div>
    </form>
  </div>
</template>
