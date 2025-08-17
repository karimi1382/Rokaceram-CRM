<?php

namespace App\Http\Controllers;

use App\Models\DisRequest;
use App\Models\Product;
use App\Models\RequestDetail;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian; // Import Jalalian class
use App\Models\HavaleData;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\DisRequestHavale; // <-- Add this line to import the DisRequestHavale model
use App\Services\HavaleSyncService;




class DisRequestController extends Controller
{
    public function index()
{
    try {
        $user = auth()->user();

        if ($user->role == 'personnel') {
            // دریافت شناسه‌ زیرمجموعه‌ها
            $childrenIds = $user->children()->pluck('user_id');

            // فقط درخواست‌های زیرمجموعه‌ها به جز In Progress
            $requests = DisRequest::whereIn('user_id', $childrenIds)
                ->where('status', '!=', 'In Progress')
                ->with(['user', 'disRequestHavales'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // برای نماینده: فقط درخواست‌های خودش
            $requests = DisRequest::where('user_id', $user->id)
                ->where('status', '!=', 'In Progress')
                ->with(['user', 'disRequestHavales'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // اضافه کردن اطلاعات محصول، تاریخ شمسی و وضعیت حواله
        foreach ($requests as $request) {
            // دریافت اطلاعات محصول از اتصال sqlsrv
            if ($request->product_id) {
                $product = DB::connection('sqlsrv')->table('vw_CRMproducts')
                    ->where('id', $request->product_id)
                    ->first();

                $request->product_details = $product ?: null;
            }

            // تبدیل تاریخ به شمسی
            $request->jalali_created_at = $request->created_at 
                ? Jalalian::fromCarbon($request->created_at)->format('Y/m/d') 
                : 'N/A';

            // بررسی حواله‌ها
            $request->havale_numbers = $request->disRequestHavales->pluck('havale_number')->toArray();

            if (!empty($request->havale_numbers)) {
                $request->status = 'In Progress';
            }
        }

        return view('dis_requests.index', compact('requests'));

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'مشکلی رخ داده است. لطفاً بعداً تلاش کنید.');
    }
}

    

public function create(Request $request)
{
    
    $distributors = \App\Models\User::whereHas('userdata', function ($query) {
        $query->where('personel_id', auth()->id());
    })->get();

    $productId = $request->get('product_id');
    
    // استفاده از کانکشن sqlsrv و فیلتر شناسه محصول برای پیدا کردن محصول مشخص‌شده

    // جستجوی رکورد محصول خاصی که شناسه‌اش در درخواست آمده است
    $product = DB::connection('sqlsrv')->table('vw_CRMproducts')
                                    ->where('id_number', $productId)
                                    ->first();

    // ارسال محصولات و محصول انتخاب‌شده به ویو
    return view('dis_requests.create', compact('product', 'productId', 'distributors'));
}

public function multiCreate(Request $request)
{
    $productIds = $request->input('selected_products', []);

    if (empty($productIds)) {
        return redirect()->back()->with('error', 'هیچ محصولی انتخاب نشده است.');
    }

    $products = DB::connection('sqlsrv')->table('vw_CRMproducts')
                ->whereIn('id_number', $productIds)
                ->get();

    $distributors = \App\Models\User::whereHas('userdata', function ($query) {
        $query->where('personel_id', auth()->id());
    })->get();

    return view('dis_requests.create', compact('products', 'distributors'));
}

public function multiStore(Request $request)
{
    $request->validate([
        'products.*.id_number' => 'required|string',
        'products.*.request_size' => 'required|numeric',
        'status' => 'required|in:Pending,Approved,Rejected,Completed',
        'tel_number' => 'required|string',
        'request_owner' => 'required|string',
        'address' => 'required|string',
    ]);

    $user_id = auth()->user()->role == 'distributor'
        ? auth()->id()
        : $request->input('distributor_id');

    foreach ($request->input('products') as $product) {
        DisRequest::create([
            'product_id'     => $product['id_number'],
            'request_size'   => $product['request_size'],
            'request_type'   => $request->request_type,
            'status'         => $request->status,
            'user_id'        => $user_id,
            'tel_number'     => $request->tel_number,
            'request_owner'  => $request->request_owner,
            'address'        => $request->address,
        ]);
    }

    if (auth()->user()->role == 'personnel') {
        return redirect()->route('requests.personeelindex')->with('success', 'همه درخواست‌ها با موفقیت ثبت شدند.');
    }

    return redirect()->route('dis_requests.index')->with('success', 'همه درخواست‌ها با موفقیت ثبت شدند.');
}



    public function store(Request $request)
    {
        
        $request->validate([
            // 'product_id' => 'required|exists:products,id',
            // 'request_size' => 'required|numeric',
            // 'request_type' => 'required|string',
            // 'address' => 'required|string',
            // 'tel_number' => 'required|string',
            // 'request_owner' => 'required|string',
            'status' => 'required|in:Pending,Approved,Rejected,Completed',
        ]);
        if($request->request_type == 'distributor'){
        DisRequest::create(array_merge(
            $request->all(),
            ['user_id' => auth()->id()]
        ));
        }else{
            DisRequest::create(array_merge(
                $request->all(),
                ['user_id' => $request->distributor_id]
            ));
            return redirect()->route('requests.personeelindex')->with('success', 'درخواست با موفقیت ثبت شد');

        }


        return redirect()->route('dis_requests.index')->with('success', 'درخواست با موفقیت ثبت شد');
    }

    public function storeHavaleNumber(Request $request, $id)
    {
        // Validate the input for 'havale_number'
        $validated = $request->validate([
            'havale_number' => 'required|string|max:255',
        ]);
    
        // Check if the havale_number already exists in the dis_request_havales table
        $existingHavale = DisRequestHavale::where('havale_number', $request->havale_number)->exists();
    
        if ($existingHavale) {
           
            // Find the DisRequest by ID
      $disRequest = DisRequest::findOrFail($id);
  
      // Insert the havale_number into dis_request_havales table
      $disRequestHavale = new DisRequestHavale();
      $disRequestHavale->dis_request_id = $disRequest->id; // Associate it with the current request
      $disRequestHavale->havale_number = $request->havale_number; // Insert havale number
      $disRequestHavale->save(); // Save the new havale record
  
      // Update the status of the DisRequest to 'in_progress'
      $disRequest->status = 'In Progress';
      $disRequest->save(); // Save the updated status
  
      return redirect()->back()->with('success', 'این حواله قبلا برای درخواست دیگر استفاده شده است - درخواست شما به آن اضافه شد');
 

          // If the havale number already exists, return an error message
         // return redirect()->back()->with('error', 'این شماره حواله قبلاً ثبت شده است و نمی‌تواند دوباره ثبت شود.')
                 //                ->withInput(); // This will make sure the input stays in the form.
      }
    
        // Find the DisRequest by ID
        $disRequest = DisRequest::findOrFail($id);
    
        // Insert the havale_number into dis_request_havales table
        $disRequestHavale = new DisRequestHavale();
        $disRequestHavale->dis_request_id = $disRequest->id; // Associate it with the current request
        $disRequestHavale->havale_number = $request->havale_number; // Insert havale number
        $disRequestHavale->save(); // Save the new havale record
    
        // Update the status of the DisRequest to 'in_progress'
        $disRequest->status = 'In Progress';
        $disRequest->save(); // Save the updated status
    
        return redirect()->back()->with('success', 'حواله جدید با موفقیت اضافه شد');
    }
    


public function deleteHavale($id)
{
    // Find the DisRequestHavale record by ID
    $disRequestHavale = DisRequestHavale::findOrFail($id);

    // Delete the record
    $disRequestHavale->delete();

    // Redirect back with a success message
    return redirect()->back()->with('success', 'حواله با موفقیت حذف شد');
}
public function show($id)
{
     
    // دریافت درخواست با جزئیات و حواله‌ها
    $request = DisRequest::with(['requestDetails' => function ($query) {
        $query->orderBy('id', 'desc');
    }, 'requestDetails.user', 'disRequestHavales']) // شامل حواله‌ها
    ->findOrFail($id);

    // بررسی اینکه آیا حواله‌ای برای این درخواست وجود دارد یا خیر
    if ($request->disRequestHavales->isEmpty()) {
        // اگر هیچ حواله‌ای وجود نداشته باشد، وضعیت را به 'pending' تغییر دهید
        $request->status = 'pending';
        $request->save();
        
        // دوباره درخواست را از دیتابیس بارگذاری کنید تا وضعیت جدید در ویو بیاید
        $request = DisRequest::with(['requestDetails' => function ($query) {
            $query->orderBy('id', 'desc');
        }, 'requestDetails.user', 'disRequestHavales'])->findOrFail($id);
    }

    // تبدیل زمان created_at درخواست به جلالی در صورت وجود
    if ($request->created_at) {
        $request->created_at_jalali = Jalalian::fromCarbon($request->created_at)->format('Y-m-d H:i:s');
    } else {
        $request->created_at_jalali = 'N/A';  // یا هر مقدار پیش‌فرض دیگر
    }

    // تبدیل زمان created_at هر جزئیات درخواست به جلالی در صورت وجود
    foreach ($request->requestDetails as $detail) {
        if ($detail->created_at) {
            $detail->created_at_jalali = Jalalian::fromCarbon($detail->created_at)->format('Y-m-d H:i:s');
        } else {
            $detail->created_at_jalali = 'N/A';  // یا هر مقدار پیش‌فرض دیگر
        }
    }

    // دریافت کد محصول از جدول درخواست
    $productId = $request->product_id; // اینجا کد محصول که در ستون product_id است گرفته می‌شود.

    // وضعیت اتصال به دیتابیس
    $connectionIsOk = true;

$user_request_id_show = DisRequestHavale::where('dis_request_id', $id)->first();


if($user_request_id_show)
{
    
    try {
    $product = DB::connection('sqlsrv')->table('vw_HavaleData')
                    ->where('havale', $user_request_id_show->havale_number) // جستجو بر اساس id (کد محصول)
                    ->first();
    }
    catch (\Throwable $e) {
        $connectionIsOk = false; // در صورتی که اتصال به دیتابیس قطع شود
        $product = null; // محصول برابر با null قرار می‌دهیم
    }
    
    if ($product) {
        
        //$request->product_details = $product; // اضافه کردن اطلاعات محصول به درخواست
        $request->product_details = (object)[
            'name' => $product->product_name,
            'degree' => '',
            'size' => '',
            'model' => '',
            'color' => '',
            'color_code' => ''
        ];
    } else {
        // اگر محصول پیدا نشد، یک شیء پیش‌فرض برای محصول ایجاد می‌کنیم
        $request->product_details = (object)[
            'name' => 'نام محصول یافت نشد',
            'degree' => '',
            'size' => '',
            'model' => '',
            'color' => '',
            'color_code' => ''
        ];
    }


}
else 
{

    // جستجو در کانکشن جدید برای دریافت اطلاعات محصول
    try {
        $product = DB::connection('sqlsrv')->table('vw_CRMproducts')
                    ->where('id', $productId) // جستجو بر اساس id (کد محصول)
                    ->first();
    } catch (\Throwable $e) {
        $connectionIsOk = false; // در صورتی که اتصال به دیتابیس قطع شود
        $product = null; // محصول برابر با null قرار می‌دهیم
    }

     // اضافه کردن اطلاعات محصول به درخواست
     if ($product) {
        $request->product_details = $product; // اضافه کردن اطلاعات محصول به درخواست
    } else {
        // اگر محصول پیدا نشد، یک شیء پیش‌فرض برای محصول ایجاد می‌کنیم
        $request->product_details = (object)[
            'name' => 'نام محصول یافت نشد',
            'degree' => '',
            'size' => '',
            'model' => '',
            'color' => '',
            'color_code' => ''
        ];
    }



}

   

    // بازگشت به ویو با داده‌های درخواست و وضعیت اتصال به دیتابیس
    return view('dis_requests.show', compact('request', 'connectionIsOk'));
}




    public function addComment(Request $request, $id)
    {
        $disRequest = DisRequest::findOrFail($id);  // Get the DisRequest object

        // Validate the request input
        $validatedData = $request->validate([
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:2048',  // Validation for file
        ]);
    
        // Prepare the data for the new request detail
        $data = [
            'description' => $validatedData['description'],
            'user_id' => auth()->id(),  // Assuming the logged-in user is the one adding the comment
        ];
    
        // If a file is uploaded, store it and add the file path to the data
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('dis_request_files', 'public');  // Store file in 'dis_request_files' folder
    
            $data['file_path'] = $filePath;  // Add file path to the data
        }
    
        // Create a new comment (request detail) and associate it with the DisRequest
        $disRequest->requestDetails()->create($data);
    

        return back()->with('success', 'توضیحات با موفقیت اضافه شد');
    }

    public function completedRequests()
    {
        try {
            // گرفتن درخواست‌هایی که وضعیت‌شان inprogress یا rejected هستند
            $inProgressRequests = DisRequest::where('status', 'In Progress')
            ->where('file_path',Null)
                ->where('user_id', auth()->id())
                ->with('disRequestHavales')  // بارگذاری شماره حواله‌ها
                ->get();
    
            $rejectedRequests = DisRequest::where('status', 'Rejected')
            ->where('file_path',Null)
                ->where('user_id', auth()->id())
                ->with('disRequestHavales')  // بارگذاری شماره حواله‌ها
                ->get();
    
            return view('dis_requests.completed', compact('inProgressRequests', 'rejectedRequests'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'مشکلی رخ داده است. لطفاً بعداً تلاش کنید.');
        }
    }
    

public function showCompletedRequest($id)
{
    $request = DisRequest::with(['product', 'requestDetails.user'])->findOrFail($id);
    return view('dis_requests.show_completed', compact('request'));
}

public function personnelindex()
{
    $user = auth()->user(); // Logged-in user
    $childrenIds = $user->children()->pluck('user_id'); // Get children IDs

    // Fetch requests made by children, excluding the ones that are 'In Progress'
    $requests = DisRequest::whereIn('user_id', $childrenIds)
        ->where('status', '!=', 'In Progress') // Exclude 'In Progress' requests
        ->with('user') // Eager load user details
        ->orderBy('created_at', 'desc') // Order by latest created_at
        ->get();

    // Initialize variable to track connection status
    $connectionIsOk = true;

    // Loop through requests to fetch product details using the new connection
    foreach ($requests as $request) {
        // Check if product_id exists and is not null
        if ($request->product_id) {
            try {
                // Fetch the product details from the new connection
                $product = DB::connection('sqlsrv')->table('vw_CRMproducts')
                            ->where('id', $request->product_id)
                            ->first();

                // If product is found, add it to the request
                if ($product) {
                    $request->product_details = $product;
                } else {
                    $request->product_details = null; // No product found
                }
            } catch (\Throwable $e) {
                $connectionIsOk = false; // Connection failed
                $request->product_details = null; // Set product details to null
            }
        }
    }

    // Convert created_at to Jalali date format for each request
    foreach ($requests as $request) {
        // Check if created_at is not null
        if ($request->created_at) {
            $request->jalali_created_at = Jalalian::fromCarbon($request->created_at)->format('Y/m/d '); // Convert to Jalali date
        } else {
            $request->jalali_created_at = 'N/A'; // Set a default value if created_at is null
        }
    }

    // Return the view with the requests, now with product details included
    return view('dis_requests.index', compact('requests', 'connectionIsOk'));
}

public function destroy($id)
{
    $request = DisRequest::findOrFail($id);
    $request->delete();

    return redirect()->route('dis_requests.index')->with('success', 'درخواست با موفقیت حذف شد.');
}


    public function personnelshow($id)
    {
        // Fetch the request along with its details
        $request = DisRequest::with(['user.userdata.city', 'requestDetails.user.userdata.city'])
            ->findOrFail($id);
           
        return view('dis_requests.show', compact('request'));
    }


//     public function updateStatus(Request $request, $id)
// {
//     // Validate the incoming request
//     $request->validate([
//         'status' => 'required|in:Pending,Approved,Rejected,Completed,In Progress',
//     ]);

//     // Find the DisRequest by its ID
//     $disRequest = DisRequest::findOrFail($id);

//     // Update the status
//     $disRequest->status = $request->input('status');
//     $disRequest->save();

//     // Redirect back with a success message
//     return redirect()->route('dis_requests.show', $disRequest->id)->with('success', 'وضعیت درخواست با موفقیت تغییر یافت.');
// }
public function personelcompletedRequests(HavaleSyncService $havaleSync)
{
    $havaleSync->sync();
    $user = auth()->user(); // Logged-in user
    $childrenIds = $user->children()->pluck('user_id'); // Get children IDs
   
    // Fetch requests made by children with "In Progress" status
    $inProgressRequests = DisRequest::whereIn('user_id', $childrenIds)
    ->whereNull('file_path')
    ->where('status', 'In Progress')
    ->whereHas('disRequestHavales', function ($query) {
        $query->where('status', '<>', 'Completed');
    })
    ->with([
        'user',
        'disRequestHavales' => function ($query) {
            $query->where('status', '<>', 'Completed');
        }
    ])
    ->get();
    foreach ($inProgressRequests as $request) {
        $request->shamsi_created_at = Jalalian::fromCarbon(\Carbon\Carbon::parse($request->created_at))->format('Y/m/d');
    }

       // dd($inProgressRequests);
    // Fetch requests made by children with "Rejected" status
    $rejectedRequests = DisRequest::whereIn('user_id', $childrenIds)
    ->where('file_path'  ,null)
        ->where('status', 'Rejected') // Filter by status
        ->with('user', 'disRequestHavales') // Eager load user and havales
        ->get();

    // Return view with the inProgress and rejected requests
    return view('dis_requests.completed', compact('inProgressRequests', 'rejectedRequests'));
}



public function showApprovedRequests()
{
    // Fetch dis_requests where status is 'approved'
    $disRequests = DisRequest::with(['user', 'product', 'user.UserData.parent']) // Eager load user, product, and parent user
        ->where('status', 'In Progress')
        ->get();
        

    return view('admin.approved_requests', compact('disRequests'));
}

public function showApprovedRequestspage()
{
    // Fetch dis_requests where status is 'approved'
    $disRequests = DisRequest::with(['user', 'product', 'user.UserData.parent']) // Eager load user, product, and parent user
    ->whereIn('status', ['Approved', 'Completed']) // Check if status is either 'Approved' or 'Completed'
        ->get();

    return view('admin.showapproved_requests', compact('disRequests'));
}



public function updateApprovedRequests(Request $request, $id)
{
    // Validate the request
    $request->validate([
        'status' => 'required|in:Approved,pending,rejected', // Ensure status is valid
    ]);
    
    // Find the DisRequest by ID and update the status
    $disRequest = DisRequest::findOrFail($id);
    $disRequest->status = $request->status;
    $disRequest->save();

    // Redirect back with a success message
    return redirect()->route('approved.requests')->with('success', 'وضعیت سفارش با موفقیت تغییر کرد');
}

public function updateSize(Request $request, $id)
{
    // Validate input (ensure it's a positive number)
    $request->validate([
        'request_size' => 'required|numeric|min:1',
    ]);

    // Find the request and update
    $disRequest = DisRequest::findOrFail($id);
    $disRequest->request_size = $request->request_size;
    $disRequest->save();

    return redirect()->back()->with('success', 'متراژ درخواست با موفقیت بروزرسانی شد.');
}

public function updateStatus(Request $request, $id)
    {
        // Validate the input
        $validated = $request->validate([
            'file_path' => 'nullable|numeric', // Validate that it's a number or null
        ]);

        // Find the request by ID
        $disRequest = DisRequest::findOrFail($id);

        // If 'file_path' is provided, update it, otherwise set it to null
        $filePath = $request->input('file_path') ?? null;
        $disRequest->file_path = $filePath;

        // Update the status based on 'file_path' content
        if (empty($filePath)) {
            $disRequest->status = 'Pending'; // Status will be 'Pending' if file_path is empty
        } else {
            $disRequest->status = 'In Progress'; // Status will be 'In Progress' if file_path is not empty
        }

        // Save the changes to the database
        $disRequest->save();

        // Redirect back to the previous page (using session or redirect()->back())
        return redirect()->back()->with('success', ' شماره حواله به این درخواست پیوست شد');
    }

    // Cancel Request (optional)
    public function cancelRequest($id)
    {
        // Find the request by ID
        $disRequest = DisRequest::findOrFail($id);

        // Update the file_path to null and status to 'Rejected'
        $disRequest->file_path = null;
        $disRequest->status = 'Rejected';

        // Save the changes to the database
        $disRequest->save();

        // Redirect back to the dis_requests index page
        return redirect()->route('dis_requests.personelcompleted')->with('success', 'این درخواست لغو شد');
    }

   
   

  

   
    public function havaleIndex(HavaleSyncService $havaleSync)
{
    $havaleSync->sync();
    try {
        $user = auth()->user();
        $childrenIds = $user->children()->pluck('user_id')->toArray();
        $allUserIds = array_merge([$user->id], $childrenIds);
    
        // دریافت درخواست‌ها با حواله‌های مربوطه
        $requests = DisRequest::whereIn('user_id', $allUserIds)
            ->with(['user', 'disRequestHavales'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        if ($requests->isEmpty()) {
            return redirect()->back()->with('error', 'هیچ حواله‌ای یافت نشد.');
        }
    
        $havaleRecords = [];
    
        foreach ($requests as $request) {
            foreach ($request->disRequestHavales as $havale) {
                $havaleData = DB::connection('sqlsrv')
                    ->select("SELECT * FROM vw_HavaleData WHERE havale = ?", [$havale->havale_number]);
    
                if (!empty($havaleData)) {
                    $havaleInfo = $havaleData[0];
    
                    // **بررسی وضعیت حواله**
                    $status = 'In Progress';
                    if ($havaleInfo->mali == 1) {
                        $status = 'Approved';
                    }
                    if (!is_null($havaleInfo->tavali)) {
                        $status = 'Completed';
                    }
    
                    // فقط حواله‌هایی که وضعیتشان "Completed" نیست را نمایش می‌دهیم
                    if ($status === 'Completed') {
                        continue; // حواله‌هایی که وضعیتشان تکمیل‌شده است، نادیده گرفته می‌شوند
                    }
    
                    // **محاسبه زمان باقی‌مانده برای `In Progress`**
                    $remainingDays = null;
                    if ($status === 'In Progress') {
                        $createdAt = Carbon::parse($havale->created_at);
                        $deadline = $createdAt->addDays(30);
                        $remainingDays = Carbon::now()->diffInDays($deadline, false);
    
                        if ($remainingDays <= 0) {
                            $status = 'Rejected';
                            $remainingDays = 0;
                        }
                    }
    
                    // **ذخیره وضعیت جدید در پایگاه داده**
                    if ($havale->status !== $status) {
                        $havale->status = $status;
                        $havale->save();
                    }
    
                    // **اضافه کردن به آرایه خروجی فقط حواله‌هایی که وضعیتشان "Completed" نیست**
                    $havaleRecords[] = (object)[
                        'id' => $request->id,
                        'user' => $request->user,
                        'havale_number' => $havale->havale_number,
                        'status' => $status,
                        'created_at' => $havale->created_at,
                        'jalali_created_at' => Jalalian::fromCarbon($havale->created_at)->format('Y/m/d'),
                        'remaining_days' => $remainingDays
                    ];
                }
            }
        }
    
        return view('dis_requests.havale_index', compact('havaleRecords'));
    
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'مشکلی رخ داده است. لطفاً بعداً تلاش کنید.');
    }
}

    

    

    
public function showHavaleData($id)
{
    try {
        // Find the request by ID
        $request = DisRequestHavale::where('havale_number',$id)->first();
        $disRequest = DisRequest::with('user')->find($request->dis_request_id);
        
        if (!$request) {
            return redirect()->back()->with('error', 'شماره حواله یافت نشد.');
        }

        $remainingDays = null;
        if ($request->status == 'In Progress') {
            $createdAt = Carbon::parse($request->created_at);
            $deadline = $createdAt->addDays(30);
            $remainingDays = Carbon::now()->diffInDays($deadline, false);
            $remainingDays = $remainingDays > 0 ? $remainingDays : 0;
        }

        // Get the havale_number from the request and query the SQL Server for it
        try {
            // Use havale_number instead of file_path
            $havaleData = DB::connection('sqlsrv')->select("SELECT * FROM vw_HavaleData WHERE havale = ?", [$request->havale_number]);

            // If no data is found for this havale_number
            if (empty($havaleData)) {
                return redirect()->back()->with('error', 'اطلاعاتی برای این شماره حواله یافت نشد.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'ارتباط با سرور موقتا قطع می‌باشد. لطفاً بعداً تلاش کنید.');
        }

        // Return the view with the data
        return view('dis_requests.havale_show', compact('disRequest','request', 'havaleData', 'remainingDays'));

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'مشکلی رخ داده است. لطفاً بعداً تلاش کنید.');
    }
}
    


public function completedHavale()
{
    try {
        $user = auth()->user();
        $childrenIds = $user->children()->pluck('user_id')->toArray();
        $allUserIds = array_merge([$user->id], $childrenIds);
    
        $requests = DisRequest::whereIn('user_id', $allUserIds)
            ->with(['user', 'disRequestHavales'])
            ->orderBy('created_at', 'desc')
            ->get();
    
        if ($requests->isEmpty()) {
            return redirect()->back()->with('error', 'هیچ حواله‌ای یافت نشد.');
        }
    
        $havaleRecords = [];
    
        foreach ($requests as $request) {
            foreach ($request->disRequestHavales as $havale) {
                $havaleData = DB::connection('sqlsrv')
                    ->select("SELECT * FROM vw_HavaleData WHERE havale = ?", [$havale->havale_number]);
    
                if (!empty($havaleData)) {
                    $havaleInfo = $havaleData[0];
    
                    // **بررسی وضعیت حواله**
                    $status = 'In Progress';
                    if ($havaleInfo->mali == 1) {
                        $status = 'Approved';
                    }
                    if (!is_null($havaleInfo->tavali)) {
                        $status = 'Completed';
                    }
    
                    // فقط حواله‌هایی که وضعیت 'Completed' دارند
                    if ($status !== 'Completed') {
                        continue;
                    }
    
                    // **محاسبه زمان باقی‌مانده برای `In Progress`**
                    $remainingDays = null;
                    if ($status === 'In Progress') {
                        $createdAt = Carbon::parse($havale->created_at);
                        $deadline = $createdAt->addDays(30);
                        $remainingDays = Carbon::now()->diffInDays($deadline, false);
    
                        if ($remainingDays <= 0) {
                            $status = 'Rejected';
                            $remainingDays = 0;
                        }
                    }
    
                    // **ذخیره وضعیت جدید در پایگاه داده**
                    if ($havale->status !== $status) {
                        $havale->status = $status;
                        $havale->save();
                    }
    
                    // **اضافه کردن به آرایه خروجی**
                    $havaleRecords[] = (object)[
                        'id' => $request->id,
                        'user' => $request->user,
                        'havale_number' => $havale->havale_number,
                        'status' => $status,
                        'created_at' => $havale->created_at,
                        'jalali_created_at' => Jalalian::fromCarbon($havale->created_at)->format('Y/m/d'),
                        'remaining_days' => $remainingDays
                    ];
                }
            }
        }
    
        return view('dis_requests.havale_index', compact('havaleRecords'));
    
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'مشکلی رخ داده است. لطفاً بعداً تلاش کنید.');
    }
}









}
