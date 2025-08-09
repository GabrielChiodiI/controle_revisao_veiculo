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
            <option value="todosVeiculos">Todos os Veículos</option>
            <option value="todosVeiculosPorPessoa">Todos Veículos por Pessoa</option>
            <option value="maisVeiculosPorSexo">Sexo com Mais Veículos</option>
            <option value="marcasPorNumeroVeiculos">Marcas Ordenadas por Qtd. Veículos</option>
            <option value="marcasQuantidadePorSexo">Marcas por Qtd. Separadas por Sexo</option>
            <option value="todasPessoas">Todas as Pessoas</option>
            <option value="pessoasPorSexoComMediaIdade">Pessoas por Sexo (Média de Idade)</option>
            <option value="revisoesPorPeriodo">Revisões Realizadas em um Período</option>
            <option value="marcasComMaisRevisoes">Marcas com Mais Revisões</option>
            <option value="pessoasComMaisRevisoes">Pessoas com Mais Revisões</option>
            <option value="mediaTempoEntreRevisoes">Média de Tempo Entre Revisões por Pessoa</option>
            <option value="proximaRevisaoPorPessoa">Próxima Revisão Base no Tempo Médio do Cliente</option>
          </select>
        </div>
        <button class="btn btn-primary" @click="gerarRelatorio" :disabled="!relatorioSelecionado">
          Gerar Relatório
        </button>
      </div>

      <div v-if="relatorioSelecionado === 'revisoesPorPeriodo'" class="d-flex gap-2 align-items-end">
        <div>
          <label class="form-label mb-0">Data inicial</label>
          <input type="date" class="form-control" v-model="periodo.inicio">
        </div>
        <div>
          <label class="form-label mb-0">Data final</label>
          <input type="date" class="form-control" v-model="periodo.fim">
        </div>
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
      <!-- Gráficos -->
      <div
        v-if="relatorioSelecionado === 'pessoasPorSexoComMediaIdade' && dados && Array.isArray(dados) && dados.length"
        class="mt-4"
      >
        <h5 class="text-center mb-3">Gráfico: Pessoas por Sexo</h5>
        <div class="d-flex justify-content-center">
          <DoubleBarChart :data="dados" />
        </div>
      </div>
      <div
        v-if="relatorioSelecionado === 'todosVeiculos' && dados && Array.isArray(dados) && dados.length"
        class="mt-4"
      >
        <h5 class="text-center mb-3">Distribuição dos Veículos por Marca</h5>
          <div class="d-flex justify-content-center">
            <PieChart :data="dados" />
          </div>
      </div>
      <div class="d-flex justify-content-center">
        <GroupBarChart
          :data="dados"
          v-if="relatorioSelecionado === 'marcasQuantidadePorSexo' && dados && Array.isArray(dados) && dados.length"
        />
      </div>
      <div class="d-flex justify-content-center">
        <ScatterPlotChartRevisoes
          v-if="relatorioSelecionado === 'revisoesPorPeriodo' && dados && Array.isArray(dados) && dados.length"
          :data="dados"
        />
      </div>
      <div class="d-flex justify-content-center">
        <IcicleChart
          :data="dados"
          v-if="relatorioSelecionado === 'todosVeiculosPorPessoa' && dados && Array.isArray(dados) && dados.length"
        />
      </div>
      <div class="d-flex justify-content-center">
        <ScatterPlotChart
          :data="dados"
          v-if="relatorioSelecionado === 'proximaRevisaoPorPessoa' && dados && Array.isArray(dados) && dados.length"
        />
      </div>
      <div class="d-flex justify-content-center">
        <BarChart
          v-if="relatorioSelecionado === 'marcasPorNumeroVeiculos' && dados && Array.isArray(dados) && dados.length"
          :data="dados"
          xKey="Marca"
          yKey="Quantidade"
          label="Veículos por Marca"
        />
      </div>
      <div class="d-flex justify-content-center">
        <BarChart
          v-if="relatorioSelecionado === 'marcasComMaisRevisoes' && dados && Array.isArray(dados) && dados.length"
          :data="dados"
          xKey="Marca"
          yKey="Total"
          label="Revisões por Marca"
        />
      </div>
      <div class="d-flex justify-content-center">
        <BarChart
          v-if="relatorioSelecionado === 'pessoasComMaisRevisoes' && dados && Array.isArray(dados) && dados.length"
          :data="dados"
          xKey="Pessoa"
          yKey="Quantidade"
          label="Revisões por Pessoa"
        />
      </div>
      <div class="d-flex justify-content-center">
        <BarChart
          v-if="relatorioSelecionado === 'mediaTempoEntreRevisoes' && dados && Array.isArray(dados) && dados.length"
          :data="dados"
          xKey="Cliente"
          yKey="Media de Tempo em Dias"
          label="Média de Tempo Entre Revisões (Dias)"
        />
      </div>
    </div>
  </DefaultLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import DefaultLayout from '../../layouts/DefaultLayout.vue'
import BarChart from './BarChart.vue'
import DoubleBarChart from './DoubleBarChart.vue'
import PieChart from './PieChart.vue'
import IcicleChart from './IcicleChart.vue'
import GroupBarChart from './GroupBarChart.vue'
import ScatterPlotChart from './ScatterPlotChart.vue'
import ScatterPlotChartRevisoes from './ScatterPlotChartRevisoes.vue'

const dados = ref(null)
const relatorioSelecionado = ref('')
const periodo = ref({ inicio: '2025-01-01', fim: '2025-08-07' })

async function gerarRelatorio() {
  let url = ''
  let params = {}

  switch (relatorioSelecionado.value) {
    case 'todosVeiculos':
      url = '/relatorios/todos-veiculos'
      break
    case 'todosVeiculosPorPessoa':
      url = '/relatorios/veiculos-por-pessoa'
      break
    case 'maisVeiculosPorSexo':
      url = '/relatorios/mais-veiculos-por-sexo'
      break
    case 'marcasPorNumeroVeiculos':
      url = '/relatorios/marcas-ordenadas'
      break
    case 'marcasQuantidadePorSexo':
      url = '/relatorios/marcas-quantidade-por-sexo'
      break
    case 'todasPessoas':
      url = '/relatorios/todas-pessoas'
      break
    case 'pessoasPorSexoComMediaIdade':
      url = '/relatorios/pessoas-por-sexo'
      break
    case 'revisoesPorPeriodo':
      url = '/relatorios/revisoes-por-periodo'
      params = { inicio: periodo.value.inicio, fim: periodo.value.fim }
      break
    case 'marcasComMaisRevisoes':
      url = '/relatorios/marcas-mais-revisoes'
      break
    case 'pessoasComMaisRevisoes':
      url = '/relatorios/pessoas-mais-revisoes'
      break
    case 'mediaTempoEntreRevisoes':
      url = `/relatorios/media-tempo-entre-revisoes`
      break
    case 'proximaRevisaoPorPessoa':
      url = `/relatorios/proxima-revisao`
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