<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Business;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;



class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::all();
    }


public function show($invoice_id)
{
    $invoice = Invoice::where('invoice_id', $invoice_id)->first();

    if (!$invoice) {
        return response()->json(['error' => 'Invoice not found'], 404);
    }

    return response()->json($invoice);
}

    public function store(Request $request)
    {
        try{
            $request->validate([

                //FOR BUSINESS INFORMATION
                    'business_id' => 'nullable|exists:businesses,business_id',

                    'business_Name' => 'required|string|max:255',
                    'business_Address' => 'required|string|max:255',
                    'business_TIN' => 'required|numeric',
        
                //FOR INVOICE
                    'invoice_id' => 'required|integer|unique:invoices,invoice_id',
                    'date' => 'required|date',
                    'terms' => 'required|string|max:255',
                    'status' => 'required|string|max:255',
                    'authorized_Representative' => 'required|string|max:255',
                    'payment_Type' => 'required|string|max:255', 
        
                //FOR CUSTOMER INFO IN INVOICE
                    'customer_Name' => 'required|string|max:255',
                    'customer_Address' => 'required|string|max:255',
                    'customer_TIN' => 'required|integer',
                    'customer_Business_Style' => 'required|string|max:255',
                    'customer_PO_No' => 'required|integer',
                    'customer_OSCA_PWD_ID_No' => 'required|integer',
        
                //FOR STORING VAT, DISCOUNTS, ETC. (WITH COMPUTATIONS)
                    'VATable_Sales' => 'required|numeric',
                    'VAT_Exempt_Sales' => 'required|numeric',
                    'Zero_Rated_Sales' => 'required|numeric',
                    'VAT_Amount' => 'required|numeric',
            
                //FOR STORING TOTAL AMOUNT DUE (FINAL COMPUTATION)
                    'VAT_Inclusive' => 'required|numeric',
                    'Less_VAT' => 'required|numeric',
                    'Amount_NET_of_VAT' => 'required|numeric',
                    'Less_SC_PWD_Discount' => 'required|numeric',
                    'Amount_Due' => 'required|numeric',
                    'Add_VAT' => 'required|numeric',
        
                    'tax' => 'required|numeric',
                    'total_Amount_Due' => 'required|numeric'
        
                ]);
                
                
                $businessId = $request->input('business_id');
                $business = Business::where('business_id', $businessId)->first();
        
                if (!$business) {
                    // Use the first available business record if the provided one is invalid
                    $business = Business::first();
                    if (!$business) {
                        return response()->json(['error' => 'No business record found'], 404);
                    }
                }
                // $business = Business::where('business_id', $request->business_id)->first(); //CHECKS IF THERE'S A BUSINESS ID
                
                if($business){
                        $invoice = Invoice::create([
                        'business_id' => $business->business_id,
                        'business_Name' => $business->business_Name,  // Use $business directly
                        'business_Address' => $business->business_Address,
                        'business_TIN' => $business->business_TIN,

                        'invoice_id' => $request->invoice_id,
                        'date' => $request->date,
                        'terms' => $request->terms,
                        'status' => $request->status,
                        'authorized_Representative' => $request->authorized_Representative,
                        'payment_Type' => $request->payment_Type,

                        'customer_Name' => $request->customer_Name,
                        'customer_Address' => $request->customer_Address,
                        'customer_TIN' => $request->customer_TIN,
                        'customer_Business_Style' => $request->customer_Business_Style,
                        'customer_PO_No' => $request->customer_PO_No,
                        'customer_OSCA_PWD_ID_No' => $request->customer_OSCA_PWD_ID_No,


                        'VATable_Sales' => $request->VATable_Sales,
                        'VAT_Exempt_Sales' => $request->VAT_Exempt_Sales,
                        'Zero_Rated_Sales' => $request->Zero_Rated_Sales,
                        'VAT_Amount' => $request->VAT_Amount,

                        'VAT_Inclusive' => $request->VAT_Inclusive,
                        'Less_VAT' => $request->Less_VAT,
                        'Amount_NET_of_VAT' => $request->Amount_NET_of_VAT,
                        'Less_SC_PWD_Discount' => $request->Less_SC_PWD_Discount,
                        'Amount_Due' => $request->Amount_Due,
                        'Add_VAT' => $request->Add_VAT,

                        'tax' => $request->tax,
                        'total_Amount_Due' => $request->total_Amount_Due,

                ]);
            }else {
                return response()->json(['error' => 'Business not found'], 404);
            }
        }
        catch(Exception $e){
            return response()->json(['error in generating invoice' => $e->getMessage()], 500);
        }
    }




    public function update(Request $request, $invoice_system_id)
    {
        try {
            $invoice = Invoice::where('invoice_system_id', $invoice_system_id)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }
    
            $validated = $request->validate([
                'invoice_id' => 'nullable|unique:invoices,invoice_id',
                'date' => 'required|date',
                'terms' => 'required|string|max:255',
                'status' => 'required|string|max:255',
                'authorized_Representative' => 'required|string|max:255',
                'payment_Type' => 'required|string|max:255',
                'customer_Name' => 'required|string|max:255',
                'customer_Address' => 'required|string|max:255',
                'customer_TIN' => 'required|integer',
                'customer_Business_Style' => 'required|string|max:255',
                'customer_PO_No' => 'required|integer',
                'customer_OSCA_PWD_ID_No' => 'required|integer',
                'VATable_Sales' => 'required|numeric',
                'VAT_Exempt_Sales' => 'required|numeric',
                'Zero_Rated_Sales' => 'required|numeric',
                'VAT_Amount' => 'required|numeric',
                'VAT_Inclusive' => 'required|numeric',
                'Less_VAT' => 'required|numeric',
                'Amount_NET_of_VAT' => 'required|numeric',
                'Less_SC_PWD_Discount' => 'required|numeric',
                'Amount_Due' => 'required|numeric',
                'Add_VAT' => 'required|numeric',
                'tax' => 'required|numeric',
                'total_Amount_Due' => 'required|numeric'
            ]);
    
            $invoice->update($validated);
    
            return response()->json($invoice);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unable to update invoice: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($invoice_system_id)
    {
        try {
            // Find the invoice by invoice_system_id
            $invoice = Invoice::where('invoice_system_id', $invoice_system_id)->first();
            
            if (!$invoice) {
                return response()->json(['error' => 'Invoice not found'], 404);
            }
    
            // Delete the invoice
            $invoice->delete();
    
            return response()->json(['message' => 'Invoice deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unable to delete invoice: ' . $e->getMessage()], 500);
        }
    }
}

