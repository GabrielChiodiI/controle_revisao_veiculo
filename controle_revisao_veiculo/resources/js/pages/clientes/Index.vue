<template>
  <DefaultLayout>
    <div class="container mt-5">
      <h1 class="mb-4 text-center">Lista de Clientes</h1>

      <!-- Formulário de cadastro -->
      <div class="card p-4 mb-4 shadow-sm">
        <form @submit.prevent="submit">
          <div class="row mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
              <input v-model="form.nome" type="text" class="form-control" placeholder="Nome" required>
            </div>
            <div class="col-md-6">
              <input v-model="form.sobrenome" type="text" class="form-control" placeholder="Sobrenome" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
              <input v-model="cpfFormatado" type="text" class="form-control" placeholder="CPF" maxlength="14" required>
            </div>
            <div class="col-md-6">
              <input v-model="telefoneFormatado" type="text" class="form-control" placeholder="Telefone" maxlength="15" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
              <input v-model="form.email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="col-md-6">
              <input v-model="form.data_nascimento" type="date" class="form-control" placeholder="Data de Nascimento" required>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <select v-model="form.sexo" class="form-select" required>
                <option disabled value="">Selecione o Sexo</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100">
            Cadastrar
          </button>
        </form>
      </div>

      <!-- Tabela de clientes -->
      <div class="card p-3 shadow-sm">
        <table class="table mb-0 table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Sobrenome</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cliente in clientes" :key="cliente.id_cliente">
              <td>{{ cliente.id_cliente }}</td>
              <td>{{ cliente.nome }}</td>
              <td>{{ cliente.sobrenome }}</td>
            </tr>
          </tbody>
        </table>
        <div v-if="!clientes.length" class="alert alert-info mt-3 mb-0 text-center rounded">
          Nenhum cliente encontrado.
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
import DefaultLayout from '../../layouts/DefaultLayout.vue'

defineProps({
  clientes: {
    type: Array,
    required: true
  }
})

const form = useForm({
  nome: '',
  sobrenome: '',
  cpf: '',
  telefone: '',
  email: '',
  data_nascimento: '',
  sexo: ''
})

// Sempre mantém cpf só números
watch(() => form.cpf, val => {
  if (val) form.cpf = val.replace(/\D/g, '').slice(0, 11)
})

// CPF formatado para exibir
const cpfFormatado = computed({
  get() {
    if (!form.cpf) return ''
    return form.cpf
      .replace(/\D/g, '')
      .replace(/^(\d{3})(\d)/, '$1.$2')
      .replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3')
      .replace(/^(\d{3})\.(\d{3})\.(\d{3})(\d)/, '$1.$2.$3-$4')
      .slice(0, 14)
  },
  set(v) {
    form.cpf = v.replace(/\D/g, '').slice(0, 11)
  }
})

const telefoneFormatado = computed({
  get() {
    if (!form.telefone) return ''
    return form.telefone
      .replace(/\D/g, '')
      .replace(/^(\d{2})(\d)/, '($1) $2')
      .replace(/^(\(\d{2}\)\s)(\d{5})(\d)/, '$1$2-$3')
      .slice(0, 15)
  },
  set(v) {
    form.telefone = v.replace(/\D/g, '').slice(0, 11)
  }
})

// Sempre mantém telefone só números
watch(() => form.telefone, val => {
  if (val) form.telefone = val.replace(/\D/g, '').slice(0, 11)
})

// Só cadastro
function submit() {
  form.post('/clientes', {
    onSuccess: () => form.reset()
  });
}
</script>
