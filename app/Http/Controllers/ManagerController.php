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
        // بررسی اتصال به دیتابیس
        DB::connection('sqlsrv')->getPdo();

        // تعیین سال و ماه — اگر از فرم نیاد، تاریخ امروز
        $shamsiYear = $request->input('year') ?? Jalalian::now()->getYear();
        $shamsiMonth = $request->input('month') ?? Jalalian::now()->getMonth();

        // ساخت تاریخ شروع و پایان ماه انتخاب‌شده
        $startDate = Jalalian::fromFormat(
            'Y/m/d',
            $shamsiYear . '/' . str_pad($shamsiMonth, 2, '0', STR_PAD_LEFT) . '/01'
        )->toCarbon();

        $lastDay = Jalalian::fromFormat(
            'Y/m/d',
            $shamsiYear . '/' . str_pad($shamsiMonth, 2, '0', STR_PAD_LEFT) . '/01'
        )->getMonthDays();

        $endDate = Jalalian::fromFormat(
            'Y/m/d',
            $shamsiYear . '/' . str_pad($shamsiMonth, 2, '0', STR_PAD_LEFT) . '/' . $lastDay
        )->toCarbon();

        // گرفتن لیست سرپرست‌ها
        $personnels = User::where('role', 'personnel')->get();

        $results = $personnels->map(function ($personnel) use ($startDate, $endDate, $shamsiYear, $shamsiMonth) {

            $childUserIds = UserData::where('personel_id', $personnel->id)->pluck('user_id');
            $childrenCount = $childUserIds->count();

            // استعلام حواله‌های این ماه
            $havaleUserMap = DB::table('dis_requests')
                ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
                ->whereIn('dis_requests.user_id', $childUserIds)
                ->whereIn('dis_request_havales.status', ['approved', 'completed'])
                ->select('dis_requests.user_id', 'dis_request_havales.havale_number', 'dis_request_havales.status', 'dis_request_havales.created_at', 'dis_request_havales.date_target')
                ->get();

            // فیلتر کردن حواله‌ها بر اساس تاریخ مناسب
            $filteredHavales = $havaleUserMap->filter(function ($item) use ($startDate, $endDate) {
                $status = strtolower(trim($item->status));
                $date = ($status === 'completed' && $item->date_target) ? $item->date_target : $item->created_at;
                return $date >= $startDate && $date <= $endDate;
            });

            $havaleNumbers = $filteredHavales->pluck('havale_number')->unique();

            // متراژ حواله‌های این ماه
            $havaleDataRaw = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->select('havale', DB::raw('SUM(product_MR) as request_size'))
                ->whereIn('havale', $havaleNumbers)
                ->groupBy('havale')
                ->get()
                ->keyBy('havale');

            $totalApproved = 0;
            $totalCompleted = 0;

            foreach ($filteredHavales as $item) {
                $status = strtolower(trim($item->status));
                $havale = $item->havale_number;
                $size = isset($havaleDataRaw[$havale]) ? floatval($havaleDataRaw[$havale]->request_size) : 0;

                if ($status === 'approved') {
                    $totalApproved += $size;
                } elseif ($status === 'completed') {
                    $totalCompleted += $size;
                }
            }

            // تارگت ماه جاری
            $target = UserTarget::where('user_id', $personnel->id)
                                ->where('year', $shamsiYear)
                                ->where('month', $shamsiMonth)
                                ->value('target');

            // مجموع تارگت سال جاری
            $yearlyTarget = UserTarget::where('user_id', $personnel->id)
                                      ->where('year', $shamsiYear)
                                      ->sum('target');

            // مجموع متراژ تکمیل‌شده در سال جاری
            $yearStart = Jalalian::fromFormat('Y/m/d', $shamsiYear . '/01/01')->toCarbon()->startOfDay();
            $yearEnd = Jalalian::fromFormat('Y/m/d', $shamsiYear . '/12/29')->toCarbon()->endOfDay();

            $yearlyHavaleUserMap = DB::table('dis_requests')
                ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
                ->whereIn('dis_requests.user_id', $childUserIds)
                ->whereIn('dis_request_havales.status', ['approved', 'completed'])
                ->select('dis_requests.user_id', 'dis_request_havales.havale_number', 'dis_request_havales.status', 'dis_request_havales.created_at', 'dis_request_havales.date_target')
                ->get();

            // فیلتر کردن حواله‌ها بر اساس تاریخ مناسب
            $filteredYearlyHavales = $yearlyHavaleUserMap->filter(function ($item) use ($yearStart, $yearEnd) {
                $status = strtolower(trim($item->status));
                $date = ($status === 'completed' && $item->date_target) ? $item->date_target : $item->created_at;
                return $date >= $yearStart && $date <= $yearEnd;
            });

            $yearlyHavaleNumbers = $filteredYearlyHavales->pluck('havale_number')->unique();

            $yearlyHavaleData = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->select('havale', DB::raw('SUM(product_MR) as request_size'))
                ->whereIn('havale', $yearlyHavaleNumbers)
                ->groupBy('havale')
                ->get()
                ->keyBy('havale');

            $totalCompletedYearly = 0;

            foreach ($filteredYearlyHavales as $item) {
                $status = strtolower(trim($item->status));
                $havale = $item->havale_number;

                $size = isset($yearlyHavaleData[$havale]) ? floatval($yearlyHavaleData[$havale]->request_size) : 0;

                if ($status === 'completed') {
                    $totalCompletedYearly += $size;
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
        // اگر خطا بود، همه داده‌ها نال باشه
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

    // دریافت تمام حواله‌ها بدون محدودیت زمانی
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

    // فیلتر کردن دستی با توجه به نوع تاریخ بر اساس وضعیت
    $filteredHavales = $havaleUserMap->filter(function ($item) use ($startDate, $endDate) {
        $status = strtolower(trim($item->status));
        $date = ($status === 'completed' && $item->date_target) ? $item->date_target : $item->created_at;
        return $date >= $startDate && $date < $endDate;
    });

    $havaleNumbers = $filteredHavales->pluck('havale_number')->unique();

    $havaleDataRaw = DB::connection('sqlsrv')
        ->table('vw_HavaleData')
        ->select('havale', DB::raw('SUM(product_MR) as request_size'))
        ->whereIn('havale', $havaleNumbers)
        ->groupBy('havale')
        ->get()
        ->keyBy('havale');

    $userHavaleStats = [];

    foreach ($filteredHavales as $row) {
        $userId = $row->user_id;
        $status = strtolower(trim($row->status));
        $havaleNumber = $row->havale_number;

        if (!isset($havaleDataRaw[$havaleNumber])) continue;

        $requestSize = floatval($havaleDataRaw[$havaleNumber]->request_size);

        if (!isset($userHavaleStats[$userId])) {
            $userHavaleStats[$userId] = [
                'approved' => 0,
                'completed' => 0,
            ];
        }

        if ($status == 'approved') {
            $userHavaleStats[$userId]['approved'] += $requestSize;
        } elseif ($status == 'completed') {
            $userHavaleStats[$userId]['completed'] += $requestSize;
        }
    }

    $childrenProfiles = $childrenProfiles->map(function ($child) use ($userHavaleStats) {
        $child->reserved_request_size = $userHavaleStats[$child->user->id]['approved'] ?? 0;
        $child->completed_request_size = $userHavaleStats[$child->user->id]['completed'] ?? 0;
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





}
