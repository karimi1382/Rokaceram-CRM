<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductTemp;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;

class ProductUploadController extends Controller
{
    // Display upload form
    public function uploadForm()
    {
        
        return view('products.upload');
    }

    // Process uploaded Excel file
    public function processUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
     ProductTemp::truncate(); // 
        // Load the Excel file
        $file = $request->file('file');
        $data = Excel::toArray([], $file); // Reads the file into an array
    
        $rows = $data[0]; // Assuming data is in the first sheet
    
        foreach ($rows as $key => $row) {
            if ($key === 0) continue; // Skip header row
    
            // Parse the row data
            $idNumber = $row[0];
            $info = explode('-', $row[1]);
            $inventory = $row[2];
    
            // Clean up the fields by removing spaces
            $name = str_replace(' ', '', $info[0] ?? '');
            $degree = str_replace(' ', '', $info[1] ?? '');
            $size = str_replace(' ', '', $info[2] ?? '');
            $model = str_replace(' ', '', $info[3] ?? '');
            $color = str_replace(' ', '', $info[4] ?? '');
            $colorCode = str_replace(' ', '', $info[7] ?? '');
            if($name == 'ROKACERAM' || $name == 'ROKAMAX'){
                if( $inventory >= 23.00 ) {
                    if( $degree != 4 ) {
                        if( $degree != 'D' ) {
                    ProductTemp::updateOrCreate(
                        ['id_number' => $idNumber],
                        [
                            'name' => $name,
                            'degree' => $degree,
                            'size' => $size,
                            'model' => $model,
                            'color' => $color,
                            'color_code' => $colorCode,
                            'inventory' => $inventory ?? 0,
                            'is_valid' => true,
                        ]
                    );
                        }
                    }
                    
                }
        }
        }
    
        return redirect()->route('products.temp.index')->with('success', 'File uploaded successfully!');
    }
    

    // Show the temporary table data
    public function tempIndex()
    {
        $temps = ProductTemp::all();
        return view('products.temp', compact('temps'));
    }

    // Finalize data: Move from temporary to main table
    public function finalize()
    {
        // Retrieve all records from ProductTemp
        $temps = ProductTemp::get();
     Product::query()->update(['state' => 0]);
        foreach ($temps as $temp) {
            // Check if the record with id_number exists in the products table
            Product::updateOrCreate(
                ['id_number' => $temp->id_number], // Check by id_number
                [
                    'name' => $temp->name,
                    'degree' => $temp->degree,
                    'size' => $temp->size,
                    'model' => $temp->model,
                    'color' => $temp->color,
                    'color_code' => $temp->color_code,
                    'inventory' => $temp->inventory,
                    'state' => 1,
                ]
            );
        }
    
        // Optionally truncate the ProductTemp table after transferring data
        ProductTemp::truncate();
    
        // Redirect back with a success message
        return redirect()->route('products.index')->with('success', 'محصولات با موفقیت ذخیره شدند');
    }
    

}

