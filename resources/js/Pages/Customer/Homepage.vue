<script setup>
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref } from 'vue';
const businessInfo = {
    businessImage: ref(''),
    businessName: ref(''),
    business_Email: ref(''),
    business_Contact_Number: ref(''),
    business_Address: ref(''),
    business_Facebook: ref(''),
    business_X: ref(''),
    business_Instagram: ref(''),
    business_Tiktok: ref(''),
    businessDescription: ref(''), // Add this
    businessDetails: ref(''), // Add this
    homePageImage: ref('') // Add this
}

const websiteInfo = {
    about_us1: ref(''),
    about_us2: ref(''),
    about_us3: ref(''),
    website_footNote: ref('')
}

function logout(button){
    Inertia.post(route('logout'), {button});
}

onMounted(()=>{
    getWebsiteInfo();
})

async function getWebsiteInfo(){
    try{
        // const response_userId = await axios.get('/user-id');
        // const userId = response_userId.data.user_id;
        // console.log(userId);

        // const getBusinessInfo = await axios.get('/api/business_info', {
        //     params: {user_id: userId}
        // });
        // console.log(getBusinessInfo.data);
        // const businessId = getBusinessInfo.data.business_id;

        const getWebsiteInfo = await axios.get('/api/website', {
            params: {business_id: 1}
        });

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });
        console.log(getWebsiteInfo.data);

        businessInfo.businessImage.value = getBusinessInfo.data.business_image;
        businessInfo.businessName.value = getBusinessInfo.data.business_Name;
        businessInfo.businessDescription.value = getWebsiteInfo.data.website_description;
        businessInfo.businessDetails.value = getWebsiteInfo.data.website_details;
        const imgUrl = `/storage/${getWebsiteInfo.data.website_image}`;
        businessInfo.homePageImage.value=imgUrl;
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

</script>

<template>
        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a>Home</a>
                    <a class="text-white text-[18px]">Chat with Us</a>
                    <a class="text-white text-[18px]">Products & Services</a>
                    <a class="text-white text-[18px]">About Us</a>
                    <p>|</p>
                    <button @click="logout('register')" class="text-white">Register</button>
                    <button @click="logout('logout')" class=" cursor-pointer bg-white border border-white rounded-sm py-1 px-3">Log Out</button>
                </div>
        </div>

        <section>

        <div class="bg-website-main flex min-h-screen">


            <div class="mt-[150px] ml-[80px] flex-col h-1/2">
                <div>
                    <p class="text-white text-[40px] tracking-[4px]">{{businessInfo.businessName.value}}</p>
                </div>
                <div class="mt-[30px]">
                    <div class="max-w-[550px]">
                    <p class="font-extrabold text-[25px] text-white">{{ businessInfo.businessDescription.value }}</p>
                    </div>
                </div>
                <div class="mt-[30px]" >
                    <div class="max-w-[550px]">
                        <p id="business-details" class="text-[19px] text-white">{{ businessInfo.businessDetails.value }} </p>
                    </div>
                </div>

                <div class="mt-[90px] flex flex-row">
                    <button @click="logout('register')" class="mr-[20px] cursor-pointer bg-white border border-white rounded-sm py-[8px] px-[70px]">Register</button>
                    <p class="text-white text-xl">|</p>
                    <a class="ml-[35px] justify-center text-white text-[18px]">See All Products</a>
                </div>
            </div>


            <!-- image -->
            <div class="mr-[8px] mt-[130px] ml-auto flex-grow-0 w-1/2 max-w-xl">
                
                <img :src='businessInfo.homePageImage.value' class ="mt-8 w-full h-[390px] object-cover rounded-tl-[30px]"/>
            </div>
        </div>
        </section>
</template>