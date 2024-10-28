<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

let businessImage = ref('');
let businessName = ref('');
let isLoading = ref(true);

onMounted(() => {
    getWebsiteInfo();
});

async function getWebsiteInfo() {
    const getBusinessInfo = await axios.get('/api/business_info', {
        params: { user_id: 1 }
    });
    console.log(getBusinessInfo.data);

    if (getBusinessInfo.data.business_image) {
        businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
        isLoading.value = false;
    }
    businessName.value = getBusinessInfo.data.business_Name;
}
</script>

<template>
    <div style="background-color: #0F2C4A;"><br><br></div>
    <div class="min-h-screen flex flex-col justify-center sm:justify-center items-center pt-6 sm:pt-0 md:flex-row shadow-2xl" 
         style="background: linear-gradient(90deg, rgba(15,44,74,1) 0%, rgba(32,75,120,1) 50%, rgba(13,76,89,1) 100%); color: #0F2C4A; justify-content: space-evenly;">
        <div class="max-w-lg h-auto flex flex-col items-center">
            <Link href="/" class="max-w-lg h-auto flex flex-col items-center">
            <div class="text-white font-bold text-5xl mb-6">
                {{ businessName }} 
            </div>
            <div class="relative">
                <div class="absolute w-[400px] h-[400px] rounded-full bg-transparent -z-10"
                     style="box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);"></div>
                <img v-if="isLoading" src='/storage/business_logos/default-profile.png' 
                     class="hidden md:block rounded-full shadow-2xl shadow-slate-950" 
                     style="border-color: #081626; border-width: 10px; background-color: #FFFFFF;" />
                <img v-else-if="businessImage" :src='businessImage' 
                     class="hidden md:block rounded-full shadow-2xl shadow-slate-950" 
                     style="border-color: #081626; border-width: 10px; background-color: #FFFFFF;" />
                <img v-else src='/storage/business_logos/default-profile.png' 
                     class="hidden md:block rounded-full shadow-2xl shadow-slate-950" 
                     style="border-color: #081626; border-width: 10px; background-color: #FFFFFF;" />
            </div>
            </Link>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-20 max-w-xl shadow-2xl shadow-slate-950 overflow-hidden sm:rounded-3xl" 
             style="background-color: #FFFFFF; max-width: 40rem; color: #0F2C4A; border-width: 5px; border-color: #081626;">
            <slot />
        </div>
    </div>
    <div style="background-color: #0F2C4A;"><br><br></div>
</template>
