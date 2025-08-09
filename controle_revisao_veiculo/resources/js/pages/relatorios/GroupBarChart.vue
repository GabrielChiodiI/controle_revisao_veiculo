<template>
  <div ref="chart"></div>
</template>

<script setup>
import { ref, watch } from 'vue'
import * as d3 from 'd3'

const props = defineProps({
  data: { type: Array, required: true }
})

const chart = ref(null)

watch(() => props.data, render, { immediate: true })

function render() {
  d3.select(chart.value).selectAll('*').remove()
  if (!props.data || props.data.length === 0) return

  const width = 700
  const height = 350
  const margin = { top: 30, right: 30, bottom: 80, left: 60 }

  // Processa os dados para obter as marcas e sexos únicos
  const marcas = Array.from(new Set(props.data.map(d => d.Marca)))
  const sexos = Array.from(new Set(props.data.map(d => d.Sexo)))

  // Monta um dicionário para acessar os valores
  const dataPivot = marcas.map(marca => {
    const item = { Marca: marca }
    sexos.forEach(sexo => {
      const found = props.data.find(d => d.Marca === marca && d.Sexo === sexo)
      item[sexo] = found ? found.Quantidade : 0
    })
    return item
  })

  // Escalas
  const x0 = d3.scaleBand()
    .domain(marcas)
    .range([margin.left, width - margin.right])
    .paddingInner(0.1)

  const x1 = d3.scaleBand()
    .domain(sexos)
    .range([0, x0.bandwidth()])
    .padding(0.05)

  const y = d3.scaleLinear()
    .domain([0, d3.max(props.data, d => d.Quantidade)]).nice()
    .range([height - margin.bottom, margin.top])

  const color = d3.scaleOrdinal()
    .domain(sexos)
    .range(d3.schemeCategory10)

  // SVG
  const svg = d3.select(chart.value)
    .append('svg')
    .attr('width', width)
    .attr('height', height)

  // Barras
  svg.append('g')
    .selectAll('g')
    .data(dataPivot)
    .join('g')
    .attr('transform', d => `translate(${x0(d.Marca)},0)`)
    .selectAll('rect')
    .data(d => sexos.map(sexo => ({ sexo, quantidade: d[sexo] })))
    .join('rect')
    .attr('x', d => x1(d.sexo))
    .attr('y', d => y(d.quantidade))
    .attr('width', x1.bandwidth())
    .attr('height', d => y(0) - y(d.quantidade))
    .attr('fill', d => color(d.sexo))

  // Eixo X
  svg.append('g')
    .attr('transform', `translate(0,${height - margin.bottom})`)
    .call(d3.axisBottom(x0))
    .selectAll('text')
    .attr('transform', 'rotate(-30)')
    .style('text-anchor', 'end')

  // Eixo Y
  svg.append('g')
    .attr('transform', `translate(${margin.left},0)`)
    .call(d3.axisLeft(y))

  // Legenda
  svg.append('g')
    .attr('transform', `translate(${width - margin.right - 100},${margin.top})`)
    .selectAll('rect')
    .data(sexos)
    .join('rect')
    .attr('y', (d, i) => i * 20)
    .attr('width', 15)
    .attr('height', 15)
    .attr('fill', d => color(d))

  svg.append('g')
    .attr('transform', `translate(${width - margin.right - 80},${margin.top})`)
    .selectAll('text')
    .data(sexos)
    .join('text')
    .attr('y', (d, i) => i * 20 + 12)
    .text(d => d)
    .attr('font-size', 12)
}
</script>
