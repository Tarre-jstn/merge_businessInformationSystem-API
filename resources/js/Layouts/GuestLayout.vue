<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

let businessImage = ref('');
let businessName = ref('');
let isLoading = ref(true);
onMounted(()=>{
    getWebsiteInfo();
    });

async function getWebsiteInfo(){

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });
        console.log(getBusinessInfo.data);

        if(getBusinessInfo.data.business_image){
        businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
        isLoading.value = false;
    }
        businessName.value = getBusinessInfo.data.business_Name;
        
    }
</script>

<template>
    <div class="min-h-screen flex flex-row gap-x-[280px] sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900 backdrop-blur-md bg-opacity-50">
        <div class="-mt-10">
            <Link href="/">
                <h1 class="mb-[20px] text-[35px] text-white text-center font-Montserrat">{{businessName}}</h1>
                <div class="relative">
                    <div class="absolute w-[400px] h-[400px] rounded-full bg-transparent -z-10"
                    style="box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);"></div>
                <img v-if="isLoading" src='/storage/business_logos/default-profile.png'/>
                <img v-else-if="businessImage" :src='businessImage' class="w-[400px] h-[400px] object-cover border border-black border-[4px] rounded-full"/>
                <img v-else src='/storage/business_logos/default-profile.png'/>
                </div>
            </Link>
        </div>

        <div
            class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg"
        >
            <slot />
        </div>
    </div>
</template>
