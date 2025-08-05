<script setup>
import { onMounted, watch, ref } from 'vue'
import * as d3 from 'd3'

const props = defineProps({
  data: { type: Array, required: true }
})

const chartRef = ref(null)

function renderChart() {
  const data = props.data
  const width = 400
  const height = 300
  const margin = { top: 20, right: 20, bottom: 40, left: 40 }

  d3.select(chartRef.value).selectAll('*').remove()

  const svg = d3.select(chartRef.value)
    .append('svg')
    .attr('width', width)
    .attr('height', height)

  const x = d3.scaleBand()
    .domain(data.map(d => d.sexo))
    .range([margin.left, width - margin.right])
    .padding(0.3)

  const y = d3.scaleLinear()
    .domain([0, d3.max(data, d => d.total)])
    .nice()
    .range([height - margin.bottom, margin.top])

  svg.append('g')
    .selectAll('rect')
    .data(data)
    .join('rect')
      .attr('x', d => x(d.sexo))
      .attr('y', d => y(d.total))
      .attr('height', d => y(0) - y(d.total))
      .attr('width', x.bandwidth())
      .attr('fill', '#2062ab')

  svg.append('g')
    .attr('transform', `translate(0,${height - margin.bottom})`)
    .call(d3.axisBottom(x))

  svg.append('g')
    .attr('transform', `translate(${margin.left},0)`)
    .call(d3.axisLeft(y))

  svg.selectAll('.label')
    .data(data)
    .enter()
    .append('text')
      .attr('class', 'label')
      .attr('x', d => x(d.sexo) + x.bandwidth() / 2)
      .attr('y', d => y(d.total) - 6)
      .attr('text-anchor', 'middle')
      .attr('fill', '#222')
      .attr('font-size', '13px')
      .text(d => d.total)
}

onMounted(renderChart)
watch(() => props.data, renderChart)
</script>

<template>
  <div ref="chartRef"></div>
</template>
