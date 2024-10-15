<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Reactive Data
const business = ref({
    business_id: null,
    name: '',
    email: '',
    province: '',
    city: '',
    barangay: '',
    address: '',
    phone: '',
    telephone: '',
    facebook: '',
    x: '',
    instagram: '',
    tiktok: '',
    image: null,
});

const profilePicture = ref(null);
const provinces = ref([]);
const filteredCities = ref([]);
const filteredBarangays = ref([]);
const loading = ref(true);

// Fetch Provinces
const fetchProvinces = async () => {
    try {
        const regions = ['01', '02', '03', '05', '13'];
        const response = await axios.get('https://psgc.gitlab.io/api/provinces');
        provinces.value = response.data.filter(province => regions.includes(province.regionCode.slice(0, 2)));
    } catch (error) {
        console.error("Error fetching provinces:", error);
    }
};

// Fetch Cities
const fetchCities = async (provinceCode) => {
    try {
        const response = await axios.get(`https://psgc.gitlab.io/api/provinces/${provinceCode}/cities-municipalities`);
        filteredCities.value = response.data;
        console.log('Fetched cities:', filteredCities.value);
    } catch (error) {
        console.error('Error fetching cities:', error);
    }
};

// Fetch Barangays
const fetchBarangays = async (cityCode) => {
    try {
        const response = await axios.get(`https://psgc.gitlab.io/api/cities-municipalities/${cityCode}/barangays`);
        filteredBarangays.value = response.data;
        console.log('Fetched barangays:', filteredBarangays.value);
    } catch (error) {
        console.error('Error fetching barangays:', error);
    }
};

// Fetch Business Data
const fetchBusinessData = async () => {
    try {
        const response = await axios.get('/api/Business');
        if (response.data && response.data.length > 0) {
            const businessData = response.data[0];
            Object.assign(business.value, {
                business_id: businessData.business_id,
                name: businessData.business_Name,
                email: businessData.business_Email,
                province: businessData.business_Province,
                city: businessData.business_City,
                barangay: businessData.business_Barangay,
                address: businessData.business_Address,
                phone_number: businessData.business_Phone_Number,
                telephone_number: businessData.business_Telephone_Number,
                facebook: businessData.business_Facebook,
                x: businessData.business_X,
                instagram: businessData.business_Instagram,
                tiktok: businessData.business_Tiktok,
                image: businessData.business_image,
            });

            profilePicture.value = businessData.business_image 
                ? `/storage/business_logos/${businessData.business_image}` 
                : '/storage/business_logos/default_logo.png';

            const province = provinces.value.find(p => p.name === business.value.province);
            if (province) {
                await fetchCities(province.code);
                const city = filteredCities.value.find(c => c.name === business.value.city);
                if (city) await fetchBarangays(city.code);
            }
        }
    } catch (error) {
        console.error("Error fetching business data:", error);
        alert('Failed to fetch business profile');
    } finally {
        loading.value = false;
    }
};

// Watch for Province Changes
watch(() => business.value.province, async (newProvince) => {
    if (newProvince) {
        const selectedProvince = provinces.value.find(p => p.name === newProvince);
        if (selectedProvince) {
            await fetchCities(selectedProvince.code);
            business.value.city = '';
            business.value.barangay = '';
            filteredBarangays.value = [];
        }
    }
});

// Watch for City Changes
watch(() => business.value.city, async (newCity) => {
    if (newCity) {
        const selectedCity = filteredCities.value.find(c => c.name === newCity);
        if (selectedCity) {
            await fetchBarangays(selectedCity.code);
            business.value.barangay = '';
        }
    }
});

onMounted(async () => {
    loading.value = true;
    await fetchProvinces();
    await fetchBusinessData();
    loading.value = false;
});

// Handle Profile Picture Change
const onProfilePictureChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        profilePicture.value = URL.createObjectURL(file);
        business.value.image = file;
    } else {
        profilePicture.value = '/storage/business_logos/default_logo.png';
        business.value.image = null;
    }
};

// Update Business
const updateBusiness = async () => {
    if (
        !business.value.name ||
        !business.value.email ||
        !business.value.province ||
        !business.value.city ||
        !business.value.barangay ||
        !business.value.address ||
        !business.value.phone_number ||
        !business.value.telephone_number 
    ) {
        alert('Please fill out all required fields');
        return;
    }

    if (!business.value.business_id) {
        console.error("Business ID is missing!");
        return;
    }

    const formData = new FormData();
    Object.keys(business.value).forEach(key => {
        formData.append(key, business.value[key]);
    });

    try {
        const response = await axios.post(`/api/Business/${business.value.business_id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-HTTP-Method-Override': 'PUT'
            },
        });
        if (response.data.success) {
            alert('Business profile updated successfully');
        } else {
            console.error("Update failed:", response.data);
        }
    } catch (error) {
        console.error("Error updating business data:", error);
        alert('Failed to update business profile');
    }
};
</script>


    <template>
        <AuthenticatedLayout>
            <Head title="Update Business Profile" />

            <div class="container mx-auto p-4 flex flex-col md:flex-row">
                <!-- Left Column (Form Fields) -->
                <div class="w-full md:w-1/2 pt-20">
                    <h1 class="text-3xl font-bold mb-4">Update Business Profile</h1>

                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="business-name" class="block text-gray-700 text-sm font-bold mb-2">
                                    Business Name
                                </label>
                                <input
                                    type="text"
                                    id="business-name"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.name"
                                >
                            </div>

                            <div>
                                <label for="email-address" class="block text-gray-700 text-sm font-bold mb-2">
                                    Email Address
                                </label>
                                <input
                                    type="email"
                                    id="email-address"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.email"
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Province Dropdown -->
                            <div>
                                <label for="province" class="block text-gray-700 text-sm font-bold mb-2">
                                    Province
                                </label>
                                <select v-model="business.province">
                                    <option value="">Select Province</option>
                                    <option v-for="province in provinces" :key="province.code" :value="province.name">
                                        {{ province.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- City Dropdown -->
                            <div>
                                <label for="city" class="block text-gray-700 text-sm font-bold mb-2">
                                    Municipality/City
                                </label>
                                <select v-model="business.city">
                                    <option value="">Select City</option>
                                    <option v-for="city in filteredCities" :key="city.code" :value="city.name">
                                        {{ city.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Barangay Dropdown -->
                            <div>
                                <label for="barangay" class="block text-gray-700 text-sm font-bold mb-2">
                                    Barangay
                                </label>
                                <select v-model="business.barangay">
                                    <option value="">Select Barangay</option>
                                    <option v-for="barangay in filteredBarangays" :key="barangay.code" :value="barangay.name">
                                        {{ barangay.name }}
                                    </option>
                                </select>
                            </div>
                        </div>



                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="address" class="block text-gray-700 text-sm font-bold mb-2">
                                    Address
                                </label>
                                <input
                                    type="text"
                                    id="address"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.address"
                                >
                            </div>

                            <div>
                                <label for="phone-number" class="block text-gray-700 text-sm font-bold mb-2">
                                    Phone Number
                                </label>
                                <input
                                    type="text"
                                    id="phone-number"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.phone_number"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="telephone-number" class="block text-gray-700 text-sm font-bold mb-2">
                                Telephone Number
                            </label>
                            <input
                                type="text"
                                id="telephone-number"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="business.telephone_number"
                            >
                        </div>

                        <div>
                            <button
                                type="button"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                @click="updateBusiness"
                            >
                                Update Business
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Profile Picture and Social Links) -->
                <div class="w-full md:w-1/2 flex flex-col items-center mt-8 md:mt-0 py-0">
                    <div class="relative group">
                        <img :src="profilePicture" alt="Profile Picture" class="w-32 h-32 rounded-full border-4 border-blue-500 object-cover">
                            <input
                                type="file"
                                id="profile-upload"
                                class="absolute inset-0 opacity-0 cursor-pointer z-10" 
                                @change="onProfilePictureChange"
                            >
                        <label for="profile-upload" class="absolute bottom-0 right-0 bg-gray-800 text-white p-1 rounded-full cursor-pointer">
                            <i class="fas fa-camera"></i>
                        </label>
                        <!-- Overlay for hover effect -->
                        <div class="absolute inset-0 pl-4 bg-gray-800 bg-opacity-50 flex items-center justify-center text-white text-sm font-bold opacity-0 rounded-full group-hover:opacity-100 transition-opacity z-0">
                            Please select a profile picture
                        </div>
                    </div>


                    <!-- Social Media Links -->
                    <div class="mt-6 w-3/4 space-y-4">
                        <!-- Facebook Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                Facebook
                            </label>
                            <div class="flex items-center space-x-2">
                               
                                <input type="text" 
                                    placeholder="Facebook @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.facebook"
                                >
                            </div>
                        </div>

                        <!-- X Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                X
                            </label>
                            <div class="flex items-center space-x-2">
                               
                                <input type="text" 
                                    placeholder="X @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.x"
                                >
                            </div>
                        </div>

                        <!-- Instagram Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                Instagram
                            </label>
                            <div class="flex items-center space-x-2">
                               
                                <input type="text" 
                                    placeholder="Instagram @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.instagram"
                                >
                            </div>
                        </div>

                        <!-- TikTok Section -->
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                TikTok
                            </label>
                            <div class="flex items-center space-x-2">
                              
                                <input type="text" 
                                    placeholder="TikTok @username" 
                                    class="w-full h-10 shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.tiktok"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>
