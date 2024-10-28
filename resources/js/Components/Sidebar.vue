<script setup>
import { Link } from '@inertiajs/vue3';
import { ArchiveBoxIcon, BuildingLibraryIcon, ChatBubbleLeftRightIcon, GlobeAltIcon, HomeIcon, ReceiptPercentIcon, Cog6ToothIcon, ArrowDownTrayIcon  } from '@heroicons/vue/24/solid'
import { onMounted, ref } from 'vue';
import { useBusinessStore } from '../store/businessStore';
import { useReloadStore } from '../store/useReloadStore';
import axios from 'axios';



const businessImage = ref('');
const businessName = ref('');


const businessStore = useBusinessStore();
const reloadStore = useReloadStore();
const { state } = businessStore;

onMounted(() => {
  businessStore.fetchBusinessData();
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

    function navigateAndHiddenReload(routeName) {
    // Check reloadOnce from Pinia store
    if (reloadStore.reloadOnce === 0) {
        reloadStore.setReloadOnce(1); // Update the store to prevent further reloads
        console.log("ReloadOnce updated to:", reloadStore.reloadOnce);
        // Navigate to the new page
        window.location.href = route(routeName);

        // Set up a listener for when the page becomes hidden (during reload)
        document.addEventListener('visibilitychange', function listener() {
            if (document.visibilityState === 'hidden') {
                document.removeEventListener('visibilitychange', listener);
                window.location.reload(true); // Reload the page
            }
        });
    } else {
        // Just navigate without reloading if `reloadOnce` is already set to 1
        window.location.href = route(routeName);
    }
}


// Reset the reloadOnce counter when you navigate away or on another condition
function resetReloadOnce() {
    localStorage.removeItem('reloadOnce');
}


</script>

<template>
    <div class="sidebar dark:bg-gray-800 fixed sm:relative sm:translate-x-0 -translate-x-full">
        <div class="logo text-white">
            <div class="items-center flex flex-col text-center pt-5 px-10" v-if="state.isLoading">
                <img :src="'/storage/business_logos/' + state.businessImage" :alt="state.businessName">
                    <p>Default Name</p>
            </div>
            <div v-else class="items-center flex flex-col text-center pt-5" >
                <img :src="'/storage/business_logos/' + state.businessImage" :alt="state.businessName">
                <h1>{{ state.businessName }}</h1>
            </div>
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

                <li class="flex items-center mb-4">
                    <a href="#" @click.prevent="navigateAndHiddenReload('inventory')" :class="{ active: route().current('inventory') }">
                        <ArchiveBoxIcon class="size-6"/>
                        <span class="ml-3">Inventory</span>
                    </a>
                </li> 

                <li class="flex items-center mb-4"><Link :href="route('invoice')" :class="{ active: route().current('invoice') }">
                    <ReceiptPercentIcon class="size-6"/>
                    <span class="ml-3">Invoice</span>
                </Link></li>

                <li class="flex items-center mb-4"><Link :href="route('finance')" :class="{ active: route().current('finance') }">
                    <BuildingLibraryIcon class="size-6"/>
                    <span class="ml-3">Finance</span>
                </Link></li>

                <!-- <li class="flex items-center mb-4">
                    <a href="#" @click.prevent="navigateAndHiddenReload('invoice')" :class="{ active: route().current('invoice') }">
                        <ArchiveBoxIcon class="size-6"/>
                        <span class="ml-3">Invoice</span>
                    </a>
                </li> 

                <li class="flex items-center mb-4">
                    <a href="#" @click.prevent="navigateAndHiddenReload('finance')" :class="{ active: route().current('finance') }">
                        <BuildingLibraryIcon class="size-6"/>
                        <span class="ml-3">Finance</span>
                    </a>
                </li> -->

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
    transition: all 0.3s ease;
    z-index: 100;
    height: 150vh;
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
    background-color: white;
}
.logo h1 {
    font-size: 1.5em;
    margin: 0; 
}

.logo p {
    font-size: 1.5em;
    margin: 0; 
}

.active {
    background-color: #ddd;
    color: #202c34;
}



</style>