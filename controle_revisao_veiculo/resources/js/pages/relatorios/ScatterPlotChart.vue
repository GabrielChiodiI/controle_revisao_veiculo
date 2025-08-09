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
  const height = 400
  const margin = { top: 40, right: 30, bottom: 70, left: 70 }

  // Filtra clientes com próxima revisão
  const validData = props.data
    .filter(d => d['Proxima Revisão'] !== 'Apenas uma revisão realizada!')
    .map(d => ({
      Cliente: d.Cliente,
      Data: d3.timeParse('%d/%m/%Y')(d['Proxima Revisão'])
    }))

  // Clientes sem próxima revisão
  const semRevisao = props.data
    .filter(d => d['Proxima Revisão'] === 'Apenas uma revisão realizada!')

  // Eixo X: Clientes
  const clientes = props.data.map(d => d.Cliente)
  const x = d3.scalePoint()
    .domain(clientes)
    .range([margin.left, width - margin.right])
    .padding(0.5)

  // Eixo Y: Datas
  const minDate = d3.min(validData, d => d.Data)
  const maxDate = d3.max(validData, d => d.Data)
  const y = d3.scaleTime()
    .domain([minDate, maxDate])
    .range([height - margin.bottom, margin.top])

  const svg = d3.select(chart.value)
    .append('svg')
    .attr('width', width)
    .attr('height', height)

  // Eixo X
  svg.append('g')
    .attr('transform', `translate(0,${height - margin.bottom})`)
    .call(d3.axisBottom(x))
    .selectAll('text')
    .attr('transform', 'rotate(-30)')
    .style('text-anchor', 'end')

  // Eixo Y
  svg.append('g')
    .attr('transform', `translate(${margin.left},0)`)
    .call(d3.axisLeft(y).tickFormat(d3.timeFormat('%d/%m/%Y')))

  // Pontos dos clientes com data
  svg.append('g')
    .selectAll('circle')
    .data(validData)
    .join('circle')
    .attr('cx', d => x(d.Cliente))
    .attr('cy', d => y(d.Data))
    .attr('r', 8)
    .attr('fill', '#4285f4')

  // Marca os clientes sem próxima revisão com um "X"
  svg.append('g')
    .selectAll('text.sem')
    .data(semRevisao)
    .join('text')
    .attr('x', d => x(d.Cliente))
    .attr('y', height - margin.bottom - 10)
    .attr('text-anchor', 'middle')
    .attr('fill', 'red')
    .attr('font-size', 24)
    .text('✗')
}
</script>

