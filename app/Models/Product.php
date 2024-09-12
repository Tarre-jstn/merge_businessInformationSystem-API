<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'price',
        'category',
        'stock',
        'sold',
        'status',
        'description',
        'expDate',
        'image',
        'seniorPWD_discountable',
        'on_sale',
        'on_sale_price',
        'featured',
    ];
}
