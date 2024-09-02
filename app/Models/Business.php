<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_image',
        'user_id',
        'business_Name',
        'business_Address',
        'business_Contact_Number',
        'business_Email',
        'business_SocialMedia'
    ];

    

}
