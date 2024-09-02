<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        try{
            Log::info('Store method is being called.');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'user_type'=> 'nullable|in:customer,owner'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'contact_number' => $request->contact_number,
            'user_type'=> $request->user_type
        ]);
        


        event(new Registered($user));

        Auth::login($user);


        return redirect(route('dashboard', absolute: false));
    }catch (\Exception $e) {
            Log::error('Registration Error: ', ['error' => $e->getMessage()]);
            return back()->withErrors('Registration failed, please try again.');
        }
    }
}
