<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Website;
use Exception;

class WebsiteController extends Controller
{
    public function index()
    {
        Website::all();
    }
    
    public function store (Request $request){
        try{
            $request->validate([
                'website_description'=>'nullable|string|max:255',
                'website_details' => 'nullable|string|max:255',
                'website_image'=>'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'about_us1'=>'nullable|string|max:255',
                'about_us2'=>'nullable|string|max:255',
                'about_us3'=>'nullable|string|max:255'
            ]);
    
            $website=Website::create($request->all());
            return response()->json($website, 201);
            }
            catch(Exception $e){
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    public function info(Request $request){
        $business_id = $request->query('business_id');
        $website = Website::where('business_id', $business_id)->first();
        return response()->json($website);
    }
}
