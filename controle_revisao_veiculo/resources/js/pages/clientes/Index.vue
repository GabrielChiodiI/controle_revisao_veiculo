<template>
  <DefaultLayout>
    <div class="container mt-5">
      <h1 class="mb-4 text-center">Lista de Clientes</h1>

      <!-- Formulário (Cadastro / Edição inline) -->
      <div class="card p-4 mb-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-0">{{ isEditing ? 'Editar cliente' : 'Cadastrar cliente' }}</h5>
          <span v-if="isEditing" class="badge text-bg-warning d-inline-flex align-items-center">
            <i class="bi bi-pencil-square me-1"></i> Modo edição (#{{ editId }})
          </span>
        </div>

        <form @submit.prevent="submit" novalidate>
          <div class="row mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
              <input
                v-model="form.nome"
                @blur="touched.nome = true"
                type="text"
                class="form-control"
                :class="{'is-invalid': touched.nome && !validNome}"
                placeholder="Nome"
                maxlength="20"
                required
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ form.nome.length }}/20</small>
                <small v-if="touched.nome && !validNome" class="text-danger">Máx. 20 caracteres.</small>
              </div>
            </div>

            <div class="col-md-6">
              <input
                v-model="form.sobrenome"
                @blur="touched.sobrenome = true"
                type="text"
                class="form-control"
                :class="{'is-invalid': touched.sobrenome && !validSobrenome}"
                placeholder="Sobrenome"
                maxlength="40"
                required
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ form.sobrenome.length }}/40</small>
                <small v-if="touched.sobrenome && !validSobrenome" class="text-danger">Máx. 40 caracteres.</small>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
              <input
                v-model="cpfFormatado"
                @blur="touched.cpf = true"
                type="text"
                class="form-control"
                :class="{'is-invalid': touched.cpf && !validCPF}"
                placeholder="CPF"
                inputmode="numeric"
                required
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ form.cpf.length }}/11 dígitos</small>
                <small v-if="touched.cpf && !validCPF" class="text-danger">CPF deve ter 11 dígitos.</small>
              </div>
            </div>

            <div class="col-md-6">
              <input
                v-model="telefoneFormatado"
                @blur="touched.telefone = true"
                type="text"
                class="form-control"
                :class="{'is-invalid': touched.telefone && !validTelefone}"
                placeholder="Telefone"
                inputmode="numeric"
                required
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ form.telefone.length }}/11 dígitos</small>
                <small v-if="touched.telefone && !validTelefone" class="text-danger">Telefone deve ter 11 dígitos.</small>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6 mb-2 mb-md-0">
              <input
                v-model="form.email"
                @blur="touched.email = true"
                type="email"
                class="form-control"
                :class="{'is-invalid': touched.email && !validEmail}"
                placeholder="Email"
                maxlength="60"
                required
              >
              <div class="d-flex justify-content-between">
                <small class="text-muted">{{ form.email.length }}/60</small>
                <small v-if="touched.email && !validEmail" class="text-danger">Máx. 60 caracteres.</small>
              </div>
            </div>

            <div class="col-md-6">
              <input
                v-model="form.data_nascimento"
                @blur="touched.data_nascimento = true"
                type="date"
                class="form-control"
                :class="{'is-invalid': touched.data_nascimento && !validData}"
                placeholder="Data de Nascimento"
                required
              >
              <small v-if="touched.data_nascimento && !validData" class="text-danger">Data obrigatória.</small>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col">
              <select
                v-model="form.sexo"
                @blur="touched.sexo = true"
                class="form-select"
                :class="{'is-invalid': touched.sexo && !validSexo}"
                required
              >
                <option disabled value="">Selecione o Sexo</option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
              </select>
              <small v-if="touched.sexo && !validSexo" class="text-danger">Selecione uma opção.</small>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary" :disabled="!canSubmit">
              <i class="bi" :class="isEditing ? 'bi-save2' : 'bi-plus-lg'"></i>
              <span class="ms-2">{{ isEditing ? 'Salvar alterações' : 'Cadastrar' }}</span>
            </button>

            <button
              v-if="isEditing"
              type="button"
              class="btn btn-outline-secondary"
              @click="cancelarEdicao"
            >
              <i class="bi bi-x-lg"></i><span class="ms-2">Cancelar</span>
            </button>
          </div>
        </form>
      </div>

      <!-- Tabela de clientes -->
      <div class="card p-3 shadow-sm">
        <table class="table mb-0 table-bordered align-middle">
          <thead class="table-dark">
            <tr>
              <th style="width:90px">ID</th>
              <th>Cliente</th>
              <th style="width:260px">Ações</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cliente in clientes" :key="cliente.id_cliente">
              <td class="fw-semibold">{{ cliente.id_cliente }}</td>
              <td>{{ cliente.nome }} {{ cliente.sobrenome }}</td>
              <td class="text-nowrap">
                <!-- Ver veículos -->
                <Link
                  :href="`/clientes/${cliente.id_cliente}/veiculos`"
                  class="btn btn-sm btn-outline-secondary me-1"
                  title="Ver veículos"
                >
                  <i class="bi bi-car-front"></i>
                </Link>

                <!-- Adicionar veículo -->
                <Link
                  :href="`/clientes/${cliente.id_cliente}/veiculos`"
                  class="btn btn-sm btn-outline-success me-1"
                  title="Adicionar veículo"
                >
                  <i class="bi bi-plus-square"></i>
                </Link>

                <!-- Editar inline -->
                <button
                  class="btn btn-sm btn-outline-primary me-1"
                  title="Editar cliente"
                  @click="carregarParaEdicao(cliente)"
                >
                  <i class="bi bi-pencil-square"></i>
                </button>

                <!-- Excluir cliente -->
                <button
                  class="btn btn-sm btn-outline-danger"
                  title="Excluir cliente"
                  @click="excluir(cliente)"
                >
                  <i class="bi bi-trash"></i>
                </button>
              </td>
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
import { Link, useForm, router } from '@inertiajs/vue3'
import { computed, reactive, watch, ref, nextTick } from 'vue'
import DefaultLayout from '../../layouts/DefaultLayout.vue'

const props = defineProps({
  clientes: {
    type: Array,
    required: true
  }
})

/* ---- estado de edição inline ---- */
const isEditing = ref(false)
const editId = ref(null)

/* ---- form compartilhado (create/update) ---- */
const form = useForm({
  nome: '',
  sobrenome: '',
  cpf: '',
  telefone: '',
  email: '',
  data_nascimento: '',
  sexo: ''
})

/* ---- touched ---- */
const touched = reactive({
  nome: false,
  sobrenome: false,
  cpf: false,
  telefone: false,
  email: false,
  data_nascimento: false,
  sexo: false
})

/* ---- normalização ---- */
watch(() => form.cpf, v => { if (v != null) form.cpf = String(v).replace(/\D/g, '').slice(0, 11) })
watch(() => form.telefone, v => { if (v != null) form.telefone = String(v).replace(/\D/g, '').slice(0, 11) })

/* ---- exibição formatada ---- */
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
    form.cpf = (v || '').replace(/\D/g, '').slice(0, 11)
  }
})

const telefoneFormatado = computed({
  get() {
    if (!form.telefone) return ''
    const d = form.telefone.replace(/\D/g, '')
    if (d.length <= 10) {
      return d
        .replace(/^(\d{2})(\d)/, '($1) $2')
        .replace(/^(\(\d{2}\)\s)(\d{4})(\d)/, '$1$2-$3')
        .slice(0, 14)
    }
    return d
      .replace(/^(\d{2})(\d)/, '($1) $2')
      .replace(/^(\(\d{2}\)\s)(\d{5})(\d)/, '$1$2-$3')
      .slice(0, 15)
  },
  set(v) {
    form.telefone = (v || '').replace(/\D/g, '').slice(0, 11)
  }
})

/* ---- validações ---- */
const validNome = computed(() => form.nome.length > 0 && form.nome.length <= 20)
const validSobrenome = computed(() => form.sobrenome.length > 0 && form.sobrenome.length <= 40)
const validCPF = computed(() => form.cpf.length === 11)
const validTelefone = computed(() => form.telefone.length === 11)
const validEmail = computed(() => form.email.length > 0 && form.email.length <= 60)
const validData = computed(() => !!form.data_nascimento)
const validSexo = computed(() => !!form.sexo)

const canSubmit = computed(() =>
  validNome.value &&
  validSobrenome.value &&
  validCPF.value &&
  validTelefone.value &&
  validEmail.value &&
  validData.value &&
  validSexo.value
)

/* ---- helpers ---- */
function resetTouched() {
  Object.keys(touched).forEach(k => touched[k] = false)
}

/* ---- submit (POST/PUT) ---- */
function submit() {
  Object.keys(touched).forEach(k => touched[k] = true)
  if (!canSubmit.value) return

  if (!isEditing.value) {
    // CREATE
    form.post('/clientes', {
      onSuccess: () => {
        form.reset()
        resetTouched()
      }
    })
  } else {
    // UPDATE
    form.put(`/clientes/${editId.value}`, {
      onSuccess: () => {
        isEditing.value = false
        editId.value = null
        form.reset()
        resetTouched()
        // opcional: rolar para tabela
        nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))
      }
    })
  }
}

/* ---- carregar registro para edição inline ---- */
function carregarParaEdicao(c) {
  isEditing.value = true
  editId.value = c.id_cliente

  form.nome = c.nome || ''
  form.sobrenome = c.sobrenome || ''
  form.cpf = (c.cpf || '').replace(/\D/g, '').slice(0, 11)
  form.telefone = (c.telefone || '').replace(/\D/g, '').slice(0, 11)
  form.email = c.email || ''
  form.data_nascimento = c.data_nascimento || ''
  form.sexo = c.sexo || ''

  resetTouched()
  nextTick(() => window.scrollTo({ top: 0, behavior: 'smooth' }))
}

/* ---- cancelar edição ---- */
function cancelarEdicao() {
  isEditing.value = false
  editId.value = null
  form.reset()
  resetTouched()
}

/* ---- excluir ---- */
function excluir(c) {
  if (!confirm(`Excluir cliente #${c.id_cliente}? Isso removerá também seus veículos.`)) return
  router.delete(`/clientes/${c.id_cliente}`)
}
</script>
