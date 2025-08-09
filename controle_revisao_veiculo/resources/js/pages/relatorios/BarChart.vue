<template>
  <div ref="chartRef"></div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import * as d3 from 'd3'

const props = defineProps({
  data: { type: Array, required: true },
  xKey: { type: String, required: true },
  yKey: { type: String, required: true },
  label: { type: String, default: '' }
})

const chartRef = ref(null)

function renderChart() {
  const margin = { top: 30, right: 30, bottom: 50, left: 60 }
  const width = 480
  const height = 320

  d3.select(chartRef.value).selectAll('*').remove()

  const svg = d3.select(chartRef.value)
    .append('svg')
    .attr('width', width + margin.left + margin.right)
    .attr('height', height + margin.top + margin.bottom)
    .append('g')
    .attr('transform', `translate(${margin.left},${margin.top})`)

  // X axis
  const x = d3.scaleBand()
    .domain(props.data.map(d => d[props.xKey]))
    .range([0, width])
    .padding(0.2)
  svg.append('g')
    .attr('transform', `translate(0,${height})`)
    .call(d3.axisBottom(x))
    .selectAll('text')
    .attr('transform', 'rotate(-40)')
    .style('text-anchor', 'end')

  // Y axis
  const y = d3.scaleLinear()
    .domain([0, d3.max(props.data, d => +d[props.yKey])])
    .range([height, 0])
  svg.append('g')
    .call(d3.axisLeft(y))

  // Bars
  svg.selectAll('rect')
    .data(props.data)
    .enter()
    .append('rect')
    .attr('x', d => x(d[props.xKey]))
    .attr('y', d => y(+d[props.yKey]))
    .attr('width', x.bandwidth())
    .attr('height', d => height - y(+d[props.yKey]))
    .attr('fill', '#3262AB')

  if (props.label) {
    svg.append('text')
      .attr('x', width / 2)
      .attr('y', -10)
      .attr('text-anchor', 'middle')
      .style('font-size', '16px')
      .text(props.label)
  }
}

watch(() => props.data, renderChart, { immediate: true })
onMounted(renderChart)
</script>
