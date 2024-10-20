<script setup>
import { ref, onMounted } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import axios from 'axios';
import { Head } from '@inertiajs/vue3';

function logout(button){
    Inertia.post(route('logout'), {button});
}

function account(){
    Inertia.visit(route('account_settings'));
}
let userInfo = ref({
    user_id: '',
    profile_img: '',
    name: '',
    email: '',
    address: '',
    contact_number: ''
});

const businessInfo = {
    businessImage: ref(''),
    businessName: ref(''),
    business_Email: ref(''),
    business_Contact_Number: ref(''),
    business_Telephone_Number: ref(''),
    business_Address: ref(''),
    business_Facebook: ref(''),
    business_X: ref(''),
    business_Instagram: ref(''),
    business_Tiktok: ref(''),
    businessDescription: ref(''),
    businessDetails: ref(''), 
    business_Province: ref(''),
    business_City: ref(''),
    business_Barangay: ref(''),
    website_footNote: ref('')
}


let businessImage = ref('');
let isLoading = ref(true);

const profilePicture = ref(null);

const formatUrl = (url) => {
    // Check if the URL starts with http:// or https://
    if (!/^https?:\/\//i.test(url)) {
        // Prepend https:// if it doesn't
        return `https://${url}`;
    }
    return url;
};

async function getWebsiteInfo(){
    try{
        const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}
onMounted(async () => {
    getWebsiteInfo();
try {
    const response = await axios.get('/showUser');
    console.log("API Response:", response.data);

    if (response.data) {
        userInfo.value = {
            user_id: response.data.id,
            profile_img: response.data.profile_img,
            name: response.data.name,
            email: response.data.email,
            address: response.data.address,
            contact_number: response.data.contact_number,
            
    };

    const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });

        
        businessInfo.businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
        businessInfo.businessName.value = getBusinessInfo.data.business_Name;
        businessInfo.business_Email.value = getBusinessInfo.data.business_Email;
        businessInfo.business_Contact_Number.value = getBusinessInfo.data.business_Contact_Number;
        businessInfo.business_Telephone_Number.value = getBusinessInfo.data.business_Telephone_Number;
        businessInfo.business_Address.value = getBusinessInfo.data.business_Address;

        businessInfo.business_Province.value = getBusinessInfo.data.business_Province;
        businessInfo.business_City.value = getBusinessInfo.data.business_City;
        businessInfo.business_Barangay.value = getBusinessInfo.data.business_Barangay;

        businessInfo.business_Facebook.value = getBusinessInfo.data.business_Facebook;
        businessInfo.business_X.value = getBusinessInfo.data.business_X;
        businessInfo.business_Instagram.value = getBusinessInfo.data.business_Instagram;
        businessInfo.business_Tiktok.value = getBusinessInfo.data.business_Tiktok;


        // Set profile picture or default if not available
        profilePicture.value = response.data.profile_img 
        ? `/storage/user_profile/${response.data.profile_img}` 
        : '/storage/user_profile/default-profile.png';

        if(userInfo.value.profile_img){
            isLoading.value=false;
        }

    } else {
        console.error("No business data found or invalid format.");
    }
} catch (error) {
    console.error("Error fetching business data:", error);
    alert('Failed to fetch business profile');
}
});




// Handle file input for the profile picture
const onProfilePictureChange = (event) => {
const file = event.target.files[0];
if (file) {
    profilePicture.value = URL.createObjectURL(file);  // Display selected image preview
    userInfo.value.profile_img = file;  // Store the file to be sent to the backend
} else {
    profilePicture.value = '/storage/user_profile/default-profile.png';  // Default image fallback
    userInfo.value.profile_img = null;
}
};

const updateBusiness = async () => {

const formData = new FormData();
formData.append('name', userInfo.value.name.trim());
formData.append('email', userInfo.value.email.trim());
formData.append('address', userInfo.value.address.trim());
formData.append('contact_number', userInfo.value.contact_number.trim());

if (userInfo.value.profile_img instanceof File) {
    formData.append('profile_img', userInfo.value.profile_img);
}

for (let pair of formData.entries()) {
    console.log(`Data to be updated: ${pair[0]}: ${pair[1]}`);
}

try {
    const response = await axios.post(`/update_user/${userInfo.value.user_id}`, formData, {
    headers: {
        'Content-Type': 'multipart/form-data',
        'X-HTTP-Method-Override': 'PUT'
    },
    });
    if (response.data.success) {
    alert('User profile updated successfully');
    } else {
    console.error("Update failed:", response.data);
    alert('Please input only .jpg .png or .jpeg');
    }
} catch (error) {
    console.error("Error updating business data:", JSON.stringify(error.response?.data, null, 2));
    alert('Failed to update business profile. Invalid Image input.');
}
};
</script>

<template>
        <Head title="Update Business Profile" />

        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-white text-[18px]" :href="route('homepage')">Home</a>
                    <a class="text-white text-[18px]">Chat with Us</a>
                    <a class="text-white text-[18px]" :href="route('products_page')">Products & Services</a>
                    <a class="text-white text-[18px]" :href="route('aboutUs_page')">About Us</a>
                    <p>|</p>
                    <div class="flex flex-col">
                        <a @click="logout('logout')" class=" cursor-pointer text-white text-[14px] underline">Log Out</a>
                        <a @click="account" class=" cursor-pointer text-white text-[14px] underline">Account</a>
                    </div> 
                    <div class="w-[50px] h-[50px]">
                        <img v-if="isLoading" src='/storage/user_profile/default-profile.png'/>
                        <img v-else-if="profilePicture" :src='profilePicture' alt="Logo" class="h-full object-cover rounded-full" />
                        <img v-else src='/storage/user_profile/default-profile.png'/>
                    </div>
                    
                </div>
        </div>

        <div class="bg-website-main flex min-h-screen">
        <div class="container mx-auto p-4 flex flex-col md:flex-row">
            <!-- Left Column (Form Fields) -->
            <div class="ml-[80px] w-full md:w-1/2 pt-20">
                <h1 class="text-3xl text-white font-bold mb-4">Update Profile</h1>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="business-name" class="block text-gray-700 text-sm font-bold mb-2">
                                Name
                            </label>
                            <input
                                type="text"
                                id="business-name"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="userInfo.name"
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
                                v-model="userInfo.email"
                            >
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
                                v-model="userInfo.address"
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
                                v-model="userInfo.contact_number"
                            >
                        </div>
                    </div>

                    <div class="mx-auto">
                        <button
                            type="button"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            @click="updateBusiness"
                        >
                            Update Profile
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column (Profile Picture)-->
            <div class="w-full md:w-1/2 flex flex-col items-center mt-[30px]">
                <div class="relative group">
                        <img :src='profilePicture' alt="Profile Picture" class="w-[350px] h-[350px] rounded-full border-4 border-black object-cover" />
                    
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
               
            </div>


        </div>
    </div>

    <section>
        <div class="bg-website-main flex flex-col min-h-screen" style="min-height: calc(70vh);">

            <div class="w-full">
                <hr class="border-white mx-auto w-11/12">
            </div>
<div class="flex flex-row justify-between mt-[5px] ml-8 mr-8 w-full">
<!-- FootNote -->
<div class="mr-auto mt-40 ml-8 flex flex-col h-1/2 w-1/2 max-w-md">
    <div class="max-w-[50px]">
        <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
    </div>

    <div class="mt-5">
        <p class=" text-xl text-white">{{ businessInfo.website_footNote }}</p>
        <div class="mt-[20px]">
        <a :href="formatUrl(businessInfo.business_Facebook.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-facebook-f"></i></a>
        <a :href="formatUrl(businessInfo.business_X.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa fa-twitter"></i></a>
        <a :href="formatUrl(businessInfo.business_Instagram.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-instagram"></i></a>
        <a :href="formatUrl(businessInfo.business_Tiktok.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-tiktok"></i></a>
        </div>
    </div>

</div>

<!-- Contact Us -->
<div class="mt-[100px] ml-auto flex flex-grow-0 w-1/2 max-w-md w-1/2 max-w-md">
    <div class="mt-2 flex flex-col ">
        <p class="text-white font-bold text-[50px]">Contact Us</p>
        <p class="text-white mt-[10px]">Email: {{ businessInfo.business_Email }} </p>
        <p class="text-white">Contact No.: {{ businessInfo.business_Contact_Number }} </p>
        <p class="text-white">Address: {{ businessInfo.business_Address }} </p>
        <p class="text-white">{{ businessInfo.business_Province }}, 
            {{ businessInfo.business_City }}, {{ businessInfo.business_Barangay }}  </p>
            <p class="text-white">Telephone No.: {{ businessInfo.business_Telephone_Number }} </p>
    </div>
</div>
</div>

<div class="mt-[60px] w-full">
    <hr class="border-white mx-auto w-11/12">
</div>

<div class="ml-[60px] mr-auto w-full">
    <p class="text-[17px] text-white mt-2"><i class="fa fa-copyright"></i> {{ businessInfo.businessName }} All rights reserved</p>
</div>
        </div>

</section>
</template>
<style>
.icon-color {
    background-color: #306091; 
}
.fa.fa-twitter{
	font-family:sans-serif;
}
.fa.fa-twitter::before{
	content:"𝕏";
	font-size:1.2em;
}
</style>

