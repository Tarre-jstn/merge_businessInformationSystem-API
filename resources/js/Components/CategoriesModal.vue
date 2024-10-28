<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['close-categories-modal']);

const listedCategories = ref([]);
const unlistedCategories = ref([]);

onMounted(async () => {
    await fetchCategories();
});

const fetchCategories = async () => {
    try {
        const response = await axios.get('/api/categories');
        const categories = response.data;

        listedCategories.value = categories.filter(category => category.status === 'listed');
        unlistedCategories.value = categories.filter(category => category.status === 'unlisted');
    } catch (error) {
        console.error("Error fetching categories:", error);
    }
};

const addCategory = async (type) => {
    const categoryName = prompt("Enter category name:");
    if (!categoryName) return;

    const newCategory = {
        name: categoryName,
        status: type
    };

    try {
        const response = await axios.post('/api/categories', newCategory);
        const savedCategory = response.data;

        if (type === 'listed') {
            listedCategories.value.push(savedCategory);
        } else {
            unlistedCategories.value.push(savedCategory);
        }

        emit('category-added');
    } catch (error) {
        console.error("Error adding category:", error);
    }
};

const removeCategory = async (category, type) => {
    try {
        if (type === 'listed') {
            listedCategories.value = listedCategories.value.filter(c => c.id !== category.id);
            category.status = 'unlisted';
            await axios.put(`/api/categories/${category.id}`, category);
            unlistedCategories.value.push(category);
        } else {
            unlistedCategories.value = unlistedCategories.value.filter(c => c.id !== category.id);
            category.status = 'listed';
            await axios.put(`/api/categories/${category.id}`, category);
            listedCategories.value.push(category);
        }
    } catch (error) {
        console.error("Error removing category:", error);
    }
};


const closeModal = () => {
    emit('close-categories-modal');
};

const saveCategories = async () => {
    try {
        const allCategories = [...listedCategories.value, ...unlistedCategories.value];

        for (const category of allCategories) {
            await axios.put(`/api/categories/${category.id}`, category);
        }

        emit('close-categories-modal');
    } catch (error) {
        console.error("Error saving categories:", error);
    }
};
</script>

<template>
    <div @click="closeModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div  @click.stop class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-2xl text-center align-middle font-semibold mb-4">Categories</h2>
            <div class="mb-4">
                <h3 class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-lg text-white ">Listed Categories</h3>
                <div class="border p-4 rounded-lg mb-2">
                    <div v-for="category in listedCategories" :key="category.id" class="inline-flex items-center bg-gray-200 text-gray-800 py-1 px-3 rounded-full mr-2 mb-2">
                        {{ category.name }}
                        <button @click="removeCategory(category, 'listed')" class="ml-2 text-red-500">x</button>
                    </div>
                </div>
                <div class="mb-2">
                    <button @click="addCategory('listed')" class=" hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">+ Add Category</button>
                </div>
                <p class="text-sm text-gray-500">*Listed Categories will BE SHOWN on the website</p>
            </div>
            <div class="mb-4">
                <h3 class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-lg text-white">Unlisted Categories</h3>
                <div class="border p-4 rounded-lg mb-2">
                    <div v-for="category in unlistedCategories" :key="category.id" class="inline-flex items-center bg-gray-200 text-gray-800 py-1 px-3 rounded-full mr-2 mb-2">
                        {{ category.name }}
                        <button @click="removeCategory(category, 'unlisted')" class="ml-2 text-red-500">x</button>
                    </div>
                </div>
                <p class="text-sm text-gray-500">*Unlisted Categories will NOT BE SHOWN on the website</p>
            </div>
            <div class="flex justify-center gap-4">
                <button @click="closeModal"     class="px-4 py-2 block text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Cancel</button>
                <button @click="saveCategories" class=" hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">Save Categories</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fixed {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
