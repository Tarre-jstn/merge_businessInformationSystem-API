<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Chatbot from '@/Components/Chatbot.vue';
import ErrorToast from '@/Components/ErrorToast.vue';
import SuccessToast from '@/Components/SuccessToast.vue';

// Define the chatbot data structure using ref
const chatbot = ref({
    id: null,
    bwh: '',     // Business Working Hours
    pd: '',      // Product Description
    shopee: '',  // Shopee link
    lazada: '',  // Lazada link
    region1: '', // Region 1 info
    region2: '', // Region 2 info
    region3: '', // Region 3 info
    region4a: '', // Region 4-A info
    region4b: '', // Region 4-B info
    region5: '',  // Region 5 info
    ncr: '',      // NCR info
    car: ''       // CAR info
});

const showErrorToast = ref(false);
const showSuccessToast = ref(false);
const toastMessage = ref('');

const showToast = (message, type) => {
  toastMessage.value = message;
  if (type === 'error') showErrorToast.value = true;
  if (type === 'success') showSuccessToast.value = true;
  setTimeout(() => {
    showErrorToast.value = false;
    showSuccessToast.value = false;
  }, 3000);
};

onMounted(async () => {
    try {
        const response = await axios.get('/api/chatbot-response');
        const chatbotData = response.data[0];  // Get the first record
        if (chatbotData) {
            chatbot.value = {
                id: chatbotData.chatbot_id, 
                bwh: chatbotData.chabot_BWHours,
                pd: chatbotData.chabot_BPDescription,
                shopee: chatbotData.chabot_Shopee,
                lazada: chatbotData.chabot_Lazada,
                region1: chatbotData.chabot_Region1,
                region2: chatbotData.chabot_Region2,
                region3: chatbotData.chabot_Region3,
                region4a: chatbotData.chabot_Region4A,
                region4b: chatbotData.chabot_Region4B,
                region5: chatbotData.chabot_Region5,
                ncr: chatbotData.chabot_NCR,
                car: chatbotData.chabot_CAR
            };
        }
    } catch (error) {
        console.error(error);
    }
});


const updateChatbotData = async () => {
    try {
        const response = await axios.put(`/api/chatbot-response/${chatbot.value.id}`, {
            chabot_BWHours: chatbot.value.bwh,
            chabot_BPDescription: chatbot.value.pd,
            chabot_Lazada: chatbot.value.lazada,
            chabot_Shopee: chatbot.value.shopee,
            chabot_Region1: chatbot.value.region1,
            chabot_Region2: chatbot.value.region2,
            chabot_Region3: chatbot.value.region3,
            chabot_Region4A: chatbot.value.region4a,
            chabot_Region4B: chatbot.value.region4b,
            chabot_Region5: chatbot.value.region5,
            chabot_NCR: chatbot.value.ncr,
            chabot_CAR: chatbot.value.car
        });
        console.log("Response data:", response.data);

        if (response.data.success) {
            showToast("Business Info Has Been Updated", "success");
            setTimeout(() => {
                window.location.reload();
            }, 2000);  
        } else {
            showToast("Failed to update respons", "error");

        }
    } catch (error) {
        console.error("Error updating responses:", JSON.stringify(error.response?.data, null, 2));
        showToast("Failed to update response", "error");
    }
};

</script>


<template>
    <Head title="Update Chatbot Responses" />
    
    <AuthenticatedLayout>
        <div class="container mx-auto p-4 flex flex-col md:flex-row">
            <!-- Left Column (Business Details) -->
            <div class="w-full md:w-1/2 pt-20">
                <h1 class="text-3xl font-bold mb-4">Update Chatbot Responses</h1>
                <div class="space-y-6">
                    <div>
                        <label for="business-working-hours" class="block text-gray-700 text-sm font-bold mb-2">
                                Business Working Hours
                            </label>
                            <input
                                type="text"
                                placeholder="e.g. 9AM - 9PM" 
                                id="business-working-hours"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="chatbot.bwh"
                            >
                    </div>
                    <div>
                        <label for="product-description" class="block text-gray-700 text-sm font-bold mb-2">
                                Product Description
                            </label>
                            <input
                                type="text"
                                placeholder="e.g. Food Products" 
                                id="product-description"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="chatbot.pd"
                            >
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="lazada" class="flex items-center text-gray-700 text-sm font-bold mb-2">
                                Lazada
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 100 100">
                                <path d="M 25.992188 17.001953 C 25.605279 17.001953 25.224109 17.108194 24.890625 17.314453 L 8.4648438 27.421875 C 7.5534844 27.982137 7 28.976035 7 30.044922 L 7 59.128906 C 7 60.190844 7.552998 61.183824 8.4550781 61.744141 L 48.417969 86.548828 C 48.749873 86.75509 49.118262 86.888512 49.5 86.953125 L 49.5 87 L 50 87 L 50.5 87 L 50.5 86.953125 C 50.881738 86.888512 51.250127 86.75509 51.582031 86.548828 L 91.544922 61.744141 C 92.447002 61.183824 92.998047 60.190844 92.998047 59.128906 L 92.998047 30.042969 C 92.998047 28.974082 92.444562 27.982137 91.533203 27.421875 L 75.109375 17.314453 C 74.777357 17.109101 74.394819 17.001953 74.007812 17.001953 C 73.617447 17.001953 73.23202 17.111022 72.900391 17.316406 L 72.898438 17.316406 L 57.544922 26.847656 C 55.276867 28.255608 52.667174 28.998047 49.998047 28.998047 C 47.32892 28.998047 44.72118 28.255608 42.453125 26.847656 L 27.099609 17.316406 C 26.766378 17.109573 26.382552 17.001953 25.992188 17.001953 z M 25.992188 19.001953 C 26.011817 19.001953 26.030152 19.008448 26.044922 19.017578 L 41.398438 28.546875 C 43.982382 30.150924 46.957174 30.998047 49.998047 30.998047 C 53.03892 30.998047 56.015664 30.150924 58.599609 28.546875 L 73.953125 19.017578 C 73.973495 19.004958 73.988173 19.001953 74.007812 19.001953 C 74.028802 19.001953 74.042661 19.005052 74.056641 19.013672 L 74.058594 19.015625 L 90.486328 29.125 C 90.522141 29.147016 90.551166 29.178616 90.583984 29.205078 L 50 51.429688 L 9.4160156 29.205078 C 9.4488802 29.17857 9.4778589 29.147016 9.5136719 29.125 L 25.941406 19.015625 C 25.953926 19.007925 25.973098 19.001953 25.992188 19.001953 z M 74.013672 22.949219 C 73.557725 22.950146 73.103028 23.075242 72.701172 23.324219 L 67.236328 26.716797 A 0.50005 0.50005 0 1 0 67.763672 27.566406 L 73.226562 24.175781 C 73.710851 23.875734 74.320245 23.873693 74.804688 24.171875 L 84.154297 29.925781 A 0.5002983 0.5002983 0 1 0 84.679688 29.074219 L 75.328125 23.320312 C 74.925346 23.072402 74.469619 22.948292 74.013672 22.949219 z M 65.318359 27.996094 A 0.50005 0.50005 0 0 0 65.048828 28.074219 L 63.4375 29.074219 A 0.50081172 0.50081172 0 0 0 63.964844 29.925781 L 65.576172 28.925781 A 0.50005 0.50005 0 0 0 65.318359 27.996094 z M 9 30.119141 L 49.5 52.296875 L 49.5 84.861328 C 49.491356 84.856307 49.481172 84.854901 49.472656 84.849609 L 9.5097656 60.044922 C 9.1818457 59.841238 9 59.514969 9 59.128906 L 9 30.119141 z M 90.998047 30.119141 L 90.998047 59.128906 C 90.998047 59.514969 90.818154 59.841238 90.490234 60.044922 L 50.527344 84.849609 C 50.518828 84.854901 50.508644 84.856307 50.5 84.861328 L 50.5 52.296875 L 90.998047 30.119141 z M 12.492188 34.992188 A 0.50005 0.50005 0 0 0 12 35.5 L 12 56.669922 C 12 57.533887 12.447481 58.33724 13.181641 58.792969 L 29.236328 68.757812 A 0.50005 0.50005 0 1 0 29.763672 67.908203 L 13.708984 57.943359 C 13.267144 57.669088 13 57.189957 13 56.669922 L 13 35.5 A 0.50005 0.50005 0 0 0 12.492188 34.992188 z M 36.478516 72.173828 A 0.50005 0.50005 0 0 0 36.236328 73.103516 L 40.236328 75.585938 A 0.50005 0.50005 0 1 0 40.763672 74.736328 L 36.763672 72.253906 A 0.50005 0.50005 0 0 0 36.478516 72.173828 z M 43.478516 76.519531 A 0.50005 0.50005 0 0 0 43.236328 77.447266 L 44.236328 78.068359 A 0.50005 0.50005 0 1 0 44.763672 77.21875 L 43.763672 76.597656 A 0.50005 0.50005 0 0 0 43.478516 76.519531 z"></path>
                                </svg>
                            </label>
                            <div>
                                <input
                                    type="text"
                                    placeholder="lazada.com.ph/shop/Store"
                                    id="lazada"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.lazada"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="shopee" class="flex items-center text-gray-700 text-sm font-bold mb-2">
                                Shopee
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" viewBox="0 0 100 100">
                                <path d="M 50 7 C 40.31831 7 32.567577 15.489997 32.089844 26 L 16.998047 26 C 15.873687 26 14.950576 26.961048 15 28.085938 L 17.166016 78.34375 C 17.350697 82.614225 20.886038 86 25.160156 86 L 74.839844 86 C 79.113962 86 82.648471 82.615469 82.832031 78.345703 L 85 28.085938 C 85.049424 26.961048 84.126313 26 83.001953 26 L 67.910156 26 C 67.432423 15.489997 59.68169 7 50 7 z M 50 9 C 58.78496 9 66 16.99056 66 27 A 1.0001 1.0001 0 0 0 67 28 L 83 28 L 80.833984 78.259766 C 80.695544 81.48 78.063726 84 74.839844 84 L 25.160156 84 C 21.936274 84 19.305334 81.479338 19.166016 78.257812 L 17 28 L 33 28 A 1.0001 1.0001 0 0 0 34 27 C 34 16.99056 41.21504 9 50 9 z M 50 12 C 42.750286 12 37 18.815147 37 27 A 1.0001 1.0001 0 0 0 38 28 L 62.001953 28 A 1.0001 1.0001 0 0 0 63.001953 27 C 63.000893 18.815217 57.249714 12 50 12 z M 50 14 C 55.690503 14 60.370248 19.251674 60.830078 26 L 39.171875 26 C 39.630919 19.251772 44.309516 14 50 14 z M 50.099609 36 C 43.335962 36 38.099609 40.279716 38.099609 46 C 38.099609 49.217019 39.642501 51.453316 41.779297 53.03125 C 43.916093 54.609184 46.637356 55.593706 49.150391 56.511719 C 52.184523 57.620843 54.592719 58.598183 56.1875 59.832031 C 57.782281 61.065879 58.599609 62.493209 58.599609 64.767578 C 58.599609 66.43674 57.671018 67.973016 56.09375 69.121094 C 54.516482 70.269172 52.301403 71 49.849609 71 C 47.118757 71 44.554841 69.994259 42.671875 68.972656 C 41.730392 68.461855 40.959155 67.94873 40.423828 67.5625 C 40.156165 67.369385 39.946967 67.209099 39.804688 67.095703 C 39.662407 66.982307 39.536803 66.870657 39.591797 66.919922 L 39.166016 66.539062 L 36.894531 69.886719 L 37.251953 70.175781 C 38.527877 71.209755 43.584463 75 49.849609 75 C 56.926063 75 62.599609 70.553269 62.599609 64.767578 C 62.599609 60.999587 60.931807 58.431943 58.595703 56.636719 C 56.2596 54.841494 53.277012 53.761387 50.523438 52.755859 C 47.807778 51.762861 45.650028 50.896576 44.226562 49.869141 C 42.803097 48.841705 42.099609 47.732671 42.099609 46.001953 C 42.099609 44.276625 42.901507 42.810746 44.306641 41.736328 C 45.711774 40.66191 47.729597 40.001953 50.099609 40.001953 C 53.257442 40.001953 56.047452 41.208102 57.554688 42.021484 C 57.708655 42.104454 58.096661 42.333363 58.435547 42.544922 L 58.4375 42.544922 C 58.901764 42.833358 59.524593 42.686049 59.8125 42.224609 L 59.814453 42.222656 L 60.849609 40.542969 L 60.847656 40.542969 C 61.129486 40.088882 61.001131 39.470585 60.544922 39.177734 C 59.248364 38.342541 55.246259 36 50.099609 36 z M 50.099609 37 C 54.93728 37 58.773557 39.227224 59.998047 40.015625 C 59.998978 40.016225 60.002906 40.016978 60.003906 40.017578 L 60.003906 40.019531 C 60.005706 40.020731 60.004212 40.005755 59.998047 40.015625 L 59.998047 40.017578 L 58.964844 41.697266 C 58.611729 41.476824 58.235329 41.251653 58.029297 41.140625 C 56.434532 40.280007 53.509777 39.001953 50.099609 39.001953 C 47.544622 39.001953 45.312836 39.707574 43.699219 40.941406 C 42.085602 42.175238 41.099609 43.960281 41.099609 46.001953 C 41.099609 48.026235 42.04934 49.531123 43.640625 50.679688 C 45.23191 51.828252 47.450347 52.697312 50.179688 53.695312 C 52.933113 54.700786 55.825182 55.768912 57.986328 57.429688 C 60.147475 59.090463 61.599609 61.286569 61.599609 64.767578 C 61.599609 69.895887 56.511156 74 49.849609 74 C 44.260521 74 39.724894 70.786656 38.267578 69.644531 L 39.371094 68.019531 C 39.507941 68.125727 39.639118 68.228227 39.839844 68.373047 C 40.403423 68.779661 41.208171 69.315989 42.195312 69.851562 C 44.169596 70.922563 46.875462 72 49.849609 72 C 52.498816 72 54.909159 71.219859 56.681641 69.929688 C 58.454122 68.639515 59.599609 66.809416 59.599609 64.767578 C 59.599609 62.236948 58.572047 60.412918 56.798828 59.041016 C 55.025609 57.669114 52.544009 56.687142 49.494141 55.572266 C 46.981175 54.654278 44.340751 53.679629 42.373047 52.226562 C 40.405343 50.773497 39.099609 48.906981 39.099609 46 C 39.099609 40.888284 43.751256 37 50.099609 37 z M 21.271484 45.994141 A 0.50005 0.50005 0 0 0 20.800781 46.521484 L 22.058594 75.736328 C 22.184878 78.673814 24.614956 81 27.554688 81 L 58.5 81 A 0.50005 0.50005 0 1 0 58.5 80 L 27.554688 80 C 25.140418 80 23.162309 78.105874 23.058594 75.693359 L 21.798828 46.478516 A 0.50005 0.50005 0 0 0 21.271484 45.994141 z M 63.5 80 A 0.50005 0.50005 0 1 0 63.5 81 L 69.5 81 A 0.50005 0.50005 0 1 0 69.5 80 L 63.5 80 z M 72.5 80 A 0.50005 0.50005 0 1 0 72.5 81 L 73.5 81 A 0.50005 0.50005 0 1 0 73.5 80 L 72.5 80 z"></path>
                                </svg>
                            </label>
                            <input
                                type="text"
                                placeholder="shopee.ph/Store" 
                                id="shopee"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                v-model="chatbot.shopee"
                            >
                        </div>
                    </div>


                    <div>
                        <button
                            type="button"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                            @click="updateChatbotData"
                        >
                            Update Responses
                        </button>
                    </div>
                </div>
            </div>


            <!-- Right Column (Regional Delivery Times) -->
            <div class="w-full md:w-1/2 flex flex-col items-center mt-8 md:mt-0 py-16">
                    <div class="mt-6 w-3/4 space-y-4">
                        <h4 class="text-xl font-bold mb-4">Regional Delivery Times</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 1 - Ilocos Region
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region1"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 2 - Cagayan Valley
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region2"
                                >
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 3 - Central Luzon
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region3"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 4A - CALABARZON
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region4a"
                                >
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 4B - MIMAROPA
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region4b"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                Region 5 - Bicol
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.region5"
                                >
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                NCR - National Capital Region
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.ncr"
                                >
                            </div>

                            <div>
                                <label for="regional-delivery" class="block text-gray-700 text-sm font-bold mb-2">
                                CAR - Cordillera Administrative Region
                                </label>
                                <input
                                    type="text"
                                    placeholder="Days/Months/Years" 
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    v-model="chatbot.car"
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <Chatbot />
        </div>
            <ErrorToast
                v-if="showErrorToast"
                :visible="showErrorToast"
                :message="toastMessage"
                @close="showErrorToast = false"
            />
            <SuccessToast
                v-if="showSuccessToast"
                :visible="showSuccessToast"
                :message="toastMessage"
                @close="showSuccessToast = false"
            />
    </AuthenticatedLayout>
</template>


