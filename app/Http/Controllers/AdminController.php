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
        \DB::connection('sqlsrv')->getPdo();

        $currentYear = Jalalian::now()->getYear();
        $startOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/01/01")->toCarbon()->startOfDay();
        $endOfYear = Jalalian::fromFormat('Y/m/d', "$currentYear/12/29")->toCarbon()->endOfDay();

        // موارد اصلی بدون تغییر:
        $requestsInCurrentYear = DisRequest::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->whereNull('file_path')->pluck('id');

        $havaleRequestIds = DisRequestHavale::whereIn('dis_request_id', $requestsInCurrentYear)->pluck('dis_request_id');

        $unconfirmedRequests = $requestsInCurrentYear->diff($havaleRequestIds);
        $totalUnconfirmedCount = $unconfirmedRequests->count();

        $totalInProgressHavale = DisRequestHavale::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->where('status', 'In Progress')->count();

        $approvedHavaleNumbers = DisRequestHavale::whereBetween('created_at', [$startOfYear, $endOfYear])
            ->where('status', 'Approved')->pluck('havale_number')->toArray();

        $totalReservedSize = 0;
        if (!empty($approvedHavaleNumbers)) {
            $totalReservedSize = DB::connection('sqlsrv')
                ->table('vw_HavaleData')
                ->whereIn('havale', $approvedHavaleNumbers)
                ->where('mali', 1)
                ->sum('product_MR');
        }

        $approvedHavaleNumbers = DisRequestHavale::where('status', 'Completed')
            ->whereBetween('date_target', [$startOfYear, $endOfYear])
            ->pluck('havale_number')->toArray();

        $totalCompletedSize = !empty($approvedHavaleNumbers) ? DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $approvedHavaleNumbers)
            ->where('mali', 1)
            ->whereNotNull('tavali')
            ->sum('product_MR') : 0;

        // -----------------------------
        // نمودار ماهانه فروش
        // -----------------------------
        $havaleData = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $approvedHavaleNumbers)
            ->where('mali', 1)
            ->whereNotNull('tavali')
            ->get(['havale', 'product_MR', 'tavali']);

        $monthlyTotal = array_fill(1, 12, 0);
        foreach ($havaleData as $record) {
            $month = Jalalian::fromCarbon(Carbon::parse($record->tavali))->getMonth();
            $monthlyTotal[$month] += $record->product_MR;
        }

        $monthLabels = ['فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'];
        $soldMetersData = array_values($monthlyTotal);

        // -----------------------------
        // نمودار ۵ محصول برتر (براساس product_MR)
        // -----------------------------
        $soldProducts = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $approvedHavaleNumbers)
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
            $size = $nameParts[2] ?? '';
            $grade = $nameParts[1] ?? '';
            $productLabels[] = "$brand - $color - $size - $grade";
            $productData[] = round($product->total_MR, 2);
        }

        // -----------------------------
        // نمودار متراژ به تفکیک سایز
        // -----------------------------
        $sizeProducts = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $approvedHavaleNumbers)
            ->where('mali', 1)
            ->whereNotNull('tavali')
            ->select('product_name', DB::raw('SUM(product_MR) as total_MR'))
            ->groupBy('product_name')
            ->get();

        $sizeData = [];
        foreach ($sizeProducts as $product) {
            $size = explode(' - ', $product->product_name)[2] ?? 'نامشخص';
            $sizeData[$size] = ($sizeData[$size] ?? 0) + $product->total_MR;
        }
        $sizeLabels = array_keys($sizeData);
        $sizeValues = array_map(fn($v) => round($v, 2), array_values($sizeData));

        // -----------------------------
        // ۵ نماینده برتر بر اساس متراژ در tavali
        // -----------------------------
        $distributors = DB::table('users')
            ->join('profiles', 'users.id', '=', 'profiles.user_id')
            ->join('cities', 'profiles.city_id', '=', 'cities.id')
            ->where('users.role', 'distributor')
            ->select('users.id', 'users.name', 'cities.name as city_name', 'users.target')
            ->get();

        $distributorIds = $distributors->pluck('id')->toArray();

        $completedHavaleNumbers = DB::table('dis_requests')
            ->join('dis_request_havales', 'dis_requests.id', '=', 'dis_request_havales.dis_request_id')
            ->whereIn('dis_requests.user_id', $distributorIds)
            ->where('dis_request_havales.status', 'Completed')
            ->select('dis_requests.user_id', 'dis_request_havales.havale_number')
            ->get();

        $havaleMap = [];
        foreach ($completedHavaleNumbers as $item) {
            $havaleMap[$item->user_id][] = $item->havale_number;
        }

        $allHavaleNumbers = collect($havaleMap)->flatten()->unique()->toArray();

        $havaleData = DB::connection('sqlsrv')
            ->table('vw_HavaleData')
            ->whereIn('havale', $allHavaleNumbers)
            ->where('mali', 1)
            ->whereNotNull('tavali')
            ->select('havale', 'product_MR')
            ->get();

        $requestSizes = [];
        foreach ($havaleMap as $userId => $havaleList) {
            $total = $havaleData->whereIn('havale', $havaleList)->sum('product_MR');
            $requestSizes[$userId] = round($total, 2);
        }

        $topDistributorsWithSizes = $distributors->filter(function ($d) use ($requestSizes) {
            return isset($requestSizes[$d->id]) && $requestSizes[$d->id] > 0;
        })->sortByDesc(fn($d) => $requestSizes[$d->id])->take(5)->map(function ($d) use ($requestSizes) {
            $d->product_mr = $requestSizes[$d->id] ?? 0;
            return $d;
        });

        return view('manager.index', compact(
            'totalUnconfirmedCount',
            'totalInProgressHavale',
            'totalReservedSize',
            'totalCompletedSize',
            'monthLabels', 'soldMetersData',
            'productLabels', 'productData',
            'sizeLabels', 'sizeValues',
            'topDistributorsWithSizes',
            'requestSizes'
        ));

    } catch (\Exception $e) {
        $error = 'ارتباط با دیتابیس برقرار نیست. لطفاً اتصال خود را بررسی کنید.';
        return view('manager.index', compact(
            'error'
        ));
    }
}

    
    
    
    public function personneltargetshow(Request $request)
    {
        $shamsiYear = $request->query('year');
        $shamsiMonth = $request->query('month');
        $now = Jalalian::now();
    
        if (!$shamsiYear || !$shamsiMonth) {
            $shamsiYear = $now->getYear();
            $shamsiMonth = $now->getMonth();
        }
    
        $shamsiYear = (int)$shamsiYear;
        $shamsiMonth = (int)$shamsiMonth;
    
        $startDate = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/01', $shamsiYear, $shamsiMonth))->toCarbon()->startOfDay();
        $endDate = (clone $startDate)->addMonth()->startOfDay();
    
        // گرفتن تاریخ‌های بارگیری شده از ویو vw_HavaleData
        $havaleData = collect(DB::connection('sqlsrv')->select("SELECT havale_number, tavali_date, user_id, request_size FROM vw_HavaleData"))
            ->map(function ($item) {
                $item->tavali_date = Carbon::parse($item->tavali_date);
                return $item;
            });
    
        // فیلتر فقط داده‌هایی که در بازه‌ی این ماه هستند
        $filtered = $havaleData->filter(function ($item) use ($startDate, $endDate) {
            return $item->tavali_date >= $startDate && $item->tavali_date < $endDate;
        });
    
        // گروه‌بندی بر اساس user_id (نماینده‌ها)
        $completedByUser = $filtered->groupBy('user_id')->map(function ($items) {
            return $items->sum('request_size');
        });
    
        // گروه‌بندی بر اساس کل سال (برای کل متراژ سال)
        $yearStart = Jalalian::fromFormat('Y/m/d', sprintf('%04d/01/01', $shamsiYear))->toCarbon()->startOfDay();
        $yearEnd = (clone $yearStart)->addYear()->startOfDay();
    
        $yearlyCompleted = $havaleData->filter(function ($item) use ($yearStart, $yearEnd) {
            return $item->tavali_date >= $yearStart && $item->tavali_date < $yearEnd;
        })->groupBy('user_id')->map(function ($items) {
            return $items->sum('request_size');
        });
    
        $personnel = User::where('role', 'personnel')
            ->with('children') // نماینده‌ها
            ->get();
    
        $results = [];
    
        foreach ($personnel as $person) {
            $children = $person->children;
            $completedTotal = 0;
            $yearlyCompletedTotal = 0;
    
            foreach ($children as $child) {
                $completedTotal += $completedByUser[$child->user_id] ?? 0;
                $yearlyCompletedTotal += $yearlyCompleted[$child->user_id] ?? 0;
            }
    
            $results[] = [
                'personnel_id' => $person->id,
                'personnel_name' => $person->name,
                'children_count' => $children->count(),
                'approved_total' => 0, // اگه نیاز داری پر بشه بگو
                'completed_total' => $completedTotal,
                'target' => $person->target ?? 0,
                'yearly_completed_total' => $yearlyCompletedTotal,
            ];
        }
    
        return view('admin.personneltargetshow', compact('results', 'shamsiYear', 'shamsiMonth'));
    }



    public function personneltargetshowdetile(Request $request, $id)
    {
        $shamsiYear = $request->query('year');
        $shamsiMonth = $request->query('month');
        $now = Jalalian::now();
    
        if (!$shamsiYear || !$shamsiMonth) {
            $shamsiYear = $now->getYear();
            $shamsiMonth = $now->getMonth();
        }
    
        $shamsiYear = (int)$shamsiYear;
        $shamsiMonth = (int)$shamsiMonth;
    
        $startDate = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/01', $shamsiYear, $shamsiMonth))->toCarbon()->startOfDay();
        $endDate = (clone $startDate)->addMonth()->startOfDay();
    
        $personnel = User::findOrFail($id);
    
        $children = $personnel->children()->with('user')->get();
    
        // خواندن داده‌های بارگیری‌شده از ویو
        $havaleData = collect(DB::connection('sqlsrv')->select("SELECT user_id, tavali_date, request_size FROM vw_HavaleData"))
            ->map(function ($item) {
                $item->tavali_date = Carbon::parse($item->tavali_date);
                return $item;
            });
    
        // آماده‌سازی لیست نهایی
        $childrenProfiles = [];
    
        foreach ($children as $child) {
            $childUser = $child->user;
    
            // فیلتر بر اساس user_id و بازه زمانی
            $completedTotal = $havaleData
                ->filter(function ($item) use ($childUser, $startDate, $endDate) {
                    return $item->user_id == $childUser->id && $item->tavali_date >= $startDate && $item->tavali_date < $endDate;
                })
                ->sum('request_size');
    
            // در صورت نیاز می‌تونی reserved رو هم اضافه کنی از منابع دیگر
    
            $childrenProfiles[] = (object) [
                'user' => $childUser,
                'reserved_request_size' => 0, // می‌تونی بعداً تکمیلش کنی
                'completed_request_size' => $completedTotal,
            ];
        }
    
        return view('admin.personneltargetshowdetile', [
            'parentUser' => $personnel,
            'childrenProfiles' => $childrenProfiles,
            'shamsiYear' => $shamsiYear,
            'shamsiMonth' => $shamsiMonth,
        ]);
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
        'cities.state as city_state',
        'cities.country as city_country',
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

    
    public function completedHavale(Request $request, HavaleSyncService $havaleSync)
    {
        $havaleSync->sync();
    
        // دریافت پارامترهای تاریخ از درخواست یا مقدار پیش‌فرض
        $year = $request->query('year');
        $month = $request->query('month');
        $now = Jalalian::now();
    
        if (!$year || !$month) {
            $year = $now->getYear();
            $month = $now->getMonth();
        }
    
        $year = (int) $year;
        $month = (int) $month;
    
        // گرفتن همه داده‌های تاریخ از connection دوم
        $havaleDates = collect(DB::connection('sqlsrv')->select("SELECT havale, tavali_Date FROM vw_HavaleData"))
            ->keyBy('havale');
    
        // min/max برای ساخت سال‌ها
        $dates = $havaleDates->pluck('tavali_Date')->map(fn($d) => Carbon::parse($d));
        $minYear = $dates->min() ? Jalalian::fromCarbon($dates->min())->getYear() : $year;
        $maxYear = $dates->max() ? Jalalian::fromCarbon($dates->max())->getYear() : $year;
    
        $years = range($maxYear + 2, $minYear - 2);
    
        $months = [
            1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر',
            5 => 'مرداد', 6 => 'شهریور', 7 => 'مهر', 8 => 'آبان',
            9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند'
        ];
    
        // محاسبه بازه تاریخ فیلتر
        $startDate = Jalalian::fromFormat('Y/m/d', sprintf('%04d/%02d/01', $year, $month))->toCarbon()->startOfDay();
        $endDate = (clone $startDate)->addMonth()->startOfDay();
    
        // دریافت حواله‌های تکمیل‌شده
        $havales = DB::table('dis_request_havales')
            ->join('dis_requests', 'dis_request_havales.dis_request_id', '=', 'dis_requests.id')
            ->join('users', 'dis_requests.user_id', '=', 'users.id')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->leftJoin('cities', 'profiles.city_id', '=', 'cities.id')
            ->where('dis_request_havales.status', 'Completed')
            ->select(
                'dis_request_havales.*',
                'dis_requests.user_id',
                'users.name as user_name',
                'cities.name as city_name',
                'cities.state as ostan_name',
                'cities.country as country_name'
            )
            ->get();
    
        // فیلتر کردن بر اساس تاریخ از کانکشن دوم
        $filtered = $havales->filter(function ($item) use ($havaleDates, $startDate, $endDate) {
            $havale = $havaleDates->get($item->havale_number);
            if (!$havale) return false;
    
            $havaleDate = Carbon::parse($havale->tavali_Date);
            $item->jalali_created_at = Jalalian::fromCarbon($havaleDate)->format('Y/m/d');
            return $havaleDate >= $startDate && $havaleDate < $endDate;
        });
    
        // گروه‌بندی بر اساس شماره حواله
        $uniqueRequests = $filtered->groupBy('havale_number');
    
        return view('admin.havale_completed', compact('uniqueRequests', 'months', 'years', 'month', 'year'));
    }
    


}
