<template>
    <div class="bg-white p-4 cursor-pointer hover:shadow-md shadow rounded-lg">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </template>

  <script setup>
  import { ref, onMounted , watch } from 'vue';
  import { Chart, registerables } from 'chart.js';

  Chart.register(...registerables);

  const props = defineProps({
    chartData: {
      type: Object,
      required: true
    },
    chartOptions: {
      type: Object,
      default: () => {}
    }
  });

  const chartCanvas = ref(null);
  let chartInstance = null;

const renderChart = () => {
  if (chartInstance) {
    chartInstance.destroy(); // Destroy the previous chart instance
  }

  chartInstance = new Chart(chartCanvas.value, {
    type: 'bar', // Specify the type of chart
    data: props.chartData,
    options: props.chartOptions
  });
};

onMounted(() => {
  renderChart(); // Render the chart initially
});

watch(() => props.chartData, (newData) => {
  renderChart(); // Re-render the chart with the new data
});

  </script>
