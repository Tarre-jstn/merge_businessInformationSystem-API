<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;



class ProductNotificationSettings extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        //FOR FINANCE CATEGORY
        'stock_expDate',
        'count',
    ];

}
