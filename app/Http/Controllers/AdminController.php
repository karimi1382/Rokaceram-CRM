<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DisRequest;
use App\Models\UserData;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\DisRequestHavale;
use App\Services\HavaleSyncService;



use Carbon\Carbon;
use Morilog\Jalali\Jalalian;


class AdminController extends Controller
{
    //

    
    public function index()
    {
    
        try {
            // بررسی اتصال به دیتابیس
            \DB::connection('sqlsrv')->getPdo();
    
    
    
    
    $currentYear = Jalalian::now()->getYear();
    // تبدیل شروع و پایان سال شمسی به میلادی برای فیلتر دیتابیس
    $startOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/01/01")->toCarbon()->startOfDay();
    $endOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/12/29")->toCarbon()->endOfDay();
    // دریافت همه درخواست‌هایی که در سال جاری ساخته شده‌اند
    $requestsInCurrentYear = DisRequest::whereBetween('created_at', [$startOfYear, $endOfYear])
    ->where('file_path',Null)
    ->pluck('id');
    // دریافت همه dis_request_id که در جدول حواله ثبت شده‌اند
    $havaleRequestIds = DisRequestHavale::whereIn('dis_request_id', $requestsInCurrentYear)->pluck('dis_request_id');
    // فیلتر کردن درخواست‌هایی که هنوز حواله ثبت نشده دارند
    
    
    $unconfirmedRequests = $requestsInCurrentYear->diff($havaleRequestIds);
    // تعداد درخواست‌هایی که حواله ندارند
    $totalUnconfirmedCount = $unconfirmedRequests->count();
    
    
    //===================================
    
    $totalInProgressHavale = DisRequestHavale::whereBetween('created_at', [$startOfYear, $endOfYear])
        ->where('status', 'In Progress')
        ->count();
    
    //================================
    
    $approvedHavaleNumbers = DisRequestHavale::whereBetween('created_at', [$startOfYear, $endOfYear])
        ->where('status', 'Approved')
        ->pluck('havale_number')
        ->toArray();
    
    // مرحله 3: اگر حواله‌ای وجود داشت، متراژ رو از دیتابیس دوم حساب کن
    $totalReservedSize = 0;
    
    if (!empty($approvedHavaleNumbers)) {
        $totalReservedSize = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $approvedHavaleNumbers)
            ->where('mali', '=', 1)  // فقط حواله‌های تایید مالی
            ->sum('product_MR');     // جمع متراژ کل
    
    } else {
        $totalReservedSize = 0;
    }
    
    //=====================================
    
    // دریافت حواله‌های با وضعیت Completed در سال جاری
    $approvedHavaleNumbers = DisRequestHavale::where('status', 'Completed')
        ->whereBetween('created_at', [$startOfYear, $endOfYear])
        ->pluck('havale_number')
        ->toArray();
    
    // اگر داده‌ای وجود داشت، در دیتابیس دوم استعلام بزن و جمع متراژ بگیر
    if (!empty($approvedHavaleNumbers)) {
        $totalCompletedSize = \DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $approvedHavaleNumbers)
            ->where('mali', '=', 1)
            ->whereNotNull('tavali')  // شرط جدید: تولی نباید نال باشه
            ->sum('product_MR');
    } else {
        $totalCompletedSize = 0;
    }
    
    //=====================
    
    // گرفتن حواله‌های تایید شده در سال جاری با وضعیت completed
    $completedHavale = DisRequestHavale::where('status', 'Completed')
        ->whereBetween('created_at', [$startOfYear, $endOfYear])
        ->get();
    
    // تبدیل به آرایه ای از حواله‌نامبر‌ها
    $havaleNumbers = $completedHavale->pluck('havale_number')->unique()->toArray();
    
    // دیتای متراژ از دیتابیس دوم: فقط حواله‌های مورد تأیید و تولد ثبت شده
    $havaleData = \DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $havaleNumbers)
        ->where('mali', '=', 1)
        ->whereNotNull('tavali')
        ->get(['havale', 'product_MR']);
    
    // ساخت آرایه ماهیانه متراژ
    $monthlyTotal = array_fill(1, 12, 0);
    
    foreach ($completedHavale as $havale) {
        // استخراج ماه شمسی از تاریخ هدف (date_target)
        $month = Jalalian::fromCarbon(Carbon::parse($havale->date_target))->getMonth();
    
        // جمع متراژ حواله‌های مرتبط
        $relatedHavale = $havaleData->where('havale', $havale->havale_number);
        $sumMR = $relatedHavale->sum('product_MR');
    
        // افزودن به ماه مناسب
        $monthlyTotal[$month] += $sumMR;
    }
    
    
    // آماده‌سازی برای ارسال به ویو
    $monthLabels = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
    $soldMetersData = array_values($monthlyTotal);
    
    
    //==================================
    
    $havaleNumbers = DB::table('dis_request_havales')
        ->where('status', 'Completed')
        ->whereBetween('created_at', [$startOfYear, $endOfYear])
        ->pluck('havale_number')
        ->toArray();
    
        $soldProducts = DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $havaleNumbers)
        ->where('mali', 1)
        ->whereNotNull('tavali')
        ->select('product_name', DB::raw('SUM(product_MR) as total_MR'))
        ->groupBy('product_name')
        ->orderByDesc('total_MR')
        ->limit(5)
        ->get();
    
    
        $productLabels = [];
        $productData = [];
        foreach ($soldProducts as $product) {
            $nameParts = explode(' - ', $product->product_name);
        
            $brand = $nameParts[3] ?? '';
            $color = $nameParts[4] ?? '';
            $size  = $nameParts[2] ?? '';
            $grade = $nameParts[1] ?? '';
        
            // حالت قبل با توضیحات کامل
            $productLabels[] = "$brand - $color - $size - $grade";
            $productData[] = round($product->total_MR, 2) ;
        }
        
    
    
    //=========================================
    
    $havaleNumbers = DB::table('dis_request_havales')
        ->where('status', 'Completed')
        ->whereBetween('created_at', [$startOfYear, $endOfYear])
        ->pluck('havale_number')
        ->toArray();
    
    // دیتای محصولات فروخته‌شده با شرایط داده‌شده
    $soldProducts = DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $havaleNumbers)
        ->where('mali', 1)
        ->whereNotNull('tavali')
        ->select('product_name', DB::raw('SUM(product_MR) as total_MR'))
        ->groupBy('product_name')
        ->get();
    
    // آماده‌سازی دیتا بر اساس سایز
    $sizeData = [];
    
    foreach ($soldProducts as $product) {
        $nameParts = explode(' - ', $product->product_name);
        $size = $nameParts[2] ?? 'نامشخص';  // استخراج سایز
    
        if (!isset($sizeData[$size])) {
            $sizeData[$size] = 0;
        }
        $sizeData[$size] += $product->total_MR;
    }
    
    // مرتب‌سازی سایزها براساس متراژ (اختیاری)
    // arsort($sizeData); 
    
    // آماده‌سازی برای ویو
    $sizeLabels = array_keys($sizeData);
    $sizeValues = array_map(function($value) {
        return round($value, 2);
    }, array_values($sizeData));
    
    
    //==============================
    
    // داده‌ها را از جدول نمایندگان و متراژ خریداری شده دریافت می‌کنیم
    $distributors = DB::table('users')
        ->join('profiles', 'users.id', '=', 'profiles.user_id')
        ->join('cities', 'profiles.city_id', '=', 'cities.id')
        ->where('users.role', 'distributor')
        ->select('users.id', 'users.name', 'cities.name as city_name')
        ->get();
    
    // آماده کردن لیست آی‌دی‌ها برای درخواست‌ها
    $distributorIds = $distributors->pluck('id')->toArray();
    
    // گرفتن حواله‌های تایید شده در سال جاری برای هر نماینده
    $completedHavaleNumbers = DB::table('dis_requests')
        ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
        ->whereIn('dis_requests.user_id', $distributorIds)
        ->where('dis_request_havales.status', 'Completed')
        ->whereBetween('dis_request_havales.created_at', [$startOfYear, $endOfYear])
        ->select('dis_requests.user_id', 'dis_request_havales.havale_number')
        ->get();
    
    // گروه‌بندی حواله‌ها بر اساس نماینده
    $havaleMap = [];
    foreach ($completedHavaleNumbers as $item) {
        $havaleMap[$item->user_id][] = $item->havale_number;
    }
    
    // دیتای متراژ از دیتابیس دوم
    $allHavaleNumbers = collect($havaleMap)->flatten()->unique()->toArray();
    
    $havaleData = DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $allHavaleNumbers)
        ->where('mali', 1)
        ->whereNotNull('tavali')
        ->select('havale', 'product_MR')
        ->get();
    
    // جمع متراژ برای هر نماینده
    $requestSizes = [];
    foreach ($havaleMap as $userId => $havaleList) {
        $total = $havaleData->whereIn('havale', $havaleList)->sum('product_MR');
        $requestSizes[$userId] = round($total, 2);
    }
    
    // فیلتر نمایندگان با خرید واقعی
    $topDistributors = $distributors->filter(function ($distributor) use ($requestSizes) {
        return isset($requestSizes[$distributor->id]) && $requestSizes[$distributor->id] > 0;
    })->sortByDesc(function ($distributor) use ($requestSizes) {
        return $requestSizes[$distributor->id];
    })->take(5);
    
    // ترکیب اطلاعات نمایندگان و متراژ خریداری شده
    $topDistributorsWithSizes = $topDistributors->map(function ($distributor) use ($requestSizes) {
        $distributor->product_mr = $requestSizes[$distributor->id] ?? 0; // افزودن متراژ به هر نماینده
        return $distributor;
    });
    
    //=====================================
    
    
    
    
    
        return view('manager.index', compact(
            
    
    
    
    
            'totalUnconfirmedCount',
            'totalInProgressHavale',
            'totalReservedSize',
            'totalCompletedSize',
            'monthLabels', 'soldMetersData',
            'productLabels', 'productData',
            'sizeLabels', 'sizeValues',
            'topDistributorsWithSizes'
        ));
    
        
    } catch (\Exception $e) {
        // اگر خطا بود، همه داده‌ها نال باشه
        $totalUnconfirmedCount = null;
        $totalInProgressHavale = null;
        $totalReservedSize = null;
        $totalCompletedSize = null;
        $monthLabels = null;
        $soldMetersData = null;
        $productLabels = null;
        $productData = null;
        $sizeLabels = null;
        $sizeValues = null;
        $topDistributorsWithSizes = null;
       
        $error = 'ارتباط با دیتابیس برقرار نیست. لطفاً اتصال خود را بررسی کنید.';
    
        return view('manager.index', compact(
            
            'totalUnconfirmedCount',
            'totalInProgressHavale',
            'totalReservedSize',
            'totalCompletedSize',
            'monthLabels', 'soldMetersData',
            'productLabels', 'productData',
            'sizeLabels', 'sizeValues',
            'topDistributorsWithSizes',
            'error'
        ));
    
    }
    }
    
    
    
    public function personneltargetshow(){
        $personnel = User::where('role', 'personnel')
        ->with(['children.user' => function ($query) {
            $query->withSum(['disRequests as total_request_size' => function ($query) {
                $query->where('status', 'completed');
            }], 'request_size');
        }])
        ->get();

      

    return view('admin.personneltargetshow', compact('personnel'));
    }



    public function personneltargetshowdetile($id)
{
    $personnel = User::findOrFail($id);

    // Fetch children and calculate their total request size
    $children = $personnel->children()
        ->with(['user' => function ($query) {
            $query->withSum(['disRequests as total_request_size' => function ($query) {
                $query->where('status', 'completed');
            }], 'request_size')->with('userData.city');;
        }])
        ->get();

    return view('admin.personneltargetshowdetile', compact('personnel', 'children'));
}











    /**
     * نمایش تمام حواله‌های ثبت‌شده
     */
    public function allHavale(HavaleSyncService $havaleSync)
{
    $havaleSync->sync();
    // گرفتن داده‌ها از جدول حواله اصلی + جوین با dis_requests برای گرفتن اطلاعات یوزر
    $havales = DB::table('dis_request_havales')
    ->join('dis_requests', 'dis_request_havales.dis_request_id', '=', 'dis_requests.id')
    ->join('users', 'dis_requests.user_id', '=', 'users.id')
    ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
    ->leftJoin('cities', 'profiles.city_id', '=', 'cities.id')
    ->where('dis_request_havales.status', '!=', 'Completed')
    ->select(
        'dis_request_havales.*',
        'dis_requests.user_id',
        'users.name as user_name',
        'cities.name as city_name',
        'dis_requests.created_at as request_created_at'
    )
    ->orderBy('dis_request_havales.created_at', 'desc')
    ->get();

    // گروه‌بندی بر اساس شماره حواله
    $uniqueRequests = $havales->groupBy('havale_number');

    $remainingDaysArray = [];

    foreach ($uniqueRequests as $havaleNumber => $requestsForHavale) {
        $request = $requestsForHavale->first(); // گرفتن اولین رکورد از هر گروه

        // تلاش برای گرفتن اطلاعات از سرور SQL (vw_HavaleData)
        try {
            $havaleData = DB::connection('sqlsrv')->select("SELECT * FROM vw_HavaleData WHERE havale = ?", [$request->havale_number]);
            $havaleData = $havaleData ? $havaleData[0] : null;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'ارتباط با سرور موقتا قطع می‌باشد.');
        }

        // محاسبه روزهای باقی‌مانده
        $remainingDays = null;
        if ($request->status == 'In Progress') {
            $createdAt = Carbon::parse($request->created_at);
            $deadline = $createdAt->addDays(30);
            $remainingDays = Carbon::now()->diffInDays($deadline, false);
            $remainingDays = $remainingDays > 0 ? $remainingDays : 0;
        }

        $remainingDaysArray[$request->id] = $remainingDays;

        // تبدیل تاریخ به شمسی
        $request->jalali_created_at = $request->created_at
            ? Jalalian::fromCarbon(Carbon::parse($request->created_at))->format('Y/m/d')
            : 'N/A';
    }

    

    return view('admin.havale_all', compact('uniqueRequests', 'remainingDaysArray'));
}



    /**
     * نمایش حواله‌های تکمیل‌شده
     */
    public function completedHavale(Request $request , HavaleSyncService $havaleSync)
    {
        $havaleSync->sync();
        // 1. دریافت پارامترهای فیلتر یا مقدار پیش‌فرض (سال و ماه)
        $year = $request->query('year');
        $month = $request->query('month');
    
        // تاریخ امروز به صورت جلالی
        $now = Jalalian::now();
    
        if (!$year || !$month) {
            $year = $now->getYear();
            $month = $now->getMonth();
        }
    
        $year = (int)$year;
        $month = (int)$month;
    
        // 2. پیدا کردن سال حداقل و حداکثر از داده‌ها (تاریخ ثبت درخواست‌ها)
        // فرض می‌کنیم ستون created_at در دیتابیس به میلادی هست و تاریخ‌ها رو تبدیل می‌کنیم به سال شمسی
    
        $minCreatedAt = DB::table('dis_request_havales')->min('created_at');
        $maxCreatedAt = DB::table('dis_request_havales')->max('created_at');
    
        $minYear = $minCreatedAt ? Jalalian::fromCarbon(\Carbon\Carbon::parse($minCreatedAt))->getYear() : $year;
        $maxYear = $maxCreatedAt ? Jalalian::fromCarbon(\Carbon\Carbon::parse($maxCreatedAt))->getYear() : $year;
    
        // 3. تعیین بازه سال‌ها: دو سال قبل از minYear تا دو سال بعد از maxYear
        $startYear = $minYear - 2;
        $endYear = $maxYear + 2;
    
        // آرایه سال‌ها (نزولی)
        $years = range($endYear, $startYear);
    
        // 4. آرایه ماه‌ها
        $months = [
            1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
            5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
            9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
        ];
    
        // 5. محاسبه بازه تاریخ برای فیلتر کوئری
        $startDate = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/01', $year, $month))->toCarbon()->startOfDay();
        $endDate = (clone $startDate)->addMonth()->startOfDay();
    
        // 6. کوئری گرفتن داده‌ها با فیلتر تاریخ
        $havales = DB::table('dis_request_havales')
            ->join('dis_requests', 'dis_request_havales.dis_request_id', '=', 'dis_requests.id')
            ->join('users', 'dis_requests.user_id', '=', 'users.id')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->leftJoin('cities', 'profiles.city_id', '=', 'cities.id')
            ->where('dis_request_havales.status', 'Completed')
            ->whereBetween('dis_request_havales.created_at', [$startDate, $endDate])
            ->select(
                'dis_request_havales.*',
                'dis_requests.user_id',
                'users.name as user_name',
                'cities.name as city_name',
                'dis_requests.created_at as request_created_at'
            )
            ->orderBy('dis_request_havales.created_at', 'desc')
            ->get();
    
        // گروه‌بندی بر اساس شماره حواله
        $uniqueRequests = $havales->groupBy('havale_number');
    
        foreach ($uniqueRequests as $havaleNumber => $requestsForHavale) {
            $request = $requestsForHavale->first();
    
            // تبدیل تاریخ به شمسی برای نمایش
            $request->jalali_created_at = $request->created_at
                ? Jalalian::fromCarbon(\Carbon\Carbon::parse($request->created_at))->format('Y/m/d')
                : 'N/A';
        }
    
        // ارسال داده‌ها به ویو
        return view('admin.havale_completed', compact('uniqueRequests', 'months', 'years', 'month', 'year'));
    }
    


}
