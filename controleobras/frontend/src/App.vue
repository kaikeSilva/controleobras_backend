<template>
  <div>
    <router-view />
    <AppToast 
      :show="toastService.toastState.show"
      :type="toastService.toastState.type"
      :title="toastService.toastState.title"
      :message="toastService.toastState.message"
      :duration="toastService.toastState.duration"
      @close="toastService.hideToast"
    />
  </div>
</template>

<script setup lang="ts">
import AppToast from './components/AppToast.vue';
import toastService from './services/toastService';
</script>
