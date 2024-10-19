<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Business;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class GetIdController extends Controller
{
    public function getUserId(Request $request)
    {
        if (Auth::check()) {
            return response()->json(['user_id' => Auth::id()]);
        } else {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
    }

    public function checkUserAuth(Request $request){

        $user = User::where('id', Auth::id())->first();
            if (!$user) {
                return response()->json(['error' => 'User record not found'], 404);
            }
            return response()->json($user);
        
        
    }

    public function getBusinessId(Request $request)
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            Log::info("User id= ".$user_id);
            $business = Business::where('user_id', $user_id)->first();
            Log::info("Business id= ". $business);
            if(!$business){
                return response()->json(['error' => 'Business record not found'], 404);
            }
            return response()->json(['business_id'=>$business->business_id]);
        } else {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
    }
}
