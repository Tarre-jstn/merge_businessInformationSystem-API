<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Inventory;
use App\Models\Business;
use App\Models\Product;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;



class InvoiceItemController extends Controller
{
    public function index(){
        return InvoiceItem::all();
    }


    public function show($invoice_system_id)
    {
        $invoice = InvoiceItem::where('invoice_system_id', $invoice_system_id)->get();
    
        if (!$invoice) {
            return response()->json(['error' => 'Invoice Items not found'], 404);
        }
    
        return response()->json($invoice);
    }

    public function store(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'invoice_system_id' => 'nullable|exists:invoices,invoice_system_id',

                'product_id' => 'nullable|exists:products,id',
                'product_name' => 'nullable|string|max:255',
                'product_price' => 'nullable|numeric',
            
                'on_sale' => 'nullable|in:yes,no',
                'on_sale_price' => 'nullable' ,

                'seniorPWD_discountable' => 'nullable|in:yes,no',

                'final price' => 'nullable|numeric',

                'quantity' => 'nullable|numeric',
                'amount' => 'nullable|numeric'
            ]);
            

            $invoice_item = InvoiceItem::create([
                'invoice_system_id' => $request->invoice_system_id,
                
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'product_price' => $request->product_price,

                'on_sale' => $request->on_sale,
                'on_sale_price' => $request->on_sale_price,
                'seniorPWD_discountable' => $request->seniorPWD_discountable,
                    'final_price' => $request->final_price,

                'quantity' => $request->quantity,
                'amount' => $request->amount,

            ]);




        }catch(Exception $e){
            return response()->json(data:['error in generating invoice items' => $e->getMessage()], status: 500);
        }
    }

    public function updateInvoiceItems(Request $request, $invoice_system_id) {
        // Find all invoice items for the given invoice_system_id

        try{
            $validatedData = $request->validate([
                'invoice_system_id' => 'nullable|exists:invoices,invoice_system_id',

                'product_id' => 'nullable|exists:products,id',
                'product_name' => 'nullable|string|max:255',
                'product_price' => 'nullable|numeric',
            
                'on_sale' => 'nullable|in:yes,no',
                'on_sale_price' => 'nullable' ,

                'seniorPWD_discountable' => 'nullable|in:yes,no',

                'final price' => 'nullable|numeric',

                'quantity' => 'nullable|numeric',
                'amount' => 'nullable|numeric'
            ]);
            

            $invoice_item = InvoiceItem::create([
                'invoice_system_id' => $request->invoice_system_id,
                
                'product_id' => $request->product_id,
                'product_name' => $request->product_name,
                'product_price' => $request->product_price,

                'on_sale' => $request->on_sale,
                'on_sale_price' => $request->on_sale_price,
                'seniorPWD_discountable' => $request->seniorPWD_discountable,
                    'final_price' => $request->final_price,

                'quantity' => $request->quantity,
                'amount' => $request->amount,

            ]);
            
        }catch(Exception $e){
            return response()->json(data:['error in generating invoice items' => $e->getMessage()], status: 500);
        }
    }
    
    // public function updateInvoiceItems(Request $request, $invoice_system_id) {
    //     // Find all invoice items for the given invoice_system_id

    //     try{
    //         $validatedData = $request->validate([
    //             'invoice_system_id' => 'nullable|exists:invoices,invoice_system_id',

    //             'product_id' => 'nullable|exists:products,id',
    //             'product_name' => 'nullable|string|max:255',
    //             'product_price' => 'nullable|numeric',
            
    //             'on_sale' => 'nullable|in:yes,no',
    //             'on_sale_price' => 'nullable' ,

    //             'seniorPWD_discountable' => 'nullable|in:yes,no',

    //             'final price' => 'nullable|numeric',

    //             'quantity' => 'nullable|numeric',
    //             'amount' => 'nullable|numeric'
    //         ]);
            

    //         $invoice_item = InvoiceItem::create([
    //             'invoice_system_id' => $request->invoice_system_id,
                
    //             'product_id' => $request->product_id,
    //             'product_name' => $request->product_name,
    //             'product_price' => $request->product_price,

    //             'on_sale' => $request->on_sale,
    //             'on_sale_price' => $request->on_sale_price,
    //             'seniorPWD_discountable' => $request->seniorPWD_discountable,
    //                 'final_price' => $request->final_price,

    //             'quantity' => $request->quantity,
    //             'amount' => $request->amount,

    //         ]);
    //     }catch(Exception $e){
    //     }
    // }
    
    
    // public function updateInvoiceItems(Request $request, $invoice_system_id) {
    //     try {
    //         // Validate request data
    //         $validatedData = $request->validate([
    //             'invoice_system_id' => 'required|exists:invoices,invoice_system_id',
    //             'product_id' => 'required|exists:products,id',
    //             'product_name' => 'required|string|max:255',
    //             'product_price' => 'required|numeric',
    //             'on_sale' => 'nullable|in:yes,no',
    //             'on_sale_price' => 'nullable|numeric',
    //             'seniorPWD_discountable' => 'nullable|in:yes,no',
    //             'final_price' => 'required|numeric',
    //             'quantity' => 'required|integer|min:1',
    //             'amount' => 'required|numeric'
    //         ]);
    
    //         // Retrieve existing item or create a new one if it doesn’t exist
    //         $invoiceItem = InvoiceItem::updateOrCreate(
    //             [
    //                 'invoice_system_id' => $invoice_system_id,
    //                 'product_id' => $request->product_id
    //             ],
    //             [
    //                 'product_name' => $request->product_name,
    //                 'product_price' => $request->product_price,
    //                 'on_sale' => $request->on_sale,
    //                 'on_sale_price' => $request->on_sale_price,
    //                 'seniorPWD_discountable' => $request->seniorPWD_discountable,
    //                 'final_price' => $request->final_price,
    //                 'quantity' => $request->quantity,
    //                 'amount' => $request->amount,
    //             ]
    //         );
    
    //         // Adjust stock based on the new quantity
    //         $product = Product::find($request->product_id);
    //         $newStock = max(0, $product->stock - $request->quantity);
    //         $product->update(['stock' => $newStock]);
    
    //         return response()->json(['success' => 'Invoice item updated successfully'], 200);
            
    //     } catch (Exception $e) {
    //         return response()->json(['error' => 'Error updating invoice items: ' . $e->getMessage()], 500);
    //     }
    // }


    public function deleteInvoiceItems(Request $request, $invoice_system_id){
        InvoiceItem::where('invoice_system_id', $invoice_system_id)->delete();
        return response()->json(['message' => 'Invoice deleted successfully']);
    }

}

    








        // Validate the request data
        // $request->validate([
        //     'invoice_items.*.product_id' => 'nullable|exists:products,id',
        //     'invoice_items.*.product_name' => 'nullable|string|max:255',
        //     'invoice_items.*.product_price' => 'nullable|numeric',
        //     'invoice_items.*.on_sale' => 'nullable|in:yes,no',
        //     'invoice_items.*.on_sale_price' => 'nullable|numeric',
        //     'invoice_items.*.seniorPWD_discountable' => 'nullable|in:yes,no',
        //     'invoice_items.*.final_price' => 'nullable|numeric',
        //     'invoice_items.*.quantity' => 'nullable|numeric',
        //     'invoice_items.*.amount' => 'nullable|numeric'
        // ]);
    
        // // Loop through the invoice items and update them
        // foreach ($invoiceItems as $item) {
        //     $itemData = $request->input('invoice_items')[$item->id] ?? [];
        //     $item->update($itemData);
        // }