<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { App } from '@inertiajs/inertia-vue3';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { Inertia } from '@inertiajs/inertia';

const textAreas = {
    card1: ref(''),
    card1_img: ref(''),
    card2: ref(''),
    card2_img: ref(''),
    card3: ref(''),
    card3_img: ref(''),
    card4: ref(''),
    card4_img: ref(''),
    card5: ref(''),
    card5_img: ref(''),
    card6: ref(''),
    card6_img: ref('')
}
onMounted(()=>{

    getWebsiteInfo();
});

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

        const getProductsInfo = await axios.get('/api/featured-products', {
            params: {business_id: businessId}
        });
        console.log('Products Info:', getProductsInfo.data);

        const featuredProducts = getProductsInfo.data.slice(0, 6);
        console.log('featuredProducts Info:', featuredProducts);

        if(featuredProducts.length<5){
            console.error('Featured Products must be 6 in number');
        }else{
            
        featuredProducts.forEach((product, index) => {
            const imgPath = product.product_img.replace('products/', '');
        if (index === 0) {
            textAreas.card1.value = product.product_name;
            textAreas.card1_img.value = imgPath;
        } else if (index === 1) {
            textAreas.card2.value = product.product_name;
            textAreas.card2_img.value = imgPath;
        } else if (index === 2) {
            textAreas.card3.value = product.product_name;
            textAreas.card3_img.value = imgPath;
        } else if (index === 3) {
            textAreas.card4.value = product.product_name;
            textAreas.card4_img.value = imgPath;
        } else if (index === 4) {
            textAreas.card5.value = product.product_name;
            textAreas.card5_img.value = imgPath;
        }else if (index === 5) {
            textAreas.card6.value = product.product_name;
            textAreas.card6_img.value = imgPath;
        }
        });
    }

    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

// const editButton=ref(null);
// function edit(area){
//     editButton.value = area;
// }

// async function save(){
//     editButton.value = false;

//     const response_userId = await axios.get('/user-id');
//         const userId = response_userId.data.user_id;
//         console.log("Save function called");
//     const getBusinessInfo = await axios.get('/api/business_info', {
//             params: {user_id: userId}
//         });
//         const businessId = getBusinessInfo.data.business_id;

//         const formData = new FormData();
//         formData.append('business_id', businessId);
//         formData.append('about_us1', textAreas.about_us1.value);
//         formData.append('about_us2', textAreas.about_us2.value);
//         formData.append('about_us3', textAreas.about_us3.value);

//     const saveBusinessDesc = await axios.post('/api/website-update', formData, {
//         headers:{
//             'Content-Type': 'multipart/form-data'
//         }
//     });
//     console.log('Save response:', saveBusinessDesc.data);
    
// }

function goToEditWebsite4(){
    Inertia.visit(route('editWebsite4'));
}
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
     
        <!-- <div class="bg-gray-300 flex items-center p-2">
            <p class="flex-grow text-center mr-3">You are currently using the edit mode.</p>
                <button @click="save()" class="px-6 py-1 bg-gray-600 ml-auto">Save</button>
        </div> -->

        
        <div class="ml-1 bg-website-main flex min-h-screen relative">

            <div class="flex flex-col items-center p-3 absolute top-[10px] left-0 right-0 bottom-[500px] m-auto">
                <p class="mt-[10px] text-[40px]  text-white font-bold  text-center">Featured Products</p>
                <p class="mt-[10px] text-[20px]  text-white  text-center">
                    A list of the most popular products loved by customers. 
                    Best prices guaranteed everyday.
                </p>
            </div>

            <!-- edit business info wag to iedit kasi business name ito-->
            <div class=" mt-8 mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg mt-[10px] px-4 pt-[200px]">
                
                <div class="block flex flex-row gap-5">
                <!-- card 1 -->
                    <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <img :src="`/storage/products/${textAreas.card1_img.value}`" class="w-full h-4/5 object-cover"/>
                        <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card1.value}}</p>
                    </div>

                 <!-- card 2 -->
                 <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <img :src="`/storage/products/${textAreas.card2_img.value}`" class="w-full h-4/5 object-cover"/>
                        <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card2.value}}</p>
                    </div>
                

                <!-- card 3 -->
                <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <img :src="`/storage/products/${textAreas.card3_img.value}`" class="w-full h-4/5 object-cover"/>
                        <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card3.value}}</p>
                    </div>
                </div>

                <div class="block flex flex-row gap-5">
                <!-- card 4 -->
                <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <img :src="`/storage/products/${textAreas.card4_img.value}`" class="w-full h-4/5 object-cover"/>
                        <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card4.value}}</p>
                    </div>

                <!-- card 5 -->
                <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <img :src="`/storage/products/${textAreas.card5_img.value}`" class="w-full h-4/5 object-cover"/>
                        <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card5.value}}</p>
                    </div>

                <!-- card 6 -->
                <div class="flex flex-col bg-white w-[220px] h-[240px] p-4 rounded-lg shadow-lg border border-gray-200">
                        <img :src="`/storage/products/${textAreas.card6_img.value}`" class="w-full h-4/5 object-cover"/>
                        <p class="text-black text-[18px] h-1/5 mt-[20px] text-center">{{textAreas.card6.value}}</p>
                    </div> 
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

