<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const products = ref([]);
const showAddProductModal = ref(false);
const showCategoriesModal = ref(false);
const showEditProductModal = ref(false);
const listedCategories = ref([]);
const searchQuery = ref('');

const newProduct = ref({
    name: '',
    brand: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
    seniorPWD_discountable: null,
    description: ''
});

const editProduct = ref({
    id: null,
    name: '',
    brand: '',
    price: 0,
    category: '',
    stock: 0,
    sold: 0,
    status: '',
    expDate: '',
    image: null,
    seniorPWD_discountable: null,
    description: ''
});

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

const addProduct = async () => {
    try {
        const formData = new FormData();
        for (const key in newProduct.value) {
            formData.append(key, newProduct.value[key]);
        }
        const response = await axios.post('/api/products', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        products.value.push(response.data.product);
        showAddProductModal.value = false;
        resetNewProduct();
    } catch (error) {
        console.error("Error adding product:", error);
    }
};

const validateProduct = (product) => {
    if (!product.name || !product.brand || !product.price || !product.category || !product.stock || !product.sold || !product.status || !product.expDate || !product.seniorPWD_discountable || !product.description) {
        return false;
    }
    return true;
};

const updateProduct = async () => {
    if (!validateProduct(editProduct.value)) {
        console.error("Validation error: Missing required fields");
        return;
    }

    try {
        const formData = new FormData();
        for (const key in editProduct.value) {
            if (editProduct.value[key] !== null) {
                formData.append(key, editProduct.value[key]);
            }
        }

        if (editProduct.value.image && typeof editProduct.value.image !== 'string') {
            formData.append('image', editProduct.value.image);
        }

        console.log([...formData.entries()]); // Debugging line

        const response = await axios.post(`/api/products/${editProduct.value.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'X-HTTP-Method-Override': 'PUT',
            }
        });

        const index = products.value.findIndex(product => product.id === editProduct.value.id);
        products.value[index] = response.data.product;
        showEditProductModal.value = false;
        resetEditProduct();
    } catch (error) {
        if (error.response && error.response.data) {
            console.error("Validation errors:", error.response.data);
        } else {
            console.error("Error updating product:", error);
        }
    }
};


const deleteProduct = async (id) => {
    if (confirm("Are you sure you want to delete this product?")) {
        try {
            await axios.delete(`/api/products/${id}`);
            products.value = products.value.filter(product => product.id !== id);
        } catch (error) {
            console.error("Error deleting product:", error);
        }
    }
};

const editProductDetails = (product) => {
    editProduct.value = { ...product };
    showEditProductModal.value = true;
};

const resetNewProduct = () => {
    newProduct.value = {
        name: '',
        brand: '',
        price: 0,
        category: '',
        stock: 0,
        sold: 0,
        status: '',
        expDate: '',
        image: null,
        seniorPWD_discountable: null,
        description: ''
    };
};

const resetEditProduct = () => {
    editProduct.value = {
        id: null,
        name: '',
        brand: '',
        price: 0,
        category: '',
        stock: 0,
        sold: 0,
        status: '',
        expDate: '',
        image: null,
        seniorPWD_discountable: null,
        description: ''
    };
};

const filteredProducts = computed(() => {
    if (!searchQuery.value) {
        return products.value;
    }
    return products.value.filter(product => {
        const searchTerm = searchQuery.value.toLowerCase();
        return (
            product.name.toLowerCase().includes(searchTerm) ||
            product.category.toLowerCase().includes(searchTerm) ||
            product.status.toLowerCase().includes(searchTerm)
        );
    });
});

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        newProduct.value.image = file;
    }
};

const handleEditImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        editProduct.value.image = file;
    }
};


fetchProducts();
fetchListedCategories();
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-5">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold">List of Products</h2>
                            <div class="relative w-64">
                                <font-awesome-icon
                                    :icon="['fas', 'magnifying-glass']"
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search products..."
                                    class="pl-10 pr-4 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-300 w-full h-10"
                                />
                            </div>
                        </div>
                        <div class="overflow-auto" style="max-height: 430px;">
                            <table class="min-w-full bg-white dark:bg-gray-800 text-xs">
                                <thead>
                                <tr>
                                    <th class="px-2 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">ID No.</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Image</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle">Name</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-left align-middle">Brand</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Price (PHP)</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Category</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Stock</th>
                                    <th class="px-2 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Sold</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Status</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Exp. Date</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Discountable</th>
                                    <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="filteredProducts.length === 0">
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" colspan="10">No products available.</td>
                                </tr>
                                <tr v-for="product in filteredProducts" :key="product.id">
                                    <td class="px-2 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.id }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <img :src="'/storage/' + product.image" alt="Product Image" class="w-12 h-12 object-cover"/>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-left align-middle">{{ product.name }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-left align-middle">{{ product.brand }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.price }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.category }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.stock }}</td>
                                    <td class="px-2 py-4 border-b border-gray-200 dark:border-gray-700 text-center">{{ product.sold }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center">
                                            <span :class="{
                                                'bg-red-500 text-white py-1 px-3 rounded-full': product.status === 'Unlisted',
                                                'bg-green-500 text-white py-1 px-3 rounded-full': product.status === 'Listed',
                                                'bg-gray-500 text-white py-1 px-3 rounded-full': product.status === 'Out of Stock',
                                            }">{{ product.status }}</span>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ product.expDate }}</td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 space-x-1">
                                        <input type="radio" :name="'discountable_' + product.id" value="yes" v-model="product.seniorPWD_discountable" />
                                        <label for="discountable_yes">Yes</label>
                                        <input type="radio" :name="'discountable_' + product.id" value="no" v-model="product.seniorPWD_discountable" />
                                        <label for="discountable_no">No</label>
                                    </td>
                                    <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex space-x-4">
                                            <button @click="editProductDetails(product)" class="bg-yellow-500 text-white py-2 px-3 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                            </button>
                                            <button @click="deleteProduct(product.id)" class="bg-red-500 text-white py-2 px-3 rounded-full">
                                                <font-awesome-icon :icon="['fas', 'trash-can']" size="sm" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-4 mr-5 space-x-4">
                    <button @click="showAddProductModal = true" class="bg-blue-500 text-white py-2 px-4 rounded">+ Add Product</button>
                    <button @click="showCategoriesModal = true" class="bg-gray-500 text-white py-2 px-4 rounded">Categories</button>
                </div>
            </div>
        </div>

        <div v-if="showAddProductModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg relative">
                <h3 class="text-xl font-semibold text-center mb-4">Add a Product</h3>
                <form @submit.prevent="addProduct">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label class="block text-sm font-medium text-gray-700">Image here:</label>
                            <div class="w-full h-32 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 cursor-pointer relative">
                                <input type="file" id="image" @change="handleImageUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"/>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                            <input type="text" id="name" v-model="newProduct.name" class="input-field" required/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="brand" class="block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" id="brand" v-model="newProduct.brand" class="input-field"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="price" class="block text-sm font-medium text-gray-700">Price (PHP)*</label>
                            <div class="relative">
                                <input type="number" id="price" v-model="newProduct.price" class="input-field pl-10" required/>
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="category" class="block text-sm font-medium text-gray-700">Category *</label>
                            <select id="category" v-model="newProduct.category" class="input-field" required>
                                <option v-for="category in listedCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="stock" class="block text-sm font-medium text-gray-700">Stock *</label>
                            <input type="number" id="stock" v-model="newProduct.stock" class="input-field" required/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="sold" class="block text-sm font-medium text-gray-700">Sold</label>
                            <input type="number" id="sold" v-model="newProduct.sold" class="input-field"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="status" v-model="newProduct.status" class="input-field" required>
                                <option value="Listed">Listed</option>
                                <option value="Unlisted">Unlisted</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="expDate" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                            <input type="date" id="expDate" v-model="newProduct.expDate" class="input-field"/>
                        </div>
                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">Description:</label>
                            <textarea id="description" v-model="newProduct.description" rows="3" class="input-field" placeholder="Enter your description here (will be shown in the website)…"></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Discountable:</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input type="radio" id="discountable_yes" value="yes" v-model="newProduct.seniorPWD_discountable" class="mr-1"/>
                                    <label for="discountable_yes" class="text-sm text-gray-700">Yes</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="discountable_no" value="no" v-model="newProduct.seniorPWD_discountable" class="mr-1"/>
                                    <label for="discountable_no" class="text-sm text-gray-700">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 space-x-3">
                        <button @click="showAddProductModal = false" class="button-cancel">Cancel</button>
                        <button type="submit" class="button-ok">OK</button>
                    </div>
                </form>
            </div>
        </div>

        <div v-if="showEditProductModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg relative">
                <h3 class="text-xl font-semibold text-center mb-4">Edit Product</h3>
                <form @submit.prevent="updateProduct">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_image" class="block text-sm font-medium text-gray-700">Image here:</label>
                            <div class="w-full h-32 border border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 cursor-pointer relative">
                                <input type="file" id="edit_image" @change="handleEditImageUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"/>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_name" class="block text-sm font-medium text-gray-700">Name *</label>
                            <input type="text" id="edit_name" v-model="editProduct.name" class="input-field" required/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_brand" class="block text-sm font-medium text-gray-700">Brand</label>
                            <input type="text" id="edit_brand" v-model="editProduct.brand" class="input-field"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_price" class="block text-sm font-medium text-gray-700">Price (PHP)*</label>
                            <div class="relative">
                                <input type="number" id="edit_price" v-model="editProduct.price" class="input-field pl-10" required/>
                            </div>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_category" class="block text-sm font-medium text-gray-700">Category *</label>
                            <select id="edit_category" v-model="editProduct.category" class="input-field" required>
                                <option v-for="category in listedCategories" :key="category.id" :value="category.name">{{ category.name }}</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_stock" class="block text-sm font-medium text-gray-700">Stock *</label>
                            <input type="number" id="edit_stock" v-model="editProduct.stock" class="input-field" required/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_sold" class="block text-sm font-medium text-gray-700">Sold</label>
                            <input type="number" id="edit_sold" v-model="editProduct.sold" class="input-field"/>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select id="edit_status" v-model="editProduct.status" class="input-field" required>
                                <option value="Listed">Listed</option>
                                <option value="Unlisted">Unlisted</option>
                                <option value="Out of Stock">Out of Stock</option>
                            </select>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label for="edit_expDate" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                            <input type="date" id="edit_expDate" v-model="editProduct.expDate" class="input-field"/>
                        </div>
                        <div class="col-span-2">
                            <label for="edit_description" class="block text-sm font-medium text-gray-700">Description:</label>
                            <textarea id="edit_description" v-model="editProduct.description" rows="3" class="input-field" placeholder="Enter your description here (will be shown in the website)…"></textarea>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Discountable:</label>
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center">
                                    <input type="radio" id="edit_discountable_yes" value="yes" v-model="editProduct.seniorPWD_discountable" class="mr-1"/>
                                    <label for="edit_discountable_yes" class="text-sm text-gray-700">Yes</label>
                                </div>
                                <div class="flex items-center">
                                    <input type="radio" id="edit_discountable_no" value="no" v-model="editProduct.seniorPWD_discountable" class="mr-1"/>
                                    <label for="edit_discountable_no" class="text-sm text-gray-700">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 space-x-3">
                        <button @click="showEditProductModal = false" class="button-cancel">Cancel</button>
                        <button type="submit" class="button-ok">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <CategoriesModal v-if="showCategoriesModal" @close-categories-modal="showCategoriesModal = false" />
    </AuthenticatedLayout>
</template>


<style scoped>
.input-field {
    width: 100%;
    padding: 0.4rem 0.6rem;
    border: 1px solid #CBD5E0;
    border-radius: 0.375rem;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    transition: border-color 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.input-field:focus {
    border-color: #3182CE;
    box-shadow: 0 0 0 1px #3182CE;
    outline: none;
}

.button-cancel {
    background-color: #DC2626;
    color: #FFFFFF;
    padding: 0.4rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: background-color 0.2s ease-in-out;
}

.button-cancel:hover {
    background-color: #B91C1C;
}

.button-ok {
    background-color: #059669;
    color: #FFFFFF;
    padding: 0.4rem 1rem;
    border-radius: 0.375rem;
    font-weight: 600;
    transition: background-color 0.2s ease-in-out;
}

.button-ok:hover {
    background-color: #047857;
}
</style>