<script setup>
import { Link } from '@inertiajs/vue3';
import { ArchiveBoxIcon, BuildingLibraryIcon, BuildingStorefrontIcon, ChatBubbleLeftRightIcon, CloudArrowDownIcon, GlobeAltIcon, HomeIcon, ReceiptPercentIcon } from '@heroicons/vue/24/solid';
import { onMounted, ref } from 'vue';
import axios from 'axios';

// Business object with reactive data
const business = ref({
    business_id: null,
    name: '',
    image: null,
});

// Reference for the business logo picture
const LogoPic = ref(null);

onMounted(async () => {
  try {
    // First, check if data is already in localStorage
    const storedBusinessName = localStorage.getItem('business_name');
    const storedBusinessLogo = localStorage.getItem('business_logo');

    // If the business name and logo are already in localStorage, use them
    if (storedBusinessName && storedBusinessLogo) {
      business.value.name = storedBusinessName;
      LogoPic.value = storedBusinessLogo;
      console.log('Using stored business name and logo:', storedBusinessName, storedBusinessLogo);
      return;  // Exit early since the data is already stored
    }

    // Fetch data from API if it's not in localStorage
    const response = await axios.get('/api/Business');
    console.log('API Response:', response.data); // Log the API response

    if (response.data && response.data.length > 0) {
      const businessData = response.data[0];
      console.log('Fetched Business Data:', businessData); // Log the fetched business data

      // Set the business name and logo from the API response
      business.value = {
        name: businessData.business_Name || 'Default Business Name',
        image: businessData.business_image || 'default_logo.png',
      };

      // Generate the logo URL
      const logoUrl = `/storage/business_logos/${business.value.image}`;
      LogoPic.value = logoUrl;

      // Store the business name and logo in localStorage for future use
      localStorage.setItem('business_name', business.value.name);
      localStorage.setItem('business_logo', logoUrl);

      console.log('Stored business data in localStorage:', business.value.name, logoUrl);
    } else {
      console.error("No business data found.");
    }
  } catch (error) {
    console.error("Error fetching business data:", error);
  }
});
</script>


<template>
    <div class="sidebar fixed sm:relative sm:translate-x-0 -translate-x-full">
        <div class="logo">
        <img v-if="LogoPic" :src="LogoPic" alt="Business Logo" class="logo-image" />
        <h1>{{ business.name }}</h1>
        </div>

        <nav>
            <ul>
                <li class="flex items-center mb-4">
                    <Link :href="route('home')" :class="{ active: route().current('home') }">
                        <HomeIcon class="size-6"/>
                        <span class="ml-3">Home</span>
                    </Link>
                </li>
                <li class="flex items-center mb-4">
                    <Link :href="route('website')" :class="{ active: route().current('website') }">
                        <GlobeAltIcon class="size-6"/>
                        <span class="ml-3">Website</span>
                    </Link>
                </li>
                <li class="flex items-center mb-4">
                    <Link :href="route('chats')" :class="{ active: route().current('chats') }">
                        <ChatBubbleLeftRightIcon class="size-6"/>
                        <span class="ml-3">Chats</span>
                    </Link>
                </li>
                <li class="flex items-center mb-4">
                    <Link :href="route('inventory')" :class="{ active: route().current('inventory') }">
                        <ArchiveBoxIcon class="size-6"/>
                        <span class="ml-3">Inventory</span>
                    </Link>
                </li>
                <li class="flex items-center mb-4">
                    <Link :href="route('receipt')" :class="{ active: route().current('receipt') }">
                        <ReceiptPercentIcon class="size-6"/>
                        <span class="ml-3">Receipt</span>
                    </Link>
                </li>
                <li class="flex items-center mb-4">
                    <Link :href="route('finance')" :class="{ active: route().current('finance') }">
                        <BuildingLibraryIcon class="size-6"/>
                        <span class="ml-3">Finance</span>
                    </Link>
                </li>
                <li class="flex items-center mb-4">
                    <Link :href="route('BusinessInfo')" :class="{ active: route().current('BusinessInfo') }">
                        <BuildingStorefrontIcon class="size-6"/>
                        <span class="ml-3">Business Info</span>
                    </Link>
                </li>
                <li class="flex items-center mb-4">
                    <Link :href="route('BusinessInfo')" :class="{ active: route().current('BusinessInfo') }">
                        <CloudArrowDownIcon class="size-6"/>
                        <span class="ml-3">Backup </span>
                    </Link>
                </li>
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
    margin-top: 2rem;
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
    margin-left:50px; 
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
.logo-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
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
