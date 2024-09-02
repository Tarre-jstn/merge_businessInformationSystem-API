<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;



class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try{
    $request->validate([
        'business_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'user_id' => 'required|numeric|exists:users,id',
        'business_Name' => 'required|string|max:255',
        'business_Address' => 'required|string|max:255',
        'business_Contact_Number' => 'required|string|max:255',
        'business_Email'=> 'required|string|lowercase|email|max:255|unique:businesses,business_Email',
        'business_SocialMedia'=>'required|string|max:255'
    ]);

    $business_image=null;
    if ($request->hasFile('business_image')) {
        $image = $request->file('business_image');
        $path = $image->store('public/business_logos');
        $business_image = basename($path);
    }

    if($request->user_id){
        $user = User::find($request->user_id);
        if($user->user_type=='owner'){
            $business = Business::create([
            'business_image' => $business_image,
            'user_id' => $request->user_id,
            'business_Name' => $request->business_Name,
            'business_Address' => $request->business_Address,
            'business_Contact_Number' => $request->business_Contact_Number,
            'business_Email'=> $request->business_Email,
            'business_SocialMedia'=>$request->business_SocialMedia
    ]);
}
    }else{
        throw new \Exception('The selected user must be an owner.');
    }
    }catch(Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



public function showBusiness(Request $request){
    $user_id = $request->query('user_id');
    $business = Business::where('user_id', $user_id)->first();
    if (!$business) {
        return response()->json(['error' => 'Business record not found'], 404);
    }
    return response()->json($business);
}

    /**
     * Display the specified resource.
     */
    // public function show(Request $request, Business $business_info)
    // {
    //     return $business_info->paginate(10);
        
    // }

    // public function businessName(Request $request){
    //     $user = User::find($request->user_id);
    //     if($user->user_type='owner'){
    //         return $request->business_Name;
    //     }

    // }
    /**
     * Update the specified resource in storage.
     */
//     public function update(int $id, Request $request)
// {
//     $request->validate([
//         'businesslogo' => 'nullable|string|max:255',
//         'businessname' => 'string|max:255',
//         'businessemail' => 'string|max:255',
//         'businessphone' => 'integer',
//         'businesstelephone' => 'integer',
//         'businessfb' => 'string|max:255',
//         'businessig' => 'string|max:255',
//         'businessx' => 'string|max:255',
//     ]);

//     $business_info = Business::findOrFail($id);
//     $data = $request->all();

//     if ($request->hasFile('businesslogo')) {
//         // Delete the old image if it exists
//         if ($business_info->businesslogo) {
//             Storage::delete('public/business_logos/' . $business_info->businesslogo);
//         }
//         $image = $request->file('businesslogo');
//         $path = $image->store('public/business_logos');
//         $data['businesslogo'] = basename($path);
//     }

//     if (isset($data['businessname'])) {
//         $business_info->businessname = $data['businessname'];
//     }
//     if (isset($data['businessemail'])) {
//         $business_info->businessemail = $data['businessemail'];
//     }
//     if (isset($data['businessphone'])) {
//         $business_info->businessphone = $data['businessphone'];
//     }
//     if (isset($data['businesstelephone'])) {
//         $business_info->businesstelephone = $data['businesstelephone'];
//     }
//     if (isset($data['businessfb'])) {
//         $business_info->businessfb = $data['businessfb'];
//     }
//     if (isset($data['businessig'])) {
//         $business_info->businessig = $data['businessig'];
//     }
//     if (isset($data['businessx'])) {
//         $business_info->businessx = $data['businessx'];
//     }

//     return [
//         'success' => (bool) $business_info->save()
//     ];
// }

//     /**
//      * Remove the specified resource from storage.
//      */
//     public function destroy(int $id, Business $business_info)
//     {
//         return [
//             'success' => (bool) $business_info -> where('id', $id)->delete()
//         ];
//     }
}
