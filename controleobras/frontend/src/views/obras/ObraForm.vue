<template>
  <AppLayout :title="isEditing ? 'Editar Obra' : 'Nova Obra'">
    <div v-if="loading" class="flex justify-center py-12">
      <svg class="animate-spin h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>

    <form v-else @submit.prevent="saveObra" class="bg-white shadow-md rounded-lg overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Informações da Obra</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Nome -->
          <div class="col-span-2">
            <AppInput
              v-model="form.nome"
              label="Nome da Obra"
              required
              :error="errors.nome"
            />
          </div>
          
          <!-- Endereço -->
          <div class="col-span-2">
            <AppInput
              v-model="form.endereco"
              label="Endereço"
              required
              :error="errors.endereco"
            />
          </div>
          
          <!-- Data de Início -->
          <div>
            <AppInput
              v-model="form.data_inicio"
              label="Data de Início"
              type="date"
              required
              :error="errors.data_inicio"
            />
          </div>
          
          <!-- Prazo Estimado -->
          <div>
            <AppInput
              v-model="form.prazo_estimado"
              label="Prazo Estimado (meses)"
              type="number"
              min="1"
              required
              :error="errors.prazo_estimado"
            />
          </div>
          
          <!-- Área -->
          <div>
            <AppInput
              v-model="form.area_m2"
              label="Área (m²)"
              type="number"
              min="0"
              step="0.01"
              required
              :error="errors.area_m2"
            />
          </div>
          
          <!-- Valor Estimado -->
          <div>
            <AppInput
              v-model="form.valor_estimado"
              label="Valor Estimado (R$)"
              type="number"
              min="0"
              step="0.01"
              required
              :error="errors.valor_estimado"
            />
          </div>
          
          <!-- Taxa de Administração -->
          <div>
            <AppInput
              v-model="form.taxa_administracao"
              label="Taxa de Administração (%)"
              type="number"
              min="0"
              max="100"
              step="0.01"
              required
              :error="errors.taxa_administracao"
            />
          </div>
        </div>
      </div>
      
      <!-- Descrição -->
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Descrição</h2>
        <AppTextarea
          v-model="form.descricao"
          rows="4"
          placeholder="Descreva detalhes sobre a obra..."
          :error="errors.descricao"
        />
      </div>
      
      <!-- Botões de ação -->
      <div class="p-6 flex justify-end space-x-3">
        <router-link to="/obras">
          <AppButton type="outline">Cancelar</AppButton>
        </router-link>
        <AppButton type="primary" :loading="saving" submit>
          {{ isEditing ? 'Atualizar' : 'Criar' }} Obra
        </AppButton>
      </div>
    </form>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import AppLayout from '@/components/AppLayout.vue';
import AppButton from '@/components/AppButton.vue';
import AppInput from '@/components/AppInput.vue';
import AppTextarea from '@/components/AppTextarea.vue';
import obraService, { Obra } from '@/services/obraService';
import { useToast } from '@/services/toastService';

const props = defineProps<{
  id?: string;
}>();

const router = useRouter();
const toast = useToast();

// Estado
const loading = ref(true);
const saving = ref(false);
const isEditing = computed(() => !!props.id);

// Formulário
const form = reactive<Obra>({
  nome: '',
  descricao: '',
  endereco: '',
  data_inicio: new Date().toISOString().split('T')[0], // Data atual como padrão
  prazo_estimado: 12, // 12 meses como padrão
  area_m2: 0,
  valor_estimado: 0,
  taxa_administracao: 10, // 10% como padrão
});

// Erros de validação
const errors = reactive<Record<string, string>>({});

// Métodos
const fetchObra = async () => {
  if (!props.id) {
    loading.value = false;
    return;
  }

  loading.value = true;
  try {
    const obra = await obraService.getObra(Number(props.id));
    
    // Preencher formulário com dados da obra
    Object.keys(form).forEach(key => {
      // @ts-ignore
      form[key] = obra[key];
    });
    
    // Formatar data para o formato esperado pelo input type="date"
    if (obra.data_inicio) {
      form.data_inicio = new Date(obra.data_inicio).toISOString().split('T')[0];
    }
  } catch (error) {
    toast.error('Erro ao carregar dados da obra');
    console.error('Erro ao carregar dados da obra:', error);
    router.push('/obras');
  } finally {
    loading.value = false;
  }
};

const validateForm = () => {
  const newErrors: Record<string, string> = {};
  
  if (!form.nome?.trim()) {
    newErrors.nome = 'O nome da obra é obrigatório';
  }
  
  if (!form.endereco?.trim()) {
    newErrors.endereco = 'O endereço é obrigatório';
  }
  
  if (!form.data_inicio) {
    newErrors.data_inicio = 'A data de início é obrigatória';
  }
  
  if (!form.prazo_estimado || form.prazo_estimado <= 0) {
    newErrors.prazo_estimado = 'O prazo estimado deve ser maior que zero';
  }
  
  if (form.area_m2 <= 0) {
    newErrors.area_m2 = 'A área deve ser maior que zero';
  }
  
  if (form.valor_estimado <= 0) {
    newErrors.valor_estimado = 'O valor estimado deve ser maior que zero';
  }
  
  if (form.taxa_administracao < 0 || form.taxa_administracao > 100) {
    newErrors.taxa_administracao = 'A taxa de administração deve estar entre 0 e 100%';
  }
  
  // Atualizar objeto de erros
  Object.keys(errors).forEach(key => delete errors[key]);
  Object.assign(errors, newErrors);
  
  return Object.keys(newErrors).length === 0;
};

const saveObra = async () => {
  if (!validateForm()) {
    toast.error('Por favor, corrija os erros no formulário');
    return;
  }
  
  saving.value = true;
  try {
    if (isEditing.value) {
      await obraService.updateObra(Number(props.id), form);
      toast.success('Obra atualizada com sucesso');
    } else {
      await obraService.createObra(form);
      toast.success('Obra criada com sucesso');
    }
    router.push('/obras');
  } catch (error: any) {
    // Verificar se o erro contém erros de validação do Laravel
    if (error.response?.data?.errors) {
      const validationErrors = error.response.data.errors;
      Object.keys(validationErrors).forEach(key => {
        errors[key] = validationErrors[key][0];
      });
      toast.error('Por favor, corrija os erros no formulário');
    } else {
      toast.error(isEditing.value ? 'Erro ao atualizar obra' : 'Erro ao criar obra');
    }
    console.error('Erro ao salvar obra:', error);
  } finally {
    saving.value = false;
  }
};

// Inicialização
onMounted(() => {
  fetchObra();
});
</script>
