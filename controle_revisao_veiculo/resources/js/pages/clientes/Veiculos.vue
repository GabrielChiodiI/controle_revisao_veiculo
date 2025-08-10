<template>
  <DefaultLayout>
    <div class="container mt-5">
      <h1 class="mb-3">
        Veículos de {{ props.cliente.nome }} {{ props.cliente.sobrenome }}
        (ID {{ props.cliente.id_cliente }})
      </h1>

      <!-- Formulário (ADD / EDIT) -->
      <div class="card p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-0">
            {{ isEditing ? 'Editar veículo' : 'Adicionar veículo' }}
          </h5>
          <span
            v-if="isEditing"
            class="badge text-bg-warning d-inline-flex align-items-center"
            title="Você está editando um veículo existente"
          >
            <i class="bi bi-pencil-square me-1"></i> Modo edição
          </span>
        </div>

        <form @submit.prevent="submit" novalidate>
          <div class="row mb-3">
            <div class="col-md-4 mb-2 mb-md-0">
              <label class="form-label">Placa</label>
              <input
                v-model="form.placa"
                @input="form.placa = (form.placa || '').toUpperCase().replace(/[^A-Z0-9]/g,'').slice(0,7)"
                @blur="touched.placa = true"
                type="text"
                class="form-control"
                :class="{'is-invalid': touched.placa && !validPlaca}"
                placeholder="ABC1D23"
                maxlength="7"
                required
                ref="placaRef"
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ (form.placa || '').length }}/7</small>
                <small v-if="touched.placa && !validPlaca" class="text-danger">7 chars A–Z/0–9.</small>
              </div>
            </div>

            <div class="col-md-4 mb-2 mb-md-0">
              <label class="form-label">Marca</label>
              <input
                v-model="form.marca"
                @blur="touched.marca = true"
                type="text"
                class="form-control"
                :class="{'is-invalid': touched.marca && !validMarca}"
                placeholder="Ex.: Toyota"
                maxlength="20"
                required
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ (form.marca || '').length }}/20</small>
                <small v-if="touched.marca && !validMarca" class="text-danger">Máx. 20 caracteres.</small>
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Modelo</label>
              <input
                v-model="form.modelo"
                @blur="touched.modelo = true"
                type="text"
                class="form-control"
                :class="{'is-invalid': touched.modelo && !validModelo}"
                placeholder="Ex.: Corolla"
                maxlength="20"
                required
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ (form.modelo || '').length }}/20</small>
                <small v-if="touched.modelo && !validModelo" class="text-danger">Máx. 20 caracteres.</small>
              </div>
            </div>
          </div>

          <div class="row mb-2">
            <div class="col-md-3">
              <label class="form-label">Ano</label>
              <input
                v-model.number="form.ano"
                @blur="touched.ano = true"
                type="number"
                class="form-control"
                :class="{'is-invalid': touched.ano && !validAno}"
                placeholder="2020"
                min="1900"
                max="2100"
                required
              >
              <small v-if="touched.ano && !validAno" class="text-danger">Ano entre 1900 e 2100.</small>
            </div>
          </div>

          <div class="d-flex gap-2 mt-2">
            <button
              type="submit"
              class="btn btn-primary d-inline-flex align-items-center px-4"
              :disabled="!canSubmit"
            >
              <i class="bi" :class="isEditing ? 'bi-save2' : 'bi-plus-lg'"></i>
              <span class="ms-2">{{ isEditing ? 'Salvar alterações' : 'Adicionar' }}</span>
            </button>

            <button
              v-if="isEditing"
              type="button"
              class="btn btn-outline-secondary d-inline-flex align-items-center"
              @click="cancelarEdicao"
            >
              <i class="bi bi-x-lg"></i><span class="ms-2">Cancelar</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Lista -->
      <div class="card p-3">
        <table class="table table-bordered align-middle mb-0">
          <thead class="table-dark">
            <tr>
              <th>Placa</th>
              <th>Marca</th>
              <th>Modelo</th>
              <th>Ano</th>
              <th style="width:160px">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="v in props.veiculos" :key="v.placa">
              <td>{{ v.placa }}</td>
              <td>{{ v.marca }}</td>
              <td>{{ v.modelo }}</td>
              <td>{{ v.ano }}</td>
              <td class="text-nowrap">
                <button
                  class="btn btn-sm btn-outline-primary me-1"
                  @click="carregarParaEdicao(v)"
                  title="Editar neste formulário"
                >
                  <i class="bi bi-pencil-square"></i>
                </button>
                <button
                  class="btn btn-sm btn-outline-danger"
                  @click="excluir(v)"
                  title="Excluir"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!props.veiculos.length" class="alert alert-info mt-3 mb-0 text-center rounded">
          Nenhum veículo.
        </div>
      </div>

      <div class="mt-3">
        <Link href="/clientes" class="btn btn-link">
          <i class="bi bi-arrow-left"></i> Voltar
        </Link>
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import DefaultLayout from '../../layouts/DefaultLayout.vue'
import { useForm, Link, router } from '@inertiajs/vue3'
import { ref, reactive, computed, nextTick } from 'vue'

const props = defineProps({
  cliente: { type: Object, required: true },
  veiculos: { type: Array, required: true }
})

/* ----------- ESTADO ----------- */
const isEditing = ref(false)
const oldPlaca = ref(null)
const placaRef = ref(null)

const form = useForm({ placa:'', marca:'', modelo:'', ano:null })

// touched + validações (estilo do seu form de clientes)
const touched = reactive({ placa:false, marca:false, modelo:false, ano:false })

const validPlaca  = computed(() => /^[A-Z0-9]{7}$/.test(form.placa || ''))
const validMarca  = computed(() => (form.marca || '').length > 0 && (form.marca || '').length <= 20)
const validModelo = computed(() => (form.modelo || '').length > 0 && (form.modelo || '').length <= 20)
const validAno    = computed(() => Number.isInteger(form.ano) && form.ano >= 1900 && form.ano <= 2100)

const canSubmit = computed(() => validPlaca.value && validMarca.value && validModelo.value && validAno.value)

/* ----------- AÇÕES ----------- */
function resetTouched() {
  Object.keys(touched).forEach(k => touched[k] = false)
}

function submit() {
  // marca campos
  Object.keys(touched).forEach(k => touched[k] = true)
  if (!canSubmit.value) return

  // normaliza placa
  form.placa = (form.placa || '').toUpperCase().replace(/[^A-Z0-9]/g,'').slice(0,7)

  if (!isEditing.value) {
    // ADD
    form.post(`/clientes/${props.cliente.id_cliente}/veiculos`, {
      onSuccess: () => {
        form.reset()
        resetTouched()
        focusPlaca()
      }
    })
  } else {
    // EDIT
    form.put(`/clientes/${props.cliente.id_cliente}/veiculos/${encodeURIComponent(oldPlaca.value)}`, {
      onSuccess: () => {
        isEditing.value = false
        oldPlaca.value = null
        form.reset()
        resetTouched()
        focusPlaca()
      }
    })
  }
}

function carregarParaEdicao(v) {
  isEditing.value = true
  oldPlaca.value = v.placa
  form.placa = v.placa
  form.marca = v.marca
  form.modelo = v.modelo
  form.ano = v.ano
  resetTouched()
  // foca na placa
  nextTick(() => focusPlaca())
  // scroll suave até o formulário
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

function cancelarEdicao() {
  isEditing.value = false
  oldPlaca.value = null
  form.reset()
  resetTouched()
  focusPlaca()
}

function excluir(v) {
  if (!confirm(`Excluir veículo ${v.placa}?`)) return
  router.delete(`/clientes/${props.cliente.id_cliente}/veiculos/${encodeURIComponent(v.placa)}`)
}

function focusPlaca() {
  if (placaRef.value && typeof placaRef.value.focus === 'function') {
    placaRef.value.focus()
  }
}
</script>

<style scoped>
/* melhora o botão sem exagero (usa classes padrão Bootstrap) */
</style>
