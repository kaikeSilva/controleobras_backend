<template>
  <transition
    enter-active-class="transform ease-out duration-300 transition"
    enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
    leave-active-class="transition ease-in duration-200"
    leave-from-class="opacity-100"
    leave-to-class="opacity-0"
  >
    <div
      v-if="show"
      class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start sm:justify-end z-50"
    >
      <div
        class="max-w-sm w-full shadow-lg rounded-lg pointer-events-auto overflow-hidden"
        :class="[
          type === 'success' ? 'bg-green-50 border-l-4 border-green-600' : '',
          type === 'error' ? 'bg-red-50 border-l-4 border-red-600' : '',
          type === 'warning' ? 'bg-yellow-50 border-l-4 border-yellow-500' : '',
          type === 'info' ? 'bg-blue-50 border-l-4 border-blue-600' : '',
        ]"
      >
        <div class="p-4">
          <div class="flex items-start">
            <div class="flex-shrink-0">
              <!-- Ícone de sucesso -->
              <svg
                v-if="type === 'success'"
                class="h-6 w-6 text-green-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M5 13l4 4L19 7"
                />
              </svg>
              <!-- Ícone de erro -->
              <svg
                v-if="type === 'error'"
                class="h-6 w-6 text-red-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
              <!-- Ícone de aviso -->
              <svg
                v-if="type === 'warning'"
                class="h-6 w-6 text-yellow-500"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
              <!-- Ícone de informação -->
              <svg
                v-if="type === 'info'"
                class="h-6 w-6 text-blue-600"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                />
              </svg>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
              <p class="text-sm font-medium" :class="[
                type === 'success' ? 'text-green-800' : '',
                type === 'error' ? 'text-red-800' : '',
                type === 'warning' ? 'text-yellow-800' : '',
                type === 'info' ? 'text-blue-800' : '',
              ]">
                {{ title }}
              </p>
              <p class="mt-1 text-sm" :class="[
                type === 'success' ? 'text-green-700' : '',
                type === 'error' ? 'text-red-700' : '',
                type === 'warning' ? 'text-yellow-700' : '',
                type === 'info' ? 'text-blue-700' : '',
              ]">
                {{ message }}
              </p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
              <button
                @click="close"
                class="inline-flex text-gray-400 focus:outline-none focus:text-gray-500 transition ease-in-out duration-150"
              >
                <svg
                  class="h-5 w-5"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 20 20"
                  fill="currentColor"
                >
                  <path
                    fill-rule="evenodd"
                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                    clip-rule="evenodd"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';

interface Props {
  show: boolean;
  type?: 'success' | 'error' | 'warning' | 'info';
  title?: string;
  message?: string;
  duration?: number;
}

const props = withDefaults(defineProps<Props>(), {
  type: 'info',
  title: 'Notificação',
  message: '',
  duration: 3000
});

const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

// Fechar automaticamente após a duração especificada
let timer: number | null = null;

const startTimer = () => {
  if (props.duration > 0) {
    timer = window.setTimeout(() => {
      close();
    }, props.duration);
  }
};

const clearTimer = () => {
  if (timer) {
    clearTimeout(timer);
    timer = null;
  }
};

watch(() => props.show, (newValue) => {
  if (newValue) {
    clearTimer();
    startTimer();
  } else {
    clearTimer();
  }
});

onMounted(() => {
  if (props.show) {
    startTimer();
  }
});
</script>
