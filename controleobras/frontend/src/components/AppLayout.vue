<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-md">
      <div class="max-w-7xl mx-auto px-6 lg:px-8">
        <div class="flex justify-between h-18">
          <div class="flex">
            <div class="flex-shrink-0 flex items-center">
              <h1 class="text-xl font-semibold text-gray-800">Controle de Obras</h1>
            </div>
            <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
              <router-link 
                to="/" 
                class="border-transparent text-gray-600 hover:border-blue-300 hover:text-blue-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"
                active-class="border-blue-500 text-gray-900"
              >
                Home
              </router-link>
              <router-link 
                to="/obras" 
                class="border-transparent text-gray-600 hover:border-blue-300 hover:text-blue-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition-colors duration-200"
                active-class="border-blue-500 text-gray-900"
              >
                Obras
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <div class="py-10">
      <header v-if="title">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
          <h1 class="text-3xl font-semibold leading-tight text-gray-900">{{ title }}</h1>
        </div>
      </header>
      <main>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="px-4 py-8 sm:px-0">
            <slot></slot>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  title?: string;
}>();
</script>

<style scoped>
.h-18 {
  height: 72px; /* Altura da barra de navegação conforme diretrizes */
}
</style>
