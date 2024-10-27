<script setup>
import { ref, onMounted, watch } from 'vue';
import { Chart, registerables } from 'chart.js';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';

let visitorsViewsChart = null;
let engagementRateChart = null;

function goToEditWebsite1(){
    Inertia.visit(route('editWebsite1'));
}

function goToPreviewHomePage(){
        Inertia.visit(route('preview_homepage'));
}

Chart.register(...registerables);

const selectedPeriod = ref(7);
const views = ref(0);
const visits = ref(0);
const visitors = ref(0);
const bounceRate = ref(0);
const avgVisitTime = ref('');
const engagementRate = ref(0);

const dailyData = ref([]);

const fetchAnalyticsData = async () => {
    try {
        const { data } = await axios.get(`/api/analytics`, {
            params: { period: selectedPeriod.value },
        });

        views.value = data.metricsData.views;
        visits.value = data.metricsData.visits;
        visitors.value = data.metricsData.visitors;
        bounceRate.value = data.metricsData.bounceRate;
        avgVisitTime.value = data.metricsData.avgVisitTime;

        engagementRate.value = data.metricsData.engagementRate;

        dailyData.value = data.dailyData;

        renderVisitorsViewsChart();
        renderEngagementRateChart();
    } catch (error) {
        console.error("Error fetching analytics data", error);
    }
};

const renderVisitorsViewsChart = () => {
    // If chart already exists, destroy it before creating a new one
    if (visitorsViewsChart) {
        visitorsViewsChart.destroy();
    }

    const labels = dailyData.value.map(data => data.date);
    const viewsData = dailyData.value.map(data => data.views);
    const visitorsData = dailyData.value.map(data => data.visitors);

    const ctx = document.getElementById("visitorsViewsChart").getContext("2d");
    visitorsViewsChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Visitors",
                    data: visitorsData,
                    backgroundColor: "#5E9FF2",
                    borderColor: "#5E9FF2",
                    borderWidth: 1,
                    stack: 'combined',
                },
                {
                    label: "Views",
                    data: viewsData,
                    backgroundColor: "#3F6CA6",
                    borderColor: "#3F6CA6",
                    borderWidth: 1,
                    stack: 'combined',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    beginAtZero: true,
                    stacked: true,
                },
            },
        plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                    labels: {
                        usePointStyle: true, // Use circles instead of rectangles for legend icons
                        pointStyle: 'circle',
                    },
                },
            },
        },
    });
};

const renderEngagementRateChart = () => {
    if (engagementRateChart) {
        engagementRateChart.destroy();
    }

    const labels = dailyData.value.map(data => data.date);
    const engagementRateData = dailyData.value.map(data => data.engagementRate);

    const ctx = document.getElementById("engagementRateChart").getContext("2d");
    engagementRateChart = new Chart(ctx, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Engagement Rate",
                    data: engagementRateData,
                    backgroundColor: "#E0DFFD",
                    borderColor: "#27378F",
                    fill: true,
                    tension: 0.1,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        maxRotation: 15, // Set max rotation
                        minRotation: 15, // Set min rotation to make them diagonal
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => `${value}%`,
                    },
                },
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom',
                },
            },
        },
    });
};

watch(selectedPeriod, async () => {
    await fetchAnalyticsData();  // Fetch new data based on selected period
    renderVisitorsViewsChart();  // Re-render the chart with new data
    renderEngagementRateChart();
});


// Fetch data initially and refetch on period change
onMounted(fetchAnalyticsData);

</script>



<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- Top stats section -->
        <div class="mt-4 px-8 border-b border-black">
            <div class="grid grid-cols-6 gap-4 text-center">
                <div>
                    <h2 class="text-3xl font-extrabold">{{ views }}</h2>
                    <p class="font-bold">Views</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold">{{ visits }}</h2>
                    <p class="font-bold">Visits</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold">{{ visitors }}</h2>
                    <p class="font-bold">Visitors</p>
                </div>
                <div>
                    <h2 class="text-3xl font-extrabold">{{ bounceRate }}</h2>
                    <p class="font-bold">Bounce Rate</p>
                </div>
                <div>
                    <h2 class="text-3xl font-bold">{{ avgVisitTime }}</h2>
                    <p class="font-bold">Avg Visit Time</p>
                </div>
                <div>
                    <select v-model="selectedPeriod" class="ml-4 p-2 pr-8 border rounded">
                        <option value="7">7 Days</option>
                        <option value="14">14 Days</option>
                        <option value="28">28 Days</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Visitors & Views and Retention Rate Charts side by side -->
        <div class="flex flex-wrap justify-center px-8 space-x-4"> 
            <!-- Visitors & Views Chart -->
            <div class="custom-chart-width p-4 border border-black rounded-lg mt-10"> <!-- Add padding, border, and rounded corners -->
                <canvas id="visitorsViewsChart" class="w-full" style="height: 300px;"></canvas> <!-- Set height using inline style -->
            </div>

            <!-- Retention Rate Chart -->
            <div class="custom-chart-width p-4 border border-black rounded-lg mt-10"> <!-- Add padding, border, and rounded corners -->
                <canvas id="engagementRateChart" class="w-full" style="height: 300px;"></canvas> <!-- Set height using inline style -->
            </div>
        </div>



        <!-- Footer buttons -->
        <div class="fixed bottom-0 left-0 right-0 flex justify-center mb-10">
            <button class="px-6 py-4 bg-black text-white mr-2" @click="goToPreviewHomePage">Preview Website</button>
            <button class="px-6 py-4 bg-black text-white" @click="goToEditWebsite1">Edit Website</button>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.custom-chart-width {
        width: 580px;
    }
</style>