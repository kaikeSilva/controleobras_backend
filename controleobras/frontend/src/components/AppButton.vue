<template>
  <button 
    :class="[
      'rounded-lg font-medium transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 relative overflow-hidden',
      sizeClasses,
      variantClasses,
      { 'opacity-60 cursor-not-allowed': disabled },
      { 'inline-flex items-center justify-center': icon || iconRight },
    ]"
    :disabled="disabled"
    v-bind="$attrs"
  >
    <span class="relative z-10 flex items-center">
      <span v-if="icon" class="mr-2">
        <slot name="icon"></slot>
      </span>
      <slot></slot>
      <span v-if="iconRight" class="ml-2">
        <slot name="icon-right"></slot>
      </span>
    </span>
  </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';

interface Props {
  variant?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'info' | 'ghost' | 'outline';
  size?: 'xs' | 'sm' | 'md' | 'lg';
  disabled?: boolean;
  icon?: boolean;
  iconRight?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'primary',
  size: 'md',
  disabled: false,
  icon: false,
  iconRight: false
});

const variantClasses = computed(() => {
  const variants = {
    primary: 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500 hover:shadow-md hover:-translate-y-0.5',
    secondary: 'bg-gray-600 hover:bg-gray-700 text-white focus:ring-gray-500 hover:shadow-md hover:-translate-y-0.5',
    success: 'bg-green-600 hover:bg-green-700 text-white focus:ring-green-500 hover:shadow-md hover:-translate-y-0.5',
    danger: 'bg-red-600 hover:bg-red-700 text-white focus:ring-red-500 hover:shadow-md hover:-translate-y-0.5',
    warning: 'bg-yellow-500 hover:bg-yellow-600 text-white focus:ring-yellow-400 hover:shadow-md hover:-translate-y-0.5',
    info: 'bg-indigo-600 hover:bg-indigo-700 text-white focus:ring-indigo-500 hover:shadow-md hover:-translate-y-0.5',
    ghost: 'bg-transparent hover:bg-gray-100 text-gray-700 focus:ring-gray-300',
    outline: 'bg-transparent border border-blue-600 text-blue-600 hover:bg-blue-50 focus:ring-blue-300'
  };
  
  return variants[props.variant];
});

const sizeClasses = computed(() => {
  const sizes = {
    xs: 'text-xs px-2 py-1',
    sm: 'text-sm px-3 py-1.5',
    md: 'text-base px-4 py-2.5',
    lg: 'text-lg px-6 py-3'
  };
  
  return sizes[props.size];
});
</script>

<style scoped>
button {
  font-family: var(--font-family-primary, 'Inter', sans-serif);
}
</style>
