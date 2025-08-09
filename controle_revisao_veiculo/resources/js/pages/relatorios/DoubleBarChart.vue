<script setup>
import { onMounted, watch, ref } from 'vue'
import * as d3 from 'd3'

const props = defineProps({
  data: { type: Array, required: true }
})

const chartRef = ref(null)

function renderChart() {
  // Mapeia os dados para padronizar os campos, mas SEM alterar a fonte original
  const data = props.data.map(d => ({
    sexo: d["Sexo"],
    total: +d["Total"],
    media_idade: Number(String(d["Média de Idade"]).replace(',', '.'))
  }))

  const keys = ['total', 'media_idade']

  const width = 400
  const height = 300
  const margin = { top: 20, right: 20, bottom: 70, left: 50 }

  d3.select(chartRef.value).selectAll('*').remove()

  const svg = d3.select(chartRef.value)
    .append('svg')
    .attr('width', width)
    .attr('height', height)

  const x0 = d3.scaleBand()
    .domain(data.map(d => d.sexo))
    .range([margin.left, width - margin.right])
    .padding(0.2)

  const x1 = d3.scaleBand()
    .domain(keys)
    .range([0, x0.bandwidth()])
    .padding(0.1)

  const y = d3.scaleLinear()
    .domain([0, d3.max(data, d => Math.max(d.total, d.media_idade))])
    .nice()
    .range([height - margin.bottom, margin.top])

  const color = d3.scaleOrdinal()
    .domain(keys)
    .range(['#2062ab', '#f3ae4b'])

  svg.append('g')
    .selectAll('g')
    .data(data)
    .join('g')
      .attr('transform', d => `translate(${x0(d.sexo)},0)`)
    .selectAll('rect')
    .data(d => keys.map(key => ({ key, value: d[key], sexo: d.sexo })))
    .join('rect')
      .attr('x', d => x1(d.key))
      .attr('y', d => y(d.value))
      .attr('width', x1.bandwidth())
      .attr('height', d => y(0) - y(d.value))
      .attr('fill', d => color(d.key))

  svg.append('g')
    .attr('transform', `translate(0,${height - margin.bottom})`)
    .call(d3.axisBottom(x0))

  svg.append('g')
    .attr('transform', `translate(${margin.left},0)`)
    .call(d3.axisLeft(y))

  const legendX = width / 2 - 60 // centraliza
  const legendY = height - margin.bottom + 30 // abaixo do gráfico

  const legend = svg.append('g')
    .attr('transform', `translate(${legendX},${legendY})`)

  keys.forEach((key, i) => {
    legend.append('rect')
      .attr('x', i * 120)
      .attr('y', 0)
      .attr('width', 18)
      .attr('height', 18)
      .attr('fill', color(key))
    legend.append('text')
      .attr('x', i * 120 + 26)
      .attr('y', 13)
      .attr('font-size', 13)
      .text(key === 'total' ? 'Total' : 'Média de Idade')
  })

  svg.append('g')
    .selectAll('g')
    .data(data)
    .join('g')
      .attr('transform', d => `translate(${x0(d.sexo)},0)`)
    .selectAll('text')
    .data(d => keys.map(key => ({ key, value: d[key] })))
    .join('text')
      .attr('x', d => x1(d.key) + x1.bandwidth() / 2)
      .attr('y', d => y(d.value) - 5)
      .attr('text-anchor', 'middle')
      .attr('fill', '#222')
      .attr('font-size', '12px')
      .text(d => d.value)
}

onMounted(renderChart)
watch(() => props.data, renderChart)
</script>

<template>
  <div ref="chartRef"></div>
</template>
