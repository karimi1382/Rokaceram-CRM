<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Fetch data for export.
     */
    public function collection()
    {
        $user = auth()->user();  // Get the logged-in user
        
        // Get allowed product names and degrees for this user
        $allowedProducts = DB::table('user_product_visibility')
            ->where('user_id', $user->id)
            ->get(['product_name', 'product_degree']);
        
        // Create an array to store combinations of product names and degrees that are allowed
        $allowedCombinations = [];
        foreach ($allowedProducts as $allowedProduct) {
            $allowedCombinations[] = [
                'product_name' => $allowedProduct->product_name,
                'product_degree' => $allowedProduct->product_degree,
            ];
        }
        
        // Start the query for products using the new connection
        $query = DB::connection('sqlsrv')->table('vw_CRMproducts');  // Only products that are active (state = 1)
        
        // If there are allowed combinations, filter by those combinations
        if (!empty($allowedCombinations)) {
            $query->where(function ($query) use ($allowedCombinations) {
                foreach ($allowedCombinations as $combination) {
                    $query->orWhere(function ($query) use ($combination) {
                        $query->where('name', $combination['product_name'])
                              ->where('degree', $combination['product_degree']);
                    });
                }
            });
        }
        
        // **NEW** Add condition to exclude products with color_code 'CL000'
        $query->where('color_code', '!=', 'CL000');
        $query->where('Inventory', '>', '0');
        
        // Get the selected columns
        $products = $query->get([
            'id_number', 'name', 'degree', 'size', 'model', 'color', 'color_code', 'inventory'
        ]);
        
        return $products;
    }
    
    /**
     * Define the column headings.
     */
    public function headings(): array
    {
        return [
            'ID Number', 'Name', 'Degree', 'Size', 'Model', 'Color', 'Color Code', 'Inventory'
        ];
    }

    /**
     * Modify inventory display in Excel.
     */
    public function map($product): array
    {
        return [
            $product->id_number,
            $product->name,
            $product->degree,
            $product->size,
            $product->model,
            $product->color,
            $product->color_code,
            $product->inventory > 1000 ? '>1000' : $product->inventory,
        ];
    }
}
