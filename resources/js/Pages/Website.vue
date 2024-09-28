<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { route } from 'ziggy-js';

function goToEditWebsite1(){
    Inertia.visit(route('editWebsite1'));
}
function goToPreviewHomePage(){
    Inertia.visit(route('preview_homepage'));
}

</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <div class="fixed bottom-0 left-0 right-0 flex justify-center mb-20">
            <button class="px-6 py-4 bg-black text-white mr-2" @click="goToPreviewHomePage">Preview Website</button>
            <button class="px-6 py-4 bg-black text-white" @click="goToEditWebsite1">Edit Website</button>
        </div>
    </AuthenticatedLayout>
</template>
