<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

export default {
    name:'BarChart',
    components:{ Bar },
    data() {
        return {
            chartData: {
                labels: ['January', 'February', 'March'],
                datasets: [
                    {
                        label: 'Data One',
                        backgroundColor: '#f87979',
                        data: [40, 20, 12]
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }
    }
}
</script>

<template>
    <div class="chart-container">
        <Bar id="somechart" :chart-data="chartData" :options="options" :data="chartData"/>
    </div>
    
</template>

<style>
.chart-container {
  width: 50%; /* or specify a fixed width like 400px */
  height: 400px; /* or any height you need */
  border: 1px solid #000000;
}
</style>