<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const textAreas = {
    card1: ref(''),
    card1_img: ref(''),
    about_us2: ref(''),
    about_us3: ref('')
}

const searchResult = ref(['']);
onMounted(()=>{

    getWebsiteInfo();
});

watch ( 
    ()=>textAreas.card1.value,
    async (newValue) => {
        if(newValue){
            try{
                const response = await axios.get('/api//search-product');
            }catch(error){
                console.error('Error fetching search results:', error);
            }
        }else{
            searchResults.value = []; 
        }
    }

)
// //store default / to be changed data:

async function getWebsiteInfo(){
    try{
        const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;
        console.log(userId);

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });
        const businessId = getBusinessInfo.data.business_id;

        const getWebsiteInfo = await axios.get('/api/website', {
            params: {business_id: businessId}
        });
        console.log(getWebsiteInfo.data);

        textAreas.about_us1.value = getWebsiteInfo.data.about_us1;
        textAreas.about_us2.value = getWebsiteInfo.data.about_us2;
        textAreas.about_us3.value = getWebsiteInfo.data.about_us3;

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
        formData.append('about_us1', textAreas.about_us1.value);
        formData.append('about_us2', textAreas.about_us2.value);
        formData.append('about_us3', textAreas.about_us3.value);

    const saveBusinessDesc = await axios.post('/api/website-update', formData, {
        headers:{
            'Content-Type': 'multipart/form-data'
        }
    });
    console.log('Save response:', saveBusinessDesc.data);
    
}

function goToEditWebsite4(){
    Inertia.visit(route('editWebsite4'));
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

        
        <div class="ml-1 bg-website-main flex min-h-screen relative">

            <div class="flex flex-col items-center p-3 absolute top-[5px] left-0 right-0 bottom-[500px] m-auto">
                <p class="mt-[10px] text-[30px]  text-white font-bold  text-center">Featured Products</p>
                <p class="mt-[10px] text-[15px]  text-white  text-center">
                    A list of the most popular products loved by customers. 
                    Best prices guaranteed everyday.
                </p>
            </div>

            <!-- edit business info wag to iedit kasi business name ito-->
            <div class="mt-[10px] mx-auto my-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-2 gap-y-6 w-full max-w-screen-lg">
                
                <!-- card 1 -->
                    <div class="flex flex-col bg-white w-[180px] h-[200px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <p class="text-white text-xl font-bold">{{textAreas.card1}}</p>
                    </div>

                 <!-- card 2 -->
                     <div class="flex flex-col bg-white w-[180px] h-[200px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <p class="text-white text-xl font-bold">{{textAreas.about_us1}}</p>
                    </div>
                

                <!-- card 3 -->
                    <div class="flex flex-col bg-white w-[180px] h-[200px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <p class="text-white text-xl font-bold">{{textAreas.about_us1}}</p>
                    </div>  

                <!-- card 4 -->
                     <div class="flex flex-col bg-white w-[180px] h-[200px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <p class="text-white text-xl font-bold">{{textAreas.about_us1}}</p>
                    </div>

                <!-- card 5 -->
                    <div class="flex flex-col bg-white w-[180px] h-[200px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <p class="text-white text-xl font-bold">{{textAreas.about_us1}}</p>
                    </div>  

                <!-- card 6 -->
                    <div class="flex flex-col bg-white w-[180px] h-[200px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <p class="text-white text-xl font-bold">{{textAreas.about_us1}}</p>
                    </div>  

            </div>
        </div>

        <!-- button to next section of homepage -->
        <div class="ml-auto z-50 fixed bottom-4 right-4">
                    <button @click="goToEditWebsite4()" class="bg-white border border-black rounded-2xl p-8">
                        <i class="fa fa-arrow-down "></i>
                    </button>
                </div>
    
    </AuthenticatedLayout>

</template>

