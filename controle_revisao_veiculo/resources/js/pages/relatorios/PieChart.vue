<template>
  <div ref="chart"></div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import * as d3 from 'd3'

const props = defineProps({
  data: { type: Array, required: true }
})

const chart = ref(null)

function renderChart() {
  d3.select(chart.value).selectAll('*').remove()

  if (!props.data || !props.data.length) return

  const width = 400, height = 320, radius = Math.min(width, height) / 2 - 20

  const svg = d3.select(chart.value)
    .append('svg')
    .attr('width', width)
    .attr('height', height)
    .append('g')
    .attr('transform', `translate(${width / 2},${height / 2})`)

  const color = d3.scaleOrdinal(d3.schemeCategory10)

  const pie = d3.pie()
    .sort(null)
    .value(d => d.Quantidade)

  const data_ready = pie(props.data)

  const arc = d3.arc()
    .innerRadius(0)
    .outerRadius(radius)

  // Desenha as fatias
  svg.selectAll('path')
    .data(data_ready)
    .join('path')
    .attr('d', arc)
    .attr('fill', (d, i) => color(i))
    .attr('stroke', '#fff')
    .attr('stroke-width', 1.5)

  // Adiciona os rótulos (marca)
  svg.selectAll('text')
    .data(data_ready)
    .join('text')
    .attr('transform', d => {
        // Pega centro do arco, empurra 25% além do raio original
        const [x, y] = d3.arc().innerRadius(0).outerRadius(radius * 1.90).centroid(d)
        return `translate(${x},${y})`
    })
    .attr('dy', '0.35em')
    .attr('font-size', '13px')
    .attr('text-anchor', 'middle')
    .text(d => d.data.Marca)
}

watch(() => props.data, renderChart, { deep: true })
onMounted(renderChart)
</script>
