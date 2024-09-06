<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

//DECLARATIONS
const invoices = ref([]); // holds list of invoices fetched from the server
const showAddInvoiceModal = ref(false); //Forms when adding invoice. Steps 1-3
const showEditInvoiceModal = ref(false); //Forms when editing invoice. Steps 1-3
let currentStepAdd = ref(); //Counts which page of Forms should it be when adding invoice. 
let currentStepUpdate = ref(); //Counts which page of Forms should it be when updating invoice. 
const searchQuery = ref(''); //search query

const newInvoice = ref({
    
  business_id: '',
  business_Name: 'placeholder',
  business_Address: 'placeholder',
  business_TIN: '0',
  invoice_id: '',
  date: '',
  terms: '',
  status: '',
  authorized_Representative: '',
  payment_Type: '',
  customer_Name: '',
  customer_Address: '',
  customer_TIN: '0',
  customer_Business_Style: '',
  customer_PO_No: '0',
  customer_OSCA_PWD_ID_No: '0',
  VATable_Sales: '0',
  VAT_Exempt_Sales: '0',
  Zero_Rated_Sales: '0',
  VAT_Amount: '0',
  VAT_Inclusive: '0',
  Less_VAT: '0',
  Amount_NET_of_VAT: '0',
  Less_SC_PWD_Discount: '0',
  Amount_Due: '0',
  Add_VAT: '0',
  tax: '0',
  total_Amount_Due: '0',
});

const editInvoice = ref({
    invoice_id: null,
    date: null,
    terms: null,
    status: null,
    authorized_Representative: null,
    payment_Type: null,
    customer_Name: null,
    customer_Address: null,
    customer_TIN: null,
    customer_Business_Style: null,
    customer_PO_No: null,
    customer_OSCA_PWD_ID_No: null,
    VATable_Sales: null,
    VAT_Exempt_Sales: null,
    Zero_Rated_Sales: null,
    VAT_Amount: null,
    VAT_Inclusive: null,
    Less_VAT: null,
    Amount_NET_of_VAT: null,
    Less_SC_PWD_Discount: null,
    Amount_Due: null,
    Add_VAT: null,
    tax: null,
    total_Amount_Due: null,
  });

//GET INVOICES
const fetchInvoices = async () => {
    try {
        const response = await axios.get('/api/invoice');
        invoices.value = response.data;
    } catch (error) {
        console.error("Error fetching invoice:", error);
    }
};

const filteredInvoices = computed(() => {
    if (!searchQuery.value) {
        return invoices.value;
    }

    const searchTerm = searchQuery.value.toLowerCase();

    return invoices.value.filter(invoice => {
        const invoiceId = String(invoice.invoice_id).toLowerCase();
        const customerName = String(invoice.customer_Name).toLowerCase();
        const date = String(invoice.date).toLowerCase();
        return (
            invoiceId.includes(searchTerm) ||
            customerName.includes(searchTerm) ||
            date.includes(searchTerm)
        );
    });
});

//ADD INVOICES
const addInvoice = async () => {
    try {
        const formData = new FormData();
        for (const key in newInvoice.value) {
            formData.append(key, newInvoice.value[key]);
        }
        const response = await axios.post('/api/invoice', formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        invoice.value.push(response.data);
        showAddInvoiceModal.value = false;
        
        // resetNewInvoice();
    } catch (error) {
        console.error("Error adding invoice:", error);
    }
};

//UPDATE AN INVOICE
// const updateInvoice = async () => {
//     try {
//         const formData = new FormData();

//         for (const key in editInvoice.value) {
//             // Append only if the value is not null or undefined
//             if (editInvoice.value[key] !== null && editInvoice.value[key] !== undefined) {
//                 formData.append(key, editInvoice.value[key]);
//             }
//         }
//         const response = await axios.put(`/api/invoice/${editInvoice.value.invoice_id}`, formData, {
//             headers: {
//                 'Content-Type': 'multipart/form-data',
//                 'X-HTTP-Method-Override': 'PUT'
//             }
//         });
//         const index = invoices.value.findIndex(invoice => invoice.invoice_id === editInvoice.value.invoice_id);
//         invoices.value[index] = response.data;

//         showEditInvoiceModal.value = false;
//         // resetEditInvoice();
//     } catch (error) {
//         console.error("Error updating invoice:", error);
//     }
// };

const editInvoiceDetails = (invoice) => {
    editInvoice.value = { ...invoice };
    showEditInvoiceModal.value = true;
};

//DELETE AN INVOICE

const deleteInvoice = async (invoice_system_id) => {
    try {
        await axios.delete(`/api/invoice/${invoice_system_id}`);
        // Optionally, remove the invoice from the list if the request is successful
        invoices.value = invoices.value.filter(invoice => invoice.invoice_system_id !== invoice_system_id);
        alert("Invoice deleted successfully");
    } catch (error) {
        console.error("Error deleting invoice:", error);
        alert("There was an issue deleting the invoice. Please try again.");
    }
};

fetchInvoices();

</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="py-7 ">
            <div class="max-w-auto mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="p-6 text-gray-900 dark:text-gray-100">  
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold">List of Invoice</h2>
                            <div class="relative w-100">
                                <font-awesome-icon
                                    :icon="['fas', 'magnifying-glass']"
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"
                                />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search by ID, Customer, or Date"
                                    class="pl-10 pr-5 py-4 border rounded-md dark:bg-gray-700 dark:text-gray-300 w-full h-10"
                                />
                            </div>
                        </div>

                        <div class="overflow-auto" style="max-height: 430px;">
                            <table class="min-w-full bg-white dark:bg-gray-800">
                                <thead>   
                                <!-- HEADER FOR INVOICES -->
                                    <tr>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">ID No.</th>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Customer Name</th>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Products</th>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Quantity(PHP)</th>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Total Amount</th>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Status</th>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Date</th>
                                        <th class="px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <!-- FOR SHOWING OF INVOICES -->
                                    <tbody>
                                    <tr v-if="filteredInvoices.length === 0">
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" colspan="10">No invoice available.</td>
                                    </tr>

                                    <tr class = "text-center" v-for="invoice in filteredInvoices" :key="invoice.invoice_id">
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.invoice_id }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.customer_Name.length > 20 ? invoice.customer_Name.substring(0, 20) + '...' : invoice.customer_Name }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.customer_Name.length > 20 ? invoice.customer_Name.substring(0, 20) + '...' : invoice.customer_Name }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.customer_Name.length > 20 ? invoice.customer_Name.substring(0, 20) + '...' : invoice.customer_Name }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.total_Amount }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                                <span :class="{
                                                    'bg-red-500 text-white py-1 px-3 rounded-full': invoice.status === 'unpaid',
                                                    'bg-green-500 text-white py-1 px-3 rounded-full': invoice.status === 'paid',
                                                    'bg-orange-500 text-white py-1 px-3 rounded-full': invoice.status === 'partially paid',
                                                }">{{ invoice.status }}</span>
                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.date }}</td>
                                        <td class="flex items-center justify-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <button @click="editInvoiceDetails(invoice), currentStepUpdate = 1" class="bg-yellow-500 text-white p-3 rounded-full mr-3">
                                                <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                            </button>
                                            <button @click="deleteInvoice(invoice.invoice_system_id)" class="bg-red-500 text-white p-3 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                            </table>
                        </div>
                    </div>   
                </div>
                <div class="flex justify-end mt-4 mr-10 space-x-4">
                    <button @click="showAddInvoiceModal = true, currentStepAdd = 1" class="bg-blue-500 text-white py-2 px-4 rounded">+ Add Invoice</button>
                    <button @click="" class="bg-gray-500 text-white py-2 px-4 rounded">Print Invoice Summary</button>
                </div>

            </div>
        </div>


        
        <div v-if="showEditInvoiceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-bold mb-4">Update Invoice: Step {{ currentStepUpdate }} of 3</h3>
                    
            
                <form @submit.prevent="updateInvoice" enctype="multipart/form-data">
                    <!-- Step 1: Invoice Details -->
                    <div v-if="currentStepUpdate === 1">
                        <div class="mb-4">
                            <label for="invoice_id" class="block text-sm font-medium text-gray-700">Invoice I.D. No.</label>
                            <input type="number" id="invoice_id" v-model="editInvoice.invoice_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="date" v-model="editInvoice.date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="payment_Type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                            <select id="payment_Type" v-model="editInvoice.payment_Type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" v-model="editInvoice.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="paid">Paid</option>
                                <option value="partially paid">Partially Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="terms" class="block text-sm font-medium text-gray-700">Terms</label>
                            <input type="text" id="terms" v-model="editInvoice.terms" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="authorized_Representative" class="block text-sm font-medium text-gray-700">Cashier/Authorized Representative</label>
                            <input type="text" id="authorized_Representative" v-model="editInvoice.authorized_Representative" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_Name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                            <input type="text" id="customer_Name" v-model="editInvoice.customer_Name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_Address" class="block text-sm font-medium text-gray-700">Customer Address</label>
                            <input type="text" id="customer_Address" v-model="editInvoice.customer_Address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_TIN" class="block text-sm font-medium text-gray-700">Customer TIN Number</label>
                            <input type="number" id="customer_TIN" v-model="editInvoice.customer_TIN" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_PO_No" class="block text-sm font-medium text-gray-700">Customer P.O. Number</label>
                            <input type="number" id="customer_PO_No" v-model="editInvoice.customer_PO_No" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_OSCA_PWD_ID_No" class="block text-sm font-medium text-gray-700">Customer OSCA/PWD I.D. No.</label>
                            <input type="number" id="customer_OSCA_PWD_ID_No" v-model="editInvoice.customer_OSCA_PWD_ID_No" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_Business_Style" class="block text-sm font-medium text-gray-700">Business Style</label>
                            <input type="text" id="customer_Business_Style" v-model="editInvoice.customer_Business_Style" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="flex justify-between mt-6">
                            <button @click="showEditInvoiceModal = false, currentStepUpdate = 0" type="button" class="bg-gray-500 text-white py-2 px-4 rounded">Cancel</button>
                            <button @click="currentStepUpdate = 2" class="bg-blue-500 text-white py-2 px-4 rounded">Next</button>
                        </div>
                    </div>

                    <!-- Step 2: Customer Details -->
                    <div v-if="currentStepUpdate === 2">

                        <div class="flex justify-between mt-6">
                            <button @click="currentStepUpdate = 1" type="button" class="bg-gray-500 text-white py-2 px-4 rounded">Previous</button>
                            <button @click="currentStepUpdate = 3" class="bg-blue-500 text-white py-2 px-4 rounded">Next</button>
                        </div>
                    </div>

                    <!-- Step 3: Business Details -->
                    <div v-if="currentStepUpdate === 3">
                        <div class="mb-4">
                            <label for="VATable_Sales" class="block text-sm font-medium text-gray-700">VATable Sales</label>
                            <input type="number" id="VATable_Sales" v-model="editInvoice.VATable_Sales" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="VAT_Exempt_Sales" class="block text-sm font-medium text-gray-700">VAT Exempt Sales</label>
                            <input type="number" id="VAT_Exempt_Sales" v-model="editInvoice.VAT_Exempt_Sales" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Zero_Rated_Sales" class="block text-sm font-medium text-gray-700">Zero Rated Sales</label>
                            <input type="number" id="Zero_Rated_Sales" v-model="editInvoice.Zero_Rated_Sales" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="VAT_Amount" class="block text-sm font-medium text-gray-700">VAT Amount</label>
                            <input type="number" id="VAT_Amount" v-model="editInvoice.VAT_Amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="VAT_Inclusive" class="block text-sm font-medium text-gray-700">Total Sales (VAT Inclusive)</label>
                            <input type="number" id="VAT_Inclusive" v-model="editInvoice.VAT_Inclusive" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Less_VAT" class="block text-sm font-medium text-gray-700">Less VAT</label>
                            <input type="number" id="Less_VAT" v-model="editInvoice.Less_VAT" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Amount_NET_of_VAT" class="block text-sm font-medium text-gray-700">Amount NET of VAT</label>
                            <input type="number" id="Amount_NET_of_VAT" v-model="editInvoice.Amount_NET_of_VAT" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Less_SC_PWD_Discount" class="block text-sm font-medium text-gray-700">Less SC/PWD Discount</label>
                            <input type="number" id="Less_SC_PWD_Discount" v-model="editInvoice.Less_SC_PWD_Discount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Amount_Due" class="block text-sm font-medium text-gray-700">Amount Due</label>
                            <input type="number" id="Amount_Due" v-model="editInvoice.Amount_Due" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Add_VAT" class="block text-sm font-medium text-gray-700">Add VAT</label>
                            <input type="number" id="Add_VAT" v-model="editInvoice.Add_VAT" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="tax" class="block text-sm font-medium text-gray-700">Tax</label>
                            <input type="number" id="tax" v-model="editInvoice.tax" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="total_Amount_Due" class="block text-sm font-medium text-gray-700">Total Amount Due</label>
                            <input type="number" id="total_Amount_Due" v-model="editInvoice.total_Amount_Due" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>  
                            
                        <div class="flex justify-between mt-6">
                            <button @click="currentStepUpdate = 2" class="bg-gray-500 text-white py-2 px-4 rounded">Previous</button>
                            <button type="submit"  class="bg-green-500 text-white py-2 px-4 rounded">Save</button>
                        </div>
                    </div>

                </form>
            </div>
        </div> 
        
        <div v-if="showAddInvoiceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h3 class="text-lg font-bold mb-4">Add Invoice: Step {{ currentStepAdd }} of 3</h3>
                    
            
                <form @submit.prevent="addInvoice">
                    <!-- Step 1: Invoice Details -->
                    <div v-if="currentStepAdd === 1">
                        <div class="mb-4">
                            <label for="invoice_id" class="block text-sm font-medium text-gray-700">Invoice I.D. No.</label>
                            <input type="number" id="invoice_id" v-model="newInvoice.invoice_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" id="date" v-model="newInvoice.date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="payment_Type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                            <select id="payment_Type" v-model="newInvoice.payment_Type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" v-model="newInvoice.status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="paid">Paid</option>
                                <option value="partially paid">Partially Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="terms" class="block text-sm font-medium text-gray-700">Terms</label>
                            <input type="text" id="terms" v-model="newInvoice.terms" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="authorized_Representative" class="block text-sm font-medium text-gray-700">Cashier/Authorized Representative</label>
                            <input type="text" id="authorized_Representative" v-model="newInvoice.authorized_Representative" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_Name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                            <input type="text" id="customer_Name" v-model="newInvoice.customer_Name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_Address" class="block text-sm font-medium text-gray-700">Customer Address</label>
                            <input type="text" id="customer_Address" v-model="newInvoice.customer_Address" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_TIN" class="block text-sm font-medium text-gray-700">Customer TIN Number</label>
                            <input type="number" id="customer_TIN" v-model="newInvoice.customer_TIN" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_PO_No" class="block text-sm font-medium text-gray-700">Customer P.O. Number</label>
                            <input type="number" id="customer_PO_No" v-model="newInvoice.customer_PO_No" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_OSCA_PWD_ID_No" class="block text-sm font-medium text-gray-700">Customer OSCA/PWD I.D. No.</label>
                            <input type="number" id="customer_OSCA_PWD_ID_No" v-model="newInvoice.customer_OSCA_PWD_ID_No" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="customer_Business_Style" class="block text-sm font-medium text-gray-700">Business Style</label>
                            <input type="text" id="customer_Business_Style" v-model="newInvoice.customer_Business_Style" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="flex justify-between mt-6">
                            <button @click="showAddInvoiceModal = false, currentStepAdd = 0" type="button" class="bg-gray-500 text-white py-2 px-4 rounded">Cancel</button>
                            <button @click="currentStepAdd = 2" class="bg-blue-500 text-white py-2 px-4 rounded">Next</button>
                        </div>
                    </div>

                    <!-- Step 2: Customer Details -->
                    <div v-if="currentStepAdd === 2">

                        <div class="flex justify-between mt-6">
                            <button @click="currentStepAdd = 1" type="button" class="bg-gray-500 text-white py-2 px-4 rounded">Previous</button>
                            <button @click="currentStepAdd = 3" class="bg-blue-500 text-white py-2 px-4 rounded">Next</button>
                        </div>
                    </div>

                    <!-- Step 3: Business Details -->
                    <div v-if="currentStepAdd === 3">
                        <div class="mb-4">
                            <label for="VATable_Sales" class="block text-sm font-medium text-gray-700">VATable Sales</label>
                            <input type="number" id="VATable_Sales" v-model="newInvoice.VATable_Sales" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="VAT_Exempt_Sales" class="block text-sm font-medium text-gray-700">VAT Exempt Sales</label>
                            <input type="number" id="VAT_Exempt_Sales" v-model="newInvoice.VAT_Exempt_Sales" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Zero_Rated_Sales" class="block text-sm font-medium text-gray-700">Zero Rated Sales</label>
                            <input type="number" id="Zero_Rated_Sales" v-model="newInvoice.Zero_Rated_Sales" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="VAT_Amount" class="block text-sm font-medium text-gray-700">VAT Amount</label>
                            <input type="number" id="VAT_Amount" v-model="newInvoice.VAT_Amount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="VAT_Inclusive" class="block text-sm font-medium text-gray-700">Total Sales (VAT Inclusive)</label>
                            <input type="number" id="VAT_Inclusive" v-model="newInvoice.VAT_Inclusive" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Less_VAT" class="block text-sm font-medium text-gray-700">Less VAT</label>
                            <input type="number" id="Less_VAT" v-model="newInvoice.Less_VAT" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Amount_NET_of_VAT" class="block text-sm font-medium text-gray-700">Amount NET of VAT</label>
                            <input type="number" id="Amount_NET_of_VAT" v-model="newInvoice.Amount_NET_of_VAT" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Less_SC_PWD_Discount" class="block text-sm font-medium text-gray-700">Less SC/PWD Discount</label>
                            <input type="number" id="Less_SC_PWD_Discount" v-model="newInvoice.Less_SC_PWD_Discount" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Amount_Due" class="block text-sm font-medium text-gray-700">Amount Due</label>
                            <input type="number" id="Amount_Due" v-model="newInvoice.Amount_Due" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="Add_VAT" class="block text-sm font-medium text-gray-700">Add VAT</label>
                            <input type="number" id="Add_VAT" v-model="newInvoice.Add_VAT" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="tax" class="block text-sm font-medium text-gray-700">Tax</label>
                            <input type="number" id="tax" v-model="newInvoice.tax" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                        <div class="mb-4">
                            <label for="total_Amount_Due" class="block text-sm font-medium text-gray-700">Total Amount Due</label>
                            <input type="number" id="total_Amount_Due" v-model="newInvoice.total_Amount_Due" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
                        </div>
                            
                        <div class="flex justify-between mt-6">
                            <button @click="currentStepAdd = 2" class="bg-gray-500 text-white py-2 px-4 rounded">Previous</button>
                            <button type="submit"  class="bg-green-500 text-white py-2 px-4 rounded">Submit</button>
                        </div>
                    </div>

                </form>
            </div>
        </div>



    </AuthenticatedLayout>
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

.relative {
    position: relative;
}

.absolute {
    position: absolute;
}

.left-3 {
    left: 0.75rem; /* 3 * 0.25rem (1rem = 16px) */
}

.top-1 {
    top: 50%;
}

.transform {
    transform: translateY(-50%);
}

.text-gray-500 {
    color: #6B7280;
}

.pl-10 {
    padding-left: 2.5rem; /* 10 * 0.25rem */
}

.pr-4 {
    padding-right: 1rem; /* 4 * 0.25rem */
}

.py-2 {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

.border {
    border-width: 1px;
}

.rounded-md {
    border-radius: 0.375rem; /* 6px */
}

.w-full {
    width: 100%;
}

.h-10 {
    height: 2.5rem; /* 10 * 0.25rem */
}

</style>
