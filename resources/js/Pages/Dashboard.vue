<template>
    <AuthenticatedLayout>
        <div class="flex flex-row">
            <div class="max-w-7xl sm:px-6 lg:px-8 py-6 flex flex-col" style="width: 60vw;">
                <div class="bg-whiteoverflow-hidden shadow-sm sm:rounded-lg" style="background-color: #0F2C4A;">
                    <div class="p-6 text-gray-900 dark:text-gray-100">Welcome to the Home Page!</div>
                </div>
                <div class="flex flex-row justify-center">
                    <div class="inventory_table m-4">
                        <table>
                            <tr style="font-size: 1.375rem;">
                                <div style="padding: 0.4rem 0.938rem 0.4rem;"><b>Inventory</b></div>
                            </tr>
                            <tr style="font-size: 1rem; background-color: white">
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Sold</th>
                                <th>Stock</th>
                            </tr>
                            <tr v-for="product in filteredProducts" :key="product.id">
                                <td>{{ product.name }}</td>
                                <td>{{ product.category }}</td>
                                <td>{{ product.price }}</td>
                                <td>{{ product.sold }}</td>
                                <td>{{ product.stock }}</td>
                            </tr>
                        </table>
                        <button style="margin-left: 11vw; margin-top: 1.3vh;">
                            <ResponsiveNavLink :href="route('inventory')" :active="route().current('inventory')" style="color: white; font-size: 12px;">View all products</ResponsiveNavLink>
                        </button>
                    </div>
                    <DoughnutChart />
                </div>
            </div>
            <div class="flex flex-col">
                <vue-cal hide-view-selector :time="false" active-view="month" xsmall class=" p-6" style="background-color: #0F2C4A; margin-right: 30px; margin-top: 25px; height: 45vh; color: white; font-weight: bold; border-radius: 1rem;">
                    <template #arrow-prev>
                        <i class="icon material-icons">Previous</i>
                    </template>
                    <template #arrow-next>
                        <i class="icon material-icons">Next</i>
                    </template>
                </vue-cal>
                <div>
                    <h3>Products Sold Each Month</h3>
                    <ul>
                        <li v-for="(sales, month) in soldProductsByMonth" :key="month">
                            {{ month }}: {{ sales }} products sold
                        </li>
                    </ul>
                </div>
                <div>
                    Social Media
                    <div class="flex flex-row">
                        <button>Facebook</button>
                        <button>Twitter</button>
                    </div>
                    <div class="flex flex-row">
                        <button>Instagram</button>
                        <button>Tiktok</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import DoughnutChart from '@/Components/DoughnutChart.vue';
import VueCal from 'vue-cal';
import 'vue-cal/dist/vuecal.css';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

// Product Data and Modal Management
const products = ref([]);
const showAddProductModal = ref(false);
const showEditProductModal = ref(false);
const listedCategories = ref([]);
const searchQuery = ref('');
const newProduct = ref({ /* your product structure */ });
const editProduct = ref({ /* your edit structure */ });
const imagePreviewUrl = ref(null);
const editImagePreviewUrl = ref(null);

// Fetch Products and Categories
const fetchProducts = async () => {
    try {
        const response = await axios.get('/api/products');
        products.value = response.data;
    } catch (error) {
        console.error("Error fetching products:", error);
    }
};

const fetchListedCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        listedCategories.value = response.data.filter(category => category.status === 'listed');
    } catch (error) {
        console.error("Error fetching categories:", error);
    }
};

// Filtering and Sorting Products
const filteredProducts = computed(() => {
    if (!searchQuery.value) return products.value;
    return products.value.filter(product =>
        product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Function to calculate total products sold each month
const soldProductsByMonth = computed(() => {
    const monthlySales = {};

    products.value.forEach(product => {
        const soldDate = new Date(product.soldDate);  // Assuming `soldDate` field exists
        const month = soldDate.getMonth() + 1;  // Months are 0-indexed in JS, so we add 1
        const year = soldDate.getFullYear();
        const monthKey = `${year}-${month.toString().padStart(2, '0')}`;  // Format as "YYYY-MM"

        if (!monthlySales[monthKey]) {
            monthlySales[monthKey] = 0;
        }

        monthlySales[monthKey] += product.sold;  // Add the number of sold products
    });

    return Object.entries(monthlySales).sort((a, b) => new Date(a[0]) - new Date(b[0]));  // Sort by date
});

// Fetch initial data
fetchProducts();
fetchListedCategories();
</script>

<style>
/* Button and Table Styling */
button {
    background-color: #0F2C4A;
    color:#FFFFFF;
    border-radius: 14px;
}
td, th {
    border-top: 1px solid #0F2C4A;
    border-bottom: 1px solid #0F2C4A;
    text-align: center;
}

table {
    border: 5px solid #0F2C4A;
    border-collapse: separate;
    width: 33vw;
    height: 26vh;
    border-radius: 14px;
}

/* Responsive design */
@media (max-width: 1024px) {
    .flex {
        flex-direction: column;
    }

    .inventory_table {
        width: 100%;
        margin: 1rem 0;
    }
    
    table {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .max-w-7xl {
        width: 100vw;
    }
    
    .py-6 {
        padding: 0.5rem;
    }
    
    td, th {
        font-size: 0.625rem;
        padding: 0.25rem;
    }
}
</style>
