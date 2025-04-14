<template>
  <div class="mb-6">
    <label v-if="label" :for="id" class="block text-sm font-medium text-gray-700 mb-2">
      {{ label }} <span v-if="required" class="text-red-500">*</span>
    </label>
    <div class="relative">
      <span v-if="icon" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">
        <slot name="icon"></slot>
      </span>
      <input
        :id="id"
        :type="type"
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="[
          'w-full rounded-lg border shadow-sm transition-all duration-200 h-11 bg-white',
          icon ? 'pl-10' : 'pl-4',
          error ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-gray-300 focus:border-blue-500 focus:ring-blue-200',
          'focus:outline-none focus:ring-4',
          { 'bg-gray-100 cursor-not-allowed': disabled }
        ]"
        v-bind="$attrs"
      />
    </div>
    <p v-if="error" class="mt-2 text-sm text-red-600">{{ error }}</p>
    <p v-else-if="helpText" class="mt-2 text-sm text-gray-500">{{ helpText }}</p>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  modelValue: string | number;
  label?: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  error?: string;
  helpText?: string;
  id?: string;
  icon?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  placeholder: '',
  required: false,
  disabled: false,
  error: '',
  helpText: '',
  id: () => `input-${Math.random().toString(36).substring(2, 9)}`,
  icon: false
});

defineEmits(['update:modelValue']);
</script>

<style scoped>
input {
  font-family: var(--font-family-primary, 'Inter', sans-serif);
}

input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus {
  -webkit-box-shadow: 0 0 0px 1000px white inset;
  transition: background-color 5000s ease-in-out 0s;
  -webkit-text-fill-color: inherit;
}
</style>
