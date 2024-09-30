<script setup>
import { text } from '@fortawesome/fontawesome-svg-core';
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref, reactive } from 'vue';

const userInfo = {
    
}

let businessImage = ref('');
let isLoading = ref(true);

function logout(button){
    Inertia.post(route('logout'), {button});
}

onMounted(()=>{
    getWebsiteInfo();
})


async function getWebsiteInfo(){
    try{

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });

        const getWebsiteInfo1 = await axios.get('/api/website', {
            params: {business_id: 1}
        });
        console.log('Business data: ',getBusinessInfo.data);
        
        console.log('Website data: ',getWebsiteInfo1.data);

        const imgBusinessUrl = `/storage/${getBusinessInfo.data.business_image}`;
        businessImage.value = imgBusinessUrl;
    
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

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
            image: businessData.business_image, // This should be the image filename or URL
        };

        // Set profile picture or default if not available
        profilePicture.value = businessData.business_image 
        ? `/storage/business_logos/${businessData.business_image}` 
        : '/storage/business_logos/defaultprofile.jpg';

    } else {
        console.error("No business data found or invalid format.");
    }
} catch (error) {
    console.error("Error fetching business data:", error);
    alert('Failed to fetch business profile');
}
});
</script>

<template>
        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessImage' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-white text-[18px]":href="route('homepage')">Home</a>
                    <a class="text-white text-[18px]">Chat with Us</a>
                    <a class="text-white text-[18px]">Products & Services</a>
                    <a class="text-white text-[18px]">About Us</a>
                    <p>|</p>
                    <div class="flex flex-col">
                        <a @click="links('logout')" class=" cursor-pointer text-white text-[14px] underline">Log Out</a>
                        <a @click="account" class=" cursor-pointer text-white text-[14px] underline">Account</a>
                    </div> 
                    <div class="w-[50px] h-[50px]">
                        <img v-if="isLoading" src='/storage/business_logos/default-profile.png'/>
                        <img v-else-if="businessImage" :src='businessImage' alt="Logo" />
                        <img v-else src='/storage/business_logos/default-profile.png'/>
                    </div>
                </div>
        </div>

       


    <!-- products -->
    <section>
        <div class=" bg-website-main flex min-h-screen relative" style="min-height: calc(100vh + 800px);">

            <div class="ml-auto w-1/2 flex flex-col items-center mt-12">
                <div class="relative group">
                    <img :src="profilePicture" alt="Profile Picture" class="w-[350px] h-[350px] rounded-full border-4 border-white object-cover">
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