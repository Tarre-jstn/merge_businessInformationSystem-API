<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

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
</script>

<template>
    <Head title="Website" />

    <AuthenticatedLayout>
        <!-- header of edit website to save changes -->
    
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
                    <button class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p class="text-xl text-white">BUSINESS NAME</p>
                </div>
                <div class="mt-5">
                    <button class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p class="font-bold text-xl text-white">CHEAP AND QUALITY FOOD</p>
                </div>
                <div class="mt-5">
                    <button class="bg-white border border-white rounded-xl p-1">Edit Text</button>
                    <p id="business-details"class="text-white">
                        Our food service system is designed to meet the specific 
                        needs of our customers. We ensure to avail you the cheapest and 
                        high quality product in the market.

                    </p>
                </div>
            
            </div>

            <!-- image -->
            <div class="mt-5 ml-auto flex-grow-0 w-1/2 max-w-md">
                <button class="bg-white border border-white rounded-xl p-1">Edit Photo</button>
                <img src="https://picsum.photos/200" class="mt-5 w-full h-auto object-cover rounded-tl-[30px]"/>
            </div>
        </div>
    
    </AuthenticatedLayout>

</template>

