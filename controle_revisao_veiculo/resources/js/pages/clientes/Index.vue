<template>
  <DefaultLayout>
    <div class="container mt-5">
      <h1 class="mb-4">Lista de Clientes</h1>

      <!-- Formulário de cadastro -->
      <form @submit.prevent="submit" class="mb-4 border rounded p-4 shadow-sm">
        <div class="row mb-2">
          <div class="col">
            <input v-model="form.nome" type="text" class="form-control" placeholder="Nome" required>
          </div>
          <div class="col">
            <input v-model="form.sobrenome" type="text" class="form-control" placeholder="Sobrenome" required>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <input v-model="form.cpf" type="text" class="form-control" placeholder="CPF" maxlength="11" required>
          </div>
          <div class="col">
            <input v-model="form.telefone" type="text" class="form-control" placeholder="Telefone">
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <input v-model="form.email" type="email" class="form-control" placeholder="Email">
          </div>
          <div class="col">
            <input v-model="form.data_nascimento" type="date" class="form-control" placeholder="Data de Nascimento">
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <select v-model="form.sexo" class="form-control" required>
              <option value="">Selecione o Sexo</option>
              <option value="Masculino">Masculino</option>
              <option value="Feminino">Feminino</option>
              <option value="Outro">Outro</option>
            </select>
          </div>
        </div>
        <button type="submit" class="btn btn-primary">Cadastrar</button>
      </form>

      <!-- Tabela de clientes -->
      <table class="table table-striped table-bordered">
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
      <div v-if="!clientes.length" class="alert alert-info mt-3">
        Nenhum cliente encontrado.
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
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

function submit() {
  form.post('/clientes', {
    onSuccess: () => form.reset()
  })
}
</script>
