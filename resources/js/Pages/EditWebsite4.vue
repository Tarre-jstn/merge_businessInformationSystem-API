<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';


const textAreas = {
    businessImage: ref(''),
    website_footNote: ref(''),
    businessName: ref(''),
    business_Email: ref(''),
    business_Contact_Number: ref(''),
    business_Address: ref('')
}

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

        const imgUrl = `/storage/${getBusinessInfo.data.website_image}`;
        textAreas.businessImage.value=imgUrl;
        // textAreas.businessImage.value = getBusinessInfo.data.business_image;
        textAreas.businessName.value = getBusinessInfo.data.business_Name;
        textAreas.business_Email.value = getBusinessInfo.data.business_Email;
        textAreas.business_Contact_Number.value = getBusinessInfo.data.business_Contact_Number;
        textAreas.business_Address.value = getBusinessInfo.data.business_Address;
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
}

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

        <div class="ml-1 bg-website-main flex min-h-screen">
            <!-- edit business info wag to iedit kasi business name ito-->
            <div class="mt-40 ml-8 flex-col h-1/2">
                <div>
                    <img :src='textAreas.businessImage.value' class="w-full h-full object-cover rounded-full"/>
                </div>
                <div class="mt-5">
                    <button @click="edit('website_footNote')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p class=" text-xl text-white">{{ textAreas.website_footNote }}</p>
                    <div v-if="editButton==='website_footNote'">
                        <textarea v-model="textAreas.website_footNote.value" class="rows-2 cols-50 border boder-black"></textarea>
                        <button @click="save()" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                </div>
            
            </div>

            <!-- image -->
            <div class="mt-[50px] ml-auto flex-grow-0 w-1/2 max-w-md">
                <div class="mt-2 flex flex-col ">
                    <p class="text-white font-bold text-[50px] text-center">Contact Us</p>
                    <p class="text-white">Email: {{ textAreas.business_Email }} </p>
                    <p class="text-white">Contact No.: {{ textAreas.business_Contact_Number }} </p>
                    <p class="text-white">Address: {{ textAreas.business_Address }} </p>
                </div>
            </div>
        </div>
    
    </AuthenticatedLayout>

</template>

