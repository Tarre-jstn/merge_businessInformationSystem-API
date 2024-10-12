<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Events\BusinessNameUpdated;


class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $Business = Business::where('user_id', auth()->id())->first(); 
    return response()->json($Business);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    try {
        $request->validate([
            'business_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_Name' => 'required|string|max:255',
            'business_Email' => 'required|string|lowercase|email|max:255',
            'business_Province' => 'required|string|max:255',
            'business_City' => 'required|string|max:255',
            'business_Barangay' => 'required|string|max:255',
            'business_Address' => 'required|string|max:255',
            'business_Phone_Number' => 'required|string|max:255',
            'business_Telephone_Number' => 'required|string|max:255',
            'business_Facebook' => 'nullable|string|max:255',
            'business_X' => 'nullable|string|max:255',
            'business_Instagram' => 'nullable|string|max:255',
            'business_Tiktok' => 'nullable|string|max:255'
        ]);

        $business_image = null; // Initialize the variable

        if ($request->hasFile('business_image')) {
            $image = $request->file('business_image');
            // Store the image in the 'public/business_logos' directory
            $path = $image->store('public/business_logos');
            // Get the basename of the stored file path
            $business_image = basename($path);
        }

        // Ensure the user is of type 'owner' before creating the business
        if ($request->user_id) {
            $user = User::find($request->user_id);
            if ($user->user_type == 'owner') {
                $Business = Business::create([
                    'business_image' => $business_image,  // Save the image path here
                    'business_Name' => $request->business_Name,
                    'business_Email' => $request->business_Email,
                    'business_Province' => $request->business_Province,
                    'business_City' => $request->business_City,
                    'business_Barangay' => $request->business_Barangay,
                    'business_Address' => $request->business_Address,
                    'business_Phone_Number' => $request->business_Phone_Number,
                    'business_Telephone_Number' => $request->business_Telephone_Number,
                    'business_Facebook' => $request->business_Facebook,
                    'business_X' => $request->business_X,
                    'business_Instagram' => $request->business_Instagram,
                    'business_Tiktok' => $request->business_Tiktok
                ]);

                return response()->json(['success' => true, 'business' => $Business], 201);
            }
        } else {
            throw new \Exception('The selected user must be an owner.');
        }
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Business $Business)
    {
        return $Business->all();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(int $id, Request $request, Business $Business)
    {
        $request->validate([
            'business_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'business_Name' => 'required|string|max:255',
            'business_Email'=> 'required|string|lowercase|email|max:255', 
            'business_Province' => 'required|string|max:255',
            'business_City' => 'required|string|max:255',
            'business_Barangay' => 'required|string|max:255',
            'business_Address' => 'required|string|max:255',
            'business_Phone_Number' => 'required|string|max:255',
            'business_Telephone_Number' => 'required|string|max:255',
            'business_Facebook'=>'nullable|string|max:255',
            'business_X'=>'nullable|string|max:255',
            'business_Instagram'=>'nullable|string|max:255',
            'business_Tiktok'=>'nullable|string|max:255'
        ]);

        $business = Business::findOrFail($id);
        $oldName = $business->business_Name;

        // Update business details here
        $business->update($request->all());

        // If the business name has changed, trigger the event
        if ($oldName !== $request->input('business_Name')) {
            event(new BusinessNameUpdated($oldName, $business->business_Name));
        }

        if ($request->hasFile('business_image')) {
            // Handle the file upload
            $image = $request->file('business_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // Save the image to storage/app/public/business_logos
            $image->storeAs('public/business_logos', $imageName);
    
            // Save the image name in the database (just the filename, not the full path)
            $business->business_image = $imageName;
        }

        // Update other business fields
        $business->business_Name = $request->input('business_Name');
        $business->business_Email = $request->input('business_Email');
        $business->business_Province = $request->input('business_Province');
        $business->business_City = $request->input('business_City');
        $business->business_Barangay = $request->input('business_Barangay');
        $business->business_Address = $request->input('business_Address');
        $business->business_Phone_Number = $request->input('business_Phone_Number');
        $business->business_Telephone_Number = $request->input('business_Telephone_Number');
        $business->business_Facebook = $request->input('business_Facebook');
        $business->business_X = $request->input('business_X');
        $business->business_Instagram = $request->input('business_Instagram');
        $business->business_Tiktok = $request->input('business_Tiktok');

        // Save the business profile
        $business->save();

        return response()->json(data: ['success' => true]);
    }

    


    



    /**
     * Remove the specified resource from storage.
     */
        public function destroy(int $id)
    {
        $business = Business::findOrFail($id);
        return [
            'success' => (bool) $business->delete()
        ];
    }



}
