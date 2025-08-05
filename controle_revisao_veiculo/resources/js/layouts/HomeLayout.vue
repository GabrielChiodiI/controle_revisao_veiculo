<template>
  <div>
    <header class="d-flex align-items-center p-3 mb-4" style="background:var(--primary);color:#fff;">
      <h1 class="mx-auto my-0 text-center flex-grow-1" style="font-size:2rem;">Sistema de Revisão de Oficina</h1>
      <div class="d-flex align-items-center" style="gap:0.5rem;">
        <button @click="toggleDarkMode" class="btn btn-outline-light border-0" :title="darkMode ? 'Modo Claro' : 'Modo Escuro'">
          <i :class="darkMode ? 'bi-sun-fill' : 'bi-moon-fill'" style="font-size:1.5rem;"></i>
        </button>
        <button @click="logout" :disabled="processing" class="btn btn-secondary ms-2 d-flex align-items-center">
          <i class="bi bi-box-arrow-right me-2" style="font-size:1.2rem;"></i>
          Sair
        </button>
      </div>
    </header>
    <main class="container">
      <slot />
      <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const processing = ref(false)
const error = ref('')
const darkMode = ref(document.body.classList.contains('dark-mode'))

const logout = () => {
  processing.value = true
  error.value = ''
  router.post('/logout', {}, {
    onError: (e) => {
      error.value = e.message || 'Erro ao fazer logout'
      processing.value = false
    },
    onFinish: () => {
      processing.value = false
    },
  })
}

const toggleDarkMode = () => {
  document.body.classList.toggle('dark-mode')
  darkMode.value = document.body.classList.contains('dark-mode')
}
</script>
