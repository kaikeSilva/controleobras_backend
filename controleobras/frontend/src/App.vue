<template>
  <div class="min-h-screen bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-xl shadow-md overflow-hidden md:max-w-2xl p-6">
      <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">{{ appTitle }}</h1>
      <p class="text-lg text-gray-600 text-center mb-6">{{ message }}</p>
      <div class="flex justify-center">
        <button 
          @click="checkBackend" 
          class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition duration-300 ease-in-out transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
        >
          Check Backend Connection
        </button>
      </div>
      <div v-if="backendResponse" class="mt-8 bg-gray-50 p-4 rounded-lg">
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Backend Response:</h3>
        <pre class="bg-gray-100 p-3 rounded text-sm overflow-auto">{{ backendResponse }}</pre>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue'
import axios from 'axios'
import config from './config'

export default defineComponent({
  name: 'App',
  setup() {
    const appTitle = ref(config.appTitle)
    const message = ref('Welcome to the Laravel + Vue.js Docker Application with Tailwind CSS')
    const backendResponse = ref('')

    const checkBackend = async () => {
      try {
        const response = await axios.get(`${config.apiBaseUrl}/hello`)
        backendResponse.value = JSON.stringify(response.data, null, 2)
      } catch (error) {
        backendResponse.value = 'Error connecting to backend'
        console.error('Backend connection error:', error)
      }
    }

    return {
      appTitle,
      message,
      backendResponse,
      checkBackend
    }
  }
})
</script>
