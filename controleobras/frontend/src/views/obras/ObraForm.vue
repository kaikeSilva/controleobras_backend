<template>
  <AppLayout :title="isEditing ? 'Editar Obra' : 'Nova Obra'">
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
      <form @submit.prevent="saveObra" class="px-4 py-5 sm:p-6">
        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
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
              v-model.number="form.prazo_estimado"
              type="number"
              label="Prazo Estimado (dias)"
              required
              min="1"
              :error="errors.prazo_estimado"
            />
          </div>

          <!-- Área em m² -->
          <div class="sm:col-span-2">
            <AppInput
              v-model.number="form.area_m2"
              type="number"
              label="Área (m²)"
              required
              min="0"
              step="0.01"
              :error="errors.area_m2"
            />
          </div>

          <!-- Valor estimado -->
          <div class="sm:col-span-3">
            <AppInput
              v-model.number="form.valor_estimado"
              type="number"
              label="Valor Estimado (R$)"
              required
              min="0"
              step="0.01"
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
        </div>

        <div class="mt-8 flex justify-end space-x-3">
          <AppButton 
            type="button" 
            variant="secondary" 
            @click="$router.push('/obras')"
          >
            Cancelar
          </AppButton>
          <AppButton 
            type="submit" 
            variant="primary"
            :disabled="loading"
          >
            {{ isEditing ? 'Atualizar' : 'Salvar' }}
          </AppButton>
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
import AppButton from '../../components/AppButton.vue';
import obraService, { Obra } from '../../services/obraService';

const router = useRouter();
const route = useRoute();
const loading = ref(false);

// Verificar se estamos editando uma obra existente
const isEditing = computed(() => !!route.params.id);

// Formulário
const form = reactive<Obra>({
  nome: '',
  descricao: '',
  endereco: '',
  data_inicio: '',
  prazo_estimado: 0,
  area_m2: 0,
  valor_estimado: 0,
  taxa_administracao: 0,
});

// Erros de validação
const errors = reactive<Record<string, string>>({});

// Carregar dados da obra para edição
const loadObra = async () => {
  if (!isEditing.value) return;
  
  loading.value = true;
  try {
    const id = Number(route.params.id);
    const obra = await obraService.getObra(id);
    
    // Preencher formulário com dados da obra
    Object.assign(form, {
      ...obra,
      // Garantir que a data esteja no formato correto para o input type="date"
      data_inicio: obra.data_inicio.split('T')[0],
    });
  } catch (error) {
    console.error('Erro ao carregar obra:', error);
    router.push('/obras');
  } finally {
    loading.value = false;
  }
};

// Validar formulário
const validateForm = () => {
  const newErrors: Record<string, string> = {};
  
  if (!form.nome) {
    newErrors.nome = 'O nome da obra é obrigatório';
  }
  
  if (!form.endereco) {
    newErrors.endereco = 'O endereço da obra é obrigatório';
  }
  
  if (!form.data_inicio) {
    newErrors.data_inicio = 'A data de início é obrigatória';
  }
  
  if (!form.prazo_estimado || form.prazo_estimado <= 0) {
    newErrors.prazo_estimado = 'O prazo estimado deve ser maior que zero';
  }
  
  if (form.area_m2 < 0) {
    newErrors.area_m2 = 'A área deve ser maior ou igual a zero';
  }
  
  if (form.valor_estimado < 0) {
    newErrors.valor_estimado = 'O valor estimado deve ser maior ou igual a zero';
  }
  
  if (form.taxa_administracao < 0 || form.taxa_administracao > 100) {
    newErrors.taxa_administracao = 'A taxa de administração deve estar entre 0 e 100';
  }
  
  // Atualizar objeto de erros
  Object.assign(errors, newErrors);
  
  return Object.keys(newErrors).length === 0;
};

// Salvar obra
const saveObra = async () => {
  if (!validateForm()) return;
  
  loading.value = true;
  try {
    if (isEditing.value) {
      const id = Number(route.params.id);
      await obraService.updateObra(id, form);
    } else {
      await obraService.createObra(form);
    }
    
    router.push('/obras');
  } catch (error: any) {
    console.error('Erro ao salvar obra:', error);
    
    // Tratar erros de validação do backend
    if (error.response?.data?.errors) {
      const backendErrors = error.response.data.errors;
      Object.keys(backendErrors).forEach(key => {
        errors[key] = backendErrors[key][0];
      });
    }
  } finally {
    loading.value = false;
  }
};

// Carregar obra ao montar o componente (se for edição)
onMounted(() => {
  loadObra();
});
</script>
