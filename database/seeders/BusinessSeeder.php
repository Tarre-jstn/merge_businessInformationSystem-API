<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Business;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Business::create([
            'business_id'=> 1,
            'business_image'=> null,
            'user_id'=> 1,
            'business_Name'=> 'Sample Business Name',
            'business_Email'=> 'sample@email.com',
            'business_Province'=> 'Bulacan',
            'business_City'=> 'City of Malolos',
            'business_Barangay'=> 'Mojon',
            'business_Address'=> 'Sample Address',
            'business_Phone_Number'=> '09123456789',
            'business_TIN'=> '123123123',
            'business_Telephone_Number'=> '1239942',
            'business_Facebook'=> null,
            'business_X'=> null,
            'business_Instagram'=> null,
            'business_Tiktok'=> null
        ]);
    }
}
