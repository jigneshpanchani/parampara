<template>
  <div class="bg-white p-4 cursor-pointer hover:shadow-md shadow rounded-lg">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
  chartData: {
    type: Object,
    required: true
  },
  chartOptions: {
    type: Object,
    default: () => ({})
  }
})

const chartCanvas = ref(null)
let chartInstance = null

const renderChart = () => {
  if (chartInstance) {
    chartInstance.destroy()
  }

  chartInstance = new Chart(chartCanvas.value, {
    type: 'line', // ✅ Changed to 'line'
    data: props.chartData,
    options: {
      ...props.chartOptions,
      responsive: true,
      interaction: {
        mode: 'index',      // ✅ Show all engineer data on same x-axis
        intersect: false    // ✅ Doesn't require pointer to be on a point
      },
      plugins: {
        tooltip: {
          mode: 'index',
          intersect: false,
          callbacks: {
                label: function(context) {
                const value = context.parsed.y || 0;
                return `${context.dataset.label}: ₹${value.toLocaleString('en-IN')}`;
                }
            }
        },
        legend: {
          position: 'top'
        },
        title: {
          display: true,
          text: 'Engineer Sales (April to March)'
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  })
}

onMounted(() => {
  renderChart()
})

watch(() => props.chartData, () => {
  renderChart()
})
</script>
