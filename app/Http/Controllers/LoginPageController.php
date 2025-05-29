<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LoginPage;
use Illuminate\Support\Facades\Storage;

class LoginPageController extends Controller
{
    public function edit()
    {
        $loginPage = LoginPage::first(); // Fetch the first record
        return view('admin.login_page.edit', compact('loginPage'));
    }

    public function update(Request $request)
{
    $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'text' => 'nullable|string|max:255',
    ]);

    $loginPage = LoginPage::firstOrCreate([]);

    if ($request->hasFile('image')) {
        // Delete the old image if it exists
        if ($loginPage->image) {
            Storage::delete('public/' . $loginPage->image);
        }

        // Store the new image in the public disk
        $imagePath = $request->file('image')->store('login_images', 'public');
        $loginPage->image = $imagePath;
    }

    // Update text
    $loginPage->text = $request->input('text');
    $loginPage->save();

    return redirect()->route('admin.login_page.edit')->with('success', 'اطلاعات صفحه لاگین با موفقیت ویرایش شد');
}
}
