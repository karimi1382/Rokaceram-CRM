<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index()
{
    $user = auth()->user();
    $products = collect();
    $connectionIsOk = true;

    try {
        if ($user->id != 1) {
            // محصولات مجاز این کاربر از دیتابیس MySQL
            $allowedProducts = DB::table('user_product_visibility')
                ->where('user_id', $user->id)
                ->get(['product_name', 'product_degree']);

            // تبدیل لیست مجازها به آرایه از ترکیب name و degree
            $allowedPairs = $allowedProducts->map(function ($item) {
                return ['name' => $item->product_name, 'degree' => $item->product_degree];
            });

            // گرفتن تمام محصولات فقط یک بار
            $allCRMProducts = DB::connection('sqlsrv')
                ->table('vw_CRMproducts')
                ->get();

            // فیلتر محصولات بر اساس لیست مجاز
            $products = $allCRMProducts->filter(function ($crmProduct) use ($allowedPairs) {
                return $allowedPairs->contains(function ($allowed) use ($crmProduct) {
                    return $crmProduct->name === $allowed['name'] &&
                           $crmProduct->degree === $allowed['degree'];
                });
            })->values(); // values() برای reset کردن indexها

        } else {
            // اگر کاربر ادمین بود، همه محصولات رو بیار
            $products = DB::connection('sqlsrv')
                ->table('vw_CRMproducts')
                ->get();
        }

    } catch (\Throwable $e) {
        $connectionIsOk = false;
        $products = collect(); // اگر خطا بود لیست خالی بده
        session()->flash('error', 'ارتباط با سرور قطع می‌باشد.');
    }

    return view('products.index', compact('products', 'connectionIsOk'));
}

     




    // public function index_old()
    // {
        
    //     $user = auth()->user();
    
    //     if ($user->id != 1) {
    //         // Get allowed product names and degrees for this user
    //         $allowedProducts = DB::table('user_product_visibility')
    //             ->where('user_id', $user->id)
    //             ->get(['product_name', 'product_degree']);
    
    //         // Get products that match both allowed names and degrees
    //         $products = collect();  // Initialize empty collection
    
    //         foreach ($allowedProducts as $allowedProduct) {
    //             // Find products that match the product name and product degree
    //             $matchingProducts = Product::where('name', $allowedProduct->product_name)
    //                 ->where('degree', $allowedProduct->product_degree)
    //                 ->where('state', 1)
    //                 ->get();
    
    //             // Merge matching products into the products collection
    //             $products = $products->merge($matchingProducts);
    //         }
    
    //         return view('products.index', compact('products'));
    //     } else {
    //         // For admin (user id = 1), show all products
    //         $products = Product::where('state', 1)->get();
    //         return view('products.index', compact('products'));
    //     }
    // }
    

  public function showproduct()
    {
        //

        
        $products = Product::where('state',1)->get();
        return view('showproduct', compact('products'));
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'id_number' => 'required|unique:products',
            'name' => 'required',
            'degree' => 'required',
            'size' => 'required',
            'model' => 'required',
            'color' => 'required',
            'color_code' => 'required',
            'inventory' => 'required|integer',
        ]);
    
        Product::create($validated);
    
        return redirect()->route('products.index')->with('success', 'Product created successfully!');
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
        return view('products.show', compact('product'));

        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
{
    return view('products.edit', compact('product'));
}

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    //     $validated = $request->validate([
    //         'id_number' => 'required|unique:products,id_number,' . $product->id,
    //         'name' => 'required',
    //         'degree' => 'required',
    //         'size' => 'required',
    //         'model' => 'required',
    //         'color' => 'required',
    //         'color_code' => 'required',
    //         'inventory' => 'required|integer',
    //     ]);
    
    //     $product->update($validated);
    
    //     return redirect()->route('products.index')->with('success', 'Product updated successfully!');
    
    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully!');
    
    }

    public function ShowProductsearch(Request $request)
{
   
    $user = auth()->user();  // Get the logged-in user
    
    // Get allowed product names and degrees for this user from the new connection
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
    $query = DB::connection('sqlsrv')->table('vw_CRMproducts');
    
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
    
    // Apply other search filters from the request
    if ($request->filled('id_number')) {
        $query->where('id_number', 'like', '%' . $request->id_number . '%');
    }
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }
    if ($request->filled('degree')) {
        $query->where('degree', 'like', '%' . $request->degree . '%');
    }
    if ($request->filled('size')) {
        $query->where('size', 'like', '%' . $request->size . '%');
    }
    if ($request->filled('model')) {
        $query->where('model', 'like', '%' . $request->model . '%');
    }
    if ($request->filled('color')) {
        $query->where('color', 'like', '%' . $request->color . '%');
    }
    if ($request->filled('color_code')) {
        $query->where('color_code', 'like', '%' . $request->color_code . '%');
    }
    
    // Execute the query and get the filtered products
    $products = $query->get();
    
    // Return the view with filtered products
    return view('showproduct', compact('products'));
}

    
    
public function search(Request $request)
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
    
    // Start the query for products using the new connection (sqlsrv)
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
    
    // Apply other search filters from the request
    if ($request->filled('id_number')) {
        $query->where('id_number', 'like', '%' . $request->id_number . '%');
    }
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }
    if ($request->filled('degree')) {
        $query->where('degree', 'like', '%' . $request->degree . '%');
    }
    if ($request->filled('size')) {
        $query->where('size', 'like', '%' . $request->size . '%');
    }
    if ($request->filled('model')) {
        $query->where('model', 'like', '%' . $request->model . '%');
    }
    if ($request->filled('color')) {
        $query->where('color', 'like', '%' . $request->color . '%');
    }
    if ($request->filled('color_code')) {
        $query->where('color_code', 'like', '%' . $request->color_code . '%');
    }
    
    // Execute the query and get the filtered products
    $products = $query->get();
    $connectionIsOk = true;
    // Return the view with the filtered products
    return view('products.index', compact('products','connectionIsOk'));
}

    

    
}
