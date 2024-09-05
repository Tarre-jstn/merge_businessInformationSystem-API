<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">Welcome to the Home Page!</div>
                </div>
            </div>
        </div>
        <div>
        <BarChart />
    </div>
    <div>
        <vue-cal hide-view-selector :time="false" active-view="month" xsmall>
            <template #arrow-prev>
                <i class="icon material-icons">arrow_back</i>
            </template>
            <template #arrow-next>
                <i class="icon material-icons">arrow_forward</i>
            </template>
        </vue-cal>
    </div>
    </AuthenticatedLayout>



</template>
<script>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import BarChart from '@/Components/BarChart.vue';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';

export default {
    name: 'App',
    components: { BarChart, AuthenticatedLayout , VueCal}
  }
</script>