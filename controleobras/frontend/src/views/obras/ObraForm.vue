<template>
  <AppLayout :title="isEditing ? 'Editar Obra' : 'Nova Obra'">
    <div class="bg-white shadow-md overflow-hidden rounded-lg">
      <form @submit.prevent="saveObra" class="px-6 py-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-6 sm:grid-cols-6">
          <!-- Nome da obra -->
          <div class="sm:col-span-3">
            <AppInput
              v-model="form.nome"
              label="Nome da Obra"
              required
              :error="errors.nome"
            />
          </div>

          <!-- Endereço -->
          <div class="sm:col-span-3">
            <AppInput
              v-model="form.endereco"
              label="Endereço"
              required
              :error="errors.endereco"
            />
          </div>

          <!-- Descrição -->
          <div class="sm:col-span-6">
            <AppTextarea
              v-model="form.descricao"
              label="Descrição"
              :error="errors.descricao"
            />
          </div>

          <!-- Data de início -->
          <div class="sm:col-span-2">
            <AppInput
              v-model="form.data_inicio"
              type="date"
              label="Data de Início"
              required
              :error="errors.data_inicio"
            />
          </div>

          <!-- Prazo estimado -->
          <div class="sm:col-span-2">
            <AppInput
              v-model="form.prazo_estimado"
              type="number"
              label="Prazo Estimado (dias)"
              required
              :error="errors.prazo_estimado"
            />
          </div>

          <!-- Área m² -->
          <div class="sm:col-span-2">
            <AppInput
              v-model="form.area_m2"
              type="number"
              label="Área (m²)"
              required
              :error="errors.area_m2"
            />
          </div>

          <!-- Valor estimado -->
          <div class="sm:col-span-3">
            <AppInput
              v-model="form.valor_estimado"
              type="number"
              step="0.01"
              label="Valor Estimado (R$)"
              required
              :error="errors.valor_estimado"
            />
          </div>

          <!-- Taxa de administração -->
          <div class="sm:col-span-3">
            <AppInput
              v-model.number="form.taxa_administracao"
              type="number"
              label="Taxa de Administração (%)"
              required
              min="0"
              max="100"
              step="0.01"
              :error="errors.taxa_administracao"
            />
          </div>

          <!-- Status -->
          <div class="sm:col-span-3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
              Status <span class="text-red-500">*</span>
            </label>
            <select
              id="status"
              v-model="form.status"
              class="w-full rounded-lg border shadow-sm transition-all duration-200 h-11 bg-white pl-4 border-gray-300 focus:border-blue-500 focus:ring-4 focus:ring-blue-200 focus:outline-none"
              required
            >
              <option value="planejamento">Planejamento</option>
              <option value="em_andamento">Em Andamento</option>
              <option value="concluida">Concluída</option>
              <option value="pausada">Pausada</option>
              <option value="cancelada">Cancelada</option>
            </select>
            <p v-if="errors.status" class="mt-2 text-sm text-red-600">{{ errors.status }}</p>
          </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3">
          <router-link to="/obras">
            <button 
              type="button" 
              class="rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden text-base px-4 py-2.5 bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-300"
            >
              <span class="relative z-10 flex items-center">Cancelar</span>
            </button>
          </router-link>
          <button 
            type="submit" 
            class="rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden text-base px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500 hover:shadow-md hover:-translate-y-0.5"
            :disabled="loading"
          >
            <span class="relative z-10 flex items-center">
              <span v-if="loading" class="mr-2">
                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
              </span>
              {{ isEditing ? 'Atualizar' : 'Salvar' }}
            </span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import AppLayout from '../../components/AppLayout.vue';
import AppInput from '../../components/AppInput.vue';
import AppTextarea from '../../components/AppTextarea.vue';
import obraService, { Obra } from '../../services/obraService';
import toastService from '../../services/toastService';

const router = useRouter();
const route = useRoute();
const loading = ref(false);

// Verificar se estamos editando uma obra existente
const isEditing = computed(() => !!route.params.id);

// Formulário
const form = reactive<Obra>({
  nome: '',
  endereco: '',
  descricao: '',
  data_inicio: '',
  prazo_estimado: 0,
  area_m2: 0,
  valor_estimado: 0,
  taxa_administracao: 0,
  status: 'planejamento'
});

// Erros de validação
const errors = reactive({
  nome: '',
  endereco: '',
  descricao: '',
  data_inicio: '',
  prazo_estimado: '',
  area_m2: '',
  valor_estimado: '',
  taxa_administracao: '',
  status: ''
});

// Carregar dados da obra para edição
const loadObra = async () => {
  if (!isEditing.value) return;
  
  try {
    loading.value = true;
    const obra = await obraService.getObra(route.params.id as string);
    
    // Preencher formulário com dados da obra
    Object.assign(form, {
      ...obra,
      // Garantir que a data esteja no formato correto para o input type="date"
      data_inicio: obra.data_inicio.split('T')[0],
    });
  } catch (error) {
    console.error('Erro ao carregar obra:', error);
    toastService.showErrorToast(`Erro ao carregar obra: ${error instanceof Error ? error.message : 'Erro desconhecido'}`);
  } finally {
    loading.value = false;
  }
};

// Validar formulário
const validateForm = () => {
  let isValid = true;
  
  // Resetar erros
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = '';
  });
  
  // Validar campos obrigatórios
  if (!form.nome.trim()) {
    errors.nome = 'O nome da obra é obrigatório';
    isValid = false;
  }
  
  if (!form.endereco.trim()) {
    errors.endereco = 'O endereço é obrigatório';
    isValid = false;
  }
  
  if (!form.data_inicio) {
    errors.data_inicio = 'A data de início é obrigatória';
    isValid = false;
  }
  
  if (form.prazo_estimado <= 0) {
    errors.prazo_estimado = 'O prazo estimado deve ser maior que zero';
    isValid = false;
  }
  
  if (form.area_m2 <= 0) {
    errors.area_m2 = 'A área deve ser maior que zero';
    isValid = false;
  }
  
  if (form.valor_estimado <= 0) {
    errors.valor_estimado = 'O valor estimado deve ser maior que zero';
    isValid = false;
  }
  
  if (form.taxa_administracao < 0 || form.taxa_administracao > 100) {
    errors.taxa_administracao = 'A taxa de administração deve estar entre 0 e 100';
    isValid = false;
  }
  
  return isValid;
};

// Salvar obra
const saveObra = async () => {
  if (!validateForm()) return;
  
  try {
    loading.value = true;
    
    if (isEditing.value) {
      await obraService.updateObra(route.params.id as string, form as Obra);
      toastService.showSuccessToast('Obra atualizada com sucesso!');
    } else {
      await obraService.createObra(form as Obra);
      toastService.showSuccessToast('Obra criada com sucesso!');
    }
    
    // Redirecionar para a lista de obras
    router.push('/obras');
  } catch (error) {
    console.error('Erro ao salvar obra:', error);
    toastService.showErrorToast(`Erro ao salvar obra: ${error instanceof Error ? error.message : 'Erro desconhecido'}`);
  } finally {
    loading.value = false;
  }
};

// Carregar obra ao montar o componente (se for edição)
onMounted(() => {
  loadObra();
});
</script>
