<template>
  <DefaultLayout>
    <div class="container mt-5">
      <form @submit.prevent="submit" class="mb-5 border rounded p-4 shadow-sm">
        <h2 class="mb-4">Cadastrar Revisão</h2>

        <!-- Cliente -->
        <div class="mb-3 position-relative" ref="autoRef">
          <label class="form-label">Cliente</label>
          <input
            v-model="searchCliente"
            @focus="openSugestoes()"
            @keydown.down.prevent="mover(1)"
            @keydown.up.prevent="mover(-1)"
            @keydown.enter.prevent="enterSeleciona()"
            @keydown.esc.prevent="showSugestoes = false"
            type="text"
            class="form-control"
            placeholder="Digite nome/sobrenome ou ID"
            autocomplete="off"
          />
          <ul
            v-if="showSugestoes && filteredClientes.length"
            class="list-group position-absolute w-100"
            style="z-index:1000;max-height:240px;overflow:auto;"
          >
            <li
              v-for="(c, idx) in filteredClientes"
              :key="c.id_cliente"
              class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
              :class="{ 'active': idx === highlightedIndex }"
              @mousedown.prevent="selecionar(c)"
            >
              <span>{{ c.nome }} {{ c.sobrenome }}</span>
              <small class="text-muted">#{{ c.id_cliente }}</small>
            </li>
          </ul>
          <div v-if="form.id_cliente" class="mt-2">
            <span class="badge bg-primary">
              Selecionado: {{ selecionadoNome }} (ID {{ form.id_cliente }})
            </span>
            <button type="button" class="btn btn-sm btn-link" @click="limparSelecao">trocar</button>
          </div>
        </div>

        <!-- Veículo -->
        <div class="row mb-2">
          <!-- Placa -->
          <div class="col position-relative" ref="placaRef">
            <input
              v-model="placaSearch"
              @focus="openPlacas()"
              @input="onPlacaInput"
              @keydown.down.prevent="moverPlaca(1)"
              @keydown.up.prevent="moverPlaca(-1)"
              @keydown.enter.prevent="enterSelecionaPlaca()"
              @keydown.esc.prevent="showPlacas = false"
              type="text"
              class="form-control"
              required
              placeholder="Placa"
              autocomplete="off"
              :disabled="!form.id_cliente"
            />
            <ul
              v-if="showPlacas && filteredPlacas.length"
              class="list-group position-absolute w-100"
              style="z-index:1000;max-height:240px;overflow:auto;"
            >
              <li
                v-for="(v, idx) in filteredPlacas"
                :key="v.placa"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                :class="{ 'active': idx === highlightedPlacaIndex }"
                @mousedown.prevent="selecionarPlaca(v)"
              >
                <span>{{ v.placa }}</span>
                <small class="text-muted">{{ v.marca }} {{ v.modelo }}</small>
              </li>
            </ul>
            <small v-if="!form.id_cliente" class="text-muted">selecione o cliente para listar placas</small>
          </div>

          <div class="col">
            <input v-model="form.marca" type="text" class="form-control" required placeholder="Marca">
          </div>
          <div class="col">
            <input v-model="form.modelo" type="text" class="form-control" required placeholder="Modelo">
          </div>
          <div class="col">
            <input v-model.number="form.ano" type="number" class="form-control" required placeholder="Ano">
          </div>
        </div>

        <div class="row mb-2">
          <div class="col">
            <input v-model="form.data_inicio" type="datetime-local" class="form-control" required placeholder="Data e Hora Início">
          </div>
          <div class="col">
            <input v-model.number="form.quilometragem" type="number" class="form-control" required placeholder="Quilometragem">
          </div>
        </div>

        <!-- Serviços -->
        <div v-for="(servico, i) in form.servicos" :key="i" class="border p-3 my-2 rounded">
          <h5>Serviço {{ i + 1 }}</h5>

          <!-- Autocomplete Serviço -->
          <div class="row mb-2 position-relative">
            <div class="col" style="position:relative;">
              <input
                v-model="servico._search"
                @input="buscarServicos(i)"
                @focus="abrirSugestaoServ(i)"
                @keydown.down.prevent="moverSugServ(i,1)"
                @keydown.up.prevent="moverSugServ(i,-1)"
                @keydown.enter.prevent="enterServ(i)"
                @keydown.esc.prevent="servico._showSug = false"
                type="text" class="form-control" placeholder="Descrição do Serviço" autocomplete="off">
              <ul v-if="servico._showSug && servico._sugs.length"
                  class="list-group position-absolute w-100" style="z-index:1000;max-height:240px;overflow:auto;">
                <li v-for="(s, idx) in servico._sugs" :key="s.id_servico"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                    :class="{ 'active': idx === servico._hi }"
                    @mousedown.prevent="selecionarServico(i,s)">
                  <span>{{ s.descricao }}</span>
                  <small class="text-muted">R$ {{ s.valor_mao_de_obra }}</small>
                </li>
              </ul>
            </div>

            <div class="col">
              <input v-model.number="servico.valor_mao_de_obra" type="number" step="0.01"
                     class="form-control" required placeholder="Valor Mão de Obra">
            </div>
            <div class="col-auto">
              <button class="btn btn-danger" type="button" @click="removerServico(i)">Remover</button>
            </div>
          </div>

          <!-- Peças do Serviço -->
          <div v-for="(peca, j) in servico.pecas" :key="j" class="row mb-2 position-relative align-items-end">
            <div class="col" style="position:relative;">
              <input
                v-model="peca._search"
                @input="buscarPecas(i,j)"
                @focus="abrirSugPeca(i,j)"
                @keydown.down.prevent="moverSugPeca(i,j,1)"
                @keydown.up.prevent="moverSugPeca(i,j,-1)"
                @keydown.enter.prevent="enterPeca(i,j)"
                @keydown.esc.prevent="peca._showSug = false"
                type="text" class="form-control" placeholder="Descrição da Peça" autocomplete="off">
              <ul v-if="peca._showSug && peca._sugs.length"
                  class="list-group position-absolute w-100" style="z-index:1000;max-height:240px;overflow:auto;">
                <li v-for="(p, idx) in peca._sugs" :key="p.codigo"
                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                    :class="{ 'active': idx === peca._hi }"
                    @mousedown.prevent="selecionarPeca(i,j,p)">
                  <span>{{ p.descricao }}</span>
                  <small class="text-muted">R$ {{ p.preco }}</small>
                </li>
              </ul>
            </div>

            <div class="col">
              <input v-model.number="peca.quantidade" type="number" min="1" class="form-control" placeholder="Quantidade">
            </div>
            <div class="col">
              <input v-model.number="peca.preco" type="number" step="0.01" class="form-control" placeholder="Preço">
            </div>
            <div class="col-auto">
              <button class="btn btn-outline-danger" type="button" @click="removerPeca(i, j)">-</button>
            </div>
          </div>

          <button class="btn btn-outline-primary mb-2" type="button" @click="adicionarPeca(i)">Adicionar Peça</button>
        </div>

        <button class="btn btn-outline-success my-2" type="button" @click="adicionarServico">Adicionar Serviço</button>

        <div>
          <button type="submit" class="btn btn-primary mt-3" :disabled="!form.id_cliente">Cadastrar Revisão</button>
          <small v-if="!form.id_cliente" class="text-danger ms-2">selecione um cliente</small>
        </div>
      </form>

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
            <td><button class="btn btn-success btn-sm" @click="finalizarRevisao(rev.id_revisao)">Finalizar</button></td>
          </tr>
        </tbody>
      </table>
      <div v-if="!revisoesAndamento.length" class="alert alert-info">Nenhuma revisão em andamento.</div>

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
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import DefaultLayout from '../../layouts/DefaultLayout.vue'

const props = defineProps({
  revisoes: { type: Array, required: true },
  clientes: { type: Array, required: true }
})

/* Revisões */
const revisoes = ref([])
onMounted(async () => {
  const resp = await fetch('/revisoes/todas-revisoes')
  revisoes.value = await resp.json()
})
const revisoesAndamento = computed(() => revisoes.value.filter(r => !r.data_fim))
const revisoesFinalizadas = computed(() => revisoes.value.filter(r => !!r.data_fim))

function getDateTimeLocalNow() {
  const now = new Date()
  const offset = now.getTimezoneOffset()
  const local = new Date(now.getTime() - offset * 60000)
  return local.toISOString().slice(0, 16)
}

/* Form */
const form = useForm({
  id_cliente: '',
  placa: '',
  marca: '',
  modelo: '',
  ano: '',
  data_inicio: getDateTimeLocalNow(),
  quilometragem: '',
  servicos: [
    { id_servico: null, descricao: '', _search: '', _sugs: [], _showSug: false, _hi: 0, valor_mao_de_obra: '', pecas: [] }
  ]
})

function adicionarServico() {
  form.servicos.push({ id_servico: null, descricao: '', _search: '', _sugs: [], _showSug: false, _hi: 0, valor_mao_de_obra: '', pecas: [] })
}
function removerServico(i) { form.servicos.splice(i, 1) }
function adicionarPeca(i) { form.servicos[i].pecas.push({ codigo: null, descricao: '', _search: '', _sugs: [], _showSug: false, _hi: 0, quantidade: 1, preco: '' }) }
function removerPeca(i, j) { form.servicos[i].pecas.splice(j, 1) }

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
  Inertia.post(`/revisoes/${id}/finalizar`, {}, { onSuccess: () => window.location.reload(true) })
}

/* Autocomplete Cliente */
const searchCliente = ref('')
const showSugestoes = ref(false)
const highlightedIndex = ref(-1)
const autoRef = ref(null)

const filteredClientes = computed(() => {
  const q = (searchCliente.value || '').toLowerCase().trim()
  if (!q) return props.clientes.slice(0, 10)
  return props.clientes
    .filter(c => {
      const nome = (c.nome || '').toLowerCase()
      const sobrenome = (c.sobrenome || '').toLowerCase()
      const id = String(c.id_cliente)
      return nome.includes(q) || sobrenome.includes(q) || `${nome} ${sobrenome}`.includes(q) || id.startsWith(q)
    })
    .slice(0, 20)
})
const selecionadoNome = computed(() => {
  if (!form.id_cliente) return ''
  const c = props.clientes.find(x => x.id_cliente === form.id_cliente)
  return c ? `${c.nome} ${c.sobrenome}` : ''
})
function openSugestoes() { showSugestoes.value = true; highlightedIndex.value = filteredClientes.value.length ? 0 : -1 }
function selecionar(c) { form.id_cliente = c.id_cliente; searchCliente.value = `${c.nome} ${c.sobrenome}`; showSugestoes.value = false; highlightedIndex.value = -1 }
function limparSelecao() { form.id_cliente = ''; searchCliente.value = ''; openSugestoes() }
function mover(d) {
  if (!showSugestoes.value || !filteredClientes.value.length) return
  const max = filteredClientes.value.length - 1
  highlightedIndex.value = Math.min(max, Math.max(0, highlightedIndex.value + d))
}
function enterSeleciona() {
  if (!showSugestoes.value) return
  const i = highlightedIndex.value, L = filteredClientes.value
  if (i >= 0 && i < L.length) selecionar(L[i]); else if (L.length === 1) selecionar(L[0])
}
function onDocClick(e) { const el = autoRef.value; if (el && !el.contains(e.target)) showSugestoes.value = false }
onMounted(() => document.addEventListener('click', onDocClick))
onBeforeUnmount(() => document.removeEventListener('click', onDocClick))

/* Autocomplete Placa por Cliente */
const placaRef = ref(null)
const placaSearch = ref('')
const showPlacas = ref(false)
const highlightedPlacaIndex = ref(-1)
const veiculosDoCliente = ref([])

watch(() => form.id_cliente, async (novo) => {
  form.placa = ''; placaSearch.value = ''
  form.marca = ''; form.modelo = ''; form.ano = ''
  veiculosDoCliente.value = []
  showPlacas.value = false; highlightedPlacaIndex.value = -1
  if (!novo) return
  const resp = await fetch(`/clientes/${novo}/veiculos`)
  veiculosDoCliente.value = await resp.json()
})

const filteredPlacas = computed(() => {
  const q = (placaSearch.value || '').toLowerCase().trim()
  if (!q) return veiculosDoCliente.value.slice(0, 10)
  return veiculosDoCliente.value
    .filter(v => v.placa.toLowerCase().includes(q) || `${v.marca} ${v.modelo}`.toLowerCase().includes(q))
    .slice(0, 20)
})
function openPlacas() { if (!form.id_cliente) return; showPlacas.value = true; highlightedPlacaIndex.value = filteredPlacas.value.length ? 0 : -1 }
function onPlacaInput() { form.placa = placaSearch.value.toUpperCase(); showPlacas.value = true }
function selecionarPlaca(v) {
  placaSearch.value = v.placa
  form.placa = v.placa
  form.marca = v.marca || ''
  form.modelo = v.modelo || ''
  form.ano = v.ano || ''
  showPlacas.value = false
  highlightedPlacaIndex.value = -1
}
function moverPlaca(d) {
  if (!showPlacas.value || !filteredPlacas.value.length) return
  const max = filteredPlacas.value.length - 1
  highlightedPlacaIndex.value = Math.min(max, Math.max(0, highlightedPlacaIndex.value + d))
}
function enterSelecionaPlaca() {
  if (!showPlacas.value) return
  const i = highlightedPlacaIndex.value, L = filteredPlacas.value
  if (i >= 0 && i < L.length) selecionarPlaca(L[i])
  else if (L.length === 1) selecionarPlaca(L[0])
  else showPlacas.value = false
}
function onDocClickPlaca(e) { const el = placaRef.value; if (el && !el.contains(e.target)) showPlacas.value = false }
onMounted(() => document.addEventListener('click', onDocClickPlaca))
onBeforeUnmount(() => document.removeEventListener('click', onDocClickPlaca))

/* -------- Serviços (autocomplete) -------- */
async function buscarServicos(i) {
  const srv = form.servicos[i]
  srv._showSug = true
  srv._hi = 0
  const q = encodeURIComponent(srv._search || '')
  const r = await fetch(`/revisoes/servicos?q=${q}`)
  srv._sugs = await r.json()
}
async function abrirSugestaoServ(i){
  const s = form.servicos[i]
  s._showSug = true
  s._hi = 0
  await buscarServicos(i) // carrega lista mesmo sem digitar
}
function moverSugServ(i, d){
  const s=form.servicos[i]; if(!s._showSug || !s._sugs.length) return
  const max=s._sugs.length-1; s._hi=Math.min(max, Math.max(0, s._hi + d))
}
function enterServ(i){
  const s=form.servicos[i]; if(!s._showSug || !s._sugs.length) return
  selecionarServico(i, s._sugs[s._hi])
}
function selecionarServico(i, s) {
  const srv = form.servicos[i]
  srv.id_servico = s.id_servico
  srv.descricao = s.descricao
  srv._search = s.descricao
  srv.valor_mao_de_obra = Number(s.valor_mao_de_obra ?? 0)
  srv._showSug = false
  // NÃO autopreenche peças. Apenas sugestões aparecerão ao focar no campo de peça.
}

/* -------- Peças (autocomplete com viés do serviço) -------- */
async function buscarPecas(i, j){
  const srv = form.servicos[i]; const pc = srv.pecas[j]
  pc._showSug = true; pc._hi = 0
  const q = encodeURIComponent(pc._search || '')
  const sid = srv.id_servico ? `&servico_id=${srv.id_servico}` : ''
  const r = await fetch(`/revisoes/pecas?q=${q}${sid}`)
  pc._sugs = await r.json()
}
async function abrirSugPeca(i,j){
  const p = form.servicos[i].pecas[j]
  p._showSug = true
  p._hi = 0
  await buscarPecas(i,j) // carrega lista mesmo sem digitar
}
function moverSugPeca(i,j,d){
  const p=form.servicos[i].pecas[j]; if(!p._showSug || !p._sugs.length) return
  const max=p._sugs.length-1; p._hi=Math.min(max, Math.max(0, p._hi + d))
}
function enterPeca(i,j){
  const p=form.servicos[i].pecas[j]; if(!p._showSug || !p._sugs.length) return
  selecionarPeca(i,j,p._sugs[p._hi])
}
function selecionarPeca(i,j,p){
  const pc = form.servicos[i].pecas[j]
  pc.codigo = p.codigo
  pc.descricao = p.descricao
  pc.preco = Number(p.preco ?? 0)
  if (!pc.quantidade || pc.quantidade < 1) pc.quantidade = 1
  pc._search = p.descricao
  pc._showSug = false
}
</script>
