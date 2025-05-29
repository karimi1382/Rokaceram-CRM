<?php

namespace App\Http\Controllers;
use App\Models\City;

use Illuminate\Http\Request;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $cities = City::all();
        return view('cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('cities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
        ]);
    
        City::create($validated);
    
        return redirect()->route('cities.index')->with('success', 'شهر جدید با موفقیت اضافه شد');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        //
        return view('cities.show', compact('city'));

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        //
        return view('cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        //
        $validated = $request->validate([
            'name' => 'required',
            'state' => 'nullable|string',
            'country' => 'nullable|string',
        ]);
    
        $city->update($validated);
    
        return redirect()->route('cities.index')->with('success', 'شهر جدید با موفقیت ویرایش شد');
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        //
        $city->delete();
        return redirect()->route('cities.index')->with('success', 'سایز جدید با موفقیت حذف شد');
    
    }
}
