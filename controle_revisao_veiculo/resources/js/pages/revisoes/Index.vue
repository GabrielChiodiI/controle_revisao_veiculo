<template>
  <DefaultLayout>
    <div class="container mt-5">
      <form @submit.prevent="submit" class="mb-5 border rounded p-4 shadow-sm">
        <h2 class="mb-4">Cadastrar Revisão</h2>
        <!-- Cliente -->
        <div class="mb-3">
          <label class="form-label">ID do Cliente</label>
          <input v-model="form.id_cliente" type="number" min="1" class="form-control" required placeholder="Digite o ID do cliente">
        </div>
        <!-- Veículo -->
        <div class="row mb-2">
          <div class="col">
            <input v-model="form.placa" type="text" class="form-control" required placeholder="Placa">
          </div>
          <div class="col">
            <input v-model="form.marca" type="text" class="form-control" required placeholder="Marca">
          </div>
          <div class="col">
            <input v-model="form.modelo" type="text" class="form-control" required placeholder="Modelo">
          </div>
          <div class="col">
            <input v-model="form.ano" type="number" class="form-control" required placeholder="Ano">
          </div>
        </div>
        <div class="row mb-2">
          <div class="col">
            <input v-model="form.data_inicio" type="datetime-local" class="form-control" required placeholder="Data e Hora Início">
          </div>
          <div class="col">
            <input v-model="form.quilometragem" type="number" class="form-control" required placeholder="Quilometragem">
          </div>
        </div>
        <!-- Serviços -->
        <div v-for="(servico, i) in form.servicos" :key="i" class="border p-3 my-2 rounded">
          <h5>Serviço {{ i + 1 }}</h5>
          <div class="row mb-2">
            <div class="col">
              <input v-model="servico.descricao" type="text" class="form-control" required placeholder="Descrição do Serviço">
            </div>
            <div class="col">
              <input v-model="servico.valor_mao_de_obra" type="number" class="form-control" required placeholder="Valor Mão de Obra">
            </div>
            <div class="col-auto">
              <button class="btn btn-danger" type="button" @click="removerServico(i)">Remover</button>
            </div>
          </div>
          <!-- Peças para este serviço -->
          <div v-for="(peca, j) in servico.pecas" :key="j" class="row mb-2 align-items-end">
            <div class="col">
              <input v-model="peca.descricao" type="text" class="form-control" required placeholder="Descrição da Peça">
            </div>
            <div class="col">
              <input v-model="peca.quantidade" type="number" class="form-control" required placeholder="Quantidade">
            </div>
            <div class="col">
              <input v-model="peca.preco" type="number" class="form-control" required placeholder="Preço">
            </div>
            <div class="col-auto">
              <button class="btn btn-outline-danger" type="button" @click="removerPeca(i, j)">-</button>
            </div>
          </div>
          <button class="btn btn-outline-primary mb-2" type="button" @click="adicionarPeca(i)">Adicionar Peça</button>
        </div>
        <button class="btn btn-outline-success my-2" type="button" @click="adicionarServico">Adicionar Serviço</button>
        <div>
          <button type="submit" class="btn btn-primary mt-3">Cadastrar Revisão</button>
        </div>
      </form>

      <!-- Revisões em andamento -->
      <h1 class="mb-4">Revisões em andamento</h1>
      <table class="table table-striped table-bordered mb-5">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Data Início</th>
            <th>Data Fim</th>
            <th>Quilometragem</th>
            <th>Cliente</th>
            <th>Placa</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rev in revisoesAndamento" :key="rev.id_revisao">
            <td>{{ rev.id_revisao }}</td>
            <td>{{ rev.data_inicio }}</td>
            <td>{{ rev.data_fim || '-' }}</td>
            <td>{{ rev.quilometragem }}</td>
            <td>{{ rev.id_cliente }} - {{ rev.nome }} {{ rev.sobrenome }}</td>
            <td>{{ rev.placa }}</td>
            <td>
              <button class="btn btn-success btn-sm" @click="finalizarRevisao(rev.id_revisao)">Finalizar</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="!revisoesAndamento.length" class="alert alert-info">Nenhuma revisão em andamento.</div>

      <!-- Revisões finalizadas -->
      <h1 class="mb-4">Revisões finalizadas</h1>
      <table class="table table-striped table-bordered">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Data Início</th>
            <th>Data Fim</th>
            <th>Quilometragem</th>
            <th>Cliente</th>
            <th>Placa</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="rev in revisoesFinalizadas" :key="rev.id_revisao">
            <td>{{ rev.id_revisao }}</td>
            <td>{{ rev.data_inicio }}</td>
            <td>{{ rev.data_fim }}</td>
            <td>{{ rev.quilometragem }}</td>
            <td>{{ rev.id_cliente }} - {{ rev.nome }} {{ rev.sobrenome }}</td>
            <td>{{ rev.placa }}</td>
          </tr>
        </tbody>
      </table>
      <div v-if="!revisoesFinalizadas.length" class="alert alert-info">Nenhuma revisão finalizada.</div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import DefaultLayout from '../../layouts/DefaultLayout.vue'

const props = defineProps({
  revisoes: { type: Array, required: true },
  clientes: { type: Array, required: true }
})

const revisoes = ref([])

onMounted(async () => {
  const resp = await fetch('/revisoes/todas-revisoes')
  revisoes.value = await resp.json()
})

const revisoesAndamento = computed(() =>
  revisoes.value.filter(r => !r.data_fim)
)
const revisoesFinalizadas = computed(() =>
  revisoes.value.filter(r => !!r.data_fim)
)

const form = useForm({
  id_cliente: '',
  placa: '',
  marca: '',
  modelo: '',
  ano: '',
  data_inicio: '',
  quilometragem: '',
  servicos: [
    {
      descricao: '',
      valor_mao_de_obra: '',
      pecas: []
    }
  ]
})

function adicionarServico() {
  form.servicos.push({ descricao: '', valor_mao_de_obra: '', pecas: [] })
}
function removerServico(idx) {
  form.servicos.splice(idx, 1)
}
function adicionarPeca(idx) {
  form.servicos[idx].pecas.push({ descricao: '', quantidade: '', preco: '' })
}
function removerPeca(idx, jdx) {
  form.servicos[idx].pecas.splice(jdx, 1)
}

function submit() {
  form.post('/revisoes', {
    onSuccess: async () => {
      form.reset()
      const resp = await fetch('/revisoes/todas-revisoes')
      revisoes.value = await resp.json()
    }
  })
}

function finalizarRevisao(id) {
  Inertia.post(`/revisoes/${id}/finalizar`, {}, {
    onSuccess: async () => {
      window.location.reload(true)
    }
  })
}
</script>

