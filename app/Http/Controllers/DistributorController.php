<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserData;
use App\Models\DisRequest;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use App\Models\DisRequestHavale;
use Morilog\Jalali\Jalalian;

class DistributorController extends Controller
{
    public function index()
    {

        try {
            // بررسی اتصال به دیتابیس
            \DB::connection('sqlsrv')->getPdo();


        // دریافت اطلاعات شخص وارد شده
        $user_login = UserData::where('user_id', auth()->user()->id)->first();
        $personel = $user_login ? User::where('id', $user_login->personel_id)->first() : null;
    
        // محاسبه تعداد درخواست‌های تکمیل شده و مجموع متراژ
       // گرفتن تمام درخواست‌های کاربر با وضعیت 'Completed'
       $currentYear = Jalalian::now()->getYear();

       // شروع و پایان سال جاری شمسی به میلادی برای مقایسه با created_at دیتابیس
       $startOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/01/01")->toCarbon()->startOfDay();
       $endOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/12/29")->toCarbon()->endOfDay();
       
       // گرفتن درخواست‌های این کاربر
       $completedRequests = DisRequest::where('user_id', auth()->user()->id)
           ->get();
       
       // استخراج request_id‌ها
       $requestIds = $completedRequests->pluck('id');
       
       // جستجو در جدول حواله‌ها با فیلتر سال جاری شمسی و وضعیت تکمیل شده
       $havaleNumbers = DisRequestHavale::whereIn('dis_request_id', $requestIds)
           ->where('status', 'Completed')
           ->whereBetween('created_at', [$startOfYear, $endOfYear])
           ->pluck('havale_number');
     
       // گرفتن داده‌ها از دیتابیس دوم
       $havaleDataRaw = DB::connection('sqlsrv')
           ->table('vw_HavaleData')
           ->select('havale', DB::raw('SUM(product_MR) as request_size'))
           ->whereIn('havale', $havaleNumbers)
           ->groupBy('havale')
           ->get()
           ->keyBy('havale');
         
       // جمع متراژ حواله‌ها
       $totalCompletedRequests = $havaleDataRaw->sum('request_size') ?: 0;




// گرفتن سال و ماه جاری شمسی
$currentYear = Jalalian::now()->getYear();
$currentMonth = Jalalian::now()->getMonth();

// ماه رو همیشه با دو رقم بساز
$monthFormatted = sprintf('%02d', $currentMonth);

// تاریخ شروع ماه جاری
$startOfMonth = Jalalian::fromFormat('Y/m/d', "$currentYear/$monthFormatted/01")
    ->toCarbon()
    ->startOfDay();

// تاریخ پایان ماه جاری
$endOfMonth = Jalalian::fromFormat('Y/m/d', "$currentYear/$monthFormatted/01")
    ->addMonths(1)
    ->subDays(1)
    ->toCarbon()
    ->endOfDay();

// گرفتن حواله‌های ماه جاری
$havaleNumbersThisMonth = DisRequestHavale::whereIn('dis_request_id', $requestIds)
    ->where('status', 'Completed')
    ->whereBetween('date_target', [$startOfMonth, $endOfMonth])
    ->pluck('havale_number');
// محاسبه مجموع متراژ حواله‌های ماه جاری
$totalCompletedRequestsThisMonth = DB::connection('sqlsrv')
    ->table('vw_HavaleData')
    ->whereIn('havale', $havaleNumbersThisMonth)
    ->sum('product_MR') ?: 0;


       // گرفتن تاریخ شمسی
       $currentJalali = Jalalian::now();
        $currentYear = $currentJalali->getYear();
        $currentMonth = $currentJalali->getMonth();

        // گرفتن تارگت از جدول
        $userTarget = \App\Models\UserTarget::where('user_id', auth()->user()->id)
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->value('target') ?? 0;

        // پاس دادن فقط عدد و شماره ماه
        $userTargetData = [
            'target' => $userTarget,
            'month' => $currentMonth
        ];



        


  

        $totalInProgressHavaleCount = DisRequestHavale::whereIn('dis_request_id', $requestIds)
            ->where('status', 'In Progress')
            ->count() ?: 0;


        $totalOpenRequestsCount = DisRequest::where('user_id', auth()->user()->id)
        ->where('file_path',Null)
            ->where('status','=', 'Pending')->count() ?: 0; // اگر نتیجه خالی بود، 0 نمایش داده شود
    
        // دریافت اطلاعات توزیع‌کنندگان
        $distributors = User::where('role', 'distributor')->get();
        $requestSizes = [];
    
        foreach ($distributors as $distributor) {
            $completedRequests = DisRequest::where('user_id', $distributor->id)
                ->where('status', 'Completed')
                ->where('status', '!=', 'Rejected') // Exclude rejected requests
                ->sum('request_size') ?: 0; // اگر نتیجه خالی بود، 0 نمایش داده شود
    
            $requestSizes[$distributor->id] = $completedRequests;
        }
    
        arsort($requestSizes);
        $topDistributorIds = array_slice(array_keys($requestSizes), 0, 5, true);
    
        // گرفتن اطلاعات 5 توزیع‌کننده برتر
        $topDistributors = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('cities', 'profiles.city_id', '=', 'cities.id')
            ->whereIn('users.id', $topDistributorIds)
            ->select('users.id', 'users.name', 'cities.name as city_name')
            ->get()
            ->map(function ($distributor) use ($requestSizes) {
                $distributor->total_request_size = $requestSizes[$distributor->id];
                return $distributor;
            })
            ->sortByDesc('total_request_size');
    
        // دریافت اطلاعات کاربران فرزند
        $userId = auth()->user()->id;
        $childUsers = UserData::where('user_id', $userId)->get();
        $productSizes = Product::pluck('size')->toArray();
    
        // مقداردهی پیش‌فرض برای اندازه‌ها


        $monthlySizes = [];

// حلقه برای ماه‌های سال
for ($month = 1; $month <= 12; $month++) {
    // تبدیل ماه به فرمت صحیح
    $monthFormatted = sprintf('%02d', $month);

    // تاریخ شروع و پایان ماه جاری شمسی به میلادی
    $startOfMonth = Jalalian::fromFormat('Y/m/d', "$currentYear/$monthFormatted/01")
        ->toCarbon()
        ->startOfDay();
    $endOfMonth = Jalalian::fromFormat('Y/m/d', "$currentYear/$monthFormatted/01")
        ->addMonths(1)
        ->subDays(1)
        ->toCarbon()
        ->endOfDay();

    // گرفتن حواله‌ها برای این ماه
    $havaleNumbersThisMonth = DisRequestHavale::whereIn('dis_request_id', $requestIds)
        ->where('status', 'Completed')
        ->whereBetween('date_target', [$startOfMonth, $endOfMonth])
        ->pluck('havale_number');

    // محاسبه مجموع متراژ حواله‌های این ماه
    $totalRequestSizeForMonth = DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $havaleNumbersThisMonth)
        ->sum('product_MR') ?: 0;

    // ذخیره متراژ ماه در آرایه
    $monthlySizes[$month] = $totalRequestSizeForMonth;
}

// ساخت داده‌های برای نمودار
$monthLabels = range(1, 12);  // ماه‌های سال
$monthlyData = array_map(function ($month) use ($monthlySizes) {
    return $monthlySizes[$month] ?? 0;  // اگر برای ماه داده نباشد، 0 قرار می‌دهیم
}, $monthLabels);




    
        // مدل‌های محصول و تعداد درخواست‌های آنها
        $productModels = Product::pluck('model')->toArray();
        $modelRequestCounts = [];
        
        $allHavaleNumbers = [];
        
        foreach ($childUsers as $userData) {
            $disRequests = DisRequest::where('user_id', $userData->user_id)
                ->where('status', '!=', 'Rejected')
                ->pluck('id')
                ->toArray();
        
            if (!empty($disRequests)) {
                $havaleNumbers = DisRequestHavale::whereIn('dis_request_id', $disRequests)
                      ->where('status','Completed')
                    ->pluck('havale_number')
                    ->toArray();
        
                $allHavaleNumbers = array_merge($allHavaleNumbers, $havaleNumbers);
            }
        }
        
        if (!empty($allHavaleNumbers)) {
        
            $productData = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
                ->select(
                    'product_name',
                    DB::raw('SUM(product_MR) as total_MR')
                )
                ->whereIn('havale', $allHavaleNumbers)
                ->where('mali', '=', 1)
                ->whereNotNull('tavali')
                ->groupBy('product_name')
                ->orderByDesc('total_MR')
                ->limit(5)
                ->get();
        
            $modelLabels = [];
            $modelData = [];
            $colors = ['#ff6384', '#4bc0c0', '#ffcd56', '#c9cbcf', '#36a2eb'];
        
            foreach ($productData as $index => $item) {
        
                $parts = explode(' - ', $item->product_name);
        
                $chart_name  = $parts[3] ?? ''; // طرح
                $chart_color = $parts[4] ?? ''; // رنگ
                $chart_size  = $parts[2] ?? ''; // سایز
        
                $label = "{$chart_name} {$chart_color} {$chart_size}";
        
                $modelLabels[] = $label;
                $modelData[] = round($item->total_MR, 2);
        
                if (!isset($colors[$index])) {
                    $colors[$index] = sprintf("#%06x", rand(0, 0xFFFFFF));
                }
            }
        
        } else {
            $modelLabels = [];
            $modelData = [];
            $colors = [];
        }
        
    
        $productSizes = DB::connection('sqlsrv')
        ->table('vw_HavaleData')
    ->select(
        DB::raw("
            SUBSTRING(
                product_name,
                CHARINDEX('- ', product_name) + 2, 
                CHARINDEX('*', product_name) - CHARINDEX('- ', product_name) - 2
            ) + '*' + 
            SUBSTRING(
                product_name,
                CHARINDEX('*', product_name) + 1, 
                CHARINDEX(' -', product_name, CHARINDEX('*', product_name)) - CHARINDEX('*', product_name) - 1
            ) AS size
        "),
        DB::raw('SUM(product_MR) as total_MR')
    )
    ->whereIn('havale', $allHavaleNumbers)  // لیست حواله‌هایی که قبلاً از جدول‌ها به دست آوردی
    ->where('mali', '=', 1)
    ->whereNotNull('tavali')
    ->groupBy('product_name')
    ->get()
    ->groupBy('size')
    ->map(function ($items) {
        return $items->sum('total_MR');
    })
    ->sortDesc();

$sizeLabels = $productSizes->keys()->toArray();   // لیبل سایزها
$sizeData = $productSizes->values()->toArray();  // مجموع متراژ هر سایز

    

// مرحله 1: گرفتن user_id و request_id از جدول dis_request
$requests = DisRequest::where('status', '!=', 'Rejected')
            ->select('user_id', 'id')
            ->get()
            ->groupBy('user_id');

// مرحله 2: گرفتن شماره حواله برای هر user_id
$userHavaleMap = [];
foreach ($requests as $userId => $userRequests) {
    $requestIds = $userRequests->pluck('id')->toArray();

    $havales = DisRequestHavale::whereIn('dis_request_id', $requestIds)
                ->where('status','Completed')
                ->pluck('havale_number')
                ->toArray();

    $userHavaleMap[$userId] = $havales;
}

// مرحله 3: جمع متراژ حواله‌های تایید شده از دیتابیس دوم
$userTotalMR = [];
foreach ($userHavaleMap as $userId => $havales) {
    if (count($havales) > 0) {
        $totalMR = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $havales)
            ->where('mali', '=', 1)
            ->whereNotNull('tavali')
            ->sum('product_MR');

        if ($totalMR > 0) {
            $userTotalMR[$userId] = $totalMR;
        }
    }
}

// مرحله 4: سورت کردن و انتخاب 5 نفر اول
arsort($userTotalMR);
$topUserIds = array_slice(array_keys($userTotalMR), 0, 5);

// مرحله 5: گرفتن اطلاعات کاربر + شهر
$topDistributors = User::whereIn('id', $topUserIds)
    ->with(['userData.city']) // لود رابطه userData و city
    ->get()
    ->map(function ($user) use ($userTotalMR) {
        return (object)[
            'id' => $user->id,
            'name' => $user->name,
            'city_name' => optional($user->userData->city)->name ?? '---',
            'total_mr' => $userTotalMR[$user->id] ?? 0
        ];
    })
    ->sortByDesc('total_mr');  // در صورت نیاز مجدد مرتب کن

    




        



        $personel = $personel ?? [];
        $totalCompletedRequests = $totalCompletedRequests ?? 0;
        $totalInProgressHavaleCount = $totalInProgressHavaleCount ?? 0;
        $totalOpenRequestsCount = $totalOpenRequestsCount ?? 0;
        $topDistributors = $topDistributors ?? [];
        $requestSizes = $requestSizes ?? [];
        // $sizeLabels = $sizeLabels ?? [];
        $completedData = $completedData ?? [];
        $nonCompletedData = $nonCompletedData ?? [];
        $modelLabels = $modelLabels ?? [];
        $modelData = $modelData ?? [];
        $colors = $colors ?? [];
        $modelLabelsCompleted = $modelLabelsCompleted ?? [];
        $modelDataCompleted = $modelDataCompleted ?? [];
        $colorsCompleted = $colorsCompleted ?? [];
        $userTargetText = $userTargetText ?? '';

        // بازگشت به ویو با داده‌های مختلف
        return view('distributor.index', compact(
            'personel',
            'totalCompletedRequests',
            'totalInProgressHavaleCount',
            'totalOpenRequestsCount',
            'topDistributors',
            'requestSizes',
            // 'sizeLabels',
            'completedData',
            'nonCompletedData',
            'modelLabels',
            'modelData',
            'colors',
            'modelLabelsCompleted',
            'modelDataCompleted',
            'colorsCompleted',
            'userTargetData',
            'totalCompletedRequestsThisMonth',
            'monthLabels',   // ارسال ماه‌ها
            'monthlyData',    // ارسال داده‌های متراژ ماهانه
            'sizeLabels',
            'sizeData',
            'topDistributors'
        ));
    }
        catch (\Exception $e) {
        // اگر خطا بود، همه داده‌ها نال باشه
        $personel = null ;
        $totalCompletedRequests= null ;
            $totalInProgressHavaleCount= null ;
            $totalOpenRequestsCount= null ;
            $topDistributors= null ;
            $requestSizes= null ;
          
            $completedData= null ;
            $nonCompletedData= null ;
            $modelLabels= null ;
            $modelData= null ;
            $colors= null ;
            $modelLabelsCompleted= null ;
            $modelDataCompleted= null ;
            $colorsCompleted= null ;
            $userTargetData= null ;
            $totalCompletedRequestsThisMonth= null ;
            $monthLabels= null ;
            $monthlyData= null ;
            $sizeLabels= null ;
            $sizeData= null ;
            $topDistributors = null ;
           
        $error = 'ارتباط با دیتابیس برقرار نیست. لطفاً اتصال خود را بررسی کنید.';
    
        return view('distributor.index', compact(
            'personel',
            'totalCompletedRequests',
            'totalInProgressHavaleCount',
            'totalOpenRequestsCount',
            'topDistributors',
            'requestSizes',
          
            'completedData',
            'nonCompletedData',
            'modelLabels',
            'modelData',
            'colors',
            'modelLabelsCompleted',
            'modelDataCompleted',
            'colorsCompleted',
            'userTargetData',
            'totalCompletedRequestsThisMonth',
            'monthLabels',   // ارسال ماه‌ها
            'monthlyData',    // ارسال داده‌های متراژ ماهانه
            'sizeLabels',
            'sizeData',
            'topDistributors',
            'error'
        ));
        
        }
        

    }
    
}
