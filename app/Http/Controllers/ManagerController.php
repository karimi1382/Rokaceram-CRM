<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\DisRequest;
use App\Models\UserData;
use App\Models\Product;
use App\Models\DisRequestHavale;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use App\Services\HavaleSyncService;
use Carbon\Carbon;
use App\Models\UserTarget; // 👈 اینو اضافه کن
use Morilog\Jalali\CalendarUtils;







class ManagerController extends Controller
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


// گرفتن شماره‌های havale_number از دیتابیس خود
$totalInProgressHavale = 0;

// گرفتن شماره‌های havale_number از دیتابیس خود و گروه‌بندی بر اساس شماره حواله
$havaleNumbers = DisRequestHavale::whereBetween('created_at', [$startOfYear, $endOfYear])
    ->where('status', 'In Progress')
    ->pluck('havale_number'); // فقط شماره‌ها را می‌گیریم

// گروه‌بندی شماره‌ها بر اساس مقدار havale_number
$groupedHavales = $havaleNumbers->groupBy(function ($item) {
    return $item; // گروه‌بندی براساس مقدار havale_number
});

// یکبار کانکشن به دیتابیس مرجع می‌زنیم
try {
    $havaleDataFromSql = DB::connection('sqlsrv')->table('vw_HavaleData')->select('havale')->whereIn('havale', $havaleNumbers)->get();

    // تبدیل داده‌ها به یک آرایه ساده برای جستجو سریع
    $existingHavales = $havaleDataFromSql->pluck('havale')->toArray();

    // شمارش تعداد درخواست‌ها که در دیتابیس مرجع موجود است
    foreach ($groupedHavales as $havaleNumber => $group) {
        // فقط یک بار برای هر شماره حواله چک می‌کنیم
        if (in_array($havaleNumber, $existingHavales)) {
            $totalInProgressHavale++;
        }
    }

} catch (\Exception $e) {
    // در صورتی که مشکلی در اتصال به دیتابیس مرجع باشد
    return redirect()->back()->with('error', 'ارتباط با دیتابیس مرجع موقتا قطع شده است.');
}

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
    ->whereBetween('date_target', [$startOfYear, $endOfYear])
    ->pluck('havale_number')
    ->toArray();

if (!empty($approvedHavaleNumbers)) {
    $havaleData = \DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->select('havale', 'product_MR', 'tavali')
        ->whereIn('havale', $approvedHavaleNumbers)
        ->where('mali', '=', 1)
        ->get()
        ->groupBy('havale');

    $totalCompletedSize = 0;
    foreach ($havaleData as $records) {
        foreach ($records as $row) {
            if (!is_null($row->tavali)) {
                $totalCompletedSize += floatval($row->product_MR);
            }
        }
    }
} else {
    $totalCompletedSize = 0;
}
//=====================

$completedHavale = DisRequestHavale::where('status', 'Completed')
    ->whereBetween('date_target', [$startOfYear, $endOfYear])
    ->get(['havale_number', 'date_target']);

$havaleNumbers = $completedHavale->pluck('havale_number')->unique()->toArray();

$havaleData = \DB::connection('sqlsrv')
    ->table('vw_HavaleData')
    ->select('havale', 'product_MR', 'tavali')
    ->whereIn('havale', $havaleNumbers)
    ->where('mali', '=', 1)
    ->get()
    ->groupBy('havale');

$monthlyTotal = array_fill(1, 12, 0);

foreach ($havaleData as $havale => $records) {
    $related = $completedHavale->firstWhere('havale_number', $havale);

    if ($related && $related->date_target) {
        $month = Jalalian::fromCarbon(Carbon::parse($related->date_target))->getMonth();

        foreach ($records as $row) {
            if (!is_null($row->tavali)) {
                $monthlyTotal[$month] += floatval($row->product_MR);
            }
        }
    }
}

$monthLabels = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];

$soldMetersData = array_map(function ($value) {
    return round($value, 2);
}, array_values($monthlyTotal));

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

    return view('personnel.index', compact(
        
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

    



public function personneltargetshow(Request $request, HavaleSyncService $havaleSync)
{
    $havaleSync->sync();

    try {
        DB::connection('sqlsrv')->getPdo();

        $shamsiYear = $request->input('year') ?? Jalalian::now()->getYear();
        $shamsiMonth = $request->input('month') ?? Jalalian::now()->getMonth();

        $startDate = Jalalian::fromFormat('Y/m/d', "$shamsiYear/" . str_pad($shamsiMonth, 2, '0', STR_PAD_LEFT) . "/01")->toCarbon();
        $lastDay = Jalalian::fromFormat('Y/m/d', "$shamsiYear/" . str_pad($shamsiMonth, 2, '0', STR_PAD_LEFT) . "/01")->getMonthDays();
        $endDate = Jalalian::fromFormat('Y/m/d', "$shamsiYear/" . str_pad($shamsiMonth, 2, '0', STR_PAD_LEFT) . "/$lastDay")->toCarbon();

        $personnels = User::where('role', 'personnel')->get();

        $results = $personnels->map(function ($personnel) use ($startDate, $endDate, $shamsiYear, $shamsiMonth) {

            $childUserIds = UserData::where('personel_id', $personnel->id)->pluck('user_id');
            $childrenCount = $childUserIds->count();

            $havaleUserMap = DB::table('dis_requests')
                ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
                ->whereIn('dis_requests.user_id', $childUserIds)
                ->where('dis_request_havales.status', 'completed')
                ->select('dis_request_havales.havale_number', 'dis_request_havales.date_target')
                ->get();

            $filteredHavales = $havaleUserMap->filter(function ($item) use ($startDate, $endDate) {
                $date = $item->date_target;
                return $date && $date >= $startDate && $date <= $endDate;
            });

            $havaleNumbers = $filteredHavales->pluck('havale_number')->unique();

            $havaleData = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->select('havale', 'product_MR', 'tavali')
                ->whereIn('havale', $havaleNumbers)
                ->where('mali', 1)
                ->get()
                ->groupBy('havale');

            $totalApproved = 0;
            $totalCompleted = 0;
            $usedHavales = [];

            foreach ($filteredHavales as $item) {
                $havale = $item->havale_number;
                if (in_array($havale, $usedHavales)) continue;
                $usedHavales[] = $havale;

                if (!isset($havaleData[$havale])) continue;

                foreach ($havaleData[$havale] as $row) {
                    $size = floatval($row->product_MR);
                    if (is_null($row->tavali)) {
                        $totalApproved += $size;
                    } else {
                        $totalCompleted += $size;
                    }
                }
            }

            $target = UserTarget::where('user_id', $personnel->id)
                ->where('year', $shamsiYear)
                ->where('month', $shamsiMonth)
                ->value('target');

            $yearlyTarget = UserTarget::where('user_id', $personnel->id)
                ->where('year', $shamsiYear)
                ->sum('target');

            $yearStart = Jalalian::fromFormat('Y/m/d', "$shamsiYear/01/01")->toCarbon()->startOfDay();
            $yearEnd = Jalalian::fromFormat('Y/m/d', "$shamsiYear/12/29")->toCarbon()->endOfDay();

            $yearlyHavaleUserMap = DB::table('dis_requests')
                ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
                ->whereIn('dis_requests.user_id', $childUserIds)
                ->where('dis_request_havales.status', 'completed')
                ->select('dis_request_havales.havale_number', 'dis_request_havales.date_target')
                ->get();

            $filteredYearlyHavales = $yearlyHavaleUserMap->filter(function ($item) use ($yearStart, $yearEnd) {
                $date = $item->date_target;
                return $date && $date >= $yearStart && $date <= $yearEnd;
            });

            $yearlyHavaleNumbers = $filteredYearlyHavales->pluck('havale_number')->unique();

            $yearlyHavaleData = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->select('havale', 'product_MR', 'tavali')
                ->whereIn('havale', $yearlyHavaleNumbers)
                ->where('mali', 1)
                ->get()
                ->groupBy('havale');

            $totalCompletedYearly = 0;
            $usedYearlyHavales = [];

            foreach ($filteredYearlyHavales as $item) {
                $havale = $item->havale_number;
                if (in_array($havale, $usedYearlyHavales)) continue;
                $usedYearlyHavales[] = $havale;

                if (!isset($yearlyHavaleData[$havale])) continue;

                foreach ($yearlyHavaleData[$havale] as $row) {
                    if (!is_null($row->tavali)) {
                        $totalCompletedYearly += floatval($row->product_MR);
                    }
                }
            }

            return [
                'personnel_id' => $personnel->id,
                'personnel_name' => $personnel->name,
                'children_count' => $childrenCount,
                'approved_total' => $totalApproved,
                'completed_total' => $totalCompleted,
                'target' => $target ?? 0,
                'yearly_target' => $yearlyTarget,
                'yearly_completed_total' => $totalCompletedYearly,
            ];
        });

        return view('admin.personneltargetshow', compact('results', 'shamsiYear', 'shamsiMonth'));

    } catch (\Exception $e) {
        $results = null;
        $shamsiYear = null;
        $shamsiMonth = null;
        $error = 'ارتباط با دیتابیس برقرار نیست. لطفاً اتصال خود را بررسی کنید.';

        return view('admin.personneltargetshow', compact('results', 'shamsiYear', 'shamsiMonth', 'error'));
    }
}

public function personneltargetshowdetile(Request $request, $id)
{
    $year = $request->get('year');
    $month = $request->get('month');

    if (!$year || !$month) {
        abort(400, 'اطلاعات سال یا ماه ناقص است.');
    }

    $month = str_pad($month, 2, '0', STR_PAD_LEFT);

    $startDate = Jalalian::fromFormat('Y/m/d', "$year/$month/01")->toCarbon()->startOfDay();
    $endDate = Jalalian::fromFormat('Y/m/d', "$year/$month/01")->addMonths(1)->toCarbon()->startOfDay();

    $parentUser = User::findOrFail($id);
    $childrenProfiles = $parentUser->childrenProfiles()->with(['user', 'city'])->get();

    $personelId = UserData::where('user_id', $id)->pluck('user_id')->first();

    // دریافت حواله‌ها با شناسه کاربری فرزندان
    $havaleUserMap = DB::table('dis_requests')
        ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
        ->whereIn('dis_request_havales.status', ['approved', 'completed'])
        ->whereIn('dis_requests.user_id', function ($query) use ($personelId) {
            $query->select('user_id')->from('profiles')->where('personel_id', $personelId);
        })
        ->select(
            'dis_requests.user_id',
            'dis_request_havales.havale_number',
            'dis_request_havales.status',
            'dis_request_havales.created_at',
            'dis_request_havales.date_target'
        )
        ->get();

    // فیلتر بر اساس تاریخ مناسب هر وضعیت
    $filteredHavales = $havaleUserMap->filter(function ($item) use ($startDate, $endDate) {
        $status = strtolower(trim($item->status));
        $date = ($status === 'completed' && $item->date_target) ? $item->date_target : $item->created_at;
        return $date >= $startDate && $date < $endDate;
    });

    // ساخت لیستی از حواله‌ها به تفکیک نماینده
    $userHavaleMap = [];
    foreach ($filteredHavales as $item) {
        $userId = $item->user_id;
        $status = strtolower(trim($item->status));
        $userHavaleMap[$item->havale_number] = [
            'user_id' => $userId,
            'status' => $status
        ];
    }

    $havaleNumbers = array_keys($userHavaleMap);

    // خواندن کامل داده‌ها از sql server
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
            $userHavaleStats[$userId] = [
                'approved' => 0,
                'completed' => 0,
            ];
        }

        // دقت: تشخیص رزرو یا ارسال‌شده بر اساس مقدار تولی
        if ($status == 'approved' && is_null($row->tavali)) {
            $userHavaleStats[$userId]['approved'] += $amount;
        } elseif ($status == 'completed' && !is_null($row->tavali)) {
            $userHavaleStats[$userId]['completed'] += $amount;
        }
    }

    // افزودن اطلاعات متراژ به پروفایل نمایندگان
    $childrenProfiles = $childrenProfiles->map(function ($child) use ($userHavaleStats) {
        $userId = $child->user->id;
        $child->reserved_request_size = round($userHavaleStats[$userId]['approved'] ?? 0, 2);
        $child->completed_request_size = round($userHavaleStats[$userId]['completed'] ?? 0, 2);
        return $child;
    });

    return view('admin.personneltargetshowdetile', compact('childrenProfiles', 'parentUser'));
}

    
////////////////////////// گزارشات

public function reservedProductsReport(HavaleSyncService $havaleSync)
{
    $havaleSync->sync();
    $havaleNumbers = DisRequestHavale::where('status', 'In Progress')->pluck('havale_number');

    // گرفتن تمام رکوردهای مرتبط
    $rawData = DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->whereIn('havale', $havaleNumbers)
        ->select('product_code', 'product_name', 'product_MR', 'havale')
        ->get();

    // گروه‌بندی دستی در PHP
    $grouped = $rawData->groupBy('product_code')->map(function ($items) {
        return [
            'product_name' => $items->first()->product_name,
            'total_product_mr' => $items->sum('product_MR'),
            'havales' => $items->pluck('havale')->unique()->values()->all(), // لیست یکتای حواله‌ها
        ];
    });

    return view('reports.reserved_products', ['data' => $grouped]);
}



public function agentsPerformance(Request $request, HavaleSyncService $havaleSync)
{
    $havaleSync->sync();

    $jalaliNow = Jalalian::now();

    $year = $request->input('year', $jalaliNow->getYear());
    $monthFilter = $request->input('month', null);

    // ساخت بازه تاریخ شمسی
    if ($monthFilter == '1-6') {
        $start = Jalalian::fromFormat('Y-m-d', "$year-01-01");
        $end = Jalalian::fromFormat('Y-m-d', "$year-06-31");
    } elseif ($monthFilter == '7-12') {
        $start = Jalalian::fromFormat('Y-m-d', "$year-07-01");
        $end = Jalalian::fromFormat('Y-m-d', "$year-12-30");
    } elseif ($monthFilter == 'spring') {
        $start = Jalalian::fromFormat('Y-m-d', "$year-01-01");
        $end = Jalalian::fromFormat('Y-m-d', "$year-03-31");
    } elseif ($monthFilter == 'summer') {
        $start = Jalalian::fromFormat('Y-m-d', "$year-04-01");
        $end = Jalalian::fromFormat('Y-m-d', "$year-06-31");
    } elseif ($monthFilter == 'autumn') {
        $start = Jalalian::fromFormat('Y-m-d', "$year-07-01");
        $end = Jalalian::fromFormat('Y-m-d', "$year-09-30");
    } elseif ($monthFilter == 'winter') {
        $start = Jalalian::fromFormat('Y-m-d', "$year-10-01");
        $end = Jalalian::fromFormat('Y-m-d', "$year-12-30");
    } elseif (is_numeric($monthFilter)) {
        $start = Jalalian::fromFormat('Y-m-d', "$year-" . str_pad($monthFilter, 2, '0', STR_PAD_LEFT) . "-01");
        $end = $start->addMonths(1)->subDays(1);
    } else {
        $start = Jalalian::fromFormat('Y-m-d', "$year-01-01");
        $end = Jalalian::fromFormat('Y-m-d', "$year-12-30");
    }

    $startDate = $start->toCarbon()->startOfDay();
    $endDate = $end->toCarbon()->endOfDay();

    $agents = DB::table('users')
        ->join('profiles', 'users.id', '=', 'profiles.user_id')
        ->join('cities', 'profiles.city_id', '=', 'cities.id')
        ->where('users.role', 'distributor')
        ->select('users.id', 'users.name', 'cities.name as city_name')
        ->get();

    $agentLabels = [];
    $completedMeters = [];
    $approvedMeters = [];

    foreach ($agents as $agent) {
        $requestIds = DB::table('dis_requests')
            ->where('user_id', $agent->id)
            ->pluck('id');

        if ($requestIds->isEmpty()) {
            $agentLabels[] = $agent->name;
            $completedMeters[] = 0;
            $approvedMeters[] = 0;
            continue;
        }

        // Completed
        $completedHavaleNumbers = DB::table('dis_request_havales')
            ->whereIn('dis_request_id', $requestIds)
            ->where('status', 'Completed')
            ->whereBetween('date_target', [$startDate, $endDate])
            ->pluck('havale_number');

        $completedTotalMeter = 0;
        if (!$completedHavaleNumbers->isEmpty()) {
            $completedTotalMeter = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->whereIn('havale', $completedHavaleNumbers)
                ->sum('product_MR');
        }

        // Approved
        $approvedHavaleNumbers = DB::table('dis_request_havales')
            ->whereIn('dis_request_id', $requestIds)
            ->where('status', 'Approved')
            ->whereBetween('created_at', [$startDate, $endDate])

            ->pluck('havale_number');

        $approvedTotalMeter = 0;
        if (!$approvedHavaleNumbers->isEmpty()) {
            $approvedTotalMeter = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->whereIn('havale', $approvedHavaleNumbers)
                ->sum('product_MR');
        }

        $agentLabels[] = $agent->name;
        $completedMeters[] = round($completedTotalMeter, 2);
        $approvedMeters[] = round($approvedTotalMeter, 2);
    }



$monthlyLabels = [];
$lineChartData = []; // نماینده => [ماه1 => متر، ماه2 => متر، ...]

$monthsBack = 6; // به اضافه ماه جاری میشه ۶ ماه
$currentJalali = Jalalian::now();

for ($i = $monthsBack; $i >= 0; $i--) {
    $jMonth = Jalalian::fromCarbon($currentJalali->toCarbon()->copy()->subMonths($i));
    $monthLabel = CalendarUtils::convertNumbers("{$jMonth->format('Y')}/{$jMonth->format('m')}");
    $monthlyLabels[] = $monthLabel;

    $startOfMonth = Jalalian::fromFormat('Y-m-d', $jMonth->format('Y-m') . '-01')->toCarbon()->startOfDay();
    $endOfMonth = Jalalian::fromFormat('Y-m-d', $jMonth->format('Y-m') . '-' . $jMonth->getMonthDays())->toCarbon()->endOfDay();

    foreach ($agents as $agent) {
        $agentName = $agent->name;

        $requestIds = DB::table('dis_requests')
            ->where('user_id', $agent->id)
            ->pluck('id');

        if ($requestIds->isEmpty()) {
            $lineChartData[$agentName][$monthLabel] = 0;
            continue;
        }

        $completedHavaleNumbers = DB::table('dis_request_havales')
            ->whereIn('dis_request_id', $requestIds)
            ->where('status', 'Completed')
            ->whereBetween('date_target', [$startOfMonth, $endOfMonth])
            ->pluck('havale_number');

        $monthlyTotalMeter = 0;
        if (!$completedHavaleNumbers->isEmpty()) {
            $monthlyTotalMeter = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->whereIn('havale', $completedHavaleNumbers)
                ->sum('product_MR');
        }

        $lineChartData[$agentName][$monthLabel] = round($monthlyTotalMeter, 2);
    }
}


$lineChartColors = [];
$hueStep = 360 / max(count($agents), 1); // اختلاف رنگ بین هر نماینده

foreach ($agents as $index => $agent) {
    $hue = ($index * $hueStep) % 460;
    $lineChartColors[] = "hsl($hue, 70%, 50%)";
}


$marketShareData = [];
foreach ($agentLabels as $index => $agentName) {
    $marketShareData[$agentName] = $completedMeters[$index]; // همون متراژ تکمیل‌شده
}



return view('reports.agents-performance', [
    'agentLabels' => $agentLabels,
    'completedMeters' => $completedMeters,
    'approvedMeters' => $approvedMeters,
    'currentYear' => $year,
    'currentMonth' => $monthFilter,
    'monthlyLabels' => $monthlyLabels,
    'lineChartData' => $lineChartData,
    'lineChartColors' => $lineChartColors,
    'marketShareData' => $marketShareData,
]);
}



}
