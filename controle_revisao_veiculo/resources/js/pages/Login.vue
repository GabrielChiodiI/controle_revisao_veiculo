<template>
  <LoginLayout>
    <div class="card login-box">
      <h1 class="text-center mb-4" style="color: var(--text);">Login</h1>
      <form @submit.prevent="submit">
        <div class="mb-3">
          <label class="form-label">E-mail</label>
          <input v-model="form.email" type="email" class="form-control" required autofocus />
        </div>
        <div class="mb-4">
          <label class="form-label">Senha</label>
          <input v-model="form.password" type="password" class="form-control" required />
        </div>
        <button type="submit" class="btn btn-primary w-100" :disabled="processing">
          Entrar
        </button>
        <div v-if="errors" class="alert alert-danger mt-3 text-center">
          {{ errors }}
        </div>
      </form>
    </div>
  </LoginLayout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import LoginLayout from '../layouts/LoginLayout.vue'

const form = reactive({ email: '', password: '' })
const processing = ref(false)
const errors = ref('')

function submit() {
  processing.value = true
  errors.value = ''
  router.post('/login', form, {
    onError: (e) => {
      errors.value = e.email || e.password || 'Credenciais inválidas'
      processing.value = false
    },
    onFinish: () => { processing.value = false }
  })
}
</script>

<style scoped>
.login-box {
  min-width: 320px;
  max-width: 360px;
  margin: 40px auto;
  padding: 2rem 1.5rem;
  background: var(--surface);
  border-radius: 1rem;
  box-shadow: 0 6px 32px rgba(32, 98, 171, 0.08);
}
</style>
