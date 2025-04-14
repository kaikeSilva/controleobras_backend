<template>
  <div class="mb-6">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <textarea
        :id="id"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :rows="rows"
        :class="[
          'w-full rounded-lg border shadow-sm transition-all duration-200 bg-white px-4 py-3',
          error ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-200',
          'focus:outline-none focus:ring-4',
          { 'bg-gray-100 cursor-not-allowed': disabled }
        ]"
        v-bind="$attrs"
      ></textarea>
    </div>
    <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    <p v-else-if="helpText" class="mt-2 text-sm text-gray-500">{{ helpText }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  modelValue: string;
  label?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  error?: string;
  helpText?: string;
  id?: string;
  rows?: number;
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: '',
  required: false,
  disabled: false,
  error: '',
  helpText: '',
  id: () => `textarea-${Math.random().toString(36).substring(2, 9)}`,
  rows: 4
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
textarea {
  font-family: var(--font-family-primary, 'Inter', sans-serif);
  resize: vertical;
  min-height: 100px;
}

textarea:-webkit-autofill,
textarea:-webkit-autofill:hover,
textarea:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0px 1000px white inset;
  transition: background-color 5000s ease-in-out 0s;
  -webkit-text-fill-color: inherit;
}
</style>
