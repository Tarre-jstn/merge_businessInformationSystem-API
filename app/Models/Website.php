<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'product_id',
        'website_description',
        'website_details',
        'website_image',
        'about_us1',
        'about_us2',
        'about_us3'
        
    ];

}
