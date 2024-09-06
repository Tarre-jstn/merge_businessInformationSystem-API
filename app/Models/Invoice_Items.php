<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice_Items extends Model
{
    use HasFactory;

    protected $fillable = [
        //'business_id','business_Address','business_TIN',
        
        'invoice_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'amount',
        'addtl_Costs_Description',
        'aCD_price'


    ];
}
