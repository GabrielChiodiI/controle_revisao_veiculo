<template>
  <DefaultLayout>
    <div class="relatorio-card card mx-auto mt-5 p-4" style="max-width: 700px;">
      <h1 class="mb-4 text-center">Relatórios</h1>
      <!-- Topo: seleção e botão -->
      <div class="d-flex align-items-end gap-2 mb-4">
        <div class="flex-grow-1">
          <label class="form-label">Escolha o relatório</label>
          <select v-model="relatorioSelecionado" class="form-select">
            <option disabled value="">Selecione...</option>
            <option value="pessoasPorSexo">Pessoas por Sexo (Média Idade)</option>
            <option value="veiculosPorPessoa">Veículos por Pessoa</option>
            <option value="maisVeiculosPorSexo">Mais Veículos por Sexo</option>
            <option value="marcasOrdenadas">Marcas Ordenadas</option>
            <option value="marcasPorSexo">Marcas por Sexo</option>
            <option value="revisoesPorPeriodo">Revisões por Período</option>
            <option value="marcasMaisRevisoes">Marcas com Mais Revisões</option>
            <option value="pessoasMaisRevisoes">Pessoas com Mais Revisões</option>
            <option value="mediaTempoEntreRevisoes">Média Tempo Entre Revisões (Pessoa)</option>
            <option value="proximaRevisaoPorPessoa">Próxima Revisão por Pessoa</option>
          </select>
        </div>
        <button class="btn btn-primary" @click="gerarRelatorio" :disabled="!relatorioSelecionado">
          Gerar Relatório
        </button>
      </div>

      <!-- Parte de baixo: tabela de dados -->
      <div v-if="dados && Array.isArray(dados) && dados.length" class="table-responsive">
        <table class="table table-striped table-bordered mt-4">
          <thead>
            <tr>
              <th v-for="(col, i) in colunasTabela" :key="i">{{ col }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, idx) in dados" :key="idx">
              <td v-for="(col, i) in colunasTabela" :key="i">{{ item[col] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-else-if="dados && Array.isArray(dados) && !dados.length" class="alert alert-info mt-3">
        Nenhum dado encontrado para esse relatório.
      </div>
      <div v-else-if="dados && !Array.isArray(dados)" class="alert alert-info mt-3">
        {{ dados }}
      </div>
      <!-- Gráfico de barras só para Pessoas por Sexo -->
      <div
        v-if="relatorioSelecionado === 'pessoasPorSexo' && dados && Array.isArray(dados) && dados.length"
        class="mt-4"
      >
        <h5 class="text-center mb-3">Gráfico: Pessoas por Sexo</h5>
        <BarChart :data="dados" />
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import DefaultLayout from '../../layouts/DefaultLayout.vue'
import BarChart from './BarChart.vue'

const dados = ref(null)
const relatorioSelecionado = ref('')
const idPessoa = 1
const periodo = { inicio: '2024-01-01', fim: '2024-12-31' }

async function gerarRelatorio() {
  let url = ''
  let params = {}

  switch (relatorioSelecionado.value) {
    case 'pessoasPorSexo':
      url = '/relatorios/pessoas-por-sexo'
      break
    case 'veiculosPorPessoa':
      url = '/relatorios/veiculos-por-pessoa'
      break
    case 'maisVeiculosPorSexo':
      url = '/relatorios/mais-veiculos-por-sexo'
      break
    case 'marcasOrdenadas':
      url = '/relatorios/marcas-ordenadas'
      break
    case 'marcasPorSexo':
      url = '/relatorios/marcas-por-sexo'
      break
    case 'revisoesPorPeriodo':
      url = '/relatorios/revisoes-por-periodo'
      params = { inicio: periodo.inicio, fim: periodo.fim }
      break
    case 'marcasMaisRevisoes':
      url = '/relatorios/marcas-mais-revisoes'
      break
    case 'pessoasMaisRevisoes':
      url = '/relatorios/pessoas-mais-revisoes'
      break
    case 'mediaTempoEntreRevisoes':
      url = `/relatorios/media-tempo-entre-revisoes/${idPessoa}`
      break
    case 'proximaRevisaoPorPessoa':
      url = `/relatorios/proxima-revisao/${idPessoa}`
      break
    default:
      dados.value = null
      return
  }

  // Monta query string se tiver params
  let urlFetch = url
  if (Object.keys(params).length > 0) {
    urlFetch += '?' + new URLSearchParams(params).toString()
  }

  try {
    const response = await fetch(urlFetch)
    if (!response.ok) throw new Error('Erro ao buscar relatório')
    dados.value = await response.json()
  } catch (e) {
    dados.value = `Erro: ${e.message}`
  }
}

const colunasTabela = computed(() => {
  if (dados.value && Array.isArray(dados.value) && dados.value.length > 0) {
    return Object.keys(dados.value[0])
  }
  return []
})
</script>

<style scoped>
.relatorio-card {
  background: var(--surface);
  box-shadow: 0 6px 32px rgba(32, 98, 171, 0.08);
  border-radius: 16px;
}
</style>