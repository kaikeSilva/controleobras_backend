<template>
  <AppLayout title="Dashboard">
    <div class="bg-white overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Bem-vindo ao Sistema de Controle de Obras
        </h3>
        <div class="mt-5">
          <p class="text-gray-600">
            Este sistema permite o gerenciamento completo de obras, incluindo cadastro, edição, visualização e exclusão.
          </p>
          <div class="mt-6">
            <router-link to="/obras">
              <AppButton>Gerenciar Obras</AppButton>
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '../components/AppLayout.vue';
import AppButton from '../components/AppButton.vue';
</script>
