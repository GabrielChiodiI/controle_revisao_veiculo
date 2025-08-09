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

function transformData(data) {
  // Agrupa os veículos por pessoa em um formato hierárquico
  const root = { name: "Pessoas", children: [] }
  const map = {}

  data.forEach(d => {
    if (!map[d.Pessoa]) {
      const node = { name: d.Pessoa, children: [] }
      root.children.push(node)
      map[d.Pessoa] = node
    }
    map[d.Pessoa].children.push({ name: d.Veiculo, value: 1 })
  })

  return root
}

function render() {
  d3.select(chart.value).selectAll('*').remove()
  if (!props.data || props.data.length === 0) return

  const width = 700, height = 300

  const hierarchy = d3.hierarchy(transformData(props.data))
    .sum(d => d.value)

  const partition = d3.partition()
    .size([width, height])
  partition(hierarchy)


  const svg = d3.select(chart.value)
    .append('svg')
    .attr('width', '100%')
    .attr('height', height)
    .attr('viewBox', `0 0 ${width} ${height}`)

  const color = d3.scaleOrdinal(d3.schemeCategory10)

  svg.selectAll('rect')
    .data(hierarchy.descendants())
    .join('rect')
    .attr('x', d => d.x0)
    .attr('y', d => d.y0)
    .attr('width', d => d.x1 - d.x0)
    .attr('height', d => d.y1 - d.y0)
    .attr('fill', d => color(d.depth))
    .attr('stroke', '#fff')

  svg.selectAll('text')
    .data(hierarchy.descendants().filter(d => d.depth > 0))
    .join('text')
    .attr('text-anchor', 'middle')
    .attr('font-size', '10px')
    .attr('fill', '#000')
    .attr('x', d => (d.x0 + d.x1) / 2)
    .attr('y', d => d.y0 + ((d.y1 - d.y0) / 2))
    .attr('transform', d => {
        const x = (d.x0 + d.x1) / 2
        const y = d.y0 + ((d.y1 - d.y0) / 2)
        return `rotate(-90, ${x}, ${y})`
    })
    .text(d => (d.x1 - d.x0 > 15 ? d.data.name : ''))

}
</script>