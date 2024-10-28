<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Business extends Model
{
    use HasFactory;

    // Tell Eloquent that the primary key is 'business_id'
    protected $primaryKey = 'business_id';

    
    // Disable auto-increment if you are using a custom incrementing value
    public $incrementing = true;

    // Set the key type to 'bigint' since the business_id column is a big integer
    protected $keyType = 'int';

    // Allow mass assignment for these fields
    protected $fillable = [
        'business_id',
        'business_image',
        'user_id',
        'business_Name',
        'business_Email',
        'business_Province',
        'business_City',
        'business_Barangay',
        'business_Address',
        'business_Phone_Number',
        'business_TIN',
        'business_Telephone_Number',
        'business_Facebook',
        'business_X',
        'business_Instagram',
        'business_Tiktok'
    ];

    protected $with = ['user'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}


    


