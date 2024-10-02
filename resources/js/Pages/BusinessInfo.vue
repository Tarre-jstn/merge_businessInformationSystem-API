<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
   
    // Business and other reactive properties
    const business = ref({
        business_id: null,
        name: '',
        email: '',
        province: '',
        city: '',
        barangay: '',
        address: '',
        phone_number: '',
        telephone_number: '',
        facebook: '',
        x: '',
        instagram: '',
        tiktok: '',
        image: null,
    });

    // Profile picture
    const profilePicture = ref(null);

    // Predefined provinces, cities, and barangays
    const provinces = ref(['Bulacan', 'Pampanga', 'Tarlac']);
    const citiesByProvince = ref({
        Bulacan: ['Malolos', 'Meycauayan', 'San Jose del Monte'],
        Pampanga: ['Angeles', 'San Fernando'],
        Tarlac: ['Tarlac City', 'Concepcion'],
    });
    const barangaysByCity = ref({
        Malolos: ['Mojon', 'Tikay', 'Santor'],
        Meycauayan: ['Iba', 'Pajo'],
        'San Jose del Monte': ['Kaypian', 'Sapang Palay'],
        Angeles: ['Balibago', 'Pulungbulu'],
        'San Fernando': ['Dolores', 'Sindalan'],
        'Tarlac City': ['San Sebastian', 'Matatalaib'],
        Concepcion: ['San Nicolas', 'Parang'],
    });

    // Filtered data
    const filteredCities = ref([]);
    const filteredBarangays = ref([]);

    // Filter cities based on selected province
    const filterCities = () => {

        filteredBarangays.value = [];
        filteredCities.value = citiesByProvince.value[business.value.province] || [];
    };

    const filterBarangays = () => {
        filteredBarangays.value = barangaysByCity.value[business.value.city] || [];
    };


    // Fetch business data on mount
    onMounted(async () => {
        try {
            const response = await axios.get('/api/Business');
            console.log("API Response:", response.data);

            if (response.data && Array.isArray(response.data) && response.data.length > 0) {
                const businessData = response.data[0];
                business.value = {
                    business_id: businessData.business_id,
                    name: businessData.business_Name,
                    email: businessData.business_Email,
                    province: businessData.business_Province,
                    city: businessData.business_City,
                    barangay: businessData.business_Barangay,
                    address: businessData.business_Address,
                    phone_number: businessData.business_Contact_Number,
                    telephone_number: businessData.business_Telephone_Number,
                    facebook: businessData.business_Facebook,
                    x: businessData.business_X,
                    instagram: businessData.business_Instagram,
                    tiktok: businessData.business_Tiktok,
                    image: businessData.business_image,
                };

                // Set profile picture
                profilePicture.value = businessData.business_image 
                    ? `/storage/business_logos/${businessData.business_image}` 
                    : '/storage/business_logos/default-profile.png';

                await filterCities();
                await filterBarangays();
            } else {
                console.error("No business data found or invalid format.");
            }
        } catch (error) {
            console.error("Error fetching business data:", error);
            alert('Failed to fetch business profile');
        }
    });
    // Handle profile picture change
    const onProfilePictureChange = (event) => {
        const file = event.target.files[0];
        if (file) {
            profilePicture.value = URL.createObjectURL(file);
            business.value.image = file;
        } else {
            profilePicture.value = '/storage/business_logos/defaultprofile.jpg';
            business.value.image = null;
        }
    };

    const updateBusiness = async () => {
    console.log("Business data before update:", business.value); 
    if (
        !business.value.name ||
        !business.value.email ||
        business.value.province === 'none' ||
        business.value.city === 'none' ||
        business.value.barangay === 'none' ||
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
    formData.append('business_Name', business.value.name.trim());
    formData.append('business_Email', business.value.email.trim());
    formData.append('business_Province', business.value.province.trim());
    formData.append('business_City', business.value.city.trim());
    formData.append('business_Barangay', business.value.barangay.trim());
    formData.append('business_Address', business.value.address.trim());
    formData.append('business_Contact_Number', business.value.phone_number.trim());
    formData.append('business_Telephone_Number', business.value.telephone_number.trim());
    if (business.value.facebook){
    formData.append('business_Facebook', business.value.facebook.trim());}
    if (business.value.x){
    formData.append('business_X', business.value.x.trim());}
    formData.append('business_Instagram', business.value.instagram.trim());
    formData.append('business_Tiktok', business.value.tiktok.trim());

    if (business.value.image instanceof File) {
        formData.append('business_image', business.value.image);
    }

    for (let pair of formData.entries()) {
        console.log(`${pair[0]}: ${pair[1]}`);
    }

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
        console.error("Error updating business data:", JSON.stringify(error.response?.data, null, 2));
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
                            <div>
                                <label for="province" class="block text-gray-700 text-sm font-bold mb-2">
                                    Province/Region
                                </label>
                                <select
                                    id="province"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.province"
                                    @change="filterCities"
                                >
                                    <option value="">Select Province</option>
                                    <option v-for="province in provinces" :key="province" :value="province">{{ province }}</option>
                                </select>
                            </div>

                            <!-- City Dropdown -->
                            <div>
                                <label for="city" class="block text-gray-700 text-sm font-bold mb-2">
                                    Municipality/City
                                </label>
                                <select
                                    id="city"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.city"
                                    @change="filterBarangays"
                                >
                                    <option value="">Select City</option>
                                    <option v-for="city in filteredCities" :key="city" :value="city">{{ city }}</option>
                                </select>
                            </div>

                            <!-- Barangay Dropdown -->
                            <div>
                                <label for="barangay" class="block text-gray-700 text-sm font-bold mb-2">
                                    Barangay
                                </label>
                                <select
                                    id="barangay"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="business.barangay"
                                >
                                    <option value="">Select Barangay</option>
                                    <option v-for="barangay in filteredBarangays" :key="barangay" :value="barangay">{{ barangay }}</option>
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
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                Facebook
                            </label>
                            <div class="flex items-center">
                            
                                <input type="text" 
                                placeholder="Facebook @username" 
                                class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="business.facebook"
                                >
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                X
                            </label>
                            <div class="flex items-center">
                            
                                <input type="text" 
                                placeholder="X @username" 
                                class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="business.x">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                Instagram
                            </label>
                            <div class="flex items-center">
                            
                                <input type="text" 
                                placeholder="Instagram @username" 
                                class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="business.instagram">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="Social_Media" class="block text-gray-700 text-sm font-bold mb-2">
                                Tiktok
                            </label>
                            <div class="flex items-center">
                                
                                <input type="text" 
                                placeholder="Tiktok @username" 
                                class="w-full shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="business.tiktok">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    </template>