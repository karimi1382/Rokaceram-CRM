<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DisRequest;
use App\Models\UserData;
use App\Models\Product;
use App\Models\DisRequestHavale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class PersonnelController extends Controller
{
    public function index()
    {


        try {
            // بررسی اتصال به دیتابیس
            \DB::connection('sqlsrv')->getPdo();

        $currentYear = Jalalian::now()->getYear();

        // شروع و پایان سال جاری شمسی به میلادی برای مقایسه با created_at دیتابیس
        $startOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/01/01")->toCarbon()->startOfDay();
        $endOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/12/29")->toCarbon()->endOfDay();
        
        
        
                // مرحله 1: کد کاربر لاگین شده
        $loggedUserId = auth()->user()->id;

        // مرحله 2: پیدا کردن user_id نماینده‌ها از جدول پروفایل‌ها
        $agentUserIds = \DB::table('profiles')
            ->where('personel_id', $loggedUserId)
            ->pluck('user_id');

        // مرحله 3: پیدا کردن درخواست‌های ثبت شده توسط این نماینده‌ها
        $requestIds = \DB::table('dis_requests')
            ->whereIn('user_id', $agentUserIds)
            ->pluck('id');

        // مرحله 4: شمارش حواله‌هایی که وضعیتشون "در حال پیشرفت" هست
        $In_Progress_request = \DB::table('dis_request_havales')
            ->whereIn('dis_request_id', $requestIds)
            ->where('status', 'In Progress')
            ->count();

            // گرفتن شماره حواله‌هایی که وضعیتشون Approved هست
        $Approved_request_numbers = \DB::table('dis_request_havales')
        ->whereIn('dis_request_id', $requestIds)
        ->where('status', 'Approved')
        ->pluck('havale_number');
        // گرفتن مجموع متراژ از دیتابیس دوم برای شماره حواله‌هایی که تایید شده‌اند
        $approvedMeter = \DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $Approved_request_numbers)
        ->sum('product_MR');
           
        $user = Auth::user();
        $userId = $user->id ?? null;

        // $openRequests = DisRequest::whereHas('user.UserData', function ($query) use ($userId) {
        //     $query->where('personel_id', $userId);
        // })
        // ->whereDoesntHave('havales')  // فرض بر اینه که رلیشن تعریف شده: DisRequest -> havales
        // ->count();
     
        $personelId = auth()->id(); // آی‌دی پرسنلی کاربر لاگین‌شده

        // گرفتن تمام user_idهایی که personel_idشون برابر با این کاربره
        $userIds = \App\Models\UserData::where('personel_id', $personelId)->pluck('user_id');
        
        // حالا استفاده از اون user_idها در DisRequest
        $openRequests = \App\Models\DisRequest::whereIn('user_id', $userIds)
        ->where('file_path'  ,null)
            ->whereDoesntHave('havales') // ریکوئست‌هایی که هیچ حواله‌ای ندارن
            ->count();
           // dd($openRequests);
        
        
        
    // dd($openRequests);

        $completed_request_numbers = \DB::table('dis_request_havales')
        ->whereIn('dis_request_id', $requestIds)
        ->where('status', 'Completed')
        ->pluck('havale_number');


        // گرفتن مجموع متراژ از دیتابیس دوم برای شماره حواله‌هایی که تایید شده‌اند
        $completed_MR = \DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $completed_request_numbers)
        ->where('mali', '=', 1)
        ->whereNotNull('tavali')
        ->sum('product_MR');





   
        // مرحله اول: تعیین سال و ماه جاری (قبل از هر شرطی)
        $currentYear = Jalalian::now()->getYear();
        $currentMonth = Jalalian::now()->getMonth();
        $monthFormatted = sprintf('%02d', $currentMonth);
        
        $startOfMonth = Jalalian::fromFormat('Y/m/d', "$currentYear/$monthFormatted/01")
            ->toCarbon()
            ->startOfDay();
        
        $endOfMonth = Jalalian::fromFormat('Y/m/d', "$currentYear/$monthFormatted/01")
            ->addMonths(1)
            ->subDays(1)
            ->toCarbon()
            ->endOfDay();
        
        // مرحله دوم: گرفتن ID نماینده‌ها
        $agentIds = \DB::table('profiles')
            ->where('personel_id', $userId)
            ->pluck('user_id')
            ->toArray();
        
        $soldMeterInThisMonth = 0; // پیش‌فرض
        $userTarget = 0;           // پیش‌فرض
        
        if (!empty($agentIds)) {
        
            // گرفتن درخواست‌ها
            $requestIds = \DB::table('dis_requests')
                ->whereIn('user_id', $agentIds)
                ->pluck('id')
                ->toArray();
        
            if (!empty($requestIds)) {
        
                // حواله‌های تکمیل‌شده
                $completedHavale = \DB::table('dis_request_havales')
                    ->whereIn('dis_request_id', $requestIds)
                    ->where('status', 'Completed')
                    ->get();
        
                // فیلتر تاریخ بر اساس ماه جاری
                $filteredHavale = $completedHavale->filter(function ($item) use ($startOfMonth, $endOfMonth) {
                    return Carbon::parse($item->created_at)->between($startOfMonth, $endOfMonth);
                });
        
                // گرفتن شماره حواله‌ها
                $havaleNumbers = $filteredHavale->pluck('havale_number')->filter()->unique()->toArray();
        
                if (!empty($havaleNumbers)) {
                    // جمع متراژ از دیتابیس دوم
                    $totalMeter = \DB::connection('sqlsrv')
                        ->table('vw_HavaleData')
                        ->whereIn('havale', $havaleNumbers)
                        ->sum('product_MR');
        
                    $soldMeterInThisMonth = $totalMeter;
                }
            }
        }
        
        // تارگت کاربر در سال و ماه جاری
        $userTarget = \App\Models\UserTarget::where('user_id', auth()->user()->id)
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->value('target') ?? 0;
        
       
        


     
           


        // مرحله اول: گرفتن شناسه یوزر لاگین کرده
        // مرحله دوم: بررسی اینکه سرپرست هست یا نماینده
        // اگر توی جدول profiles شخصی با personel_id برابر با این یوزر وجود داشت → یعنی سرپرسته
        $agentIds = \DB::table('profiles')
            ->where('personel_id', $userId)
            ->pluck('user_id')
            ->toArray();

            // اینجا سرپرسته و باید ریکویست‌های زیردست‌هاش رو بیاره
            $requestIds = \DB::table('dis_requests')
                ->whereIn('user_id', $agentIds)
                ->pluck('id')
                ->toArray();
       
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
                    ->whereBetween('created_at', [$startOfYear, $endOfYear]) // فیلتر سال جاری

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




               




                $childUsers = UserData::where('personel_id', $userId)->get();
                $allHavaleNumbers = [];
        
                foreach ($childUsers as $userData) {
                    $disRequests = DisRequest::where('user_id', $userData->user_id)
                        ->where('status', '!=', 'Rejected')
                        ->pluck('id')
                        ->toArray();
                
                    if (!empty($disRequests)) {
                        $havaleNumbers = DisRequestHavale::whereIn('dis_request_id', $disRequests)
                        ->whereBetween('created_at', [$startOfYear, $endOfYear]) // فیلتر سال جاری

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
                
                        $modelLabels_2[] = $label;
                        $modelData_2[] = round($item->total_MR, 2);
                
                        if (!isset($colors[$index])) {
                            $colors[$index] = sprintf("#%06x", rand(0, 0xFFFFFF));
                        }
                    }
                
                } else {
                    $modelLabels_2 = [];
                    $modelData_2 = [];
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
        



        




// تعیین بازه سال شمسی جاری



// مرحله 1: گرفتن user_id و request_id از جدول dis_request
$requests = DisRequest::where('status', '!=', 'Rejected')
    ->select('user_id', 'id')
    ->get()
    ->groupBy('user_id');

// مرحله 2: گرفتن شماره حواله برای هر user_id فقط در سال جاری
$userHavaleMap = [];

foreach ($requests as $userId => $userRequests) {
    $requestIds = $userRequests->pluck('id')->toArray();

    $havales = DisRequestHavale::whereIn('dis_request_id', $requestIds)
        ->where('status', 'Completed')
        ->whereBetween('created_at', [$startOfYear, $endOfYear]) // فیلتر سال جاری
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
    ->with(['userData.city'])
    ->get()
    ->map(function ($user) use ($userTotalMR) {
        return (object)[
            'id' => $user->id,
            'name' => $user->name,
            'city_name' => optional($user->userData->city)->name ?? '---',
            'total_mr' => $userTotalMR[$user->id] ?? 0
        ];
    })
    ->sortByDesc('total_mr');



    
      
    
      
   
    //dd( $userId);
        $childUsers = UserData::where('personel_id', $loggedUserId)->get();
       // dd($childUsers);
        $childCount = $childUsers->count();
    
    
    
       
      
    
        return view('personnel.index', compact(
       
            
           
         

            'In_Progress_request',
            'approvedMeter',
            'openRequests',
            'completed_MR',
            'childCount',
            'currentYear',
            'currentMonth',
            'soldMeterInThisMonth',
            'userTarget',
            'monthLabels',
            'monthlyData',

            'modelLabels_2',
            'modelData_2',
            'colors',
            'sizeLabels',
            'sizeData',

            'topDistributors'

        ));

    } catch (\Exception $e) {
        // اگر خطا بود، همه داده‌ها نال باشه
        $In_Progress_request = null;
        $approvedMeter = null;
        $openRequests = null;
        $completed_MR = null;
        $childCount = null;
        $currentYear = null;
        $currentMonth = null;
        $soldMeterInThisMonth = null;
        $userTarget = null;
        $monthLabels = null;
        $monthlyData = null;
        $modelLabels_2 = null;
        $modelData_2 = null;
        $colors = null;
        $sizeLabels = null;
        $sizeData = null;
        $topDistributors = null;
        $error = 'ارتباط با دیتابیس برقرار نیست. لطفاً اتصال خود را بررسی کنید.';
    
        return view('personnel.index', compact(
            'In_Progress_request',
            'approvedMeter',
            'openRequests',
            'completed_MR',
            'childCount',
            'currentYear',
            'currentMonth',
            'soldMeterInThisMonth',
            'userTarget',
            'monthLabels',
            'monthlyData',
            'modelLabels_2',
            'modelData_2',
            'colors',
            'sizeLabels',
            'sizeData',
            'topDistributors',
            'error'
        ));

    }
    

}}
