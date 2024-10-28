<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const emit = defineEmits(['close-categories-modal']);

const financeCategories = ref([]);

onMounted(async () => {
    await fetchFinanceCategories();
});


const fetchFinanceCategories = async () => {
    try {
        const response = await axios.get('/api/finance');
        financeCategories.value = response.data.financeCategories;
    } catch (error) {
        console.error("Error fetching finance and finance Categories:", error);
    }
};


const addFinanceCategory = async () => {
    const categoryName = prompt("Enter category name:");
    if (!categoryName) return;

    const newCategory = {
        category: categoryName,
    };

    try {
        const response = await axios.post('/api/finance_category', newCategory);
        const savedCategory = response.data;
            financeCategories.value.push(savedCategory);
        
        fetchFinanceCategories();
        emit('category-added');
    } catch (error) {
        console.error("Error adding category:", error);
    }
};

const removeFinanceCategory = async (category) => {
    try {
            financeCategories.value = financeCategories.value.filter(c => c.id !== category.id);
            await axios.delete(`/api/finance_category/${category.id}`, category);

    } catch (error) {
        console.error("Error removing category:", error);
    }
};


const closeModal = () => {
    emit('close-categories-modal');
};

const saveFinanceCategories = async () => {
        closeModal();
};
fetchFinanceCategories();
</script>

<template>


    <div @click="closeModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div  @click.stop class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-2xl text-center align-middle font-semibold mb-4">Categories</h2>
            <div class="mb-4">
                <h3 class="pl-2 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline text-md text-white font-semibold">Finance Categories</h3>
                <div class="border p-4 rounded-lg mb-2">
                    <div v-for="category in financeCategories" :key="category.id" class="inline-flex items-center bg-gray-200 text-gray-800 py-1 px-3 rounded-full mr-2 mb-2">
                        {{ category.category }}
                        <!-- Conditionally render the remove button -->
                        <button 
                            v-if="category.category !== 'income' && category.category !== 'expense'"
                            @click="removeFinanceCategory(category)"
                            class="ml-2 text-red-500">x</button>
                    </div>
                </div>
                <div class="flex justify-center items-center space-x-2">
                    <button 
                    @click="closeModal" 
                    type="button"
                    class="px-4 py-2 block text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 hover:scale-105 duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Cancel
                </button>
                    <button @click="addFinanceCategory" class="hover:bg-blue-600 transition hover:scale-105 ease-in-out duration-150 bg-blue-500 text-white py-2 px-4 rounded">+ Add Category</button>

                </div>

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
