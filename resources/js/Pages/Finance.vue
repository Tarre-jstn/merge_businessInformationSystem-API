<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FinanceCategoriesModal  from "@/Components/FinanceCategories.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

const finances = ref([]);
const financeListedCategories = ref([]);
const showAddFinanceModal = ref(false);
const showFinanceCategoriesModal = ref(false);
const showEditFinanceModal = ref(false);
const searchQuery = ref('');

const newFinance = ref({
    id: null,
    description: '',
    date: '',
    category: '',
    amount: 0, 
});

const editFinance = ref({
    id: null,
    description: '',
    date: '',
    category: '',
    amount: 0, 

});

// watch(() => editFinance.value.on_sale, (newValue) => {
//     if (newValue === 'no') {
//         editFinance.value.on_sale_price = 0;
//     }
// });

const fetchFinances = async () => {
    try {
        const response = await axios.get('/api/finance');
        finances.value = response.data.finances;
        financeListedCategories.value = response.data.financeCategories;
    } catch (error) {
        console.error("Error fetching finance and finance Categories:", error);
    }
};

// const fetchFinanceCategories = async () => {
//     try {
//         const response = await axios.get('/api/finance');
//         finances.value = response.data;
//         financeListedCategories.value = response.data;
//     } catch (error) {
//         console.error("Error fetching finance and finance Categories:", error);
//     }
// };

const handleFinanceCategoryAdded = () => {
    fetchFinances();
};


const showSuccessAddModal = ref(false);
const addFinance = async () => {
    try {
        const formData = new FormData();
        for (const key in newFinance.value) {
            if (newFinance.value[key] !== null) {
                formData.append(key, newFinance.value[key]);
            }
        }
        const response = await axios.post('/api/finance', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        finances.value.push(response.data.finance);
        showAddFinanceModal.value = false;

        showSuccessAddModal.value = true;
        setTimeout(() => {
        showSuccessAddModal.value = false;
        }, 1000) 
        fetchFinances();

        resetNewFinance();
    } catch (error) {
        console.error("Error adding finance:", error);
    }
};

const showSuccessEditModal = ref(false);
const updateFinance = async () => {
    try {
        const formData = new FormData();

        
        // for (const key in editFinance.value) {
        //     if (editFinance.value[key] !== null) {
        //         formData.append(key, editFinance.value[key]);
        //     }
        // }

        for (const key in editFinance.value) {
            if (editFinance.value[key] !== null) {
                console.log(`Appending ${key}:`, editFinance.value[key]);
                formData.append(key, editFinance.value[key]);
            }
        }

        const response = await axios.post(`/api/finance/${editFinance.value.id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        
        const index = finances.value.findIndex(finance => finance.id === editFinance.value.id);
        finances.value[index] = response.data.finance;
        showEditFinanceModal.value = false;


        showSuccessEditModal.value = true

        setTimeout(() => {
        showSuccessEditModal.value = false
        }, 1000) 
        fetchFinances();

        resetEditFinance();
    } catch (error) {
        if (error.response && error.response.data) {
            console.error("Validation errors:", error.response.data);
        } else {
            console.error("Error updating finance:", error);
        }
    }
};



const props = defineProps({
  filteredFinances: {
    type: Array,
    required: true
  },
})

const emit = defineEmits(['financeDeleted', 'editFinance', 'viewFinance'])

const showDeleteModal = ref(false)
const showSuccessModal = ref(false)
const financeToDelete = ref(null)

const openDeleteModal = (id) => {
  financeToDelete.value = id
  showDeleteModal.value = true
}

const closeDeleteModal = () => {
  showDeleteModal.value = false
  financeToDelete.value = null
}

const confirmDelete = async () => {
  try {
    await axios.delete(`/api/finance/${financeToDelete.value}`)
    closeDeleteModal()
    showSuccessModal.value = true
    emit('financeDeleted', financeToDelete.value)
    setTimeout(() => {
      showSuccessModal.value = false
    }, 1000) // Auto-close after 2 seconds
    fetchFinances();
  } catch (error) {
    console.error("Error deleting finance:", error)
    // You might want to show an error message to the user here
  }
}


const editFinanceDetails = (finance) => {
    editFinance.value = { ...finance };
    showEditFinanceModal.value = true;
};

const cancelAddFinance = () => {
    showAddFinanceModal.value = false;
    resetNewFinance();
};

const resetNewFinance = () => {
    newFinance.value = {
        description: '',
        date: '',
        category: '',
        amount: 0, 
    };
};

const resetEditFinance = () => {
    editFinance.value = {
        description: '',
        date: '',
        category: '',
        amount: 0, 
    };
};

const filteredFinances = computed(() => {
    if (!searchQuery.value) {
        return finances.value;
    }
    return finances.value.filter(finance => {
        const searchTerm = searchQuery.value.toLowerCase();
        return (
            finance.description.toLowerCase().includes(searchTerm) ||
            finance.date.toLowerCase().includes(searchTerm) ||
            finance.category.toLowerCase().includes(searchTerm) || 
            finance.amount.toLowerCase().includes(searchTerm)
        );
    });
});





const sortOrder = ref('asc'); // Default sort order

function sortByDescription() {
    // Toggle the sort order
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current sort order
    finances.value.sort((a, b) => {
        if (sortOrder.value === 'asc') {
            return a.name.localeCompare(b.name);
        } else {
            return b.name.localeCompare(a.name);
        }
    });
}

const DateSortOrder = ref('asc'); // Default sort order for Exp. Date

function sortByDate() {
    // Toggle the sort order for DateSortOrder. Date
    DateSortOrder.value = DateSortOrder.value === 'asc' ? 'desc' : 'asc';

    // Sort the products array in-place based on the current expDate sort order
    finances.value.sort((a, b) => {
        const dateA = new Date(a.Date);
        const dateB = new Date(b.Date);

        if (DateSortOrder.value === 'asc') {
            return dateA - dateB; // Sort ascending by date (earliest first)
        } else {
            return dateB - dateA; // Sort descending by date (latest first)
        }
    });
}

const isDateFiltered = ref(false);
const startDate = ref('');
const endDate = ref('');
const financesByDate = ref([]);

const fetchFinancesByDate = async () => {
    try {
        const response = await axios.get('/api/finance_by_date', {
            params: {
                start_date: startDate.value,
                end_date: endDate.value
            }
        });
        financesByDate.value = response.data;
        isDateFiltered.value = true; // Set flag to true after fetching by date
    } catch (error) {
        console.error("Error fetching finances by date:", error);
    }
};

watch([startDate, endDate], async ([newStartDate, newEndDate]) => {
    if (newStartDate && newEndDate) {
        await fetchFinancesByDate();
    } else {
        // Reset financesByDate and isDateFiltered if dates are cleared
        financesByDate.value = [];
        isDateFiltered.value = false;
    }
});

const showViewFinanceModal = ref(false);
const viewFinanceDetails = (finance) => {
    editFinance.value = { ...finance };
    showViewFinanceModal.value = true;
};



const financeCategories = ref([
  { id: 1, category: 'income' },
  { id: 2, category: 'expense' },
  // Add more categories as needed
]);
// Reactive array to hold selected categories
const selectedCategories = ref([]);

// Watch for changes in the selectedCategories
watch(selectedCategories, (newValue) => {
  console.log('Selected categories:', newValue);
  // You can also perform other actions here, such as saving the selection
});

const fetchFinanceCategories = async () => {
    try {
        const response = await axios.get('/api/finance');
        financeCategories.value = response.data.financeCategories;
    } catch (error) {
        console.error("Error fetching finance and finance Categories:", error);
    }
};



const showPrintFinanceSummaryByDate = ref(false);
function printFinanceSummaryByDate() {
    
    fetchFinanceCategories();
    showPrintFinanceSummaryByDate.value = true;
}
const summaryOption = ref({
  option: "",
});
watch(
  () => summaryOption.value.option, // Watch the specific value within the ref
  (newOption) => {
    console.log('NEW OPTION VALUE:', newOption);
    // Add additional logic here if needed
  }
);

// Computed property to check if all categories are selected
const isAllSelected = computed(() => {
  return selectedCategories.value.length === financeCategories.value.length;
});

// Method to toggle all selections
const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedCategories.value = []; // Uncheck all
  } else {
    selectedCategories.value = financeCategories.value.map(category => category.category); // Check all
  }
};


const startDatePrint = ref('');
const endDatePrint = ref('');

function printFinancesByDate() {
    try {
        const startDate = startDatePrint.value;
        const endDate = endDatePrint.value;
        
        const categoriesString = selectedCategories.value.join(',');

        if(summaryOption.value.option === 'summaryPdf'){
            window.open(`/api/print/summary/finance_by_date_category/pdf?startDatePrint=${startDate}&endDatePrint=${endDate}&categories=${categoriesString}`, '_blank');
        }
        else{
            window.open(`/api/print/summary/finance_by_date_category/xlsx?startDatePrint=${startDate}&endDatePrint=${endDate}&categories=${categoriesString}`, '_blank');
        }

    } catch (error) {
        console.error("Error fetching invoices by date:", error);
    }
};

watch([startDatePrint, endDatePrint], (newValues) => {
    const [newStartDate, newEndDate] = newValues;
    console.log('Start Date:', newStartDate);
    console.log('End Date:', newEndDate);
    // Add additional logic here if needed
});


const clearFetchFinancesPrintByDate = async () => {

startDatePrint.value = '';
endDatePrint.value = '';
};

fetchFinanceCategories();
fetchFinances();
</script>

<template>
    <AuthenticatedLayout>
        <div class="py-5 h-full">
            <div class="max-w-auto h-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-[84%]">
                    <div class="p-6 h-full text-gray-900 dark:text-gray-100 flex flex-col">
                    <div class="mt-4 mb-8 flex justify-between items-center mb-4">
                        <h2 class="font-semibold text-4xl">List of Finances</h2>

                        <div class="flex">
                        <div>
                            <div class="relative w-64">
                            <font-awesome-icon
                                :icon="['fas', 'magnifying-glass']"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-white"
                            />
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search finances..."
                                class="text-white pl-10 pr-4 py-2 border rounded-md dark:bg-gray-700 dark:text-gray-300 w-full h-10"
                            />
                            </div>
                        </div>

                        <div class="items-center flex ml-10 text-white">
                            <div class="mr-2 text-xs">Filter by Date:</div>    
                            <input id="startDate" class="dark:bg-gray-700 dark:border-gray-600 text-sm" type="date" v-model="startDate" />
                            <div class="mx-2 text-xs"> To </div>
                            <input id="endDate" class="dark:bg-gray-700 dark:border-gray-600 text-sm mr-2" type="date" v-model="endDate" />
                            <button @click="clearFetchFinancesByDate" class="text-xs bg-stone-600 rounded-lg p-2 text-white ml-2 me-5">Clear</button>
                        </div>  
                        </div>
                    </div>

                    <div class="flex-grow overflow-hidden border sm:rounded-lg border-gray-900">
                        <div class="overflow-x-auto h-full">
                        <table class="min-w-full table-fixed">
                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                            <tr>
                                <th 
                                class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-center align-middle cursor-pointer whitespace-nowrap"
                                @click="sortByDate">
                                <div class="items-center text-center text-xl space-x-1">
                                    <font-awesome-icon 
                                    :icon="['fas', 'angle-down']" 
                                    :class="DateSortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                    class="ml-2 transition-transform duration-300 ease-in-out" 
                                    />
                                    <span>Date</span>
                                </div>
                                </th>
                                <th 
                                class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-center align-middle cursor-pointer"
                                @click="sortByDescription">
                                <div class="text-xl items-center text-center space-x-1">
                                    <font-awesome-icon 
                                    :icon="['fas', 'angle-down']"
                                    :class="sortOrder === 'asc' ? 'rotate-180' : 'rotate-0'"
                                    class="ml-2 transition-transform duration-300 ease-in-out" 
                                    /> 
                                    <span>Decription</span>
                                </div>
                                </th>
                                <th class="text-xl px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Category</th>
                                <th class="text-xl px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Amount</th>
                                <th class="text-xl px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Actions</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 text-white">
                            <tr v-if="filteredFinances.length === 0 || (financesByDate.length === 0 && isDateFiltered)">
                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" colspan="5">No finances available.</td>
                            </tr>

                            <tr class="" v-for="finance in financesByDate" :key="finance.id" v-if="isDateFiltered" >
                                <td class="text-white px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.date }}</td>
                                <td class="text-white px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.description }}</td>
                                <td class="text-white px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">
                                <div>   
                                    <span :class="{
                                    'bg-green-800 text-white py-1 px-3 rounded-full': finance.category === 'income',
                                    'bg-blue-900 text-white py-1 px-3 rounded-full': finance.category === 'expense',
                                    'bg-yellow-700 text-white py-1 px-3 rounded-full': !['income', 'expense'].includes(finance.category) && finance.category !== '',
                                    }">
                                    <font-awesome-icon v-if="finance.category === 'income'" icon="fa-solid fa-peso-sign" size="sm" class="mr-1" />
                                    <font-awesome-icon v-if="finance.category === 'expense'" icon="fa-solid fa-peso-sign" size="sm" class="mr-1" />
                                    <font-awesome-icon v-if="finance.category !== 'income' && finance.category !== 'expense'" icon="fa-solid fa-money-bill" size="sm" class="mr-1" />
                                    {{ finance.category.charAt(0).toUpperCase() + finance.category.slice(1) }}
                                    </span>
                                </div>
                                </td>
                                <td class="text-white px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.amount }}</td>
                                <td class="text-white px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-center space-x-2">
                                    <button @click="editFinanceDetails(finance)" class="bg-yellow-500 text-white py-1 px-2 rounded-full">
                                    <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                    </button>
                                    <button @click="viewFinanceDetails(finance)" class="bg-blue-500 text-white px-2 py-1  rounded-full">
                                    <font-awesome-icon icon="fa-solid fa-eye" size="sm"/>
                                    </button>
                                    <button @click="openDeleteModal(finance.id)" class="bg-red-500 text-white py-1 px-2 rounded-full">
                                    <font-awesome-icon :icon="['fas', 'trash-can']" size="sm" />
                                    </button>
                                </div>
                                </td>
                            </tr>
                            <tr v-for="finance in filteredFinances" :key="finance.id" v-if="!isDateFiltered">
                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.date }}</td>
                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.description }}</td>
                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">
                                <div>
                                    <span :class="{
                                    'bg-green-800 text-white py-1 px-3 rounded-full': finance.category === 'income',
                                    'bg-blue-900 text-white py-1 px-3 rounded-full': finance.category === 'expense',
                                    'bg-yellow-700 text-white py-1 px-3 rounded-full': !['income', 'expense'].includes(finance.category) && finance.category !== '',
                                    }">
                                    <font-awesome-icon v-if="finance.category === 'income'" icon="fa-solid fa-peso-sign" size="sm" class="mr-1" />
                                    <font-awesome-icon v-if="finance.category === 'expense'" icon="fa-solid fa-peso-sign" size="sm" class="mr-1" />
                                    <font-awesome-icon v-if="finance.category !== 'income' && finance.category !== 'expense'" icon="fa-solid fa-money-bill" size="sm" class="mr-1" />
                                    {{ finance.category.charAt(0).toUpperCase() + finance.category.slice(1) }}
                                    </span>
                                </div>
                                </td>
                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 text-center align-middle">{{ finance.amount }}</td>
                                <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-center space-x-1">
                                    <button @click="editFinanceDetails(finance)" class="bg-yellow-500 text-white px-2 py-1  rounded-full">
                                    <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                    </button>
                                    <button @click="viewFinanceDetails(finance)" class="bg-blue-500 text-white px-2 py-1  rounded-full">
                                    <font-awesome-icon icon="fa-solid fa-eye" size="sm"/>
                                    </button>
                                    <button @click="openDeleteModal(finance.id)" class="bg-red-500 text-white py-1 px-2 rounded-full">
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
                </div>
                <div class="flex justify-end mt-4 mr-5 space-x-4">
                    <button @click="showAddFinanceModal = true" class="bg-blue-500 text-white py-2 px-4 rounded">+ Add Finance</button>
                    <button @click="showFinanceCategoriesModal = true" class="bg-gray-500 text-white py-2 px-4 rounded">Categories</button>
                    <button @click="printFinanceSummaryByDate()" class=" bg-gray-500 text-white py-2 px-4 rounded">
                    <font-awesome-icon icon="fa-solid fa-print" size="sm" />
                    Print Finance Summary
                    </button>
                </div>
            </div>
        </div>


        <transition name="modal-fade" >
            <div v-show="showDeleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-trash" size="8x" style="margin-top:2px; color: red;"/>
                    <h2 class="mt-4 text-xl text-center font-bold mb-2">Confirm Deletion</h2>
                    <p class="mb-4 text-center">Are you sure you want to delete this finance?</p>
                    <div class="flex justify-center space-x-2">
                        <button @click="closeDeleteModal" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                            No
                        </button>
                        <button @click="confirmDelete" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            Yes
                        </button>
                    </div>
                </div>
            </div>
        </transition>

        <transition name="modal-fade" >
            <div v-if="showSuccessModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The finance has been successfully deleted.</p>
                </div>
            </div>
        </transition>


        <transition name="modal-fade" >
            <div v-if="showSuccessEditModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Finance Information has been successfully Updated!.</p>
                </div>
            </div>
        </transition>


        
        <transition name="modal-fade" >
            <div v-if="showSuccessAddModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50 overflow-y-auto h-full w-full">
                <div class="flex flex-col mx-12 items-center justify-center bg-white p-5 rounded-lg shadow-xl text-center">
                    <font-awesome-icon icon="fa-solid fa-check" size="10x" style="color: green;"/>
                    <h2 class="text-xl font-bold mb-4">Success!</h2>
                    <p class="mb-4">The Finance Information has been successfully Added!.</p>
                </div>
            </div>
        </transition>


        <transition name="modal-fade">
            <div v-if="showEditFinanceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                <h2 class="text-2xl font-bold text-center mb-6">Edit Finance</h2>

                <button 
                    @click="showEditFinanceModal = false" 
                    class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    aria-label="Close"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <form @submit.prevent="updateFinance" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Date<span class="text-red-500">*</span></label>
                            <input 
                                type="date" 
                                id="date" 
                                v-model="editFinance.date" 
                                class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required 
                            />
                        </div>

                        <div>
                            <label for="description" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Description<span class="text-red-500">*</span></label>
                            <input 
                                type="text" 
                                id="description" 
                                v-model="editFinance.description" 
                                class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required 
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="category" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Category<span class="text-red-500">*</span></label>
                            <select 
                                id="category" 
                                v-model="editFinance.category" 
                                class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                            >
                                <option v-for="category in financeListedCategories" :key="category.id" :value="category.category">
                                    {{ category.category }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount<span class="text-red-500">*</span></label>
                            <div class=" relative rounded-md shadow-sm">
                                <input 
                                    type="number" 
                                    id="amount" 
                                    v-model="editFinance.amount" 
                                    class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="0.00"
                                    step="0.01"
                                    required 
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-3 pt-4">
                        <button 
                            @click="showEditFinanceModal = false" 
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </transition>
        
        <transition name="modal-fade">
            <div v-if="showPrintFinanceSummaryByDate" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl p-6 relative">
            <h2 class="text-3xl font-bold text-center mb-6">Print Finance Summary</h2>
            
            <button 
                @click="showPrintFinanceSummaryByDate = false" 
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                aria-label="Close"
            >
                <font-awesome-icon icon="fa-solid fa-x" size="lg" />
            </button>

            <div class="flex space-x-6">
                <!-- Left column: Print options and date filter -->
                <div class="w-1/2 space-y-6">
                <div>
                    <label for="invoiceSummaryOption" class="w-44 block pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-sm text-white">Print as:</label>
                    <select 
                    id="invoiceSummaryOption" 
                    v-model="summaryOption.option"
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    >
                    <option value="summaryPdf">PDF (.pdf)</option>
                    <option value="summaryExcel">Excel Sheet (.xlsx)</option>
                    </select>
                </div>

                <div class="">
                    <p class="w-44 block pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 text-xs text-white">Filter by Date:</p>
                    <div class="flex items-center space-x-2">
                    <input 
                        id="startDate" 
                        v-model="startDatePrint" 
                        type="date" 
                        class="block w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <span class="text-gray-500">to</span>
                    <input 
                        id="endDate" 
                        v-model="endDatePrint" 
                        type="date" 
                        class="block w-full px-3 py-2 text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    </div>
                </div>

                <button 
                    @click="clearFetchFinancesPrintByDate" 
                    class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Clear Dates
                </button>
                </div>

                <!-- Right column: Categories -->
                <div class="w-1/2">
                <div class="flex items-center">
                    <h3 class="text-lg font-semibold mb-3 mr-3">Select Categories to Print</h3>
                    <div class="mb-2 pb-2">
                        <input
                            type="checkbox"
                            id="select-all"
                            :checked="isAllSelected"
                            @change="toggleSelectAll"
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        />
                        <label for="select-all" class="ml-2 text-sm text-gray-700">Select All</label>
                    </div>
                </div>

                <div class="pl-6 h-64 overflow-y-auto pr-4">

                    <div v-for="category in financeCategories" :key="category.id" class="mb-2">
                    <input
                        type="checkbox"
                        :value="category.category"
                        :id="category.category" 
                        v-model="selectedCategories"
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    />
                    <label :for="category.category" class="ml-2 text-sm text-gray-700">{{ category.category }}</label>
                    </div>
                </div>
                </div>
            </div>

            <div class="mt-6 flex justify-center">
                <button 
                @click.prevent="printFinancesByDate" 
                class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                <font-awesome-icon icon="fa-solid fa-print" class="mr-2" />
                Print Finance Summary
                </button>
            </div>
            </div>
        </div>

        </transition>


        <transition name="modal-fade">
            <div v-if="showAddFinanceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
            <h2 class="text-2xl font-bold text-center mb-6">Add a Finance</h2>
            
            <button 
                @click="cancelAddFinance" 
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                aria-label="Close"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <form @submit.prevent="addFinance" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="date" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Date<span class="text-red-500">*</span></label>
                    <input 
                    type="date" 
                    id="date" 
                    v-model="newFinance.date" 
                    class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required 
                    />
                </div>

                <div>
                    <label for="description" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Description<span class="text-red-500">*</span></label>
                    <input 
                    type="text" 
                    id="description" 
                    v-model="newFinance.description" 
                    class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required 
                    />
                </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="category" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Category<span class="text-red-500">*</span></label>
                    <select 
                    id="category" 
                    v-model="newFinance.category" 
                    class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                    required
                    >
                    <option v-for="category in financeListedCategories" :key="category.id" :value="category.category">
                        {{ category.category }}
                    </option>
                    </select>
                </div>

                <div>
                    <label for="amount" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount<span class="text-red-500">*</span></label>
                    <div class=" relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        
                    </div>
                    <input 
                        type="number" 
                        id="amount" 
                        v-model="newFinance.amount" 
                        class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        placeholder="0.00"
                        step="0.01"
                        required 
                    />
                    </div>
                </div>
                </div>

                <div class="flex justify-center space-x-3 pt-4">
                <button 
                    @click="cancelAddFinance" 
                    type="button"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="px-4 py-2 text-sm    font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    + Add Finance
                </button>
                </div>
            </form>
            </div>
        </div>

        </transition>

        
        <transition name="modal-fade">
            <div v-if="showViewFinanceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-6 relative">
                <h2 class="text-2xl font-bold text-center mb-6">View Finance</h2>
                
                <button 
                    @click="showViewFinanceModal = false" 
                    class="absolute top-2 right-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    aria-label="Close"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <form @submit.prevent="updateFinance" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="date" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Date<span class="text-red-500">*</span></label>
                            <input 
                                disabled 
                                type="date" 
                                id="date" 
                                v-model="editFinance.date" 
                                class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required 
                            />
                        </div>

                        <div>
                            <label for="description" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Description<span class="text-red-500">*</span></label>
                            <input 
                                disabled 
                                type="text" 
                                id="description" 
                                v-model="editFinance.description" 
                                class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required 
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="edit_category" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Category<span class="text-red-500">*</span></label>
                            <select 
                                id="edit_category" 
                                v-model="editFinance.category" 
                                class=" block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required
                                disabled
                            >
                                <option v-for="category in financeListedCategories" :key="category.id" :value="category.category">
                                    {{ category.category }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount<span class="text-red-500">*</span></label>
                            <div class=" relative rounded-md shadow-sm">
                                <input 
                                    disabled
                                    type="number" 
                                    id="amount" 
                                    v-model="editFinance.amount" 
                                    class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="0.00"
                                    step="0.01"
                                    required 
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center space-x-3 pt-4">
                        <button 
                            @click="showViewFinanceModal = false" 
                            type="button"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </transition>







        <FinanceCategoriesModal v-if="showFinanceCategoriesModal" 
                        @close-categories-modal="showFinanceCategoriesModal = false" 
                        @category-added="fetchFinances" />
    </AuthenticatedLayout>
</template>


<style scoped>
.overflow-x-auto {
  overflow-y: auto;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.modal-fade-enter-to,
.modal-fade-leave-from {
  opacity: 1;
  transform: scale(1);
}

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



.switch {
    position: relative;
    display: inline-block;
    width: 40px;
    height: 20px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 12px;
    width: 12px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #4CAF50;
}

input:checked + .slider:before {
    transform: translateX(20px);
}

input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0; /* Make the input invisible */
    cursor: pointer; 
    z-index: 10; /* Bring the input above other elements */
}

.image-upload {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
}

.image-upload svg,
.image-upload img {
    position: absolute;
    z-index: 1; /* Ensure the preview image is below the file input */
}

</style>