<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Website;

class WebsiteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Website::create([
            'business_id'=>1,
            'website_description'=> null,
            'website_details'=> null,
            'website_image'=> null,
            'about_us1'=> null,
            'about_us2'=> null,
            'about_us3'=> null,
            'website_footNote'=> null,
            'featured_section'=> true,
            'onSale_section'=> true
        ]);
    }
}
