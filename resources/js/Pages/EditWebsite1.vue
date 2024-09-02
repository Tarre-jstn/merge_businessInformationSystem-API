<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Inertia } from '@inertiajs/inertia';

// const textAreas = {
//     businessName: ref('BUSINESS NAME'),
//     businessDescription: ref('CHEAP AND QUALITY FOOD'),
//     businessDetails: ref("Our food service system is designed to meet the specific needs of our customers. We ensure to avail you the cheapest high quality product in the market."),
//     homePageImage: ref('https://picsum.photos/200')
// }
const textAreas = {
    businessName: ref(null),
    businessDescription: ref(null),
    businessDetails: ref(null),
    homePageImage: ref(null)
}

onMounted(()=>{
    function insertBreakLines(businessDetails){
        let words = businessDetails.textContent.split(' ');
        let formattedParagraph = '';

        for(let i=0; i<=words.length;i++){
            formattedParagraph+=" " +words[i];
            if((i+1)%11===0){
                formattedParagraph+='<br>';
            }
        }
        businessDetails.innerHTML=formattedParagraph.trim();
    }
    const paragraph = document.getElementById('business-details');
    if(paragraph){
        insertBreakLines(paragraph);
    }
});

//store default / to be changed data:

async function getWebsiteInfo(){
    try{
        const response_userId = await axios.get('/user-id');
        const userId = response_userId.data.user_id;

        const response_businessId = await axios.get('/business-id');
        const businessId = response_businessId.data.business_id
        console.log(businessId);

        const getBusinessName = await axios.get('/api/business_info', {
            params: {user_id: userId}
        });


        textAreas.businessName.value = getBusinessName.data.business_Name;

    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}


const editButton=ref(null);
function edit(area){
    editButton.value = area;
}

function save(){
    editButton.value = false;
}

function imageUpload(event){
    const file = event.target.files[0];
    if (file){
        const reader = new FileReader();
        reader.onload = (e) => {
            textAreas.homePageImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- header of edit website to save changes -->
    
        <div>
            <button @click="getWebsiteInfo">Show</button>
        </div>
        <div class="bg-gray-300 flex items-center p-2">
            <p class="flex-grow text-center mr-3">You are currently using the edit mode.</p>
                <button class="px-6 py-1 bg-gray-600 ml-auto">Save</button>
        </div>

        <!-- header of business editable template -->
        <div class="ml-1 bg-business-website-header flex items-center p-2">
            <div class="ml-6 w-10 h-10">
                <img src="https://picsum.photos/200" class="w-full h-full object-cover rounded-full"/>
            </div>
                <div class="ml-auto flex items-center space-x-4 ">
                    <a>Home</a>
                    <a class="text-white">Chat with Us</a>
                    <a class="text-white">Products & Services</a>
                    <a class="text-white">About Us</a>
                    <p>|</p>
                    <a class="text-white">Register</a>
                    <a class="bg-white border border-white rounded-sm py-1 px-3">Log In</a>
                </div>
        </div>

        <div class="ml-1 bg-website-main flex min-h-screen">
            <!-- edit business info -->
            <div class="mt-40 ml-8 flex-col h-1/2">
                <div>
                    <button @click="edit('businessName')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p class="text-white text-xl font-bold">{{textAreas.businessName}}</p>
                <div v-if="editButton==='businessName'">
                    <textarea v-model="textAreas.businessName" class="rows-2 cols-50 border boder-black"></textarea>
                    <button @click="save()" class="bg-white rounded-xl p-1">Save</button>
                </div>

                </div>
                <div class="mt-5">
                    <button @click="edit('businessDescription')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p class="font-bold text-xl text-white">{{ textAreas.businessDescription }}</p>
                    <div v-if="editButton==='businessDescription'">
                        <textarea v-model="textAreas.businessDescription" class="rows-2 cols-50 border boder-black"></textarea>
                        <button @click="save()" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                </div>
                <div class="mt-5" >
                    <button @click="edit('businessDetails')" class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p id="business-details" class="text-white">{{ textAreas.businessDetails }} </p>
                    <div v-if="editButton==='businessDetails'">
                        <textarea v-model="textAreas.businessDetails" class="rows-2 cols-100 border boder-black"></textarea>
                        <button @click="save()" class="bg-white rounded-xl p-1">Save</button>
                    </div>
                </div>
            
            </div>

            <!-- image -->
            <div class="mt-3 ml-auto flex-grow-0 w-1/2 max-w-md">
                <div class="flex items-center">
                <button @click="edit('image')" class="bg-white border border-white rounded-xl">Edit Photo</button>
                <img :src='textAreas.homePageImage.value' class ="mt-8 w-full h-auto object-cover rounded-tl-[30px]"/>
                <div v-if="editButton==='image'" class="bg-white">
                    <input class="p6" type="file" @change="imageUpload"/>
                </div>
            </div>
            </div>
        </div>

        <!-- button to next section of homepage -->
        <div class="ml-auto z-50 fixed bottom-4 right-4">
                    <button class="bg-white border border-black rounded-2xl p-8">
                        <i class="fa fa-arrow-down "></i>
                    </button>
                </div>
    
    </AuthenticatedLayout>

</template>

