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
              @blur="touched.placa = true"
              type="text"
              class="form-control"
              :class="{'is-invalid': touched.placa && !validPlaca}"
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
            <div class="d-flex justify-content-between mt-1">
              <small class="text-muted">{{ (form.placa || '').length }}/7</small>
              <small v-if="touched.placa && !validPlaca" class="text-danger">Formato: 7 chars A–Z/0–9.</small>
            </div>
            <small v-if="!form.id_cliente" class="text-muted">selecione o cliente para listar placas</small>
          </div>

          <div class="col">
            <input
              v-model="form.marca"
              @blur="touched.marca = true"
              type="text"
              class="form-control"
              :class="{'is-invalid': touched.marca && !validMarca}"
              required
              placeholder="Marca"
              maxlength="20"
            >
            <div class="d-flex justify-content-between mt-1">
              <small class="text-muted">{{ (form.marca || '').length }}/20</small>
              <small v-if="touched.marca && !validMarca" class="text-danger">Máx. 20 caracteres.</small>
            </div>
          </div>

          <div class="col">
            <input
              v-model="form.modelo"
              @blur="touched.modelo = true"
              type="text"
              class="form-control"
              :class="{'is-invalid': touched.modelo && !validModelo}"
              required
              placeholder="Modelo"
              maxlength="20"
            >
            <div class="d-flex justify-content-between mt-1">
              <small class="text-muted">{{ (form.modelo || '').length }}/20</small>
              <small v-if="touched.modelo && !validModelo" class="text-danger">Máx. 20 caracteres.</small>
            </div>
          </div>

          <!-- Ano -->
          <div class="col">
            <input
              v-model.number="form.ano"
              @blur="touched.ano = true"
              type="number"
              min="1900" max="2100"
              class="form-control"
              :class="{'is-invalid': touched.ano && !validAno}"
              required
              placeholder="Ano"
            >
            <div class="d-flex justify-content-between mt-1">
              <small class="text-muted">1900–2100</small>
              <small v-if="touched.ano && !validAno" class="text-danger">Ano inválido.</small>
            </div>
          </div>

          <div class="col">
            <!-- KM com máscara visual; salva inteiro -->
            <input
              :value="quilometragemView"
              @input="onKmInput"
              @blur="onKmBlur"
              type="text"
              class="form-control"
              placeholder="Ex.: 1.000.000,00"
              inputmode="numeric"
            >
            <div class="d-flex justify-content-between mt-1">
              <small class="text-muted">Máx. 1.000.000 KM</small>
            </div>
          </div>
        </div>

        <div class="row mb-2">
          <div class="col">
            <input v-model="form.data_inicio" type="datetime-local" class="form-control" required placeholder="Data e Hora Início">
          </div>
          <div class="col"></div>
        </div>

        <!-- Serviços -->
        <div v-for="(servico, i) in form.servicos" :key="i" class="border p-3 my-2 rounded">
          <h5>Serviço {{ i + 1 }}</h5>

          <!-- Autocomplete Serviço -->
          <div class="row mb-2 position-relative">
            <div class="col" style="position:relative;">
              <input
                v-model="servico._search"
                @input="servico._search = (servico._search || '').slice(0,60); buscarServicos(i)"
                @focus="abrirSugestaoServ(i)"
                @keydown.down.prevent="moverSugServ(i,1)"
                @keydown.up.prevent="moverSugServ(i,-1)"
                @keydown.enter.prevent="enterServ(i)"
                @keydown.esc.prevent="servico._showSug = false"
                @blur="servico._touchedDesc = true"
                type="text" class="form-control"
                :class="{'is-invalid': servico._touchedDesc && !validServicoDesc(servico)}"
                placeholder="Descrição do Serviço" autocomplete="off">
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
              <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">{{ (servico._search || '').length }}/60</small>
                <small v-if="servico._touchedDesc && !validServicoDesc(servico)" class="text-danger">Máx. 60 caracteres.</small>
              </div>
            </div>

            <div class="col">
              <!-- Mão de obra -->
              <input
                :value="servico._maoView"
                @input="onMaoInput(i, $event)"
                @blur="onMaoBlur(i)"
                type="text"
                class="form-control"
                placeholder="R$ 0,00"
                inputmode="numeric"
                required
              >
            </div>
            <div class="col-auto">
              <button class="btn btn-danger" type="button" @click="removerServico(i)">Remover</button>
            </div>
          </div>

          <!-- Peças do Serviço -->
          <div v-for="(peca, j) in servico.pecas" :key="j" class="row mb-2 position-relative">
            <div class="col" style="position:relative;">
              <input
                v-model="peca._search"
                @input="peca._search = (peca._search || '').slice(0,30); buscarPecas(i,j)"
                @focus="abrirSugPeca(i,j)"
                @keydown.down.prevent="moverSugPeca(i,j,1)"
                @keydown.up.prevent="moverSugPeca(i,j,-1)"
                @keydown.enter.prevent="enterPeca(i,j)"
                @keydown.esc.prevent="peca._showSug = false"
                @blur="peca._touchedDesc = true"
                type="text" class="form-control"
                :class="{'is-invalid': peca._touchedDesc && !validPecaDesc(peca)}"
                placeholder="Descrição da Peça" autocomplete="off">
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
              <div class="d-flex justify-content-between mt-1">
                <small class="text-muted">{{ (peca._search || '').length }}/30</small>
                <small v-if="peca._touchedDesc && !validPecaDesc(peca)" class="text-danger">Máx. 30 caracteres.</small>
              </div>
            </div>

            <div class="col">
              <input v-model.number="peca.quantidade" type="number" min="1" class="form-control" placeholder="Quantidade">
              <small class="text-transparent">.</small>
            </div>
            <div class="col">
              <!-- Preço -->
              <input
                :value="peca._precoView"
                @input="onPrecoInput(i, j, $event)"
                @blur="onPrecoBlur(i, j)"
                type="text"
                class="form-control"
                placeholder="R$ 0,00"
                inputmode="numeric"
              >
            </div>
            <div class="col-auto">
              <button class="btn btn-outline-danger" type="button" @click="removerPeca(i, j)">-</button>
            </div>
          </div>

          <button class="btn btn-outline-primary mb-2" type="button" @click="adicionarPeca(i)">Adicionar Peça</button>
        </div>

        <button class="btn btn-outline-success my-2" type="button" @click="adicionarServico">Adicionar Serviço</button>

        <div>
          <button type="submit" class="btn btn-primary mt-3" :disabled="!form.id_cliente || !allValid">Cadastrar Revisão</button>
          <small v-if="!form.id_cliente" class="text-danger ms-2">selecione um cliente</small>
        </div>
      </form>

      <!-- Tabela ANDAMENTO -->
      <h1 class="mb-3">Revisões em andamento</h1>
      <table class="table table-striped table-bordered mb-2">
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
          <tr v-for="rev in pag.andamento.data" :key="rev.id_revisao">
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
      <div v-if="!pag.andamento.data.length" class="alert alert-info">Nenhuma revisão em andamento.</div>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-sm btn-outline-secondary"
                :disabled="!pag.andamento.hasPrev"
                @click="irParaPagina('andamento', pag.andamento.page - 1)">
          « Anterior
        </button>
        <span class="align-self-center small">Página {{ pag.andamento.page }}</span>
        <button class="btn btn-sm btn-outline-secondary"
                :disabled="!pag.andamento.hasNext"
                @click="irParaPagina('andamento', pag.andamento.page + 1)">
          Próxima »
        </button>
      </div>

      <!-- Tabela FINALIZADAS -->
      <h1 class="mb-3">Revisões finalizadas</h1>
      <table class="table table-striped table-bordered mb-2">
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
          <tr v-for="rev in pag.finalizadas.data" :key="rev.id_revisao">
            <td>{{ rev.id_revisao }}</td>
            <td>{{ rev.data_inicio }}</td>
            <td>{{ rev.data_fim }}</td>
            <td>{{ rev.quilometragem }}</td>
            <td>{{ rev.id_cliente }} - {{ rev.nome }} {{ rev.sobrenome }}</td>
            <td>{{ rev.placa }}</td>
            <td>
              <button class="btn btn-sm btn-primary" @click="abrirVisualizar(rev.id_revisao)">Visualizar</button>
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="!pag.finalizadas.data.length" class="alert alert-info">Nenhuma revisão finalizada.</div>
      <div class="d-flex justify-content-between align-items-center mt-3">
        <button class="btn btn-sm btn-outline-secondary"
                :disabled="!pag.finalizadas.hasPrev"
                @click="irParaPagina('finalizadas', pag.finalizadas.page - 1)">
          « Anterior
        </button>
        <span class="align-self-center small">Página {{ pag.finalizadas.page }}</span>
        <button class="btn btn-sm btn-outline-secondary"
                :disabled="!pag.finalizadas.hasNext"
                @click="irParaPagina('finalizadas', pag.finalizadas.page + 1)">
          Próxima »
        </button>
      </div>
    </div>

    <!-- Modal Visualizar Revisão -->
    <div v-if="showModal" class="position-fixed top-0 start-0 w-100 h-100"
         style="background: rgba(0,0,0,.5); z-index:1050;"
         @click.self="fecharModal">
      <div class="bg-white rounded shadow p-4"
           style="max-width: 900px; width: 95%; margin: 5vh auto; max-height: 90vh; overflow: auto;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h4 class="m-0">Revisão #{{ det?.revisao?.id_revisao || '' }}</h4>
          <button class="btn btn-outline-secondary btn-sm" @click="fecharModal">Fechar</button>
        </div>

        <div v-if="loading"><em>Carregando...</em></div>
        <div v-else-if="!det"><div class="text-danger">Não foi possível carregar os dados.</div></div>
        <div v-else>
          <!-- Cabeçalho -->
          <div class="mb-3">
            <div><strong>Cliente:</strong> {{ det.revisao.nome }} {{ det.revisao.sobrenome }} (ID {{ det.revisao.id_cliente }})</div>
            <div><strong>Veículo:</strong> {{ det.revisao.marca }} {{ det.revisao.modelo }} — {{ det.revisao.ano }} • Placa {{ det.revisao.placa }}</div>
            <div><strong>Quilometragem:</strong> {{ formatIntBR(det.revisao.quilometragem) }}</div>
            <div><strong>Período:</strong> {{ det.revisao.data_inicio }} → {{ det.revisao.data_fim }}</div>
          </div>

          <!-- Serviços e Peças -->
          <div v-for="s in det.servicos" :key="s.id_servico" class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between">
              <div><strong>Serviço:</strong> {{ s.descricao }}</div>
              <div><strong>Mão de obra:</strong> {{ money(s.valor_mao_de_obra) }}</div>
            </div>

            <div class="mt-2">
              <strong>Peças</strong>
              <table class="table table-sm table-bordered mt-2">
                <thead class="table-light">
                  <tr>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th class="text-end">Qtd</th>
                    <th class="text-end">Preço</th>
                    <th class="text-end">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="p in (det.pecas[s.id_servico] || [])" :key="p.codigo">
                    <td>{{ p.codigo }}</td>
                    <td>{{ p.descricao }}</td>
                    <td class="text-end">{{ p.quantidade }}</td>
                    <td class="text-end">{{ money(p.preco) }}</td>
                    <td class="text-end">{{ money(p.preco * (p.quantidade || 1)) }}</td>
                  </tr>
                  <tr v-if="!(det.pecas[s.id_servico] || []).length">
                    <td colspan="5" class="text-center text-muted">Sem peças</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Totais -->
          <div class="d-flex justify-content-end gap-4">
            <div><strong>Total Mão de Obra:</strong> {{ money(totalMaoObra) }}</div>
            <div><strong>Total Peças:</strong> {{ money(totalPecas) }}</div>
            <div><strong>Total Geral:</strong> {{ money(totalMaoObra + totalPecas) }}</div>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, onBeforeUnmount, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import DefaultLayout from '../../layouts/DefaultLayout.vue'

defineProps({}) // nada vindo do servidor

/* fetch JSON helper */
async function fetchJSON(url, init) {
  const resp = await fetch(url, { credentials: 'include', headers: { Accept: 'application/json' }, ...init })
  const ct = (resp.headers.get('content-type') || '').toLowerCase()
  if (!resp.ok) throw new Error(`[${resp.status}] ${resp.statusText} – ${url}`)
  if (!ct.includes('application/json')) throw new Error(`Conteúdo não-JSON em ${url} (CT=${ct})`)
  return await resp.json()
}

/* --------- Helpers --------- */
function formatIntBR(n){
  const s = String(Math.max(0, Number.isFinite(n) ? Math.trunc(n) : 0))
  return s.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}
function formatMoneyViewFromNumber(v){
  const cents = Math.round((Number(v) || 0)*100)
  const int = Math.trunc(cents/100)
  const dec = String(cents%100).padStart(2,'0')
  return `R$ ${formatIntBR(int)},${dec}`
}
function formatMoneyViewFromCents(cents){
  const int = Math.trunc(cents/100)
  const dec = String(cents%100).padStart(2,'0')
  return `R$ ${formatIntBR(int)},${dec}`
}
function formatKmViewFromDigits(digits){
  const clean = digits.replace(/\D/g,'') || '0'
  const dec = clean.slice(-2).padStart(2,'0')
  const int = clean.slice(0, -2) || '0'
  return `${int.replace(/\B(?=(\d{3})+(?!\d))/g, '.')},${dec}`
}
function getDateTimeLocalNow() {
  const now = new Date()
  const offset = now.getTimezoneOffset()
  const local = new Date(now.getTime() - offset * 60000)
  return local.toISOString().slice(0, 16)
}

/* --------- Form --------- */
const form = useForm({
  id_cliente: '',
  placa: '',
  marca: '',
  modelo: '',
  ano: '',
  data_inicio: getDateTimeLocalNow(),
  quilometragem: 0, // inteiro (KM)
  servicos: [
    { id_servico: null, descricao: '', _search: '', _sugs: [], _showSug: false, _hi: 0, _touchedDesc: false, valor_mao_de_obra: 0, _maoView: 'R$ 0,00', pecas: [] }
  ]
})

/* ------- KM (visual) ------- */
const KM_MAX = 1_000_000
const quilometragemRawDigits = ref('0')
const quilometragemView = computed(() => formatKmViewFromDigits(quilometragemRawDigits.value))
function syncKmToForm(){
  const clean = (quilometragemRawDigits.value||'').replace(/\D/g,'') || '0'
  const intPart = Math.min(KM_MAX, Number(clean.slice(0, -2) || '0'))
  form.quilometragem = intPart
  const dec = clean.slice(-2).padStart(2,'0')
  quilometragemRawDigits.value = String(intPart) + dec
}
function onKmInput(e){
  let digits = (e.target.value || '').replace(/\D/g,'')
  if (digits === '') digits = '0'
  const intPart = digits.slice(0, -2)
  const decPart = digits.slice(-2)
  let intVal = Number(intPart || '0')
  if (intVal > KM_MAX) intVal = KM_MAX
  quilometragemRawDigits.value = String(intVal) + decPart.padStart(2,'0')
  syncKmToForm()
  e.target.value = quilometragemView.value
}
function onKmBlur(e){ e.target.value = quilometragemView.value }

/* ------- Mão de obra / Preço ------- */
const MONEY_MAX = 100_000
function moneyInputToCents(evValue){ return Number((String(evValue || '').replace(/\D/g,'') || '0')) }
function clampMoneyCents(cents){ return Math.min(cents, MONEY_MAX * 100) }
function onMaoInput(i, e){
  const cents = clampMoneyCents(moneyInputToCents(e.target.value))
  form.servicos[i].valor_mao_de_obra = cents / 100
  form.servicos[i]._maoView = formatMoneyViewFromCents(cents)
  e.target.value = form.servicos[i]._maoView
}
function onMaoBlur(i){
  const cents = Math.round((form.servicos[i].valor_mao_de_obra||0)*100)
  form.servicos[i]._maoView = formatMoneyViewFromCents(clampMoneyCents(cents))
}
function onPrecoInput(i, j, e){
  const cents = clampMoneyCents(moneyInputToCents(e.target.value))
  const reais = cents / 100
  form.servicos[i].pecas[j].preco = reais
  form.servicos[i].pecas[j]._precoView = formatMoneyViewFromCents(cents)
  e.target.value = form.servicos[i].pecas[j]._precoView
}
function onPrecoBlur(i, j){
  const cents = Math.round((form.servicos[i].pecas[j].preco||0)*100)
  form.servicos[i].pecas[j]._precoView = formatMoneyViewFromCents(clampMoneyCents(cents))
}
function formatMoneyNumber(n){ return formatMoneyViewFromNumber(Number(n)||0).replace(/^R\$ /,'') }

/* add/remove */
function adicionarServico() {
  form.servicos.push({ id_servico: null, descricao: '', _search: '', _sugs: [], _showSug: false, _hi: 0, _touchedDesc: false, valor_mao_de_obra: 0, _maoView: 'R$ 0,00', pecas: [] })
}
function removerServico(i) { form.servicos.splice(i, 1) }
function adicionarPeca(i) { form.servicos[i].pecas.push({ codigo: null, descricao: '', _search: '', _sugs: [], _showSug: false, _hi: 0, _touchedDesc: false, quantidade: 1, preco: 0, _precoView: 'R$ 0,00' }) }
function removerPeca(i, j) { form.servicos[i].pecas.splice(j, 1) }

/* -------- Submissão -------- */
function submit() {
  form.post('/revisoes', {
    onSuccess: async () => {
      form.reset()
      quilometragemRawDigits.value = '0'
      // recarrega a primeira página de cada status
      await Promise.all([buscar('andamento', 1), buscar('finalizadas', 1)])
    }
  })
}
function finalizarRevisao(id) {
  router.put(`/revisoes/${id}/finalizar`, {}, {
    preserveScroll: true,
    preserveState: true,
    onSuccess: async () => {
      // permanece na página atual de cada lista
      await Promise.all([
        buscar('andamento', pag.andamento.page),
        buscar('finalizadas', pag.finalizadas.page)
      ])
    },
  })
}

/* -------- Autocomplete Cliente (via endpoint) -------- */
const clientesSug = ref([])
const searchCliente = ref('')
const showSugestoes = ref(false)
const highlightedIndex = ref(-1)
const autoRef = ref(null)

async function carregarClientesSug () {
  const q = encodeURIComponent(searchCliente.value || '')
  try {
    const r = await fetchJSON(`/revisoes/clientes?q=${q}`)
    clientesSug.value = Array.isArray(r) ? r : []
  } catch {
    clientesSug.value = []
  }
}
const filteredClientes = computed(() => clientesSug.value)
const selecionadoNome = computed(() => {
  const c = clientesSug.value.find(x => x.id_cliente === form.id_cliente)
  return c ? `${c.nome} ${c.sobrenome}` : ''
})
function openSugestoes(){ showSugestoes.value = true; highlightedIndex.value = 0; carregarClientesSug() }
watch(searchCliente, () => { if (showSugestoes.value) carregarClientesSug() })
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

/* -------- Autocomplete Placa por Cliente -------- */
const placaRef = ref(null)
const placaSearch = ref('')
const showPlacas = ref(false)
const highlightedPlacaIndex = ref(-1)
const veiculosDoCliente = ref([])

async function carregarVeiculosDoCliente(id){
  veiculosDoCliente.value = []
  if (!id) return
  const data = await fetchJSON(`/revisoes/veiculos-do-cliente/${id}`)
  veiculosDoCliente.value = Array.isArray(data) ? data : []
}
watch(() => form.id_cliente, async (novo) => {
  form.placa = ''; placaSearch.value = ''
  form.marca = ''; form.modelo = ''; form.ano = ''
  showPlacas.value = false; highlightedPlacaIndex.value = -1
  if (novo) await carregarVeiculosDoCliente(novo)
})
const filteredPlacas = computed(() => {
  const q = (placaSearch.value || '').toLowerCase().trim()
  const base = Array.isArray(veiculosDoCliente.value) ? veiculosDoCliente.value : []
  if (!q) return base.slice(0, 10)
  return base
    .filter(v => {
      const placa = String(v?.placa || '').toLowerCase()
      const mm = `${v?.marca || ''} ${v?.modelo || ''}`.toLowerCase()
      return placa.includes(q) || mm.includes(q)
    })
    .slice(0, 20)
})
async function openPlacas() {
  if (!form.id_cliente) return
  if (!veiculosDoCliente.value.length) await carregarVeiculosDoCliente(form.id_cliente)
  showPlacas.value = true
  highlightedPlacaIndex.value = filteredPlacas.value.length ? 0 : -1
}
function onPlacaInput() {
  placaSearch.value = (placaSearch.value || '').toUpperCase().replace(/[^A-Z0-9]/g,'').slice(0,7)
  form.placa = placaSearch.value
  showPlacas.value = true
}
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

/* -------- Validações -------- */
const touched = reactive({ placa:false, marca:false, modelo:false, ano:false })
const validPlaca = computed(() => /^[A-Z0-9]{7}$/.test(form.placa || ''))
const validMarca = computed(() => (form.marca || '').length > 0 && (form.marca || '').length <= 20)
const validModelo = computed(() => (form.modelo || '').length > 0 && (form.modelo || '').length <= 20)
const validAno = computed(() => {
  const a = Number(form.ano)
  return Number.isInteger(a) && a >= 1900 && a <= 2100
})
function validServicoDesc(srv){ return (srv?._search || '').length > 0 && (srv?._search || '').length <= 60 }
function validPecaDesc(pc){ return (pc?._search || '').length > 0 && (pc?._search || '').length <= 30 }
const allValid = computed(() => {
  if (!validPlaca.value || !validMarca.value || !validModelo.value || !validAno.value) return false
  for (const s of form.servicos) {
    if (!validServicoDesc(s)) return false
    for (const p of (s.pecas || [])) {
      if (!validPecaDesc(p)) return false
    }
  }
  return true
})

/* ---------- Modal Visualizar ---------- */
const showModal = ref(false)
const loading = ref(false)
const det = ref(null)
async function abrirVisualizar(id){
  showModal.value = true
  loading.value = true
  det.value = null
  try { det.value = await fetchJSON(`/revisoes/${id}/detalhes`) }
  catch (e) { console.error(e) }
  finally { loading.value = false }
}
function fecharModal(){ showModal.value = false; det.value = null }
const totalMaoObra = computed(() => {
  if(!det.value?.servicos) return 0
  return det.value.servicos.reduce((acc,s)=> acc + Number(s.valor_mao_de_obra||0), 0)
})
const totalPecas = computed(() => {
  if(!det.value?.pecas || !det.value?.servicos) return 0
  let sum = 0
  for (const s of det.value.servicos) {
    const pcs = det.value.pecas[s.id_servico] || []
    for (const p of pcs) sum += Number(p.preco||0) * (Number(p.quantidade||1))
  }
  return sum
})
function money(v){ return formatMoneyViewFromNumber(Number(v||0)) }

/* -------- Paginação AJAX (simplePaginate) -------- */
const pag = reactive({
  andamento:   { page: 1, data: [], hasPrev: false, hasNext: false },
  finalizadas: { page: 1, data: [], hasPrev: false, hasNext: false },
})

async function buscar(status, page = 1) {
  const r = await fetchJSON(`/revisoes/lista?status=${status}&page=${page}`)
  const rows = Array.isArray(r?.data) ? r.data : (Array.isArray(r) ? r : [])
  pag[status].data = rows
  pag[status].page = r?.current_page ?? page
  pag[status].hasPrev = !!r?.prev_page_url
  pag[status].hasNext = !!r?.next_page_url
}

function irParaPagina(status, page) {
  if (page < 1) return
  if (page > pag[status].page && !pag[status].hasNext) return
  if (page < pag[status].page && !pag[status].hasPrev) return
  buscar(status, page)
}

onMounted(() => {
  buscar('andamento', 1)
  buscar('finalizadas', 1)
})

/* -------- Serviços (autocomplete dinâmico) -------- */
async function buscarServicos(i) {
  const srv = form.servicos[i]
  srv._showSug = true
  srv._hi = 0
  const q = encodeURIComponent(srv._search || '')
  try {
    const r = await fetchJSON(`/revisoes/servicos?q=${q}`)
    srv._sugs = Array.isArray(r) ? r : (r?.data ?? [])
  } catch (e) {
    console.error('servicos', e); srv._sugs = []
  }
}
async function abrirSugestaoServ(i){
  const s = form.servicos[i]
  s._showSug = true
  s._hi = 0
  await buscarServicos(i)
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
  srv._search = (s.descricao || '').slice(0, 60)
  srv.valor_mao_de_obra = Number(s.valor_mao_de_obra ?? 0)
  srv._maoView = formatMoneyViewFromNumber(srv.valor_mao_de_obra)
  srv._showSug = false
}

/* -------- Peças (autocomplete) -------- */
async function buscarPecas(i, j){
  const srv = form.servicos[i]; const pc = srv.pecas[j]
  pc._showSug = true; pc._hi = 0
  const q = encodeURIComponent(pc._search || '')
  const sid = srv.id_servico ? `&servico_id=${srv.id_servico}` : ''
  try {
    const r = await fetchJSON(`/revisoes/pecas?q=${q}${sid}`)
    pc._sugs = Array.isArray(r) ? r : (r?.data ?? [])
  } catch (e) {
    console.error('pecas', e); pc._sugs = []
  }
}
async function abrirSugPeca(i,j){
  const p = form.servicos[i].pecas[j]
  p._showSug = true
  p._hi = 0
  await buscarPecas(i,j)
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
  pc._precoView = formatMoneyNumber(pc.preco)
  if (!pc.quantidade || pc.quantidade < 1) pc.quantidade = 1
  pc._search = (p.descricao || '').slice(0, 30)
  pc._showSug = false
}
</script>
