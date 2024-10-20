<script setup>
import { Link } from '@inertiajs/vue3';
import { ArchiveBoxIcon, BuildingLibraryIcon, ChatBubbleLeftRightIcon, GlobeAltIcon, HomeIcon, ReceiptPercentIcon, Cog6ToothIcon, ArrowDownTrayIcon  } from '@heroicons/vue/24/solid'
import { onMounted, ref } from 'vue';

let businessImage = ref('');
let businessName = ref('');
let isLoading = ref(true);
onMounted(()=>{
    getWebsiteInfo();
    });

async function getWebsiteInfo(){
    const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log(userId);

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
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
    <div class="sidebar fixed sm:relative sm:translate-x-0 -translate-x-full">
        <div class="logo">
            <img v-if="isLoading" src='/storage/business_logos/default-profile.png'/>
            <img v-else-if="businessImage" :src='businessImage' alt="Logo" />
            <img v-else src='/storage/business_logos/default-profile.png'/>
            <h1>{{businessName}}</h1>
        </div>
        <nav>
            <ul>
                <li class="flex items-center mb-4"><Link :href="route('home')" :class="{ active: route().current('home') }">
                    <HomeIcon class="size-6"/>
                    <span class="ml-3">Home</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('website')" :class="{ active: route().current('website') }">
                    <GlobeAltIcon class="size-6"/>
                    <span class="ml-3">Website</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('chats')" :class="{ active: route().current('chats') }">
                    <ChatBubbleLeftRightIcon class="size-6"/>
                    <span class="ml-3">Chats</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('inventory')" :class="{ active: route().current('inventory') }">
                    <ArchiveBoxIcon class="size-6"/>
                    <span class="ml-3">Inventory</span>
                </Link></li> 

                <li class="flex items-center mb-4"><Link :href="route('invoice')" :class="{ active: route().current('receipt') }">
                    <ReceiptPercentIcon class="size-6"/>
                    <span class="ml-3">Receipt</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('finance')" :class="{ active: route().current('finance') }">
                    <BuildingLibraryIcon class="size-6"/>
                    <span class="ml-3">Finance</span>
                </Link></li>
                <li class="flex items-center mb-4"><Link :href="route('BusinessInfo')" :class="{ active: route().current('BusinessInfo') }">
                    <Cog6ToothIcon class="size-6"/>
                    <span class="ml-2">Business Information</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('backup-main')" :class="{ active: route().current('backup-main') }">
                    <ArrowDownTrayIcon class="size-6"/>
                    <span class="ml-2">Backup and<br>Restore</span>
                </Link></li>
            </ul>
        </nav>
    </div>
</template>

<style scoped>
.sidebar {
    width: 250px;
    background-color: #202c34;
    transition: all 0.3s ease;
    z-index: 100;
    height: 100vh;
    box-shadow: 2px 0px 12px rgba(0, 0, 0, 0.1);
}


.sidebar h1 {
    font-size: 1.25rem;
    color: #f0f0f0;
    text-align: center;
    margin-top: 1rem;
}

nav ul {

    margin-top: 1rem;
    display: flex;
    flex-direction: column; 
    align-items: center; 
}

nav ul li {
    list-style-type: none;
    margin: 0;
    width: 100%; 
}

nav ul li a {
    width: 100%;
    justify-content: flex-start;
    text-decoration: none;
    color: #f0f0f0;
    font-size: 1rem;
    display: flex;
    align-items: center;
    padding-left: 50px;
    padding: 0.75rem 1rem;
    transition: background-color 0.2s ease-in-out;
}

nav ul li a svg {
    margin-right: 10px;
    margin-left:37px; 
    width: 30px;
    height: 30px;
}

nav ul li a span {
    display: inline-block; 
    width: calc(100% - 50px); 
    text-align: left; 
}

nav ul li a.active { 
    width: 100%;
    background-color: #ddd;
    color: #202c34;
    
}

nav ul li a:hover {
    width: 100%;
    background-color: #ddd;
    color: #202c34;
}


.logo {
    text-align: center;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center; 
}
.logo img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin-bottom: 10px; 
}
.logo h1 {
    font-size: 1.5em;
    margin: 0; 
}
.active {
    background-color: #ddd;
    color: #202c34;
}



</style>