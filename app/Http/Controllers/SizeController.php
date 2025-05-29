<?php

namespace App\Http\Controllers;
use App\Models\size;

use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $sizes = Size::all();
        return view('sizes.index', compact('sizes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('sizes.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        Size::create($validated);
    
        return redirect()->route('sizes.index')->with('success', 'سایز جدید با موفقیت اضافه شد');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Size $size)
    {
        //
        return view('sizes.show', compact('size'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Size $size)
    {
        //
        return view('sizes.show', compact('size'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        $size->update($validated);
    
        return redirect()->route('sizes.index')->with('success', 'سایز جدید با موفقیت ویرایش شد');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        //
        $size->delete();
        return redirect()->route('sizes.index')->with('success', 'سایز جدید با موفقیت حذف شد');
    
    }
}
