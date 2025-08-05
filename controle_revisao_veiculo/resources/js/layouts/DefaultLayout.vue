<template>
  <div class="d-flex">
    <!-- Sidebar melhorada -->
    <aside class="sidebar p-0 border-end" style="width:220px; background: var(--surface); min-height:100vh;">
      <nav class="nav flex-column py-3">
        <Link
          v-for="item in menu"
          :key="item.to"
          :href="item.to"
          class="nav-link px-4 py-2 mb-1 rounded sidebar-link d-flex align-items-center"
          :class="{ active: $page.url === item.to }"
        >
          <i :class="item.icon" class="me-2"></i>{{ item.label }}
        </Link>
      </nav>
    </aside>

    <div class="flex-grow-1">
      <header class="d-flex align-items-center p-3 mb-4" style="background:var(--primary);color:#fff;">
        <!-- Botão retorno à esquerda -->
        <button @click="goBack" class="btn btn-outline-light border-0 me-3" title="Voltar">
          <i class="bi bi-arrow-left-circle" style="font-size:1.6rem;"></i>
        </button>
        <!-- Título centralizado -->
        <h1 class="mx-auto my-0 text-center flex-grow-1" style="font-size:2rem;">Sistema de Revisão de Oficina</h1>
        <!-- Ações à direita -->
        <div class="d-flex align-items-center" style="gap:0.5rem;">
          <button @click="toggleDarkMode" class="btn btn-outline-light border-0" :title="darkMode ? 'Modo Claro' : 'Modo Escuro'">
            <i :class="darkMode ? 'bi-sun-fill' : 'bi-moon-fill'" style="font-size:1.5rem;"></i>
          </button>
          <button @click="logout" :disabled="processing" class="btn btn-secondary ms-2 d-flex align-items-center">
            <i class="bi bi-box-arrow-right me-2" style="font-size:1.2rem;"></i>
            Logout
          </button>
        </div>
      </header>
      <main class="container py-4">
        <slot />
        <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const processing = ref(false)
const error = ref('')
const darkMode = ref(document.body.classList.contains('dark-mode'))

const menu = [
  { label: 'Início', to: '/', icon: 'bi bi-house-door' },
  { label: 'Clientes', to: '/clientes', icon: 'bi bi-people' },
  { label: 'Revisões', to: '/revisoes', icon: 'bi bi-tools' },
  { label: 'Relatórios', to: '/relatorios', icon: 'bi bi-bar-chart-line' }
]

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

const goBack = () => {
  window.history.length > 1 ? window.history.back() : window.location.href = '/'
}
const toggleDarkMode = () => {
  document.body.classList.toggle('dark-mode')
  darkMode.value = document.body.classList.contains('dark-mode')
}
</script>

<style scoped>
.sidebar {
  box-shadow: 2px 0 6px rgba(32,98,171,0.04);
}
.sidebar-link {
  color: var(--text);
  font-weight: 500;
  transition: background .2s;
}
.sidebar-link.active,
.sidebar-link:hover {
  background: var(--primary);
  color: #fff;
}
</style>
