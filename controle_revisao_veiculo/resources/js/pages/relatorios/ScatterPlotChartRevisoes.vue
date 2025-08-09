<template>
  <div ref="chart"></div>
</template>

<script setup>
import { onMounted, watch, ref } from 'vue'
import * as d3 from 'd3'

const props = defineProps({
  data: { type: Array, required: true }
})
const chart = ref(null)

function parseDate(str) {
  // "10/01/2025 - 10/01/2025" -> Date(2025, 0, 10)
  if (!str) return null
  const m = str.match(/(\d{2})\/(\d{2})\/(\d{4})/)
  if (!m) return null
  return new Date(`${m[3]}-${m[2]}-${m[1]}`)
}

function preprocess(data) {
  return data.map(item => ({
    date: parseDate(item['Início/Fim Revisão']),
    cliente: item['Cliente'],
    veiculo: item['Veículo']
  }))
}

function renderChart() {
  d3.select(chart.value).selectAll('*').remove()
  if (!props.data || props.data.length === 0) return

  const data = preprocess(props.data)
  const clientes = Array.from(new Set(data.map(d => d.cliente)))
  const veiculos = Array.from(new Set(data.map(d => d.veiculo)))

  const margin = { top: 50, right: 100, bottom: 70, left: 50 }
  const width = 600 - margin.left - margin.right
  const height = 300 - margin.top - margin.bottom
  const legendWidth = 200 

  const svg = d3.select(chart.value)
    .append('svg')
    .attr('width', width + margin.left + margin.right + legendWidth)
    .attr('viewBox', `0 0 ${width + margin.left + margin.right} ${height + margin.top + margin.bottom}`)
    .attr('height', height + margin.top + margin.bottom)
    .append('g')
    .attr('transform', `translate(${margin.left},${margin.top})`)

  // Escalas
  const x = d3.scaleTime()
    .domain(d3.extent(data, d => d.date))
    .range([0, width])

  const y = d3.scalePoint()
    .domain(clientes)
    .range([0, height])
    .padding(0.5)

  const color = d3.scaleOrdinal()
    .domain(veiculos)
    .range(d3.schemeSet2)

  // Eixos
  svg.append('g')
    .attr('transform', `translate(0,${height})`)
    .call(d3.axisBottom(x).ticks(7).tickFormat(d3.timeFormat('%d/%m/%Y')))
    .selectAll("text")
      .attr("transform", "rotate(-30)")
      .style("text-anchor", "end")

  svg.append('g')
    .call(d3.axisLeft(y))

  // Pontos
  svg.selectAll('circle')
    .data(data)
    .enter()
    .append('circle')
      .attr('cx', d => {
        // Desloca ponto no eixo x por veículo (ex: -8, 0, +8 pixels)
        const offset = (veiculos.indexOf(d.veiculo) - (veiculos.length-1)/2) * 2
        return x(d.date) + offset
      })
      .attr('cy', d => y(d.cliente))
      .attr('r', 7)
      .attr('fill', d => color(d.veiculo))
      .attr('opacity', 0.8)
      .append('title')
      .text(d => `${d.cliente} - ${d.veiculo} - ${d3.timeFormat('%d/%m/%Y')(d.date)}`)

  // Legenda
  const legend = svg.append('g')
    .attr('transform', `translate(${width + 20},0)`)
  veiculos.forEach((v, i) => {
    legend.append('circle')
      .attr('cx', 0)
      .attr('cy', i * 24)
      .attr('r', 8)
      .attr('fill', color(v))
    legend.append('text')
      .attr('x', 18)
      .attr('y', i * 24 + 5)
      .text(v)
      .style('font-size', '13px')
    })
}

onMounted(renderChart)
watch(() => props.data, renderChart)
</script>
