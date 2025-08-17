<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserData;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\UserProductVisibility;
use App\Models\Product; // Import Product model
use App\Models\UserTarget;
use App\Models\DisRequestHavale;
use Morilog\Jalali\Jalalian; // Import Jalalian class
use Carbon\Carbon;
use App\Services\HavaleSyncService;










class UserController extends Controller
{
    public function index()
{
     // Get all users (with profiles)
    $users = User::with('userData')->orderBy('name', 'asc')->get();
    return view('users.index', compact('users'));
}

    public function create()
    {
        $productNames = Product::select('name')->groupBy('name')->get();
        $productDegrees = Product::select('degree')->groupBy('degree')->get();

        $cities = City::orderBy('name', 'asc')->get(); // Get all cities
        $users = User::where('role', '!=', 'admin')->orderBy('name', 'asc')->get(); // Get all users for personel_id dropdown
        return view('users.create', compact('cities', 'users','productNames','productDegrees'));
    }

    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|string|in:admin,personnel,manager,distributor',
            'phone' => 'nullable|string|max:255',
            'city_id' => 'nullable|exists:cities,id',
            'personel_id' => 'nullable|exists:users,id',
            'customer_type' => 'nullable|string|in:admin,personnel,manager,distributor',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'required|string|min:8|confirmed', // Ensure password is set and confirmed
            'visible_products' => 'nullable|array', // Validate visible_products
            'visible_products.*.degree' => 'nullable|array', // Validate product degrees
            'visible_products.*.degree.*' => 'nullable|string', // Validate the degrees array inside the product
        ]);
    
        // Create the new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password), // Hash the password
        ]);
    
        // Handle profile data
        $userData = new UserData();
        $userData->user_id = $user->id; // Link to the newly created user
        $userData->phone = $request->phone;
        $userData->city_id = $request->city_id;
        $userData->personel_id = $request->personel_id;
        $userData->customer_type = $request->role;
    
        if ($request->hasFile('profile_picture')) {
            $imagePath = $request->file('profile_picture')->store('profile_pictures', 'public');
            $userData->profile_picture = $imagePath;
        }
    
        $userData->save();
    
        // Handle product visibility for the new user
        if ($request->has('visible_products')) {
            foreach ($request->visible_products as $productName => $productData) {
                // Ensure there are degrees selected for this product
                if (isset($productData['degree'])) {
                    foreach ($productData['degree'] as $degree) {
                        // Insert the product and degree into the user_product_visibility table
                        UserProductVisibility::create([
                            'user_id' => $user->id,
                            'product_name' => $productName,
                            'product_degree' => $degree, // Store the degree as-is
                        ]);
                    }
                }
            }
        }
    
        // Redirect with success message
        return redirect()->route('users.index')->with('success', 'کاربر با موفقیت اضافه شد');
    }
    

    public function edit(User $user)
    {
        // Get all product names and degrees using the new connection
        $productNames = DB::connection('sqlsrv')
            ->table('vw_CRMproducts')
            ->select('name')
            ->groupBy('name')
            ->get();
        
        // Get all distinct degrees for each product
        $productDegrees = [];
        foreach ($productNames as $product) {
            $productDegrees[$product->name] = DB::connection('sqlsrv')
                ->table('vw_CRMproducts')
                ->where('name', $product->name)
                ->select('degree')
                ->distinct()
                ->get();
        }
    
        // Get all cities
        $cities = City::all();
    
        // Get all users except for admins, so they can be selected as personnel
        $users = User::where('role', '!=', 'admin')->get();
    
        return view('users.edit', compact('user', 'cities', 'users', 'productNames', 'productDegrees'));
    }
    

    public function update(Request $request, $id)
    {
        // Validate the incoming data
        $request->validate([
            'visible_products_names' => 'nullable|array',
            'visible_products_names.*' => 'nullable|string',
            'visible_products' => 'nullable|array',
            'visible_products.*.degree' => 'nullable|array',
            'password' => 'nullable|confirmed|min:6', // اضافه کردن اعتبارسنجی رمز جدید
        ]);
    
        $user = User::findOrFail($id);
    
        // بروزرسانی name, email, role
        $user->update($request->only('name', 'email', 'role'));
       
       
        if ($request->has('city_id')) {
            $userData = $user->userData;
        
            if ($userData) {
                $userData->city_id = $request->city_id;
                $userData->save();
            } 
        }


        // اگر پسورد جدید وارد شده باشد، پسورد را بروزرسانی کن
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }
    
        // بروزرسانی visible products
        if ($request->has('visible_products_names') && $request->has('visible_products')) {
            $user->productVisibilities()->delete();
    
            foreach ($request->visible_products_names as $productName) {
                if (isset($request->visible_products[$productName]['degree'])) {
                    $degrees = $request->visible_products[$productName]['degree'];
    
                    foreach ($degrees as $degree) {
                        UserProductVisibility::create([
                            'user_id' => $user->id,
                            'product_name' => $productName,
                            'product_degree' => $degree,
                        ]);
                    }
                }
            }
        }
    
        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }
    



    public function showChildren(Request $request, $userId, HavaleSyncService $havaleSync)
    {
        $havaleSync->sync();
    
        // همیشه از کاربر لاگین‌شده استفاده کن
        $userId = auth()->id();
        $parentUser = User::findOrFail($userId);
    
        $connectionIsOk = true;
    
        // تاریخ شمسی و بازه ماه
        $now = Jalalian::now();
        $shamsiYear = $request->input('year', $now->getYear());
        $shamsiMonth = str_pad($request->input('month', $now->getMonth()), 2, '0', STR_PAD_LEFT);
    
        $startDate = Jalalian::fromFormat('Y/m/d', "$shamsiYear/$shamsiMonth/01")->toCarbon()->startOfDay();
        $endDate   = Jalalian::fromFormat('Y/m/d', "$shamsiYear/$shamsiMonth/01")->addMonths(1)->subDays(1)->toCarbon()->endOfDay();
    
        // پروفایل‌های فرزند
        $childrenProfiles = $parentUser->childrenProfiles()->with(['user', 'city'])->get();
        $childrenUserIds = $childrenProfiles->pluck('user.id')->filter()->unique();
    
        if ($childrenUserIds->isEmpty()) {
            return view('personnel.children', [
                'childrenProfiles' => collect(),
                'shamsiYear'       => $shamsiYear,
                'shamsiMonth'      => $shamsiMonth,
                'userId'           => $userId,
                'connectionIsOk'   => $connectionIsOk,
            ]);
        }
    
        // حواله‌های ماه جاری از MySQL
        $havaleUserMap = DB::table('dis_requests')
            ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
            ->whereIn('dis_requests.user_id', $childrenUserIds)
            ->whereIn('dis_request_havales.status', ['approved', 'completed'])
            ->whereBetween('dis_request_havales.date_target', [$startDate, $endDate])
            ->select('dis_requests.user_id', 'dis_request_havales.havale_number', 'dis_request_havales.status')
            ->get();
    
        $havaleNumbers = $havaleUserMap->pluck('havale_number')->unique();
    
        // جمع متراژ حواله‌ها از SQL Server
        $havaleDataRaw = [];
        try {
            if ($havaleNumbers->isNotEmpty()) {
                $havaleDataRaw = DB::connection('sqlsrv')
                    ->table('vw_HavaleData')
                    ->select('havale', DB::raw('SUM(product_MR) as request_size'))
                    ->whereIn('havale', $havaleNumbers)
                    ->groupBy('havale')
                    ->get()
                    ->keyBy('havale');
            }
        } catch (\Throwable $e) {
            $connectionIsOk = false;
            $havaleDataRaw = [];
        }
    
        // محاسبه رزروی/ارسالی ماه
        $userHavaleStats = [];
        $seenHavales = [];
        foreach ($havaleUserMap as $row) {
            $uId = $row->user_id;
            $status = strtolower(trim($row->status));
            $havaleNumber = $row->havale_number;
            $key = $uId . '_' . $havaleNumber;
    
            if (isset($seenHavales[$key])) continue;
            $seenHavales[$key] = true;
    
            $requestSize = isset($havaleDataRaw[$havaleNumber]) ? floatval($havaleDataRaw[$havaleNumber]->request_size) : 0;
    
            if (!isset($userHavaleStats[$uId])) {
                $userHavaleStats[$uId] = ['approved' => 0, 'completed' => 0];
            }
    
            if ($status === 'approved') {
                $userHavaleStats[$uId]['approved'] += $requestSize;
            } elseif ($status === 'completed') {
                $userHavaleStats[$uId]['completed'] += $requestSize;
            }
        }
    
        // سالانه
        $yearStartDate = Jalalian::fromFormat('Y/m/d', "$shamsiYear/01/01")->toCarbon()->startOfDay();
        $yearEndDate   = Jalalian::fromFormat('Y/m/d', "$shamsiYear/12/29")->toCarbon()->endOfDay();
    
        $yearlyHavaleUserMap = DB::table('dis_requests')
            ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
            ->whereIn('dis_requests.user_id', $childrenUserIds)
            ->where('dis_request_havales.status', 'completed')
            ->whereBetween('dis_request_havales.created_at', [$yearStartDate, $yearEndDate])
            ->select('dis_requests.user_id', 'dis_request_havales.havale_number')
            ->get();
    
        $yearlyHavaleNumbers = $yearlyHavaleUserMap->pluck('havale_number')->unique();
    
        $yearlyHavaleData = [];
        try {
            if ($yearlyHavaleNumbers->isNotEmpty()) {
                $yearlyHavaleData = DB::connection('sqlsrv')
                    ->table('vw_HavaleData')
                    ->select('havale', DB::raw('SUM(product_MR) as request_size'))
                    ->whereIn('havale', $yearlyHavaleNumbers)
                    ->groupBy('havale')
                    ->get()
                    ->keyBy('havale');
            }
        } catch (\Throwable $e) {
            $connectionIsOk = false;
            $yearlyHavaleData = [];
        }
    
        $yearlyCompletedStats = [];
        $seenYearlyHavales = [];
        foreach ($yearlyHavaleUserMap as $row) {
            $uId = $row->user_id;
            $havaleNumber = $row->havale_number;
            $key = $uId . '_' . $havaleNumber;
    
            if (isset($seenYearlyHavales[$key])) continue;
            $seenYearlyHavales[$key] = true;
    
            $requestSize = isset($yearlyHavaleData[$havaleNumber]) ? floatval($yearlyHavaleData[$havaleNumber]->request_size) : 0;
    
            if (!isset($yearlyCompletedStats[$uId])) {
                $yearlyCompletedStats[$uId] = 0;
            }
            $yearlyCompletedStats[$uId] += $requestSize;
        }
    
        // --- تارگت ماهانه برای همین فرزندان ---
        // ساخت Map از user_id => target برای سال/ماه انتخاب‌شده
        $targetsMap = DB::table('user_targets')
            ->whereIn('user_id', $childrenUserIds)
            ->where('year', $shamsiYear)
            ->where('month', $shamsiMonth)   // در دیتای قبلی شما ماه به صورت '01' ذخیره می‌شد
            ->pluck('target', 'user_id');    // [user_id => target]
    
        // تزریق مقادیر به پروفایل‌ها
        $childrenProfiles = $childrenProfiles->map(function ($child) use ($userHavaleStats, $yearlyCompletedStats, $targetsMap) {
            $uId = optional($child->user)->id;
    
            $reserved  = $userHavaleStats[$uId]['approved']  ?? 0;
            $completed = $userHavaleStats[$uId]['completed'] ?? 0;
            $yearly    = $yearlyCompletedStats[$uId]         ?? 0;
            $target    = (float) ($targetsMap[$uId]          ?? 0);
    
            // افزودن فیلدها به مدل (برای نمایش در Blade)
            $child->reserved_request_size          = $reserved;
            $child->completed_request_size         = $completed;
            $child->yearly_completed_request_size  = $yearly;
            $child->target_month                   = $target;
            $child->target_percent                 = $target > 0 ? ceil(($completed * 100) / $target) : 0;
    
            return $child;
        });
    
        return view('personnel.children', compact(
            'childrenProfiles', 'shamsiYear', 'shamsiMonth', 'userId', 'connectionIsOk'
        ));
    }
    


    public function showUserHavales(Request $request, $userId)
    {
        $now = \Morilog\Jalali\Jalalian::now();
        $shamsiYear = $request->input('year', $now->getYear());
        $shamsiMonth = str_pad($request->input('month', $now->getMonth()), 2, '0', STR_PAD_LEFT);
    
        $startDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', "$shamsiYear/$shamsiMonth/01")->toCarbon()->startOfDay();
        $endDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', "$shamsiYear/$shamsiMonth/01")->addMonths(1)->toCarbon()->startOfDay();
    
        $havaleRaw = DB::table('dis_request_havales')
            ->join('dis_requests', 'dis_request_havales.dis_request_id', '=', 'dis_requests.id')
            ->where('dis_requests.user_id', $userId)
            ->select(
                'dis_request_havales.havale_number',
                'dis_request_havales.status',
                'dis_request_havales.created_at',
                'dis_request_havales.date_target'
            )
            ->get();
    
        $filtered = $havaleRaw->filter(function ($item) use ($startDate, $endDate) {
            $status = strtolower(trim($item->status));
            $date = ($status === 'completed' && $item->date_target) ? $item->date_target : $item->created_at;
            return $date >= $startDate && $date < $endDate;
        });
    
        $havaleNumbers = $filtered->pluck('havale_number')->unique();
    
        $havaleData = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->select('havale', DB::raw('SUM(product_MR) as request_size'))
            ->whereIn('havale', $havaleNumbers)
            ->where('mali', 1)
            ->groupBy('havale')
            ->get()
            ->keyBy('havale');
    
        $userHavaleStats = [];
    
        foreach ($filtered as $item) {
            $havaleNumber = $item->havale_number;
    
            // ✅ جلوگیری از پردازش تکراری
            if (isset($userHavaleStats[$havaleNumber])) continue;
    
            $status = strtolower(trim($item->status));
            if (!isset($havaleData[$havaleNumber])) continue;
    
            $requestSize = floatval($havaleData[$havaleNumber]->request_size);
            $date = ($status === 'completed' && $item->date_target) ? $item->date_target : $item->created_at;
            $jalaliDate = Jalalian::fromCarbon(Carbon::parse($date))->format('Y/m/d');
    
            $userHavaleStats[$havaleNumber][] = [
                'status' => $status,
                'request_size' => $requestSize,
                'date' => $jalaliDate
            ];
        }
    
        return view('personnel.havales', compact('userHavaleStats', 'shamsiYear', 'shamsiMonth'));
    }
    








    
    
    




public function users_with_parents(Request $request, HavaleSyncService $havaleSync)
{
    $havaleSync->sync();

    try {
        \DB::connection('sqlsrv')->getPdo(); // تست اتصال

        $now = Jalalian::now();
        $shamsiYear = $request->input('year', $now->getYear());
        $shamsiMonth = str_pad($request->input('month', $now->getMonth()), 2, '0', STR_PAD_LEFT);

        $startDate = Jalalian::fromFormat('Y/m/d', "$shamsiYear/$shamsiMonth/01")->toCarbon()->startOfDay();
        $endDate = Jalalian::fromFormat('Y/m/d', "$shamsiYear/$shamsiMonth/01")->addMonths(1)->toCarbon()->startOfDay();

        $yearStart = Jalalian::fromFormat('Y/m/d', "$shamsiYear/01/01")->toCarbon()->startOfDay();
        $yearEnd = Jalalian::fromFormat('Y/m/d', "$shamsiYear/12/29")->toCarbon()->endOfDay();

        $distributorUsers = User::where('role', 'distributor')->get();

        $havaleUserMap = DB::table('dis_requests')
            ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
            ->whereIn('dis_requests.user_id', $distributorUsers->pluck('id'))
            ->whereIn('dis_request_havales.status', ['approved', 'completed'])
            ->select(
                'dis_requests.user_id',
                'dis_request_havales.havale_number',
                'dis_request_havales.status',
                'dis_request_havales.created_at',
                'dis_request_havales.date_target'
            )
            ->get();

        $filteredHavales = $havaleUserMap->filter(function ($item) use ($startDate, $endDate) {
            $status = strtolower(trim($item->status));
            $date = ($status === 'completed' && $item->date_target) ? $item->date_target : $item->created_at;
            return $date >= $startDate && $date < $endDate;
        });

        $userHavaleMap = [];
        foreach ($filteredHavales as $item) {
            $userHavaleMap[$item->havale_number] = [
                'user_id' => $item->user_id,
                'status' => strtolower(trim($item->status))
            ];
        }

        $havaleNumbers = array_keys($userHavaleMap);

        $havaleData = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->select('havale', 'product_MR', 'tavali')
            ->whereIn('havale', $havaleNumbers)
            ->where('mali', 1)
            ->get();

        $userHavaleStats = [];
        foreach ($havaleData as $row) {
            $havale = $row->havale;
            $amount = floatval($row->product_MR);

            if (!isset($userHavaleMap[$havale])) continue;

            $userId = $userHavaleMap[$havale]['user_id'];
            $status = $userHavaleMap[$havale]['status'];

            if (!isset($userHavaleStats[$userId])) {
                $userHavaleStats[$userId] = ['approved' => 0, 'completed' => 0];
            }

            if ($status === 'approved' && is_null($row->tavali)) {
                $userHavaleStats[$userId]['approved'] += $amount;
            } elseif ($status === 'completed' && !is_null($row->tavali)) {
                $userHavaleStats[$userId]['completed'] += $amount;
            }
        }

        // سالانه
        $yearHavaleMap = DB::table('dis_requests')
            ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
            ->whereIn('dis_requests.user_id', $distributorUsers->pluck('id'))
            ->whereIn('dis_request_havales.status', ['approved', 'completed'])
            ->whereBetween('dis_request_havales.created_at', [$yearStart, $yearEnd])
            ->select('dis_requests.user_id', 'dis_request_havales.havale_number', 'dis_request_havales.status')
            ->get();

        $yearUserHavaleMap = [];
        foreach ($yearHavaleMap as $item) {
            $yearUserHavaleMap[$item->havale_number] = [
                'user_id' => $item->user_id,
                'status' => strtolower(trim($item->status))
            ];
        }

        $yearHavaleNumbers = array_keys($yearUserHavaleMap);

        $yearHavaleData = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->select('havale', 'product_MR', 'tavali')
            ->whereIn('havale', $yearHavaleNumbers)
            ->where('mali', 1)
            ->get();

        $userYearStats = [];
        foreach ($yearHavaleData as $row) {
            $havale = $row->havale;
            $amount = floatval($row->product_MR);

            if (!isset($yearUserHavaleMap[$havale])) continue;

            $userId = $yearUserHavaleMap[$havale]['user_id'];
            $status = $yearUserHavaleMap[$havale]['status'];

            if (!isset($userYearStats[$userId])) {
                $userYearStats[$userId] = ['approved' => 0, 'completed' => 0];
            }

            if ($status === 'approved' && is_null($row->tavali)) {
                $userYearStats[$userId]['approved'] += $amount;
            } elseif ($status === 'completed' && !is_null($row->tavali)) {
                $userYearStats[$userId]['completed'] += $amount;
            }
        }

        $userDetails = $distributorUsers->map(function ($user) use ($userHavaleStats, $userYearStats, $shamsiYear, $shamsiMonth) {
            $personelId = UserData::where('user_id', $user->id)->pluck('personel_id')->first();
            $personelName = User::where('id', $personelId)->pluck('name')->first();
            $userName = $user->name;

            $target = DB::table('user_targets')
                ->where('user_id', $user->id)
                ->where('year', $shamsiYear)
                ->where('month', $shamsiMonth)
                ->pluck('target')
                ->first();

            return [
                'user_id' => $user->id,
                'user_name' => $userName,
                'personel_id' => $personelId,
                'personel_name' => $personelName,
                'approved_request_size' => round($userHavaleStats[$user->id]['approved'] ?? 0, 2),
                'completed_request_size' => round($userHavaleStats[$user->id]['completed'] ?? 0, 2),
                'approved_year_total' => round($userYearStats[$user->id]['approved'] ?? 0, 2),
                'completed_year_total' => round($userYearStats[$user->id]['completed'] ?? 0, 2),
                'target' => $target ?? 0,
            ];
        });

        return view('admin.users_with_parents', compact('userDetails', 'shamsiYear', 'shamsiMonth'));

    } catch (\Exception $e) {
        $userDetails = null;
        $shamsiYear = null;
        $shamsiMonth = null;
        $error = 'ارتباط با دیتابیس برقرار نیست. لطفاً اتصال خود را بررسی کنید.';

        return view('admin.users_with_parents', compact('userDetails', 'shamsiYear', 'shamsiMonth', 'error'));
    }
}






    public function showPersonnelPage()
    {
        // Fetch all users with role 'personnel'

        $users = User::with(['UserData.city'])->where('role', 'personnel')->get();

        return view('admin.personnel', compact('users'));
    }

    // Update target for a Personnel user
    public function updateTargetPersonnel(Request $request, $id)
    {
        $validated = $request->validate([
            'target' => 'required|numeric',
            'month' => 'required|integer',
            'year' => 'required|integer',
        ]);
    
        $user = User::findOrFail($id);
        
        // Check if the target already exists for the selected month and year
        $existingTarget = UserTarget::where('user_id', $id)
                                    ->where('month', $validated['month'])
                                    ->where('year', $validated['year'])
                                    ->first();
    
        if ($existingTarget) {
            // Update existing target
            $existingTarget->target = $validated['target'];
            $existingTarget->save();
        } else {
            // Create new target entry
            UserTarget::create([
                'user_id' => $id,
                'month' => $validated['month'],
                'year' => $validated['year'],
                'target' => $validated['target'],
            ]);
        }
    
        return redirect()->route('admin.personnel.index')->with('success', 'اطلاعات تارگت با موفقت ویرایش شد');
    }

    public function deleteTarget($id, $targetId)
    {
        // Find the user by id
        $user = User::findOrFail($id);

        // Find the target by id and ensure it belongs to the user
        $target = $user->targets()->findOrFail($targetId);

        // Delete the target
        $target->delete();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Target deleted successfully.');
    }
    

    // Show the Distributor page
    public function showDistributorPage()
    {
        // Fetch all users with role 'distributor'
        $users = User::with(['UserData.city'])->where('role', 'distributor')->get();


        return view('admin.distributor', compact('users'));
    }

    // Update target for a Distributor user
    public function updateTargetDistributor(Request $request, $id)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
            'target' => 'required|numeric',
        ]);
    
        // Insert into the same personnel_target table
        UserTarget::updateOrCreate(
            [
                'user_id' => $id,
                'month' => $validated['month'],
                'year' => $validated['year'],
            ],
            ['target' => $validated['target']]
        );
    
        return redirect()->route('admin.distributor.index')->with('success', 'تارگت نماینده با موفقیت ثبت شد.');
    }
    
    // Delete a target for distributor
    public function deleteTargetDistributor($id, $targetId)
    {
        UserTarget::where('id', $targetId)->delete();
        return redirect()->route('admin.distributor.index')->with('success', 'تارگت حذف شد.');
    }
    



}
