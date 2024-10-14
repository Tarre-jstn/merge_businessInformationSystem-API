<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';
import '@fortawesome/fontawesome-free/css/all.min.css';



const textAreas = {
    businessImage: ref(''),
    website_footNote: ref(''),
    businessName: ref(''),
    business_Email: ref(''),
    business_Contact_Number: ref(''),
    business_Address: ref(''),
    business_Facebook: ref(''),
    business_X: ref(''),
    business_Instagram: ref(''),
    business_Tiktok: ref(''),
    business_Province: ref(''),
    business_City: ref(''),
    business_Barangay: ref('')
}
let isLoading = ref(true);
onMounted(()=>{
    getWebsiteInfo();
});

//store default / to be changed data:

async function getWebsiteInfo(){
    try{
        const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log(userId);

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        console.log(getBusinessInfo.data);
        const businessId = getBusinessInfo.data.business_id;

        const getWebsiteInfo = await axios.get('/api/website', {
            params: {business_id: businessId}
        });
        console.log(getWebsiteInfo.data);

        if(getBusinessInfo.data.business_image){
        textAreas.businessImage.value = `/storage/business_logos/${getBusinessInfo.data.business_image}`;
        isLoading.value = false;
    }
    // textAreas.businessImage.value = getBusinessInfo.data.business_image;
        textAreas.businessName.value = getBusinessInfo.data.business_Name;
        textAreas.business_Email.value = getBusinessInfo.data.business_Email;
        textAreas.business_Contact_Number.value = getBusinessInfo.data.business_Contact_Number;
        
        textAreas.business_Address.value = getBusinessInfo.data.business_Address;
        textAreas.business_Province.value = getBusinessInfo.data.business_Province;
        textAreas.business_City.value = getBusinessInfo.data.business_City;
        textAreas.business_Barangay.value = getBusinessInfo.data.business_Barangay;

        textAreas.business_Facebook.value = getBusinessInfo.data.business_Facebook;
        textAreas.business_X.value = getBusinessInfo.data.business_X;
        textAreas.business_Instagram.value = getBusinessInfo.data.business_Instagram;
        textAreas.business_Tiktok.value = getBusinessInfo.data.business_Tiktok;


        textAreas.website_footNote.value = getWebsiteInfo.data.website_footNote;
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}


const editButton=ref(null);
function edit(area){
    editButton.value = area;
}

async function save(){
    editButton.value = false;
    
    const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log("Save function called");
    const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        const businessId = getBusinessInfo.data.business_id;

        const formData = new FormData();
        formData.append('business_id', businessId);
        formData.append('website_footNote', textAreas.website_footNote.value);

        const saveBusinessDesc = await axios.post('/api/website-update', formData, {
        headers:{
            'Content-Type': 'multipart/form-data'
        }
        
    });
    alert("Changes Saved Successfully.");
}
const formatUrl = (url) => {
    // Check if the URL starts with http:// or https://
    if (!/^https?:\/\//i.test(url)) {
        // Prepend https:// if it doesn't
        return `https://${url}`;
    }
    return url;
};

</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- header of edit website to save changes -->
    
        <!-- <div>
            <button @click="getWebsiteInfo">Show</button>
        </div> -->
     
        <div class="bg-gray-300 flex items-center p-2">
            <p class="flex-grow text-center mr-3">You are currently using the edit mode.</p>
                <button @click="save()" class="px-6 py-1 bg-gray-600 ml-auto">Save</button>
        </div>

        <!-- header of business editable template-->

        <div class="ml-1 bg-website-main flex flex-col min-h-screen">

            <div class="flex flex-row justify-between mt-[5px] ml-8 mr-8 w-full">
            <!-- FootNote -->
            <div class="mr-auto mt-40 ml-8 flex flex-col h-1/2 w-1/2 max-w-md">
                <div class="max-w-[50px]">
                    <img v-if="isLoading" src='/storage/business_logos/default-profile.png'/>
                    <img v-else-if="textAreas.businessImage.value" :src='textAreas.businessImage.value' alt="Logo"  class="w-[50px] h-[50px] object-cover rounded-full" />
                    <img v-else src='/storage/business_logos/default-profile.png'/>
                </div>

                <div class="mt-5">
                    <button @click="edit('website_footNote')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p class=" text-xl text-white">{{ textAreas.website_footNote.value }}</p>
                    <div v-if="editButton==='website_footNote'">
                        <textarea v-model="textAreas.website_footNote.value" class="rows-2 cols-50 border boder-black"></textarea>
                        <button @click="save()" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                    <div class="mt-[20px]">
                    <a :href="formatUrl(textAreas.business_Facebook.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-facebook-f"></i></a>
                    <a :href="formatUrl(textAreas.business_X.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-x-twitter"></i></a>
                    <a :href="formatUrl(textAreas.business_Instagram.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-instagram"></i></a>
                    <a :href="formatUrl(textAreas.business_Tiktok.value)" class="w-[30px] h-[40px] bg-white rounded-xl mr-[5px] p-2 cursor-pointer"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
            
            </div>

            <!-- Contact Us -->
            <div class="mt-[100px] ml-auto flex flex-grow-0 w-1/2 max-w-md w-1/2 max-w-md">
                <div class="mt-2 flex flex-col ">
                    <p class="text-white font-bold text-[50px]">Contact Us</p>
                    <p class="text-white mt-[10px]">Email: {{ textAreas.business_Email }} </p>
                    <p class="text-white">Contact No.: {{ textAreas.business_Contact_Number }} </p>
                    <p class="text-white">Address: {{ textAreas.business_Address }} </p>
                    <p class="text-white">{{ textAreas.business_Province }}, 
                        {{ textAreas.business_City }}, {{ textAreas.business_Barangay }}  </p>
                </div>
            </div>
        </div>

            <div class="mt-[100px] w-full">
                <hr class="border-white mx-auto w-11/12">
            </div>

            <div class="ml-[60px] mr-auto w-full">
                <p class="text-[17px] text-white"><i class="fa fa-copyright"></i> {{ textAreas.businessName }} All rights reserved</p>
            </div>
        </div>
    
    </AuthenticatedLayout>

</template>

