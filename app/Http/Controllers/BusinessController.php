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
    $oldData = $business->toArray(); // Store the original data
    $oldName = $business->business_Name;
    $oldImage = $business->business_image; // Store the old image path
    
    // Collect changes to track
    $changes = [];

    // If a new image is uploaded
    if ($request->hasFile('business_image')) {
        $image = $request->file('business_image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
    
        // Move the old image to the archive folder before uploading the new one
        if ($oldImage) {
            $oldImagePath = 'public/business_logos/' . $oldImage;
            if (Storage::exists($oldImagePath)) {
                // Move old image to archive folder
                Storage::move($oldImagePath, 'public/business_logos/archive/' . $oldImage);
            }
        }
    
        // Store the new image in the business_logos folder
        $image->storeAs('public/business_logos', $imageName);
    
        // Update business with the new image name
        $business->business_image = $imageName;
        $changes['business_image'] = ['old' => $oldImage, 'new' => $imageName];
    }
    
    
    // Compare and track other changes
    if ($oldName !== $request->input('business_Name')) {
        $changes['business_Name'] = ['old' => $oldName, 'new' => $request->input('business_Name')];
    }

    $oldAddress = "{$oldData['business_Address']}, {$oldData['business_Barangay']}, {$oldData['business_City']}, {$oldData['business_Province']}";
    $newAddress = "{$business->business_Address}, {$business->business_Barangay}, {$business->business_City}, {$business->business_Province}";
    
    if ($oldAddress !== $newAddress) {
        $changes['business_Address'] = ['old' => $oldAddress, 'new' => $newAddress];
    }

    // Check for other field changes like phone number, email, etc.
    if ($oldData['business_Phone_Number'] !== $business->business_Phone_Number) {
        $changes['business_Phone_Number'] = ['old' => $oldData['business_Phone_Number'], 'new' => $business->business_Phone_Number];
    }
    if ($oldData['business_Telephone_Number'] !== $business->business_Telephone_Number) {
        $changes['business_Telephone_Number'] = ['old' => $oldData['business_Telephone_Number'], 'new' => $business->business_Telephone_Number];
    }
    if ($oldData['business_Email'] !== $business->business_Email) {
        $changes['business_Email'] = ['old' => $oldData['business_Email'], 'new' => $business->business_Email];
    }
    if ($oldData['business_Facebook'] !== $business->business_Facebook) {
        $changes['business_Facebook'] = ['old' => $oldData['business_Facebook'], 'new' => $business->business_Facebook];
    }
    if ($oldData['business_X'] !== $business->business_X) {
        $changes['business_X'] = ['old' => $oldData['business_X'], 'new' => $business->business_X];
    }
    if ($oldData['business_Instagram'] !== $business->business_Instagram) {
        $changes['business_Instagram'] = ['old' => $oldData['business_Instagram'], 'new' => $business->business_Instagram];
    }
    if ($oldData['business_Tiktok'] !== $business->business_Tiktok) {
        $changes['business_Tiktok'] = ['old' => $oldData['business_Tiktok'], 'new' => $business->business_Tiktok];
    }
    
    if (!empty($changes)) {
        // Trigger the event, now the old image is safely stored in the archive folder
        event(new BusinessNameUpdated($oldName, $business->business_Name, $changes));
    }

    // Save the updated business profile
    $business->save();


    return response()->json(['success' => true]);
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
