<?php

namespace App\Http\Controllers;
use App\Models\Tile_model;

use Illuminate\Http\Request;

class TileModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $tile_models = Tile_model::all();
        return view('tile_models.index', compact('tile_models'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('tile_models.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:255',
        ]);

        Tile_model::create($validated);

        return redirect()->route('tile_models.index')->with('success', 'طرح جدید با موفقیت اضافه شد');
   
    }

    /**
     * Display the specified resource.
     */
    public function show(Tile_model $tile_model)
    {
        return view('tile_models.show', compact('tile_model'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tile_model $tile_model)
    {
        return view('tile_models.edit', compact('tile_model'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tile_model $tile_model)
    {
        $validated = $request->validate([
            'model' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:255',
        ]);

        $tile_model->update($validated);

        return redirect()->route('tile_models.index')->with('success', 'طرح جدید با موفقیت ویرایش شد');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tile_model $tile_model)
    {
        $tile_model->delete();
        return redirect()->route('tile_models.index')->with('success', 'سایز جدید با موفقیت حذف شد');
    }
}
