<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import CategoriesModal from "@/Components/CategoriesModal.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import { parse } from '@fortawesome/fontawesome-svg-core';
//DECLARATIONS
const invoices = ref([]); // holds list of invoices fetched from the server
const invoice_computations= ref([]);
const products = ref([]);
const showAddInvoiceModal = ref(false); //Forms when adding invoice. Steps 1-3
const showProductList = ref(false);
const showEditInvoiceModal = ref(false); //Forms when editing invoice. Steps 1-3
let currentStepAdd = ref(0); //Counts which page of Forms should it be when adding invoice. 
let currentStepUpdate = ref(); //Counts which page of Forms should it be when updating invoice. 
let invoiceSystemId = ref(); //Gets the invoice_system_id as foreign key to be put on addInvoiceItem
const searchQuery = ref(''); //search query
const searchProductQuery = ref(''); //search query for products to be added on invoice items 





// Method to handle form submission
const submitForm = () => {
  console.log("Form submitted with values:", textFields.value);
  // Process the form data as needed
}; 

const newInvoice = ref({
    business_id: '',
    business_Name: 'placeholder',
    business_Address: 'placeholder',
    business_TIN: 0,              

    invoice_system_id: null,
    invoice_id: null,
    date: '',
    terms: '',
    status: '',
    authorized_Representative: '',
    payment_Type: '',

    customer_Name: null,
    customer_Address: '',
    customer_TIN: 0,
    customer_Business_Style: '',
    customer_PO_No: 0,
    customer_OSCA_PWD_ID_No: 0,

});


const newInvoiceComputation = ref({
    VATable_Sales: 0,
    VAT_Exempt_Sales: 0,
    Zero_Rated_Sales: 0,
    VAT_Amount: 0,

    VAT_Inclusive: 0,
    Less_VAT: 0,
    Amount_NET_of_VAT: 0,
    Less_SC_PWD_Discount: 0,
    Less_SC_PWD_Discount_Percent: 0,
    Amount_Due: 0,
    Add_VAT: 0,

    tax: 0,
    total_Amount_Due: 0,

    
})

const editInvoice = ref({
    business_id: '',
    business_Name: 'placeholder',
    business_Address: 'placeholder',
    business_TIN: 0,              
    invoice_system_id: null,
    invoice_id: null,
    date: '',
    terms: '',
    status: '',
    authorized_Representative: '',
    payment_Type: '',
    customer_Name: null,
    customer_Address: '',
    customer_TIN: 0,
    customer_Business_Style: '',
    customer_PO_No: 0,
    customer_OSCA_PWD_ID_No: 0,
  });

//GET INVOICES
const fetchInvoices = async () => {
    try {
        const response = await axios.get('/api/invoice');
        invoices.value = response.data;
        
        const responseComputations = await axios.get('/api/invoice_computation');
        invoice_computations.value = responseComputations.data;

        linkComputationsToInvoices();

    } catch (error) {
        console.error("Error fetching invoice:", error);
    }
};

const linkComputationsToInvoices = () => {
    invoices.value.forEach(invoice => {
        const computation = invoice_computations.value.find(c => c.invoice_system_id === invoice.invoice_system_id);
        invoice.total_Amount_Due = computation ? computation.total_Amount_Due : 'N/A';
    });
};

linkComputationsToInvoices();


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
        // Create FormData and send request to create the invoice
        const formData = new FormData();
        for (const key in newInvoice.value) {
            formData.append(key, newInvoice.value[key]);
        }
        const response = await axios.post('/api/invoice', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        // Fetch the invoice_system_id from the response
        invoiceSystemId = response.data.invoice_system_id;
        //const invoiceSystemId = response.data.invoice_system_id;

        // Set the invoice_system_id for the new invoice item
        // newInvoiceItem.value.invoice_system_id = invoiceSystemId;

        console.log("Invoice created successfully:", response);
        console.log("Invoice System ID:", invoiceSystemId);




        // Fetch updated list of invoices
        fetchInvoices();
        // Send the new invoice item
        //await addInvoiceItem(newInvoiceItem.value); // Call addInvoiceItem with the single item

            currentStepAdd.value = 2; // Move to the next step
        addInvoiceItem();

        console.log("currentStepAdd value:", currentStepAdd.value);
    } catch (error) {
        console.error("Error adding invoice:", error);
    }
};



//ADD INVOICE ITEMS

const fetchProducts = async () => {
  try {
    const response = await axios.get('/api/products');
    products.value = response.data;
  } catch (error) {
    console.error('Error fetching products:', error);
  }
};
const filteredProducts = (query) => {
  if (!query) {
    return [];
  }
  const searchTerm = query.toLowerCase();
  return products.value.filter((product) => {
    return (
      product.name.toLowerCase().includes(searchTerm) ||
      product.category.toLowerCase().includes(searchTerm) ||
      product.status.toLowerCase().includes(searchTerm) ||
      product.brand.toLowerCase().includes(searchTerm)
    );
  });
};

const textItemFields = ref([
  { 
    image:'', searchProductQuery: '', on_sale: 'no', amount: '', quantity: '', total_amount: '', areFieldsEnabled: false, isSearching: false, product_id:'', seniorPWD_discountable:'no' }
]);// Start with one pair of text fields

// Method to add a new pair of text fields
const addItemTextField = () => {
  textItemFields.value.push({
    image:'', searchProductQuery: '', on_sale: 'no', amount: '', quantity: '', total_amount: '', isSearching: false, product_id:'', seniorPWD_discountable:'no'
  });
  newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};
// Method to remove a text field
const removeItemTextField = (index) => {
    newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
  textItemFields.value.splice(index, 1); // Remove the text field pair at the specified index
};
const selectProduct = (product, index) => {
  // Assign the selected product's details to the corresponding text field
  textItemFields.value[index].image = product.image;
  textItemFields.value[index].searchProductQuery = product.name;
  textItemFields.value[index].on_sale = product.on_sale;
  // Hide the search results (clear the search query)
  textItemFields.value[index].amount = product.price;
  textItemFields.value[index].areFieldsEnabled = true;
  textItemFields.value[index].isSearching = false; // Hide the search results 
  textItemFields.value[index].product_id = product.id;
  console.log('Selected Product Image:', textItemFields.value[index].product_id)
  console.log('Selected Product Image:', textItemFields.value[index].image)
};

const addInvoiceItem = async () => {
    try {
        for (let field of textItemFields.value) {
            if(field.searchProductQuery.trim() !== ''){
            const formData = new FormData();
            formData.append('invoice_system_id', invoiceSystemId)
            formData.append('product_id', field.product_id);
            formData.append('product_name', field.searchProductQuery);
            formData.append('product_price', field.amount)
            formData.append('on_sale', field.on_sale);
            formData.append('seniorPWD_discountable', field.seniorPWD_discountable);
            formData.append('on_sale_price', field.amount);
            formData.append('quantity', field.quantity);
            formData.append('final_price', field.total_amount);

            await axios.post('/api/invoice_item', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        } else{
                console.log('Skipping empty item description');
            }

        }

        addInvoiceAdditional();

        console.log("Invoice Item added successfully.");
    } catch (error) {
        console.error("Error adding invoice item:", error);
    }
}



//ADD INVOICE ADDITIONAL

const textAdditionalFields = ref([
  { addtl_Costs_Description: '', aCD_amount:'', aCD_quantity: '', aCD_Total_Amount: ''}
]);// Start with one pair of text fields

// Method to add a new pair of text fields
const addAdditionalTextField = () => {
  textAdditionalFields.value.push({
    addtl_Costs_Description:'', aCD_amount:'', aCD_quantity: '', aCD_Total_Amount:''});
    newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};
// Method to remove a text field
const removeAdditionalTextField = (index) => {
  textAdditionalFields.value.splice(index, 1); // Remove the text field pair at the specified index
  newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
  updateTotalAdditionalAmount(index);
  
};

const addInvoiceAdditional = async () => {
    try {

            
        
        for (let field of textAdditionalFields.value) {
            if(field.addtl_Costs_Description.trim() !== ''){
            console.log('Submitting description:', field.addtl_Costs_Description); // Log the description
            console.log('Submitting amount:', field.aCD_amount); // Log the amount

            const formData = new FormData();
            formData.append('invoice_system_id', invoiceSystemId)
            formData.append('addtl_Costs_Description', field.addtl_Costs_Description);
            formData.append('aCD_amount', field.aCD_amount);
            formData.append('aCD_quantity', field.aCD_quantity);
            formData.append('aCD_Total_Amount', field.aCD_Total_Amount);
        
            await axios.post('/api/invoice_additional', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            } else{
                console.log('Skipping additional description');
            }
        }

        addInvoiceComputation();
        
        console.log("Additional costs added successfully.");
    } catch (error) {
        console.error("Error adding invoice additional:", error);
    }
}



//ADD INVOICE COMPUTATION
const totalAmountDue1 = ref(0); // Create a ref for total amount due
const LessScPwdDiscountPercent = ref(0); // Create a ref for SC discount
const LessScPwdDiscount = ref(0); // Create a ref for SC discount percent
const VatExemptSales = ref(0);
const ZeroRatedSales = ref(0);
const VaTAmount = ref(0);
const VatInclusive = ref(0);
const LessVat = ref(0);
const AmountNetOfVat = ref(0);
const VatableSales = ref(0);
const AmountDue = ref(0);
const AddVat = ref(0);
const tax = ref(0);
const seniorPwdDiscountable = ref(false); // Assuming this is a checkbox


const addInvoiceComputation = async () => {
    try {
        // Create FormData to send request
        const formData = new FormData();

        // Get values from input fields without using v-model
        const totalAmountDueValue = roundToTwoDecimals(totalAmountDue1.value.value);
        const lessSCDiscountValue = roundToTwoDecimals(LessScPwdDiscount.value.value);
        const lessSCDiscountPercentValue = roundToTwoDecimals(LessScPwdDiscountPercent.value.value);

        const VatExemptSalesValue = roundToTwoDecimals(VatExemptSales.value.value);
        const ZeroRatedSalesValue = roundToTwoDecimals(ZeroRatedSales.value.value);
        const VaTAmountValue = roundToTwoDecimals(VaTAmount.value.value);
        
        const VatInclusiveValue = roundToTwoDecimals(VatInclusive.value.value);
        const LessVatValue = roundToTwoDecimals(LessVat.value.value);
        const AmountNetOfVatValue = roundToTwoDecimals(AmountNetOfVat.value.value);
        const VatableSalesValue = roundToTwoDecimals(VatableSales.value.value);
        const AmountDueValue = roundToTwoDecimals(AmountDue.value.value);
        const AddVatValue = roundToTwoDecimals(AddVat.value.value);
        const taxValue = roundToTwoDecimals(tax.value.value);

        const isSeniorDiscountable = lessSCDiscountPercentValue > 0 ? 'yes' : 'no';


        console.log('totalamountdue', totalAmountDueValue);
        console.log('discount value', lessSCDiscountValue);
        console.log('discount percent', lessSCDiscountPercentValue);
        console.log('isseniordiscountable', isSeniorDiscountable);
        // Add these values to formData
        formData.append('invoice_system_id', invoiceSystemId);
        formData.append('total_Amount_Due', totalAmountDueValue);
        formData.append('senior_PWD_Discountable', isSeniorDiscountable);
        formData.append('Less_SC_PWD_Discount', lessSCDiscountValue);
        formData.append('Less_SC_PWD_Discount_Percent', lessSCDiscountPercentValue);
        formData.append('VATable_Sales', VatableSalesValue);
        formData.append('VAT_Exempt_Sales', VatExemptSalesValue);
        formData.append('Zero_Rated_Sales', ZeroRatedSalesValue); 
        formData.append('VAT_Amount', VaTAmountValue);
        formData.append('Amount_NET_of_VAT', AmountNetOfVatValue);
        formData.append('VAT_Inclusive', VatInclusiveValue);
        formData.append('Less_VAT', LessVatValue);
        formData.append('Amount_Due', AmountDueValue);
        formData.append('Add_VAT', AddVatValue);
        formData.append('tax', taxValue)
        // formData.append('tax', taxValue);

        // Send the request to the server
        const response = await axios.post('/api/invoice_computation', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });
        completed.value = true;
        clearAddInvoiceFields();
        console.log('completed value', completed.value);

        showSuccessAddModal.value = true;
        setTimeout(() => {
        showSuccessAddModal.value = false;
        }, 1000) 
        
        //Handle the response
        console.log("Invoice computation added successfully:", response);

    } catch (error) {
        console.error("Error adding invoice computation:", error);
        completed.value = false;
    }
};

//-----------FOR INVOICE COMPUTATION---------------
const roundToTwoDecimals = (num) => Math.round(num * 100) / 100;

const updateTotalProductAmount = (index) => {
    const field = textItemFields.value[index];
  field.total_amount = roundToTwoDecimals(roundToTwoDecimals(field.quantity) * roundToTwoDecimals(field.amount) || 0);
  newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
  count=0;

};



const updateTotalAdditionalAmount = (index) => {
  const field = textAdditionalFields.value[index];
  field.aCD_Total_Amount = roundToTwoDecimals(roundToTwoDecimals(field.aCD_quantity) * roundToTwoDecimals(field.aCD_amount) || 0);
  newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
  count=0;
};

const totalAmountDue = computed(() => {
  // Calculate the product total with rounding
  const productTotal = textItemFields.value.reduce((sum, field) => {
    return  roundToTwoDecimals(sum + roundToTwoDecimals(field.total_amount) || 0);
  }, 0);
  
  // Calculate the additional total with rounding
  const additionalTotal = textAdditionalFields.value.reduce((sum, field) => {
    return roundToTwoDecimals(sum + roundToTwoDecimals(field.aCD_Total_Amount) || 0);
  }, 0);
  
  // Round the final total amount due
  return roundToTwoDecimals(productTotal + additionalTotal);
});

const totalAmountDueHolder = ref(0);
watch(totalAmountDue, (newValue) => {
  totalAmountDueHolder.value = roundToTwoDecimals(newValue); // Round the new value
});

watch(totalAmountDueHolder, (newValue) => {
  totalAmountDueHolder.value = roundToTwoDecimals(newValue); // Round the new value

  console.log('Total Amount Due Holder updated manually:', newValue);
});


const comp_VATable_Sales_NET_of_VAT = computed(() => {
    if(count===0){
          // Use the totalAmountDueHolder value; default to 0 if undefined or falsy
  const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;
  const vatPercentage = 1.12;  // Assuming VAT is 12%

  // Calculate VATable Sales NET of VAT
  const vatableSales = roundToTwoDecimals(total / vatPercentage);

  // Round to 2 decimal places using the rounding function
  const roundedVatableSales = roundToTwoDecimals(vatableSales) ;

  return roundedVatableSales; // Return the rounded value
    }else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;  // Use totalAmountDueHolder as a number

        // Calculate VAT Inclusive as total including VAT
        const vatNetVat = roundToTwoDecimals(total);  // This assumes VAT is added on top of the total

        return roundToTwoDecimals(vatNetVat); // Return the rounded VAT inclusive amount
    }
});
const comp_VAT_Amount_Less_Add = computed(() => {
    if(count===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;
        const vatPercentage = 1.12;  // Assuming VAT is 12%

        // Calculate VATable Sales NET of VAT
        const vatableSales = roundToTwoDecimals(total / vatPercentage);

        // Round to 2 decimal places using the rounding function
        const roundedVatableSales = roundToTwoDecimals(vatableSales);
            const VATamount = roundToTwoDecimals(total-roundedVatableSales);

        return VATamount; // Return the rounded value
    }else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;

        // Calculate VATable Sales NET of VAT
        const vatableSales = roundToTwoDecimals((total) * 1.12);

        // Round to 2 decimal places using the rounding function
        const roundedVatableSales = roundToTwoDecimals(vatableSales);
            const VATamount = roundToTwoDecimals(roundedVatableSales-total);

        return VATamount; // Return the rounded value
    }

    
});

const comp_Tax = computed(() => {
    if(count===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1)) || 0;  // Use totalAmountDueHolder as a number
        const vatSalesPercentage = 1.12;
        const vatPercentage = 0.02;  // Assuming VAT is 12%, adjust this based on your requirement

        // Calculate VAT Inclusive as total including VAT
        const vatableSales = roundToTwoDecimals(total / vatSalesPercentage);
        const Tax = roundToTwoDecimals(vatableSales * vatPercentage);  // Round to two decimal places

        return Tax; // Return the rounded vat amount
    }
    else{
        let total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1)) || 0;  // Use totalAmountDueHolder as a number
        const vatSalesPercentage = 1.12;
        const vatPercentage = 0.02;  // Assuming VAT is 12%, adjust this based on your requirement

        total = roundToTwoDecimals(total*1.12);
        // Calculate VAT Inclusive as total including VAT
        const vatableSales = roundToTwoDecimals(total / vatSalesPercentage);
        const Tax = roundToTwoDecimals(vatableSales * vatPercentage);  // Round to two decimal places

        return Tax; // Return the rounded vat amount
    }

});

const comp_VAT_AmountDue = computed(() => {
    if(count===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;  // Use totalAmountDueHolder as a number
        const tax = 0.02;  // Assuming a certain tax percentage
        const vatPercentage = 1.12;

        // Calculate VAT Inclusive as total including VAT
        const amount = roundToTwoDecimals(total / vatPercentage);
        const compTax = roundToTwoDecimals(amount * tax);
        const vatAmountDue = roundToTwoDecimals(total - compTax);  // This assumes VAT is added on top of the total
        console.log('total in vat amount due', total)
        console.log('amount in vat amount due', amount);
        console.log('compTax in vat amount due', compTax);
        console.log('vatAmountDue in vat amount due', vatAmountDue);

        return roundToTwoDecimals(vatAmountDue); // Return the rounded VAT amount due
    }
    else{
        let total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;  // Use totalAmountDueHolder as a number
        const tax = 0.02;  // Assuming a certain tax percentage
        const vatPercentage = 1.12;
        total = roundToTwoDecimals(total*1.12);
        // Calculate VAT Inclusive as total including VAT
        const amount = roundToTwoDecimals(total / vatPercentage);
        const compTax = roundToTwoDecimals(amount * tax);
        const vatAmountDue = roundToTwoDecimals(total - compTax);  // This assumes VAT is added on top of the total

        console.log('123123 total in vat amount due', total)
        console.log('123123 amount in vat amount due', amount);
        console.log('123123 compTax in vat amount due', compTax);
        console.log('123123 vatAmountDue in vat amount due', vatAmountDue);
        return roundToTwoDecimals(vatAmountDue); // Return the rounded VAT amount due
    }

});

let vatInclusive = 0;
let vatInclusive1 = 0
const comp_VAT_Inclusive = computed(() => {
    if(count===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;  // Use totalAmountDueHolder as a number

        // Calculate VAT Inclusive as total including VAT
        vatInclusive1 = roundToTwoDecimals(total);  // This assumes VAT is added on top of the total

        return roundToTwoDecimals(vatInclusive1); // Return the rounded VAT inclusive amount
    }
    else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;  // Use totalAmountDueHolder as a number
        
        // Calculate VAT Inclusive as total including VAT
        vatInclusive = roundToTwoDecimals(total * 1.12);  // This assumes VAT is added on top of the total

        return roundToTwoDecimals(vatInclusive); // Return the rounded VAT inclusive amount
    }

});

const comp_VAT_Exempt_Sales = computed(() => {
    if(count===0){
        

        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;
        const vatPercentage = 1.12;  // Assuming VAT is 12%

        // Calculate VATable Sales NET of VAT
        const vatableSales = roundToTwoDecimals(total / vatPercentage);

        // Round to 2 decimal places using the rounding function
        const roundedVatableSales = roundToTwoDecimals(vatableSales) ;

        if(discountPercent === 0){
            return 0;
        }
        return roundedVatableSales; 

    }else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolder.value) + roundToTwoDecimals(discountValue1))|| 0;  // Use totalAmountDueHolder as a number
        console.log('TITE TITE TITE TITE TITE TITE TITE TITE');
        // Calculate VAT Inclusive as total including VAT
        const vatNetVat = roundToTwoDecimals(total);  // This assumes VAT is added on top of the total
        if(discountPercent === 0){
            return 0;
        }
        return roundToTwoDecimals(vatNetVat); // Return the rounded VAT inclusive amount

    }
    // Only calculate VAT Exempt Sales if Senior Discount is enabled

});

const isSeniorDiscountEnabled = ref(false);

let count = 0;
let originalTotalAmountDue = ref(0);
let originalTotalAmountDue1 =0;
let discountValue1=0;
let vatExemptSales1=0;
let discountValue2 =0;
let vatPercentage1=0;
let discountValue3=0;
let discountPercent=0;
watch(
  () => newInvoiceComputation.value.Less_SC_PWD_Discount_Percent,  // The value we're watching
  (newPercent) => {  // The callback that gets executed when the value changes
    const percentAsNumber = Number(newPercent); // Convert the selected value to a number
    newPercent = percentAsNumber;    
    discountPercent=newPercent;
    console.log('count: ', count);
        if(count === 0){
            originalTotalAmountDue.value = roundToTwoDecimals(totalAmountDueHolder.value);
            originalTotalAmountDue1 = roundToTwoDecimals(originalTotalAmountDue.value);

        }        
            console.log('TIGNAN MO KAPAG PRECENTAGE IS EQUAL TO 0', discountPercent)
            vatPercentage1 = 1.12;
            // Calculate discount value based on the selected percentage
            let discountValue = percentAsNumber ? roundToTwoDecimals((percentAsNumber / 100) * roundToTwoDecimals(originalTotalAmountDue.value)) : 0; 
            discountValue3 = discountValue;

            vatExemptSales1 = roundToTwoDecimals(roundToTwoDecimals(originalTotalAmountDue.value) / vatPercentage1); 

            discountValue2 = percentAsNumber ? roundToTwoDecimals((percentAsNumber / 100) * roundToTwoDecimals(vatExemptSales1)) : 0; 

            newInvoiceComputation.value.Less_SC_PWD_Discount = discountValue2;  // Update the Less_SC_PWD_Discount field
            discountValue1=roundToTwoDecimals(discountValue2);

            // this is where I put vatExemptSales1 or originalTotalAmountDue

            if(discountPercent === 0){
                totalAmountDueHolder.value = originalTotalAmountDue1
                count=0;
            }
            else{
                const newTotalAmountDue = roundToTwoDecimals((originalTotalAmountDue1/1.12) - discountValue2);
                totalAmountDueHolder.value = newTotalAmountDue;
                count+=1;
            }
  }  
);


function returnOriginalValue(){
    totalAmountDueHolder.value = vatInclusive;
    console.log('KAPAG PININDOT YUNG 0% SA SENIOR DISCOUNT TOTAL AMOUNT HOLDER', totalAmountDueHolder.value)
}



//DELETE AN INVOICE


const props = defineProps({
  filteredFinances: {
    type: Array,
    required: true
  },
})

const emit = defineEmits(['financeDeleted', 'editFinance', 'viewFinance'])

const showDeleteModal = ref(false)
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

  
function handleSeniorDiscountToggle() {
    
        // Clear the discount values if the checkbox is unchecked
        newInvoiceComputation.value.Less_SC_PWD_Discount = null;
        newInvoiceComputation.value.Less_SC_PWD_Discount_Percent = null;
        console.log('DITO DUMAAN SI PARE NUNG KINLICK YUNG CHECKBOX VALUE TOTAL AMOUNT HOLDER', totalAmountDueHolder.value);
        console.log('DITO DUMAAN SI PARE NUNG KINLICK YUNG CHECKBOX VALUE TOTAL AMOUNT HOLDER', totalAmountDueHolder.value);
}

function addInvoiceInformation() {
        addInvoice();
}


//REMOVE ALL FIELDS IF INVOICE IS DONE
const completed = ref(false);
function clearAddInvoiceFields() {
    
    if(completed.value){
        showAddInvoiceModal.value = false;
    }
    console.log('completed value on closing forms', completed.value);
}



//----------------------------------------------FOR VIEWING OF INOVOICE----------------------------------------------
function updateInvoiceInformation(){
    updateInvoice();
}

const showViewInvoiceModal = ref(false); 
const pdfUrl = ref(''); // Store the PDF URL

let view_invoice_system_id = 0;
let view_invoice_id = 0;
const selectedInvoice = ref(null);
const selectedInvoiceAdditionals = ref([]);
const selectedInvoiceComputation = ref([]);
const viewInvoiceDetails = async (invoice) => {
    try {
        // Assuming `invoice_id` is passed when calling this method
        const response = await axios.get(`/api/invoice/${invoice.invoice_id}`);
        selectedInvoice.value = response.data;

        view_invoice_system_id = response.data.invoice_system_id;
        view_invoice_id = response.data.invoice_id;
        showViewInvoiceModal.value = true;

        const responseItems = await axios.get(`api/invoice_item/${view_invoice_system_id}`);
        selectedInvoiceItems.value = responseItems.data;

        const responseAdditional = await axios.get(`api/invoice_additional/${view_invoice_system_id}`);
        selectedInvoiceAdditionals.value = responseAdditional.data;

        const responseComputation = await axios.get(`api/invoice_computation/${view_invoice_system_id}`);
        selectedInvoiceComputation.value = responseComputation.data;

    } catch (error) {
        console.error("Error fetching invoice details:", error);
    }
};
function printInvoice() {
    try {
        // Open the API route in a new tab for PDF generation
        window.open(`/api/invoice_print/${view_invoice_id}`, '_blank');
    } catch (error) {
        console.error("Error printing invoice:", error);
    }
}


//---------------------------------------------------FOR EDITING OF INVOICE------------------------------------------
let edit_invoice_system_id = 0;
const editInvoiceDetails = async (invoice) => {
    try {

        // Assuming `invoice_id` is passed when calling this method
        const response = await axios.get(`/api/invoice/${invoice.invoice_id}`);
        editInvoice.value = response.data;

        edit_invoice_system_id = response.data.invoice_system_id;
        // edit_invoice_id = response.data.invoice_id;
        showEditInvoiceModal.value = true;

        const responseItems = await axios.get(`api/invoice_item/${edit_invoice_system_id}`);
        selectedInvoiceItems.value = responseItems.data;

        const responseAdditional = await axios.get(`api/invoice_additional/${edit_invoice_system_id}`);
        textUpdateAdditionalFields.value = responseAdditional.data;

        const responseComputation = await axios.get(`api/invoice_computation/${edit_invoice_system_id}`);
        selectedInvoiceComputation.value = responseComputation.data;
        countUpdate=0;
        putInvoiceComputationFieldsUpdate();

        console.log('SELECTED INVOICE COMPUTATION VALUES TOTAL AMOUNT DUE: ', selectedInvoiceComputation.value.total_Amount_Due);
        console.log('SELECTED INVOICE COMPUTATION VALUES: ', selectedInvoiceComputation);
    } catch (error) {
        console.error("Error fetching invoice details:", error);
    }
};



//----------------------------------------------FOR UPDATING INVOICE------------------------------------------
const updateInvoice = async () => {
    try {

        // Create a FormData object and append the necessary fields
        const formData = new FormData();
        for (const key in editInvoice.value) {
            if (editInvoice.value[key] !== null && editInvoice.value[key] !== undefined) {
                formData.append(key, editInvoice.value[key]);
            }
        }
        
        // Use the invoice_system_id from editInvoice for the request URL
        const response = await axios.post(`/api/invoice/${editInvoice.value.invoice_system_id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        // Update the invoices array with the new data
        const index = invoices.value.findIndex(invoice => invoice.invoice_system_id === editInvoice.value.invoice_system_id);
        if (index !== -1) {
            invoices.value[index] = response.data;
        }
        
        // Hide the edit invoice modal
        updateInvoiceItem();

    } catch (error) {
        console.error("Error updating invoice:", error);
    }
};



//----------------------------------------------FOR UPDATING INVOICE ITEMS------------------------------------------
const filteredUpdateProducts = (query) => {
  if (!query) {
    return [];
  }
  const searchTerm = query.toLowerCase();
  return products.value.filter((product) => {
    return (
      product.name.toLowerCase().includes(searchTerm) ||
      product.category.toLowerCase().includes(searchTerm) ||
      product.status.toLowerCase().includes(searchTerm) ||
      product.brand.toLowerCase().includes(searchTerm)
    );
  });
};
const selectedInvoiceItems = ref([
  { 
    image:'', product_name: '', on_sale: 'no', product_price: '', quantity: '', final_price: '', areFieldsEnabled: false, isSearching: false, product_id:'', seniorPWD_discountable:'no' }
]);// Start with one pair of text fields
const addUpdateItemTextField = () => {
    selectedInvoiceItems.value.push({
    image:'', product_name: '', on_sale: 'no', product_price: '', quantity: '', final_price: '', isSearching: false, product_id:'', seniorPWD_discountable:'no'
  });
  editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
}

const removeItemTextField1 = (index) => {
    selectedInvoiceItems.value.splice(index, 1); // Remove the text field pair at the specified index
  console.log('edit remove text field clicked');
  editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};
const selectUpdateProduct = (product, index) => {
  // Assign the selected product's details to the corresponding text field
  selectedInvoiceItems.value[index].image = product.image;
  selectedInvoiceItems.value[index].product_name = product.name;
  selectedInvoiceItems.value[index].on_sale = product.on_sale;
  // Hide the search results (clear the search query)
  selectedInvoiceItems.value[index].product_price = product.price;
  selectedInvoiceItems.value[index].areFieldsEnabled = true;
  selectedInvoiceItems.value[index].isSearching = false; // Hide the search results 
  selectedInvoiceItems.value[index].product_id = product.id;
  selectedInvoiceItems.value[index].quantity = 0;
  selectedInvoiceItems.value[index].final_price = 0;
};

const updateInvoiceItem = async () => {
    try {
        // Send DELETE request to remove all invoice items for the given invoice_system_id
        await axios.delete(`/api/invoice_item/${editInvoice.value.invoice_system_id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        // Optional: update front-end state to remove the deleted invoice items from the list
        invoices.value = invoices.value.filter(invoice => invoice.invoice_system_id !== editInvoice.value.invoice_system_id);


        for (let field of selectedInvoiceItems.value) {
            if(field.product_name.trim() !== ''){
            const formData = new FormData();
            formData.append('invoice_system_id', editInvoice.value.invoice_system_id)
            formData.append('product_id', field.product_id);
            formData.append('product_name', field.product_name);
            formData.append('product_price', field.product_price)
            formData.append('on_sale', field.on_sale);
            formData.append('seniorPWD_discountable', field.seniorPWD_discountable);
            formData.append('on_sale_price', field.product_price);
            formData.append('quantity', field.quantity);
            formData.append('final_price', field.final_price);

            await axios.post(`/api/invoice_item/${editInvoice.value.invoice_system_id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
        } else{
                console.log('Skipping empty item description');
            }

        }

        updateInvoiceAdditional();

        console.log("Invoice Item added successfully.");


        
        console.log("Invoice Items deleted successfully.");
    } catch (error) {
        console.error("Error deleting invoice items:", error);
    }
};


//----------------------------------------------FOR UPDATING INVOICE ADDITIONALS------------------------------------------
const textUpdateAdditionalFields = ref([
  { addtl_Costs_Description: '', aCD_amount:'', aCD_quantity: '', aCD_Total_Amount: ''}
]);// Start with one pair of text fields

// Method to add a new pair of text fields
const addUpdateAdditionalTextField = () => {
  textUpdateAdditionalFields.value.push({
    addtl_Costs_Description:'', aCD_amount:'', aCD_quantity: '', aCD_Total_Amount:''});
    editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};
// Method to remove a text field
const removeUpdateAdditionalTextField = (index) => {
  textUpdateAdditionalFields.value.splice(index, 1); // Remove the text field pair at the specified index
  editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};
const updateInvoiceAdditional = async () => {
    try {
        await axios.delete(`/api/invoice_additional/${editInvoice.value.invoice_system_id}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        invoices.value = invoices.value.filter(invoice => invoice.invoice_system_id !== editInvoice.value.invoice_system_id);

    
        
        for (let field of textUpdateAdditionalFields.value) {
            if(field.addtl_Costs_Description.trim() !== ''){
            console.log('Submitting description:', field.addtl_Costs_Description); // Log the description
            console.log('Submitting amount:', field.aCD_amount); // Log the amount

            const formData = new FormData();
            formData.append('invoice_system_id', editInvoice.value.invoice_system_id)
            formData.append('addtl_Costs_Description', field.addtl_Costs_Description);
            formData.append('aCD_amount', field.aCD_amount);
            formData.append('aCD_quantity', field.aCD_quantity);
            formData.append('aCD_Total_Amount', field.aCD_Total_Amount);
        
            await axios.post(`/api/invoice_additional/${editInvoice.value.invoice_system_id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            });
            } else{
                console.log('Skipping additional description');
            }
        }

        updateInvoiceComputation();
        
        console.log("Additional costs added successfully.");
    } catch (error) {
        console.error("Error adding invoice additional:", error);
    }
}


//--------------------------------------------------FOR UPDATING COMPUTATIONS---------------------------------------------


const editInvoiceComputation = ref({
    VATable_Sales: 0,
    VAT_Exempt_Sales: 0,
    Zero_Rated_Sales: 0,
    VAT_Amount: 0,

    VAT_Inclusive: 0,
    Less_VAT: 0,
    Amount_NET_of_VAT: 0,
    Less_SC_PWD_Discount: 0,
    Less_SC_PWD_Discount_Percent: 0,
    Amount_Due: 0,
    Add_VAT: 0,

    tax: 0,
    total_Amount_Due: 0,

    
})

const putInvoiceComputationFieldsUpdate = async () => {
    try {
        // Create FormData to send request
        // const formData = new FormData();

        // Get values from input fields without using v-model
        totalAmountDueHolderUpdate.value = selectedInvoiceComputation.value.VAT_Inclusive;
        comp_VAT_InclusiveUpdate.value = selectedInvoiceComputation.value.VAT_Inclusive;
        // editInvoiceComputation.value.Less_SC_PWD_Discount= selectedInvoiceComputation.value.Less_SC_PWD_Discount;
        // editInvoiceComputation.value.Less_SC_PWD_Discount_Percent= parseInt(selectedInvoiceComputation.value.Less_SC_PWD_Discount_Percent, 10);
        // comp_VAT_Exempt_SalesUpdate.value= selectedInvoiceComputation.value.VAT_Exempt_Sales;
        // editInvoiceComputation.Zero_Rated_Sales= selectedInvoiceComputation.value.Zero_Rated_Sales;
        // comp_VAT_AmountDueUpdate.value= selectedInvoiceComputation.value.VAT_Amount;
        // comp_VAT_Amount_Less_AddUpdate.value= selectedInvoiceComputation.value.Less_VAT;
        // comp_VATable_Sales_NET_of_VATUpdate.value= selectedInvoiceComputation.value.Amount_NET_of_VAT;
        // comp_VATable_Sales_NET_of_VATUpdate.value= selectedInvoiceComputation.value.VATable_Sales;
        // comp_VAT_AmountDue.value= selectedInvoiceComputation.value.Amount_Due;
        // comp_VAT_Amount_Less_AddUpdate.value= selectedInvoiceComputation.value.Add_VAT;
        // comp_TaxUpdate.value= selectedInvoiceComputation.value.tax;

        console.error("LOGGING PERCENTAGE DISCOUNT SENIOR PWD:", editInvoiceComputation.value.Less_SC_PWD_Discount_Percent);
        console.error("LOGGING PERCENTAGE DISCOUNT SENIOR PWD:", editInvoiceComputation.value.Less_SC_PWD_Discount_Percent);
        console.error("LOGGING PERCENTAGE DISCOUNT SENIOR PWD:", editInvoiceComputation.value.Less_SC_PWD_Discount_Percent);
        console.error("LOGGING PERCENTAGE DISCOUNT SENIOR PWD:", editInvoiceComputation.value.Less_SC_PWD_Discount_Percent);
        
    } catch (error) {
        console.error("Error fetching invoice computation:", error);
        completed.value = false;
    }
};


const updateTotalAdditionalAmountUpdate = (index) => {
  const field = textUpdateAdditionalFields.value[index];
  field.aCD_Total_Amount = roundToTwoDecimals(roundToTwoDecimals(field.aCD_quantity) * roundToTwoDecimals(field.aCD_amount) || 0);
  editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};
const updateTotalProductAmountUpdate = (index) => {
const field = selectedInvoiceItems.value[index];
field.final_price = roundToTwoDecimals(roundToTwoDecimals(field.quantity) * roundToTwoDecimals(field.product_price) || 0);
editInvoiceComputation.value.Less_SC_PWD_Discount_Percent = 0;
};


const totalAmountDueUpdate = computed(() => {
  // Calculate the product total with rounding
  const productTotal = selectedInvoiceItems.value.reduce((sum, field) => {
    return  roundToTwoDecimals(sum + roundToTwoDecimals(field.final_price) || 0);
  }, 0);
  
  // Calculate the additional total with rounding
  const additionalTotal = textUpdateAdditionalFields.value.reduce((sum, field) => {
    return roundToTwoDecimals(sum + roundToTwoDecimals(field.aCD_Total_Amount) || 0);
  }, 0);
  
  // Round the final total amount due
  return roundToTwoDecimals(productTotal + additionalTotal);
});

const totalAmountDueHolderUpdate = ref(0);
watch(totalAmountDueUpdate, (newValue) => {
  totalAmountDueHolderUpdate.value = roundToTwoDecimals(newValue); // Round the new value
});

watch(totalAmountDueHolderUpdate, (newValue) => {
  totalAmountDueHolderUpdate.value = roundToTwoDecimals(newValue); // Round the new value

  console.log('Total Amount Due Holder updated manually:', newValue);
});


const comp_VATable_Sales_NET_of_VATUpdate = computed(() => {
    if(countUpdate===0){
          // Use the totalAmountDueHolderUpdate value; default to 0 if undefined or falsy
  const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;
  const vatPercentage = 1.12;  // Assuming VAT is 12%

  // Calculate VATable Sales NET of VAT
  const vatableSales = roundToTwoDecimals(total / vatPercentage);

  // Round to 2 decimal places using the rounding function
  const roundedVatableSales = roundToTwoDecimals(vatableSales) ;

  return roundedVatableSales; // Return the rounded value
    }else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;  // Use totalAmountDueHolderUpdate as a number

        // Calculate VAT Inclusive as total including VAT
        const vatNetVat = roundToTwoDecimals(total);  // This assumes VAT is added on top of the total

        return roundToTwoDecimals(vatNetVat); // Return the rounded VAT inclusive amount
    }
});
const comp_VAT_Amount_Less_AddUpdate = computed(() => {
    if(countUpdate===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;
        const vatPercentage = 1.12;  // Assuming VAT is 12%

        // Calculate VATable Sales NET of VAT
        const vatableSales = roundToTwoDecimals(total / vatPercentage);

        // Round to 2 decimal places using the rounding function
        const roundedVatableSales = roundToTwoDecimals(vatableSales);
            const VATamount = roundToTwoDecimals(total-roundedVatableSales);

        return VATamount; // Return the rounded value
    }else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;

        // Calculate VATable Sales NET of VAT
        const vatableSales = roundToTwoDecimals((total) * 1.12);

        // Round to 2 decimal places using the rounding function
        const roundedVatableSales = roundToTwoDecimals(vatableSales);
            const VATamount = roundToTwoDecimals(roundedVatableSales-total);

        return VATamount; // Return the rounded value
    }

    
});

const comp_TaxUpdate = computed(() => {
    if(countUpdate===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update)) || 0;  // Use totalAmountDueHolderUpdate as a number
        const vatSalesPercentage = 1.12;
        const vatPercentage = 0.02;  // Assuming VAT is 12%, adjust this based on your requirement

        // Calculate VAT Inclusive as total including VAT
        const vatableSales = roundToTwoDecimals(total / vatSalesPercentage);
        const Tax = roundToTwoDecimals(vatableSales * vatPercentage);  // Round to two decimal places

        return Tax; // Return the rounded vat amount
    }
    else{
        let total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update)) || 0;  // Use totalAmountDueHolderUpdate as a number
        const vatSalesPercentage = 1.12;
        const vatPercentage = 0.02;  // Assuming VAT is 12%, adjust this based on your requirement

        total = roundToTwoDecimals(total*1.12);
        // Calculate VAT Inclusive as total including VAT
        const vatableSales = roundToTwoDecimals(total / vatSalesPercentage);
        const Tax = roundToTwoDecimals(vatableSales * vatPercentage);  // Round to two decimal places

        return Tax; // Return the rounded vat amount
    }

});

const comp_VAT_AmountDueUpdate = computed(() => {
    if(countUpdate===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;  // Use totalAmountDueHolderUpdate as a number
        const tax = 0.02;  // Assuming a certain tax percentage
        const vatPercentage = 1.12;

        // Calculate VAT Inclusive as total including VAT
        const amount = roundToTwoDecimals(total / vatPercentage);
        const compTax = roundToTwoDecimals(amount * tax);
        const vatAmountDue = roundToTwoDecimals(total - compTax);  // This assumes VAT is added on top of the total
        console.log('total in vat amount due', total)
        console.log('amount in vat amount due', amount);
        console.log('compTax in vat amount due', compTax);
        console.log('vatAmountDue in vat amount due', vatAmountDue);

        return roundToTwoDecimals(vatAmountDue); // Return the rounded VAT amount due
    }
    else{
        let total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;  // Use totalAmountDueHolderUpdate as a number
        const tax = 0.02;  // Assuming a certain tax percentage
        const vatPercentage = 1.12;
        total = roundToTwoDecimals(total*1.12);
        // Calculate VAT Inclusive as total including VAT
        const amount = roundToTwoDecimals(total / vatPercentage);
        const compTax = roundToTwoDecimals(amount * tax);
        const vatAmountDue = roundToTwoDecimals(total - compTax);  // This assumes VAT is added on top of the total

        console.log('123123 total in vat amount due', total)
        console.log('123123 amount in vat amount due', amount);
        console.log('123123 compTax in vat amount due', compTax);
        console.log('123123 vatAmountDue in vat amount due', vatAmountDue);
        return roundToTwoDecimals(vatAmountDue); // Return the rounded VAT amount due
    }

});

let vatInclusiveUpdate = 0;
let vatInclusive1Update = 0
const comp_VAT_InclusiveUpdate = computed(() => {
    if(countUpdate===0){
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;  // Use totalAmountDueHolderUpdate as a number

        // Calculate VAT Inclusive as total including VAT
        vatInclusive1Update = roundToTwoDecimals(total);  // This assumes VAT is added on top of the total
        
        return roundToTwoDecimals(vatInclusive1Update); // Return the rounded VAT inclusive amount
    }
    else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;  // Use totalAmountDueHolderUpdate as a number
        
        // Calculate VAT Inclusive as total including VAT
        vatInclusive1Update = roundToTwoDecimals(total * 1.12);  // This assumes VAT is added on top of the total

        return roundToTwoDecimals(vatInclusive1Update); // Return the rounded VAT inclusive amount
    }

});

const comp_VAT_Exempt_SalesUpdate = computed(() => {
    if(countUpdate===0){
        

        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;
        const vatPercentage = 1.12;  // Assuming VAT is 12%

        // Calculate VATable Sales NET of VAT
        const vatableSales = roundToTwoDecimals(total / vatPercentage);

        // Round to 2 decimal places using the rounding function
        const roundedVatableSales = roundToTwoDecimals(vatableSales) ;

        if(discountPercent === 0){
            return 0;
        }
        return roundedVatableSales; 

    }else{
        const total = roundToTwoDecimals(roundToTwoDecimals(totalAmountDueHolderUpdate.value) + roundToTwoDecimals(discountValue1Update))|| 0;  // Use totalAmountDueHolderUpdate as a number

        // Calculate VAT Inclusive as total including VAT
        const vatNetVat = roundToTwoDecimals(total);  // This assumes VAT is added on top of the total
        if(discountPercent === 0){
            return 0;
        }
        return roundToTwoDecimals(vatNetVat); // Return the rounded VAT inclusive amount

    }
    // Only calculate VAT Exempt Sales if Senior Discount is enabled

});


const isSeniorDiscountEnabledUpdate = ref(false);

let countUpdate = 0;
let originalTotalAmountDueUpdate = ref(0);
let originalTotalAmountDue1Update =0;
let discountValue1Update=0;
let vatExemptSales1Update=0;
let discountValue2Update=0;
let vatPercentage1Update=0;
let discountValue3Update=0;
let discountPercentUpdate=0;
watch(
  () => editInvoiceComputation.value.Less_SC_PWD_Discount_Percent,  // The value we're watching
  (newPercentUpdate) => {  // The callback that gets executed when the value changes
    const percentAsNumber = Number(newPercentUpdate); // Convert the selected value to a number
    newPercentUpdate = percentAsNumber;    
    discountPercentUpdate=newPercentUpdate;
    console.log('countUpdate: ', countUpdate);
        if(countUpdate === 0){
            originalTotalAmountDueUpdate.value = roundToTwoDecimals(totalAmountDueHolderUpdate.value);
            originalTotalAmountDue1Update = roundToTwoDecimals(originalTotalAmountDueUpdate.value);

            console.log('COUNTUPDATE WENT TO SENIOR DISCOUNT IF COUNTUPDATE === 0')
        }        
            console.log('TIGNAN MO KAPAG PRECENTAGE IS EQUAL TO 0', discountPercentUpdate)
            vatPercentage1Update = 1.12;
            // Calculate discount value based on the selected percentage
            let discountValueUpdate = percentAsNumber ? roundToTwoDecimals((percentAsNumber / 100) * roundToTwoDecimals(originalTotalAmountDueUpdate.value)) : 0; 
            discountValue3Update = discountValueUpdate;

            vatExemptSales1Update = roundToTwoDecimals(roundToTwoDecimals(originalTotalAmountDueUpdate.value) / vatPercentage1Update); 

            discountValue2Update = percentAsNumber ? roundToTwoDecimals((percentAsNumber / 100) * roundToTwoDecimals(vatExemptSales1Update)) : 0; 

            editInvoiceComputation.value.Less_SC_PWD_Discount = discountValue2Update;  // Update the Less_SC_PWD_Discount field
            discountValue1Update = roundToTwoDecimals(discountValue2Update);

            // this is where I put vatExemptSales1Update or originalTotalAmountDue

            if(discountPercentUpdate === 0){
                totalAmountDueHolderUpdate.value = originalTotalAmountDue1Update
                countUpdate=0;
            }
            else{
                const newTotalAmountDueUpdate = roundToTwoDecimals((originalTotalAmountDue1Update/1.12) - discountValue2Update);
                totalAmountDueHolderUpdate.value = newTotalAmountDueUpdate;
                countUpdate+=1;
            }
  }  
);


function returnOriginalValueUpdate(){
    totalAmountDueHolderUpdate.value = vatInclusive;
    console.log('KAPAG PININDOT YUNG 0% SA SENIOR DISCOUNT TOTAL AMOUNT HOLDER', totalAmountDueHolder.value)
}


const totalAmountDue1Update = ref(0); // Create a ref for total amount due
const LessScPwdDiscountPercentUpdate = ref(0); // Create a ref for SC discount
const LessScPwdDiscountUpdate = ref(0); // Create a ref for SC discount percent
const VatExemptSalesUpdate = ref(0);
const ZeroRatedSalesUpdate = ref(0);
const VaTAmountUpdate = ref(0);
const VatInclusiveUpdate = ref(0);
const LessVatUpdate = ref(0);
const AmountNetOfVatUpdate = ref(0);
const VatableSalesUpdate = ref(0);
const AmountDueUpdate = ref(0);
const AddVatUpdate = ref(0);
const taxUpdate = ref(0);
const seniorPwdDiscountableUpdate = ref(false); // Assuming this is a checkbox

const updateInvoiceComputation = async () => {
    try {
        // Create FormData to send request
        const formData = new FormData();

        // Get values from input fields without using v-model
        const totalAmountDueValue = roundToTwoDecimals(totalAmountDue1Update.value.value);
        const lessSCDiscountValue = roundToTwoDecimals(LessScPwdDiscountUpdate.value.value);
        const lessSCDiscountPercentValue = roundToTwoDecimals(LessScPwdDiscountPercentUpdate.value.value);

        const VatExemptSalesValue = roundToTwoDecimals(VatExemptSalesUpdate.value.value);
        const ZeroRatedSalesValue = roundToTwoDecimals(ZeroRatedSalesUpdate.value.value);
        const VaTAmountValue = roundToTwoDecimals(VaTAmountUpdate.value.value);
        
        const VatInclusiveValue = roundToTwoDecimals(VatInclusiveUpdate.value.value);
        const LessVatValue = roundToTwoDecimals(LessVatUpdate.value.value);
        const AmountNetOfVatValue = roundToTwoDecimals(AmountNetOfVatUpdate.value.value);
        const VatableSalesValue = roundToTwoDecimals(VatableSalesUpdate.value.value);
        const AmountDueValue = roundToTwoDecimals(AmountDueUpdate.value.value);
        const AddVatValue = roundToTwoDecimals(AddVatUpdate.value.value);
        const taxValue = roundToTwoDecimals(taxUpdate.value.value);

        const isSeniorDiscountable = lessSCDiscountPercentValue > 0 ? 'yes' : 'no';


        console.log('totalamountdue', totalAmountDueValue);
        console.log('discount value', lessSCDiscountValue);
        console.log('discount percent', lessSCDiscountPercentValue);
        console.log('isseniordiscountable', isSeniorDiscountable);
        // Add these values to formData
        formData.append('invoice_system_id', editInvoice.value.invoice_system_id);
        formData.append('total_Amount_Due', totalAmountDueValue);
        formData.append('senior_PWD_Discountable', isSeniorDiscountable);
        formData.append('Less_SC_PWD_Discount', lessSCDiscountValue);
        formData.append('Less_SC_PWD_Discount_Percent', lessSCDiscountPercentValue);
        formData.append('VATable_Sales', VatableSalesValue);
        formData.append('VAT_Exempt_Sales', VatExemptSalesValue);
        formData.append('Zero_Rated_Sales', ZeroRatedSalesValue); 
        formData.append('VAT_Amount', VaTAmountValue);
        formData.append('Amount_NET_of_VAT', AmountNetOfVatValue);
        formData.append('VAT_Inclusive', VatInclusiveValue);
        formData.append('Less_VAT', LessVatValue);
        formData.append('Amount_Due', AmountDueValue);
        formData.append('Add_VAT', AddVatValue);
        formData.append('tax', taxValue)
        // formData.append('tax', taxValue);

        // Send the request to the server
        const response = await axios.post(`/api/invoice_computation/${editInvoice.value.invoice_system_id}`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });
        completed.value = true;

        const index = invoices.value.findIndex(invoice => invoice.invoice_system_id === editInvoice.value.invoice_system_id);
        if (index !== -1) {
            invoices.value[index] = response.data;
        }


        showSuccessEditModal.value = true;
        setTimeout(() => {
        showSuccessEditModal.value = false;
        }, 1000) 
        

        fetchInvoices();
        showEditInvoiceModal.value = false;

        console.log('completed value', completed.value);

        //Handle the response
        console.log("UPDATED COMPUTATION" );
        console.log('UPDATED COMPUTATION');
        console.log('UPDATED COMPUTATION');
        console.log('UPDATED COMPUTATION');
        console.log('UPDATED COMPUTATION');
        console.log('UPDATED COMPUTATION');

    } catch (error) {
        console.error("Error updating invoice computation:", error);
        completed.value = false;
    }
};


fetchInvoices();
fetchProducts();

const isDateFiltered = ref(false);
const startDate = ref('');
const endDate = ref('');
const invoicesByDate = ref([]);

const fetchInvoicesByDate = async () => {
    try {
        const response = await axios.get('/api/invoice_by_date', {
            params: {
                start_date: startDate.value,
                end_date: endDate.value
            }
        });
        invoicesByDate.value = response.data;
        isDateFiltered.value = true; // Set flag to true after fetching by date
    } catch (error) {
        console.error("Error fetching invoices by date:", error);
    }
};

const clearFetchInvoicessByDate = async () => {

    startDate.value = '';
    endDate.value = '';
    invoicesByDate.value = '';
    isDateFiltered.value = false;
};
watch([startDate, endDate], async ([newStartDate, newEndDate]) => {
    if (newStartDate && newEndDate) {
        // if(showPrintInvoiceSummaryByDate){
        //     isDateFiltered.value = false;
        // }
        await fetchInvoicesByDate();
    } else {
        // Reset financesByDate and isDateFiltered if dates are cleared
        invoicesByDate.value = [];
        isDateFiltered.value = false;
    }
});


const showPrintInvoiceSummaryByDate = ref(false);
function printInvoiceSummaryByDate() {
    showPrintInvoiceSummaryByDate.value = true;
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


const startDatePrint = ref('');
const endDatePrint = ref('');

function printInvoicesByDate() {
    try {
        const startDate = startDatePrint.value;
        const endDate = endDatePrint.value;
        
        if(summaryOption.value.option === 'summaryPdf'){
            window.open(`/api/print/summary/invoice_by_date/pdf?startDatePrint=${startDate}&endDatePrint=${endDate}`, '_blank');
        }
        else{
             window.open(`/api/print/summary/invoice_by_date/xlsx?startDatePrint=${startDate}&endDatePrint=${endDate}`, '_blank');
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


const showSuccessAddModal = ref(false);
const showSuccessModal = ref(false);
const showSuccessEditModal = ref(false);
</script>



<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <div class="py-7 ">
            <div class="max-w-auto mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">

                    <div class="p-6 text-gray-900 dark:text-gray-100">  
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-2xl font-semibold">List of Invoices</h2>
                            <div class="flex">
                                <div class="flex w-100">
                                    
                                    <div>
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

                                    <div class="items-center flex ml-10 text-white">
                                        <div class="mr-2 text-xs">Filter by Date:</div>    
                                            <input id="startDate" class="text-black text-sm" type="date" v-model="startDate" />
                                        <div class="mx-2 text-xs"> To </div>
                                            <input id="endDate" class="text-black text-sm mr-2" type="date" v-model="endDate" />
                                        <button @click="clearFetchInvoicessByDate" class="text-xs bg-stone-600 rounded-lg p-2 text-white ml-2 me-5">Clear</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="overflow-auto" style="height: 500px">
                            <table class="min-w-full bg-white dark:bg-gray-800">
                                <thead class="sticky">   
                                <!-- HEADER FOR INVOICES -->
                                    <tr class="sticky">
                                        <th class="sticky px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">ID No.</th>
                                        <th class="sticky px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Customer Name</th>
                                        <th class="sticky px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Payment Type</th>
                                        <th class="sticky px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Total Amount</th>
                                        <th class="sticky px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Status</th>
                                        <th class="sticky px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Date</th>
                                        <th class="sticky px-6 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">Actions</th>
                                    </tr>
                                </thead>
                                <!-- FOR SHOWING OF INVOICES -->
                                    <tbody>
                                    <tr v-if="filteredInvoices.length === 0 || (invoicesByDate.length === 0 && isDateFiltered)">
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700" colspan="10">No invoice available.</td>
                                    </tr>


                                    <tr class = "text-center" v-for="invoice in invoicesByDate" :key="invoice.invoice_id" v-if="isDateFiltered">
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.invoice_id }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.customer_Name }}</td>
                                        <td class="flex items-center justify-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="w-60">

                                                <span :class="{
                                                    'bg-green-800 text-white py-1 px-3 rounded-full': invoice.payment_Type === 'cash',
                                                    'bg-blue-900 text-white py-1 px-3 rounded-full': invoice.payment_Type === 'check',
                                                    'bg-yellow-700 text-white py-1 px-3 rounded-full': invoice.payment_Type === 'online transaction',
                                                }"><font-awesome-icon v-if="invoice.payment_Type === 'cash'" icon="fa-solid fa-money-bill" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.payment_Type === 'check'" icon="fa-solid fa-clipboard" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.payment_Type === 'online transaction'" icon="fa-solid fa-globe" size="sm" class="mr-1" />
                                                {{  invoice.payment_Type === 'cash' ? 'Cash' : 
                                                    invoice.payment_Type === 'check' ? 'Check' : 
                                                    invoice.payment_Type === 'online transaction' ? 'Online Transaction' : 
                                                    invoice.payment_Type  }}</span>
                                            </div>

                                            </td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">PHP {{ invoice.total_Amount_Due  }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">

                                            <div>
                                                <span :class="{
                                                    'text-sm bg-green-600 text-white py-1 px-3 rounded-full': invoice.status === 'paid',
                                                    'text-sm bg-red-600 text-white py-1 px-3 rounded-full': invoice.status === 'unpaid',
                                                    'text-sm bg-orange-600 shadow-none text-white py-1 px-3 rounded-full shadow-xs': invoice.status === 'pending refund',
                                                    'text-sm bg-orange-600 text-white py-1 px-3 rounded-full': invoice.status === 'partially paid',
                                                    'text-sm bg-green-600 shadow-none text-white py-1 px-3 rounded-full': invoice.status === 'refunded'
                                                }">
                                                <font-awesome-icon v-if="invoice.status === 'paid'" icon="fa-solid fa-check" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'unpaid'" icon="fa-solid fa-x" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'partially paid'" icon="fa-solid fa-bars" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'pending refund'" icon="fa-solid fa-bars" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'refunded'" icon="fa-solid fa-check" size="sm" class="mr-1" />
                                                {{invoice.status === 'paid' ? 'Paid' : 
                                                     invoice.status === 'unpaid' ? 'Unpaid' : 
                                                     invoice.status === 'partially paid' ? 'Partially Paid' : 
                                                     invoice.status === 'pending refund' ? 'Pending Refund' : 
                                                     invoice.status === 'refunded' ? 'Refunded' : 
                                                     invoice.status }}</span>
                                            </div>

                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.date }}</td>
                                        <td class="flex gap-1 items-center justify-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <button @click="editInvoiceDetails(invoice), currentStepUpdate = 1" class="bg-yellow-500 text-white px-2 py-1 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                            </button>
                                            <button @click="viewInvoiceDetails(invoice)" class="bg-blue-500 text-white px-2 py-1 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-eye" size="sm"/>
                                            </button>
                                            <button @click="openDeleteModal(invoice.invoice_system_id)" class="bg-red-500 text-white px-2 py-1 rounded-full">
                                            <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                            </button>
                                        </td>
                                    </tr>

                                    <tr class = "text-center" v-for="invoice in filteredInvoices" :key="invoice.invoice_id" v-if="!isDateFiltered">
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.invoice_id }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.customer_Name }}</td>
                                        <td class="flex items-center justify-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <div class="w-60">

                                                <span :class="{
                                                    'bg-green-800 text-white py-1 px-3 rounded-full': invoice.payment_Type === 'cash',
                                                    'bg-blue-900 text-white py-1 px-3 rounded-full': invoice.payment_Type === 'check',
                                                    'bg-yellow-700 text-white py-1 px-3 rounded-full': invoice.payment_Type === 'online transaction',
                                                }"><font-awesome-icon v-if="invoice.payment_Type === 'cash'" icon="fa-solid fa-money-bill" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.payment_Type === 'check'" icon="fa-solid fa-clipboard" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.payment_Type === 'online transaction'" icon="fa-solid fa-globe" size="sm" class="mr-1" />
                                                {{  invoice.payment_Type === 'cash' ? 'Cash' : 
                                                    invoice.payment_Type === 'check' ? 'Check' : 
                                                    invoice.payment_Type === 'online transaction' ? 'Online Transaction' : 
                                                    invoice.payment_Type  }}</span>
                                            </div>

                                            </td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">PHP {{ invoice.total_Amount_Due  }}</td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">

                                            <div>
                                                <span :class="{
                                                    'bg-green-600 text-white py-1 px-3 rounded-full': invoice.status === 'paid',
                                                    'bg-red-600 text-white py-1 px-3 rounded-full': invoice.status === 'unpaid',
                                                    'bg-orange-600 shadow-none text-white py-1 px-3 rounded-full shadow-xs': invoice.status === 'pending refund',
                                                    'bg-orange-600 text-white py-1 px-3 rounded-full': invoice.status === 'partially paid',
                                                    'bg-green-600 shadow-none text-white py-1 px-3 rounded-full': invoice.status === 'refunded'
                                                }">
                                                <font-awesome-icon v-if="invoice.status === 'paid'" icon="fa-solid fa-check" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'unpaid'" icon="fa-solid fa-x" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'partially paid'" icon="fa-solid fa-bars" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'pending refund'" icon="fa-solid fa-bars" size="sm" class="mr-1" />
                                                <font-awesome-icon v-if="invoice.status === 'refunded'" icon="fa-solid fa-check" size="sm" class="mr-1" />
                                                {{invoice.status === 'paid' ? 'Paid' : 
                                                     invoice.status === 'unpaid' ? 'Unpaid' : 
                                                     invoice.status === 'partially paid' ? 'Partially Paid' : 
                                                     invoice.status === 'pending refund' ? 'Pending Refund' : 
                                                     invoice.status === 'refunded' ? 'Refunded' : 
                                                     invoice.status }}</span>

                                            </div>

                                        </td>
                                        <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">{{ invoice.date }}</td>
                                        <td class="flex gap-1 items-center justify-center px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                                            <button @click="editInvoiceDetails(invoice), currentStepUpdate = 1" class="bg-yellow-500 text-white px-2 py-1 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-pen" size="sm"/>
                                            </button>
                                            <button @click="viewInvoiceDetails(invoice)" class="bg-blue-500 text-white px-2 py-1 rounded-full">
                                                <font-awesome-icon icon="fa-solid fa-eye" size="sm"/>
                                            </button>
                                            <button @click="openDeleteModal(invoice.invoice_system_id)" class="bg-red-500 text-white px-2 py-1 rounded-full">
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
                    <button @click="printInvoiceSummaryByDate()" class="bg-gray-500 text-white py-2 px-4 rounded">
                        <font-awesome-icon icon="fa-solid fa-print" size="sm" />
                        Print Invoice Summary</button>
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

        <transition name="modal-fade" >
            <div v-if="showPrintInvoiceSummaryByDate" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="pop-in bg-white rounded-lg shadow-lg w-full max-w-md p-6 relative">
            <h2 class="text-2xl font-bold text-center mb-6">Print Invoice Summary</h2>
            
            <button 
                @click="showPrintInvoiceSummaryByDate = false" 
                class=" absolute top-2 right-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                aria-label="Close"
            >
                <font-awesome-icon icon="fa-solid fa-x" size="lg" />
            </button>

            <div class="space-y-6">
                <div class="flex items-center space-x-4">
                <label for="invoiceSummaryOption" class="text-sm font-medium text-gray-700">Print as:</label>
                <select 
                    id="invoiceSummaryOption" 
                    v-model="summaryOption.option"
                    class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                >
                    <option value="summaryPdf">PDF (.pdf)</option>
                    <option value="summaryExcel">Excel Sheet (.xlsx)</option>
                </select>
                </div>

                <div class="space-y-2">
                <p class="text-sm font-medium text-gray-700">Filter by Date:</p>
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

                <div class="flex flex-col items-center space-y-3">

                <button 
                    @click.prevent="printInvoicesByDate" 
                    class="w-full max-w-xs px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    <font-awesome-icon icon="fa-solid fa-print" class="mr-2" />
                    Print Invoice Summary
                </button>
                <button 
                    @click="clearDates" 
                    class="block max-w-xs px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Clear Dates
                </button>

                </div>
            </div>
            </div>
        </div>
        </transition>


        <transition name="modal-fade" >
            <div v-if="showEditInvoiceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class="pop-in bg-white p-4 rounded-lg shadow-lg w-full max-w-7xl h-4/5 relative overflow-auto flex flex-col">
                <h3 style="font-size: 64px;" class="text-lg font-semibold text-center mt-12 mb-10">Edit this Invoice</h3>

                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 1: Invoice and Customer Info</p>
                    </div>
                    <div class="px-12 mb-10">
                        
                        <!-- STEP 1: DIV FOR INVOICE AND CUSTOMER DETAILS -->
                        <form @submit.prevent="updateInvoice" class="border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg w-full">
                        <!-- Step 1: Invoice and Customer Details -->
                            <div class="flex flex-col p-10">
                                <div class="grid grid-cols-3 gap-8 mb-4">
                                    <div>
                                        <label for="invoice_id" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Invoice I.D. No.</label>
                                        <input type="number" id="invoice_id" v-model="editInvoice.invoice_id" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    
                                    <div class="">
                                        <label for="date" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Date</label>
                                        <input type="date" id="date" v-model="editInvoice.date" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="">
                                        <label for="status" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Status</label>
                                        <select id="status" v-model="editInvoice.status" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm">
                                            <option value="paid">Paid</option>
                                            <option value="partially paid">Partially Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                            <option value="pending refund">Pending Refund</option>
                                            <option value="refunded">Refunded</option>

                                        </select>
                                    </div>
                                </div>
                                    
                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                        <label for="customer_Name" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Name</label>
                                        <input type="text" id="customer_Name" v-model="editInvoice.customer_Name" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Juan Dela Cruz"/>
                                    </div>
                                
                                    <div class="">
                                        <label for="payment_Type" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Payment Type</label>
                                        <select id="payment_Type" v-model="editInvoice.payment_Type" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm">
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="online transaction">Online Transaction</option>
                                        </select>
                                    </div>
                                

                                </div>


                                
                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                        <label for="customer_Address" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Address</label>
                                        <input type="text" id="customer_Address" v-model="editInvoice.customer_Address" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="Enter the Customer's Address (Optional)"/>
                                    </div>
                                    <div class="">
                                        <label for="customer_PO_No" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer P.O. Number</label>
                                        <input type="number" id="customer_PO_No" v-model="editInvoice.customer_PO_No" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    
                                </div>

                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                            <label for="customer_TIN" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer TIN Number</label>
                                            <input type="number" id="customer_TIN" v-model="editInvoice.customer_TIN" class="block w-full border-gray-300 rounded-bl-md rounded-r-mdd shadow-sm" placeholder="XXX-XXX-XXX-XXX-XXX"/>
                                    </div>
                                    <div class="">
                                        <label for="customer_OSCA_PWD_ID_No" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer OSCA/PWD I.D. No.</label>
                                        <input type="number" id="customer_OSCA_PWD_ID_No" v-model="editInvoice.customer_OSCA_PWD_ID_No" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="XXXX"/>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2  gap-8 mb-4">
                                    <div class="">
                                        <label for="terms" class=" w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Terms</label>
                                        <input type="text" id="terms" v-model="editInvoice.terms" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. 30 days"/>
                                    </div>

                                    <div>
                                        <label for="customer_Business_Style" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Business Style</label>
                                        <input type="text" id="customer_Business_Style" v-model="editInvoice.customer_Business_Style" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Juan Dela Cruz"/>
                                    </div>
                                
                                </div>

                                
                                <div class="">
                                    <label for="authorized_Representative" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Cashier/Authorized Representative</label>
                                    <input type="text" id="authorized_Representative" v-model="editInvoice.authorized_Representative" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Maria Clara Veronica"/>
                                </div>                                

                            </div>
                        </form>
                    </div>


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 2: Invoice Items</p>
                    </div>
                    <div class="px-12 mb-10">
                        
                        <form @submit.prevent="updateInvoiceItem" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                            <div class="overflow-auto" style="max-height: 400px;"> <!-- Adjusted for scrolling -->
                                <table class="min-w-full bg-white border-gray-700">
                                    <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Product</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Total Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(field, index) in selectedInvoiceItems" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">

                                            <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                                <div class="flex items-center justify-center">
                                                    <div class="flex flex-shrink-0 items-center">
                                                        <div v-if="field.image">
                                                            <img :src="'/storage/' + field.image" class="w-5 h-5 object-cover" />
                                                        </div>
                                                        <div v-else>
                                                            <font-awesome-icon :icon="['fas', 'image']" class="w-5 h-5 object-cover" size="sm" />
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <input  class="w-44" @input="field.isSearching = true" type="text" v-model="field.product_name" placeholder="Search for a Product" />
                                                    </div>
                                                    <ul v-if="field.product_name && field.isSearching" class="absolute z-10 w-52 bg-white shadow-lg rounded-lg mt-1 max-h-60 overflow-y-auto">
                                                    <li @click="selectUpdateProduct(product, index)" v-for="product in filteredUpdateProducts(field.product_name)" :key="product.id">
                                                        <img :src="'/storage/' + product.image" alt="Product Image" class="w-5 h-5 object-cover" />
                                                        {{ product.name }} 
                                                    </li>
                                                </ul>

                                                </div>
                                                
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <div class="flex items-center justify-center">
                                                    <div class="-ml-4 flex items-center justify-between">
                                                        <span class="-ml-20 w-24 text-right text-xs text-gray-400">
                                                            {{ field.on_sale === 'yes' ? 'On' : 'Not' }}<br>
                                                            {{ field.on_sale === 'yes' ? 'Sale' : 'On Sale' }}
                                                        </span>
                                                        <label class="switch px-3">
                                                            <input  :disabled="!field.areFieldsEnabled" type="checkbox" v-model="field.on_sale" true-value="yes" false-value="no" />
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                    <input  class="no-spinner w-32" type="number" v-model="field.product_price" placeholder="Amount" />
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input  :disabled="!field.areFieldsEnabled" class="text-center no-spinner w-16" type="number" @input="updateTotalProductAmountUpdate(index)" v-model="field.quantity" placeholder="Qty." />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input  :disabled="!field.areFieldsEnabled" class="text-center no-spinner w-32" type="number" v-model="field.final_price" placeholder="Total Amount" />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeItemTextField1(index)">
                                                    <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                                </button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="5">
                                                <div class="flex items-center justify-center my-6">
                                                    <button type="button" @click="addUpdateItemTextField" class="flex items-center justify-center">
                                                        <font-awesome-icon :icon="['fas', 'plus']" class="w-6 h-6 mr-2" />
                                                        Click to add New Field
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
  
                    </div>



                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 3: Invoice Additionals</p>
                    </div>
                    <div class="px-12 mb-10">

                        <form @submit.prevent="updateInvoiceAdditional" class="w-full border-4 border-black rounded-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                            <div class="overflow-auto" style="max-height: 400px;"> 
                                <table class="min-w-full bg-white border-gray-700">
                                    <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Description</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Total Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(field, index) in textUpdateAdditionalFields" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">
                                            <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                                <input class="col-span-5 " type="text" v-model="field.addtl_Costs_Description" :placeholder="`Add a Description`" />
                                            </td>
                                            
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input class="text-center no-spinner w-32" type="number" @input="updateTotalAdditionalAmountUpdate(index)" v-model="field.aCD_amount" placeholder="Amount." />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input class="text-center no-spinner w-16" type="number" @input="updateTotalAdditionalAmountUpdate(index)" v-model="field.aCD_quantity" placeholder="Qty." />
                                            </td>
                                            
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input class="no-spinner w-32 col-span-5" type="number" v-model="field.aCD_Total_Amount" :placeholder="`Input Total Amount`" />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeUpdateAdditionalTextField(index)">
                                                    <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                                </button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="5">
                                                <div class="flex items-center justify-center my-6">
                                                    <button type="button" @click="addUpdateAdditionalTextField" class="flex items-center justify-center">
                                                        <font-awesome-icon :icon="['fas', 'plus']" class="w-6 h-6 mr-2" />
                                                        Click to add New Field
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </form>

                    </div> 

                    
                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 4: Invoice Computation</p>
                    </div>
                    <div class="px-12 mb-10">

                        <form @submit.prevent="updateInvoiceComputation" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg">
                            <div class="flex p-14 gap-8">
                                <div class="w-1/2">
                                    <div class="mt-2">----------------------------------------------------------------------</div>
                                    <div class="mt-2 mb-2 pl-10">
                                        <label for="total_Amount_Due" class="font-extrabold w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-md font-medium text-white">Total Amount Due</label>
                                        <input ref="totalAmountDue1Update" type="number" id="total_Amount_Due" @input="countUpdate=0, discountValue1Update=0;" v-model="totalAmountDueHolderUpdate" class="w-96 block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="mt-2 mb-2 pl-10">
                                        <label for="VAT_Inclusive" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VAT Inclusive</label>
                                        <input ref="VatInclusiveUpdate" type="number" id="VAT_Inclusive" v-model="comp_VAT_InclusiveUpdate" class="w-96 block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-11">---------------------------------------------------------------------</div>

                                    <div class="mb-2">
                                        <div class="flex flex-row items-center">
                                            <label for="Less_SC_PWD_Discount" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Less SC PWD Discount</label>
                                            
                                        </div>
                                        <div class="grid grid-cols-5">
                                            <input type="number"
                                                ref="LessScPwdDiscountUpdate" 
                                                id="Less_SC_PWD_Discount" 
                                                v-model="editInvoiceComputation.Less_SC_PWD_Discount" 
                                                class="col-span-4 w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm pr-6" />
                                            <div class="relative col-span-1">
        <select 
            ref="LessScPwdDiscountPercentUpdate"
            v-model="editInvoiceComputation.Less_SC_PWD_Discount_Percent" 
            class="text-center no-spinner col-span-1 w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm" >
            <option @click="returnOriginalValueUpdate()" value="0">0%</option>
            <option value="5">5%</option>
            <option value="20">20%</option>
        </select>

                                                <!-- <span class="absolute inset-y-0 right-2 flex items-center text-gray-500">%</span> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label for="VAT_Exempt_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Vat Exempt Sales</label>
                                        <input ref="VatExemptSalesUpdate" 
                                            type="number" 
                                            id="VAT_Exempt_Sales" 
                                            v-model="comp_VAT_Exempt_SalesUpdate" 
                                            class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="Zero_Rated_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Zero Rated Sales</label>
                                        <input ref="ZeroRatedSalesUpdate" type="number" id="Zero_Rated_Sales" v-model="editInvoiceComputation.Zero_Rated_Sales" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>


                                </div>

                                <div class="w-1/2">
                                    <div class="mb-2">
                                        <label for="VAT_Amount" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VAT Amount</label>
                                        <input ref="VaTAmountUpdate" type="number" id="VAT_Amount" v-model="comp_VAT_Amount_Less_AddUpdate" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Less_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Less VAT</label>
                                        <input ref="LessVatUpdate" type="number" id="Less_VAT" v-model="comp_VAT_Amount_Less_AddUpdate" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Amount_NET_of_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount NET of VAT</label>
                                        <input ref="AmountNetOfVatUpdate" type="number" id="Amount_NET_of_VAT" v-model="comp_VATable_Sales_NET_of_VATUpdate" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="VATable_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VATable Sales</label>
                                        <input ref="VatableSalesUpdate" type="number" id="VATable_Sales" v-model="comp_VATable_Sales_NET_of_VATUpdate" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Amount_Due" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount Due</label>
                                        <input ref="AmountDueUpdate" type="number" id="Amount_Due" v-model="comp_VAT_AmountDueUpdate" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Add_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Additional VAT</label>
                                        <input ref="AddVatUpdate" type="number" id="Add_VAT" v-model="comp_VAT_Amount_Less_AddUpdate" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="mb-2">
                                        <label for="tax" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Tax</label>
                                        <input ref="taxUpdate" type="number" id="tax" v-model="comp_TaxUpdate" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                </div>
                            </div>
                        </form>


                    </div>


                    <div class="flex justify-center mt-6 gap-14">
                                    <button @click="showEditInvoiceModal = false" type="button" class="bg-gray-500 text-white py-2 px-4 rounded">Cancel</button>
                                    <button @click.prevent="updateInvoiceInformation()" class="bg-blue-500 text-white py-2 px-4 rounded">Save Receipt</button>
                    </div>

            </div>
        </div> 
        </transition>

        
        <transition name="modal-fade" >
            <div v-if="showAddInvoiceModal" class="fixed ml-[100px] inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
                <div class="pop-in bg-white p-4 rounded-lg shadow-lg w-full max-w-6xl h-4/5 relative overflow-auto flex flex-col">
                <h3 style="font-size: 64px;" class="text-lg font-semibold text-center mt-12 mb-10">Add an Invoice</h3>

                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 1: Invoice and Customer Info</p>
                    </div>
                    <div class="px-12 mb-10">
                        
                        <!-- STEP 1: DIV FOR INVOICE AND CUSTOMER DETAILS -->
                        <form @submit.prevent="addInvoice" class="border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg w-full">
                        <!-- Step 1: Invoice and Customer Details -->
                            <div class="flex flex-col p-10">
                                <div class="grid grid-cols-3 gap-8 mb-4">
                                    <div>
                                        <label for="invoice_id" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Invoice I.D. No.</label>
                                        <input type="number" id="invoice_id" v-model="newInvoice.invoice_id" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    
                                    <div class="">
                                        <label for="date" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Date</label>
                                        <input type="date" id="date" v-model="newInvoice.date" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="">
                                        <label for="status" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Status</label>
                                        <select id="status" v-model="newInvoice.status" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm">
                                            <option value="paid">Paid</option>
                                            <option value="partially paid">Partially Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                            <option value="pending refund">Pending Refund</option>
                                            <option value="refunded">Refunded</option>

                                        </select>
                                    </div>
                                </div>
                                    
                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                        <label for="customer_Name" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Name</label>
                                        <input type="text" id="customer_Name" v-model="newInvoice.customer_Name" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Juan Dela Cruz"/>
                                    </div>
                                
                                    <div class="">
                                        <label for="payment_Type" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Payment Type</label>
                                        <select id="payment_Type" v-model="newInvoice.payment_Type" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm">
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="online transaction">Online Transaction</option>
                                        </select>
                                    </div>
                                

                                </div>


                                
                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                        <label for="customer_Address" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Address</label>
                                        <input type="text" id="customer_Address" v-model="newInvoice.customer_Address" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="Enter the Customer's Address (Optional)"/>
                                    </div>
                                    <div class="">
                                        <label for="customer_PO_No" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer P.O. Number</label>
                                        <input type="number" id="customer_PO_No" v-model="newInvoice.customer_PO_No" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    
                                </div>

                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                            <label for="customer_TIN" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer TIN Number</label>
                                            <input type="number" id="customer_TIN" v-model="newInvoice.customer_TIN" class="block w-full border-gray-300 rounded-bl-md rounded-r-mdd shadow-sm" placeholder="XXX-XXX-XXX-XXX-XXX"/>
                                    </div>
                                    <div class="">
                                        <label for="customer_OSCA_PWD_ID_No" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer OSCA/PWD I.D. No.</label>
                                        <input type="number" id="customer_OSCA_PWD_ID_No" v-model="newInvoice.customer_OSCA_PWD_ID_No" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="XXXX"/>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2  gap-8 mb-4">
                                    <div class="">
                                        <label for="terms" class=" w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Terms</label>
                                        <input type="text" id="terms" v-model="newInvoice.terms" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. 30 days"/>
                                    </div>

                                    <div>
                                        <label for="customer_Business_Style" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Business Style</label>
                                        <input type="text" id="customer_Business_Style" v-model="newInvoice.customer_Business_Style" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Juan Dela Cruz"/>
                                    </div>
                                
                                </div>

                                
                                <div class="">
                                    <label for="authorized_Representative" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Cashier/Authorized Representative</label>
                                    <input type="text" id="authorized_Representative" v-model="newInvoice.authorized_Representative" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Maria Clara Veronica"/>
                                </div>                                

                            </div>
                        </form>
                    </div>


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 2: Invoice Items</p>
                    </div>
                    <div class="px-12 mb-10">
                        
                        <form @submit.prevent="addInvoiceItem" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                            <div class="overflow-auto" style="max-height: 400px;"> <!-- Adjusted for scrolling -->
                                <table class="min-w-full bg-white border-gray-700">
                                    <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Product</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Total Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(field, index) in textItemFields" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">
                                            <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                                <div class="flex items-center justify-center">
                                                    <div class="flex flex-shrink-0 items-center">
                                                        <div v-if="field.image">
                                                            <img :src="'/storage/' + field.image" class="w-5 h-5 object-cover" />
                                                        </div>
                                                        <div v-else>
                                                            <font-awesome-icon :icon="['fas', 'image']" class="w-5 h-5 object-cover" size="sm" />
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <input class="w-44" @input="field.isSearching = true" type="text" v-model="field.searchProductQuery" placeholder="Search for a Product" />
                                                    </div>
                                                </div>
                                                <ul v-if="field.searchProductQuery && field.isSearching" class="absolute z-10 w-52 bg-white shadow-lg rounded-lg mt-1 max-h-60 overflow-y-auto">
                                                    <li @click="selectProduct(product, index)" v-for="product in filteredProducts(field.searchProductQuery)" :key="product.id">
                                                        <img :src="'/storage/' + product.image" alt="Product Image" class="w-5 h-5 object-cover" />
                                                        {{ product.image }} {{ product.name }} {{ product.price }}
                                                    </li>
                                                </ul>
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <div class="flex items-center justify-center">
                                                    <div class="-ml-4 flex items-center justify-between">
                                                        <span class="-ml-20 w-24 text-right text-xs text-gray-400">
                                                            {{ field.on_sale === 'yes' ? 'On' : 'Not' }}<br>
                                                            {{ field.on_sale === 'yes' ? 'Sale' : 'On Sale' }}
                                                        </span>
                                                        <label class="switch px-3">
                                                            <input :disabled="!field.areFieldsEnabled" type="checkbox" v-model="field.on_sale" true-value="yes" false-value="no" />
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                    <input disabled class="no-spinner w-32" type="number" v-model="field.amount" placeholder="Amount" />
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input :disabled="!field.areFieldsEnabled" class="text-center no-spinner w-16" type="number" @input="updateTotalProductAmount(index)" v-model="field.quantity" placeholder="Qty." />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input :disabled="!field.areFieldsEnabled" class="text-center no-spinner w-32" type="number" v-model="field.total_amount" placeholder="Total Amount" />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeItemTextField(index)">
                                                    <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                                </button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="5">
                                                <div class="flex items-center justify-center my-6">
                                                    <button type="button" @click="addItemTextField" class="flex items-center justify-center">
                                                        <font-awesome-icon :icon="['fas', 'plus']" class="w-6 h-6 mr-2" />
                                                        Click to add New Field
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
  
                    </div>


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 3: Invoice Additionals</p>
                    </div>
                    <div class="px-12 mb-10">

                        <form @submit.prevent="addInvoiceAdditional" class=" w-full border-4 border-black rounded-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                            <div class="overflow-auto" style="max-height: 400px;"> <!-- Adjusted for scrolling -->
                                <table class="min-w-full bg-white border-gray-700">
                                    <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Description</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Total Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(field, index) in textAdditionalFields" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">
                                            <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                                <input class="col-span-5 " type="text" v-model="field.addtl_Costs_Description" :placeholder="`Add a Description`" />
                                            </td>
                                            
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input class="text-center no-spinner w-32" type="number" @input="updateTotalAdditionalAmount(index)" v-model="field.aCD_amount" placeholder="Amount." />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input class="text-center no-spinner w-16" type="number" @input="updateTotalAdditionalAmount(index)" v-model="field.aCD_quantity" placeholder="Qty." />
                                            </td>
                                            
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input class="no-spinner w-32 col-span-5" type="number" v-model="field.aCD_Total_Amount" :placeholder="`Input Total Amount`" />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeAdditionalTextField(index)">
                                                    <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                                </button>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="5">
                                                <div class="flex items-center justify-center my-6">
                                                    <button type="button" @click="addAdditionalTextField" class="flex items-center justify-center">
                                                        <font-awesome-icon :icon="['fas', 'plus']" class="w-6 h-6 mr-2" />
                                                        Click to add New Field
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </form>

                    </div> 


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 4: Invoice Computation</p>
                    </div>
                    <div class="px-12 mb-10">

                        <form @submit.prevent="addInvoiceComputation" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg">
                            <div class="flex p-14 gap-8">
                                <div class="w-1/2">
                                    <div class="mt-2">---------------------------------------------------------</div>
                                    <div class="mt-2 mb-2 pl-10">
                                        <label for="total_Amount_Due" class="font-extrabold w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-md font-medium text-white">Total Amount Due</label>
                                        <input ref="totalAmountDue1" type="number" id="total_Amount_Due" @input="count=0, discountValue1=0;" v-model="totalAmountDueHolder" class="w-96 block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="mt-2 mb-2 pl-10">
                                        <label for="VAT_Inclusive" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VAT Inclusive</label>
                                        <input ref="VatInclusive" type="number" id="VAT_Inclusive" v-model="comp_VAT_Inclusive" class="w-96 block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-11">---------------------------------------------------------</div>

                                    <div class="mb-2">
                                        <div class="flex flex-row items-center">
                                            <label for="Less_SC_PWD_Discount" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Less SC PWD Discount</label>
                                            
                                        </div>
                                        <div class="grid grid-cols-5">
                                            <input type="number"
                                                ref="LessScPwdDiscount" 
                                                id="Less_SC_PWD_Discount" 
                                                v-model="newInvoiceComputation.Less_SC_PWD_Discount" 
                                                class="col-span-4 w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm pr-6" />
                                            <div class="relative col-span-1">
                                                <select 
                                                    ref="LessScPwdDiscountPercent"
                                                    v-model="newInvoiceComputation.Less_SC_PWD_Discount_Percent" 
                                                    class="text-center no-spinner col-span-1 w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm" >
                                                    <option @click="returnOriginalValue()" value="0">0%</option>
                                                    <option value="5">5%</option>
                                                    <option value="20">20%</option>
                                                </select>

                                                <!-- <span class="absolute inset-y-0 right-2 flex items-center text-gray-500">%</span> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label for="VAT_Exempt_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Vat Exempt Sales</label>
                                        <input ref="VatExemptSales" 
                                            type="number" 
                                            id="VAT_Exempt_Sales" 
                                            v-model="comp_VAT_Exempt_Sales" 
                                            class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="Zero_Rated_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Zero Rated Sales</label>
                                        <input ref="ZeroRatedSales" type="number" id="Zero_Rated_Sales" v-model="newInvoiceComputation.Zero_Rated_Sales" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>


                                </div>

                                <div class="w-1/2">
                                    <div class="mb-2">
                                        <label for="VAT_Amount" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VAT Amount</label>
                                        <input ref="VaTAmount" type="number" id="VAT_Amount" v-model="comp_VAT_Amount_Less_Add" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Less_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Less VAT</label>
                                        <input ref="LessVat" type="number" id="Less_VAT" v-model="comp_VAT_Amount_Less_Add" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Amount_NET_of_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount NET of VAT</label>
                                        <input ref="AmountNetOfVat" type="number" id="Amount_NET_of_VAT" v-model="comp_VATable_Sales_NET_of_VAT" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="VATable_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VATable Sales</label>
                                        <input ref="VatableSales" type="number" id="VATable_Sales" v-model="comp_VATable_Sales_NET_of_VAT" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Amount_Due" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount Due</label>
                                        <input ref="AmountDue" type="number" id="Amount_Due" v-model="comp_VAT_AmountDue" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Add_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Additional VAT</label>
                                        <input ref="AddVat" type="number" id="Add_VAT" v-model="comp_VAT_Amount_Less_Add" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="mb-2">
                                        <label for="tax" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Tax</label>
                                        <input ref="tax" type="number" id="tax" v-model="comp_Tax" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                </div>
                            </div>
                        </form>


                    </div>

                    <div class="flex justify-center mt-6 gap-6">
                                    <button @click="showAddInvoiceModal = false" type="button" class="bg-gray-500 text-white py-2 px-4 rounded">Cancel</button>
                                    <button @click.prevent="addInvoiceInformation()" class="bg-blue-500 text-white py-2 px-4 rounded">Save Receipt</button>
                    </div>

                </div>
            </div>
        </transition>


        <transition name="modal-fade" >
            <div v-if="showViewInvoiceModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50">
            <div class=" bg-white p-4 rounded-lg shadow-lg w-full max-w-7xl h-4/5 relative overflow-auto flex flex-col">
                <h3 style="font-size: 64px;" class="text-lg font-semibold text-center mt-12 mb-10">View this Invoice</h3>

                <div class="flex justify-center items-center">
                    <button @click="printInvoice()" class="w-44 bg-blue-500 text-white px-2 py-1 rounded-full">
                            <font-awesome-icon icon="fa-solid fa-print" size="sm"/>
                            <span class="text-lg"> Print Invoice</span>
                    </button>
                </div>


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 1: Invoice and Customer Info</p>
                    </div>
                    <div class="px-12 mb-10">
                        
                        <!-- STEP 1: DIV FOR INVOICE AND CUSTOMER DETAILS -->
                        <form @submit.prevent="addInvoice" class="border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg w-full">
                        <!-- Step 1: Invoice and Customer Details -->
                            <div class="flex flex-col p-10">
                                <div class="grid grid-cols-3 gap-8 mb-4">
                                    <div>
                                        <label for="invoice_id" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Invoice I.D. No.</label>
                                        <input disabled type="number" id="invoice_id" v-model="selectedInvoice.invoice_id" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    
                                    <div class="">
                                        <label for="date" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Date</label>
                                        <input disabled type="date" id="date" v-model="selectedInvoice.date" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="">
                                        <label for="status" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Status</label>
                                        <select disabled id="status" v-model="selectedInvoice.status" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm">
                                            <option value="paid">Paid</option>
                                            <option value="partially paid">Partially Paid</option>
                                            <option value="unpaid">Unpaid</option>
                                            <option value="pending refund">Pending Refund</option>
                                            <option value="refunded">Refunded</option>

                                        </select>
                                    </div>
                                </div>
                                    
                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                        <label for="customer_Name" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Name</label>
                                        <input disabled type="text" id="customer_Name" v-model="selectedInvoice.customer_Name" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Juan Dela Cruz"/>
                                    </div>
                                
                                    <div class="">
                                        <label for="payment_Type" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Payment Type</label>
                                        <select disabled id="payment_Type" v-model="selectedInvoice.payment_Type" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm">
                                            <option value="cash">Cash</option>
                                            <option value="check">Check</option>
                                            <option value="online transaction">Online Transaction</option>
                                        </select>
                                    </div>
                                

                                </div>


                                
                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="mb-4">
                                        <label for="customer_Address" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Address</label>
                                        <input disabled type="text" id="customer_Address" v-model="selectedInvoice.customer_Address" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="Enter the Customer's Address (Optional)"/>
                                    </div>
                                    <div class="">
                                        <label for="customer_PO_No" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer P.O. Number</label>
                                        <input disabled type="number" id="customer_PO_No" v-model="selectedInvoice.customer_PO_No" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    
                                </div>

                                <div class="grid grid-cols-2 gap-8 mb-4">
                                    <div class="">
                                            <label for="customer_TIN" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer TIN Number</label>
                                            <input disabled type="number" id="customer_TIN" v-model="selectedInvoice.customer_TIN" class="block w-full border-gray-300 rounded-bl-md rounded-r-mdd shadow-sm" placeholder="XXX-XXX-XXX-XXX-XXX"/>
                                    </div>
                                    <div class="">
                                        <label for="customer_OSCA_PWD_ID_No" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer OSCA/PWD I.D. No.</label>
                                        <input disabled type="number" id="customer_OSCA_PWD_ID_No" v-model="selectedInvoice.customer_OSCA_PWD_ID_No" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="XXXX"/>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2  gap-8 mb-4">
                                    <div class="">
                                        <label for="terms" class=" w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Terms</label>
                                        <input disabled type="text" id="terms" v-model="selectedInvoice.terms" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. 30 days"/>
                                    </div>

                                    <div>
                                        <label for="customer_Business_Style" class="w-56 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 inline-block text-sm font-medium text-white">Customer Business Style</label>
                                        <input disabled type="text" id="customer_Business_Style" v-model="selectedInvoice.customer_Business_Style" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Juan Dela Cruz"/>
                                    </div>
                                
                                </div>

                                
                                <div class="">
                                    <label for="authorized_Representative" class="pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Cashier/Authorized Representative</label>
                                    <input disabled type="text" id="authorized_Representative" v-model="selectedInvoice.authorized_Representative" class="block w-full border-gray-300 rounded-bl-md rounded-r-md shadow-sm" placeholder="e.g. Maria Clara Veronica"/>
                                </div>                                

                            </div>
                        </form>
                    </div>


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 2: Invoice Items</p>
                    </div>
                    <div class="px-12 mb-10">
                        
                        <form @submit.prevent="addInvoiceItem" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                            <div class="overflow-auto" style="max-height: 400px;"> <!-- Adjusted for scrolling -->
                                <table class="min-w-full bg-white border-gray-700">
                                    <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Product</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Stock</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Total Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(field, index) in selectedInvoiceItems" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">
                                            <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                                <div class="flex items-center justify-center">
                                                    <div class="flex flex-shrink-0 items-center">
                                                        <div v-if="field.image">
                                                            <img :src="'/storage/' + field.image" class="w-5 h-5 object-cover" />
                                                        </div>
                                                        <div v-else>
                                                            <font-awesome-icon :icon="['fas', 'image']" class="w-5 h-5 object-cover" size="sm" />
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <input disabled class="w-44" @input="field.isSearching = true" type="text" v-model="field.product_name" placeholder="Search for a Product" />
                                                    </div>
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <div class="flex items-center justify-center">
                                                    <div class="-ml-4 flex items-center justify-between">
                                                        <span class="-ml-20 w-24 text-right text-xs text-gray-400">
                                                            {{ field.on_sale === 'yes' ? 'On' : 'Not' }}<br>
                                                            {{ field.on_sale === 'yes' ? 'Sale' : 'On Sale' }}
                                                        </span>
                                                        <label class="switch px-3">
                                                            <input disabled :disabled="!field.areitemsEnabled" type="checkbox" v-model="field.on_sale" true-value="yes" false-value="no" />
                                                            <span class="slider round"></span>
                                                        </label>
                                                    </div>
                                                    <input disabled class="no-spinner w-32" type="number" v-model="field.product_price" placeholder="Amount" />
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input disabled :disabled="!field.areFieldEnabled" class="text-center no-spinner w-16" type="number" @input="updateTotalProductAmount(index), count=0" v-model="field.quantity" placeholder="Qty." />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input disabled :disabled="!field.areFieldEnabled" class="text-center no-spinner w-32" type="number" v-model="field.final_price" placeholder="Total Amount" />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeItemTextField(index)">
                                                    <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </form>
  
                    </div>


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 3: Invoice Additionals</p>
                    </div>
                    <div class="px-12 mb-10">

                        <form @submit.prevent="addInvoiceAdditional" class="w-full border-4 border-black rounded-lg shadow-lg overflow-hidden" style="max-height: 430px;">
                            <div class="overflow-auto" style="max-height: 400px;"> <!-- Adjusted for scrolling -->
                                <table class="min-w-full bg-white border-gray-700">
                                    <thead class="border-b rounded-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Description</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Quantity</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Total Amount</th>
                                            <th class="sticky top-0 px-6 py-3 text-white bg-gray-700">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(field, index) in selectedInvoiceAdditionals" :key="index" :class="index % 2 === 0 ? 'bg-blue-900 bg-opacity-5' : 'bg-white'" class="items-center text-center">
                                            <td class="px-6 py-3 border-b border-gray-200 dark:border-gray-400 align-middle">
                                                <input disabled class="col-span-5 " type="text" v-model="field.addtl_Costs_Description" :placeholder="`Add a Description`" />
                                            </td>
                                            
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input disabled class="text-center no-spinner w-32" type="number" @input="updateTotalAdditionalAmount(index), count=0" v-model="field.aCD_amount" placeholder="Amount." />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input disabled class="text-center no-spinner w-16" type="number" @input="updateTotalAdditionalAmount(index), count=0" v-model="field.aCD_quantity" placeholder="Qty." />
                                            </td>
                                            
                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <input disabled class="no-spinner w-32 col-span-5" type="number" v-model="field.aCD_Total_Amount" :placeholder="`Input Total Amount`" />
                                            </td>

                                            <td class="px-6 py-4 border-b border-gray-200 dark:border-gray-400">
                                                <button type="button" class="bg-red-500 text-white p-3 rounded-full" @click="removeAdditionalTextField(index), count=0">
                                                    <font-awesome-icon icon="fa-solid fa-trash-can" size="sm" />
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </form>


                    </div> 


                    <div class="px-12">
                        <p class="font-bold text-9x1 p-5 border inline-block rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 font-medium text-white">Step 4: Invoice Computation</p>
                    </div>
                    <div class="px-12 mb-10">

                        <form @submit.prevent="addInvoiceComputation" class="w-full border-4 border-black rounded-bl-lg rounded-r-lg shadow-lg">
                            <div class="flex p-14 gap-8">
                                <div class="w-1/2">
                                    <div class="mt-2">----------------------------------------------------------------------</div>
                                    <div class="mt-2 mb-2 pl-10">
                                        <label for="total_Amount_Due" class="font-extrabold w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-md font-medium text-white">Total Amount Due</label>
                                        <input disabled ref="totalAmountDue1" type="number" id="total_Amount_Due" @input="count=0, discountValue1=0;" v-model="selectedInvoiceComputation.total_Amount_Due" class="w-96 block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="mt-2 mb-2 pl-10">
                                        <label for="VAT_Inclusive" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VAT Inclusive</label>
                                        <input disabled ref="VatInclusive" type="number" id="VAT_Inclusive" v-model="selectedInvoiceComputation.VAT_Inclusive" class="w-96 block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-11">---------------------------------------------------------------------</div>

                                    <div class="mb-2">
                                        <div class="flex flex-row items-center">
                                            <label for="Less_SC_PWD_Discount" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Less SC PWD Discount</label>
                                           
                                        </div>
                                        <div class="grid grid-cols-5">
                                            <input disabled type="number"
                                                ref="LessScPwdDiscount" 
                                                id="Less_SC_PWD_Discount" 
                                                v-model="selectedInvoiceComputation.Less_SC_PWD_Discount" 
                                                class="col-span-4 w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm pr-6" />
                                            <div class="relative col-span-1">
                                                <select disabled
                                                    ref="LessScPwdDiscountPercent"
                                                    v-model="selectedInvoiceComputation.Less_SC_PWD_Discount_Percent" 
                                                    class="text-center no-spinner col-span-1 w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm" >
                                                    <option @click="returnOriginalValue()" value="0">0%</option>
                                                    <option value="5">5%</option>
                                                    <option value="20">20%</option>
                                                </select>

                                                <!-- <span class="absolute inset-y-0 right-2 flex items-center text-gray-500">%</span> -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <label for="VAT_Exempt_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Vat Exempt Sales</label>
                                        <input disabled ref="VatExemptSales" 
                                            type="number" 
                                            id="VAT_Exempt_Sales" 
                                            v-model="comp_VAT_Exempt_Sales" 
                                            class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="Zero_Rated_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Zero Rated Sales</label>
                                        <input disabled ref="ZeroRatedSales" type="number" id="Zero_Rated_Sales" v-model="selectedInvoiceComputation.Zero_Rated_Sales" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>


                                </div>

                                <div class="w-1/2">
                                    <div class="mb-2">
                                        <label for="VAT_Amount" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VAT Amount</label>
                                        <input disabled ref="VaTAmount" type="number" id="VAT_Amount" v-model="selectedInvoiceComputation.VAT_Amount" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Less_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Less VAT</label>
                                        <input disabled ref="LessVat" type="number" id="Less_VAT" v-model="selectedInvoiceComputation.Less_VAT" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Amount_NET_of_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount NET of VAT</label>
                                        <input disabled ref="AmountNetOfVat" type="number" id="Amount_NET_of_VAT" v-model="selectedInvoiceComputation.Amount_NET_of_VAT" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="VATable_Sales" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">VATable Sales</label>
                                        <input disabled ref="VatableSales" type="number" id="VATable_Sales" v-model="selectedInvoiceComputation.VATable_Sales" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Amount_Due" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Amount Due</label>
                                        <input disabled ref="AmountDue" type="number" id="Amount_Due" v-model="selectedInvoiceComputation.Amount_Due" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>
                                    <div class="mb-2">
                                        <label for="Add_VAT" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Additional VAT</label>
                                        <input disabled ref="AddVat" type="number" id="Add_VAT" v-model="selectedInvoiceComputation.Add_VAT" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                    <div class="mb-2">
                                        <label for="tax" class="w-44 pl-4 p-1 border rounded-t-lg border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 block text-sm font-medium text-white">Tax</label>
                                        <input disabled ref="tax" type="number" id="tax" v-model="selectedInvoiceComputation.tax" class="w-full block border-gray-300 rounded-bl-md rounded-r-md shadow-sm"/>
                                    </div>

                                </div>
                            </div>
                        </form>


                    </div>

                    <div class="flex justify-center mt-6 gap-14">
                                    <button @click="showViewInvoiceModal = false" type="button" class="bg-gray-500 text-white py-2 px-4 rounded">Cancel</button>
                    </div>

            </div>
        </div>
        </transition>




    </AuthenticatedLayout>
</template>


<style scoped>

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



@keyframes popIn {
    0% {
      transform: scale(0.75);
      opacity: 0;
    }
    100% {
      transform: scale(1);
      opacity: 1;
    }
  }
  .pop-in {
    animation: popIn 0.1s ease-out;
  }

  @keyframes popOut {
    100% {
      transform: scale(1);
      opacity: 1;
    }
    0% {
      transform: scale(0.75);
      opacity: 0;
    }
  }
  .pop-out {
    animation: popIn 0.1s ease-in;
  }







.pdf-container {
    background-color: #f9f9f9;
    overflow: auto;
}


.opacity-50 {
    opacity: 0.5;
}
.opacity-100 {
    opacity: 1;
}

.no-spinner::-webkit-outer-spin-button,
.no-spinner::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* For Firefox */
.no-spinner {
    -moz-appearance: textfield;
}


.pointer-cursor {
  cursor: pointer;
}

.row {
  display: flex;
  flex-direction: row; /* Align items in a row */
  justify-content: space-between; /* Optional, to add space between columns */
}

.column {
  flex: 1; /* Make columns equal width */
  margin: 0 10px; /* Optional, to add spacing between columns */
}



.form-container {
  display: inline-block; /* Let the form size naturally based on its content */
  padding: 10px;
  border: 1px solid #ccc; /* Optional: add a border around the form */
  border-radius: 5px;
}

/* Basic styling for form elements */
.field-row {
  display: flex;
  align-items: center;
  gap: 10px; /* Space between the inputs and the button */
  margin-bottom: 10px; /* Space between rows */
}


form {
  display: block;
  flex-direction: column;
  gap: 10px;
  width: 300px;
}

/* Style for the floating window */
.floating-window {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 1000;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  width: 400px; /* Adjust width as needed */
  padding: 20px;
}

/* Style for the table content */
.floating-content {
  max-height: 300px; /* You can set a max height for the content */
  overflow-y: auto; /* Scrollable content if it's too large */
}

/* Style for the close button */
.close-btn {
  background-color: red;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
  float: right;
}

/* Optional backdrop to dim the background */
.backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 999;
}



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

