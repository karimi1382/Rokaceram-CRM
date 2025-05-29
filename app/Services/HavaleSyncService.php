<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\DisRequestHavale;
use Morilog\Jalali\Jalalian;

class HavaleSyncService
{
    public function sync()
    {
        $shamsiYear = jdate()->format('Y');
        $firstDayShamsiYear = "$shamsiYear-01-01";

        $startOfYearMiladi = Jalalian::fromFormat('Y-m-d', $firstDayShamsiYear)
            ->toCarbon()
            ->toDateString();

        $allhavalenums = DB::table('dis_request_havales')
            ->where('status', '<>', 'Completed')
            ->get();

        $havaleData_nomalis = DB::connection('sqlsrv')->select("
            SELECT * FROM vw_HavaleData
            WHERE mali = 1 AND CAST([date] AS DATE) >= '$startOfYearMiladi'
        ");

        $havaleMap = collect($havaleData_nomalis)->keyBy('havale');

        foreach ($allhavalenums as $allhavalenum) {
            if ($havaleMap->has($allhavalenum->havale_number)) {
                $matched = $havaleMap[$allhavalenum->havale_number];
                $record = DisRequestHavale::find($allhavalenum->id);

                if (!$record) continue;

                if ($matched->tavali === null) {
                    $record->status = 'Approved';
                } else {
                    $record->status = 'Completed';

                    // اگر null بود، مقدار امروز میلادی را وارد کن
                    if (is_null($record->date_target)) {
                        $record->date_target = now()->toDateString(); // تاریخ امروز میلادی، بدون ساعت
                    }
                }

                $record->save();
            }
        }
    }
}