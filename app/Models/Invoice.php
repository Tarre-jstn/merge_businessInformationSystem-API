<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    
    
    protected $primaryKey = 'invoice_system_id'; // Set the primary key column name
    public $incrementing = true; // Set to true if the primary key is auto-incrementing
    protected $keyType = 'int'; // Set the key type (string, integer, etc.) based on your primary key column type

    
    protected $fillable = [
        //FOR BUSINESS INFORMATION
        'business_id',
        'business_Name',
        'business_Address',
        'business_TIN',
        
        //FOR INVOICE INFORMATION 
        'invoice_id',
        'date',
        'terms',
        'status',
        'authorized_Representative',
        'payment_Type',


        //FOR CUSTOMER INFO IN INVOICE
        'customer_Name',
        'customer_Address',
        'customer_TIN',
        'customer_Business_Style',
        'customer_PO_No',
        'customer_OSCA_PWD_ID_No',

        //FOR STORING VAT, DISCOUNTS, ETC. (WITH COMPUTATIONS)
        'VATable_Sales',
        'VAT_Exempt_Sales',
        'Zero_Rated_Sales',
        'VAT_Amount',

        'VAT_Inclusive',
        'Less_VAT',
        'Amount_NET_of_VAT',
        'Less_SC_PWD_Discount',
        'Amount_Due',
        'Add_VAT',
        
        //FOR STORING TOTAL AMOUNT DUE (FINAL COMPUTATION)
        'tax',
        'total_Amount_Due',

        'timestamps'
    
    ];
    
    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id', 'business_id');
    }
    
}
