<script setup>
import { text } from '@fortawesome/fontawesome-svg-core';
import { Inertia } from '@inertiajs/inertia';
import { onMounted, ref, reactive } from 'vue';

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
    website_footNote: ref(''),
    business_Province: ref(''),
    business_City: ref(''),
    business_Barangay: ref('')
}

const textAreas = reactive({
    card1: '',
    card1_img: '',
    card1_price: '',
    card2: '',
    card2_img: '',
    card2_price: '',
    card3: '',
    card3_img: '',
    card3_price: '',
    card4: '',
    card4_img: '',
    card4_price: '',
    card5: '',
    card5_img: '',
    card5_price: '',
    card6: '',
    card6_img: '',
    card6_price: '',
    card7: '',
    card7_img: '',
    card7_price: '',
    card8: '',
    card8_img: '',
    card8_price: '',
    card9: '',
    card9_img: '',
    card9_price: '',
    website_footNote: ''
});

const next = ref(false);
const lengthArray = ref(null);
let profile_img = ref('');
const profilePicture = ref(null);

const formatUrl = (url) => {
    // Check if the URL starts with http:// or https://
    if (!/^https?:\/\//i.test(url)) {
        // Prepend https:// if it doesn't
        return `https://${url}`;
    }
    return url;
};

function logout(button){
    Inertia.post(route('logout'), {button});
}

onMounted(()=>{
    getWebsiteInfo();
})

function showMore(){
    next.value=true;
    if(lengthArray.value<9){
        alert(`There are only ${lengthArray.value} to be displayed`);
    }else{
    getWebsiteInfo();
    }
}

async function getWebsiteInfo(){
    try{

        const response = await axios.get('/showUser');
        if (response.data) {
            profile_img= response.data.profile_img;
        }
        profilePicture.value = response.data.profile_img 
        ? `/storage/user_profile/${response.data.profile_img}` 
        : '/storage/user_profile/default-profile.png';

        const getBusinessInfo = await axios.get('/api/business_info', {
            params: {user_id: 1}
        });

        const getWebsiteInfo1 = await axios.get('/api/website', {
            params: {business_id: 1}
        });
        console.log('Business data: ',getBusinessInfo.data);
        
        console.log('Website data: ',getWebsiteInfo1.data);

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

        businessInfo.website_footNote.value = getWebsiteInfo1.data.website_footNote;

        const getProductsInfo = await axios.get('/api/sale-products', {
            params: {business_id: 1}
        });

        const saleProducts = getProductsInfo.data;
        lengthArray.value = saleProducts.length;

        if(saleProducts){
        saleProducts.forEach((product, index) => {
            console.log('imgPath: '+product.product_img);
            const imgPath = product.product_img.replace('products/', '');

            
            if(!next.value){
                    if(index<9){
                    textAreas[`card${index+1}`] = product.product_name;
                    textAreas[`card${index+1}_img`] = imgPath;
                    textAreas[`card${index+1}_price`]=product.product_price;
                    }

            }else{

                for(let i=1;i<=9;i++){
                    textAreas[`card${i}`] = '';
                    textAreas[`card${i}_img`] = '';
                    textAreas[`card${i}_price`]='';
                }
                if(index>=9&&index<=17){
                textAreas[`card${index-8}`] = product.product_name;
                textAreas[`card${index-8}_img`] = imgPath;
                textAreas[`card${index-8}_price`]=product.product_desc;
                }
            }
        });
        }else{
            alert("There are currently no on SALE products.");
        }
    
    }catch(error){
        console.error('There was an error fetching the data:', error);
    }
}

function goTochatPage(){
    //none pa
}
</script>

<template>
        <!-- header -->
        <div class=" bg-business-website-header flex items-center p-5">
            <div class="ml-[50px] w-[50px] h-[50px]">
                <img :src='businessInfo.businessImage.value' class="w-full h-full object-cover rounded-full"/>
            </div>
            <div class="ml-auto flex items-center space-x-[40px] mr-[40px]">
                    <a class="text-white text-[18px]" :href="route('homepage')">Home</a>
                    <a class="text-white text-[18px]">Chat with Us</a>
                    <a class="text-black text-[18px]" :href="route('products_page')">Products & Services</a>
                    <a class="text-white text-[18px]":href="route('aboutUs_page')">About Us</a>
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

       


    <!-- products -->
    <section>
        <div class=" bg-website-main flex min-h-screen relative" style="min-height: calc(100vh + 800px);">

<div class="flex flex-col items-center p-3 absolute top-[10px] left-0 right-0 bottom-[500px] m-auto">
    <p class="mt-[30px] text-[40px]  text-white font-bold  text-center">SALE Products</p>
</div>

<div class=" mt-8 mx-auto my-auto flex flex-wrap justify-center gap-4 w-full max-w-screen-lg mt-[10px] px-4 pt-[200px]">
    
    <div class="block flex flex-row gap-9">
    <!-- card 1 -->
        <div v-if="textAreas.card1_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card1_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card1}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card1_price}}</p>
        </div>

     <!-- card 2 -->
     <div v-if="textAreas.card2_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card2_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card2}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card2_price}}</p>
        </div>
    

    <!-- card 3 -->
    <div v-if="textAreas.card3_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card3_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card3}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card3_price}}</p>
        </div>
    </div>

    <div class="block flex flex-row gap-9">
    <!-- card 4 -->
    <div v-if="textAreas.card4_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card4_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card4}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card4_price}}</p>
        </div>

    <!-- card 5 -->
    <div v-if="textAreas.card5_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card5_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card5}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card5_price}}</p>
        </div>

    <!-- card 6 -->
    <div v-if="textAreas.card6_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card6_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card6}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card6_price}}</p>
        </div> 
    </div>

    <div class="block flex flex-row gap-9">

        <!-- card 7 -->
        <div v-if="textAreas.card7_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card7_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card7}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card7_price}}</p>
        </div> 

        <!-- card 8 -->
        <div v-if="textAreas.card8_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card8_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card8}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card8_price}}</p>
        </div> 

        <!-- card 9 -->
        <div v-if="textAreas.card9_img" class="flex flex-col bg-white w-[340px] h-[360px] p-4 rounded-lg shadow-lg border border-gray-200">
            <img :src="`/storage/products/${textAreas.card9_img}`" class="w-full h-4/5 object-cover"/>
            <p class="text-black text-[18px] mt-[10px] text-center">{{textAreas.card9}}</p>
            <p class="text-black text-[18px] text-center">Price: <i class="fa-solid fa-peso-sign"></i>{{textAreas.card9_price}}</p>
        </div> 

    </div>
    <div class="block mx-auto">
        <button @click="showMore" class="cursor-pointer bg-white border border-white rounded-sm py-6 px-9">Show More</button>
        
    </div>
</div>
        </div>
    </section>

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
        <p class=" text-xl text-white">{{ textAreas.website_footNote }}</p>
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
    <p class="text-[17px] text-white mt-2"><i class="fa fa-copyright"></i> {{ textAreas.businessName }} All rights reserved</p>
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