<?php

namespace App\Http\Controllers;

use App\Models\UserData; // Import UserData model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\City;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure only authenticated users can access the profile
    }

    // Display the user profile
    public function show()
    {
        $userData = Auth::user()->userData;  // Get user's profile
        $city = $userData->city;  // Get the associated city name

        return view('profile.show', compact('userData', 'city'));
    }

    // Update the user profile (phone, city, profile picture, customer_type, etc.)
    public function update(Request $request)
{
    // Validate the form data, including the profile picture
    $request->validate([
        'phone' => 'nullable|string|max:255',
        'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Allow only specific image types and a max size of 2MB
    ]);

    try {
        // Get the authenticated user's profile data
        $userData = Auth::user()->userData;

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Validate the image before processing
            if ($userData->profile_picture && Storage::exists('public/' . $userData->profile_picture)) {
                // Delete the old profile picture
                Storage::delete('public/' . $userData->profile_picture);
            }

            // Store the new profile picture
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData->profile_picture = $imagePath;
        }

        // Update other profile details
        $userData->phone = $request->input('phone');
        $userData->save(); // Save the updated user data

        return redirect()->route('profile.show')->with('success', 'اطلاعات با موفقیت ویرایش شد');
    } catch (\Exception $e) {
        // Redirect back with an error message if something goes wrong
        return redirect()->back()->withErrors(['profile_picture' => 'خطایی در ویرایش اطلاعات رخ داده است. لطفا مجددا تلاش نمایید']);
    }
}



    // Update the password
    public function updatePassword(Request $request)
    {
        // Validate the new password only if it is provided
        $request->validate([
            'password' => 'nullable|string|min:8|confirmed', // Password must be valid if provided
        ]);
    
        // Check if a new password is provided
        if ($request->filled('password')) {
            // Get the authenticated user
            $user = Auth::user();
    
            // Update the user's password
            $user->password = Hash::make($request->password);
            $user->save();
    
            // Redirect back with a success message
            return redirect()->back()->with('success', 'رمز عبور با موفقیت تغییر یافت');
        }
    
        // If no password is provided, redirect without an error
        return redirect()->back()->with('info', 'ویرایش پسورد با مشکل روبرو شد !! لطفا مجددا تلاش نمایید');
    }

    public function changePasswordshow(){
        $userData = Auth::user()->userData;  // Get user's profile

        return view('profile.password', compact('userData'));
    }
}
