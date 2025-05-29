<?php

namespace App\Jobs;
use App\Models\JobRun;  // مدل برای ذخیره در MySQL

use App\Models\HavaleData;  // مدل برای ذخیره در MySQL
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SyncHavaleDataJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    protected $data;

    public function __construct()
    {
        // گرفتن داده‌ها از SQL Server (از ویو)
        $this->data = DB::connection('sqlsrv')->select("SELECT * FROM vw_HavaleData");
    }

    public function handle()
    {

        JobRun::create([
            'job_name' => 'SyncHavaleDataJob',
            'run_at' => now(),
        ]);


        // اگر داده‌ها به درستی دریافت شدند، شروع به ذخیره یا بروزرسانی در دیتابیس MySQL می‌کنیم
        set_time_limit(300);  // افزایش زمان اجرا به 5 دقیقه

        foreach ($this->data as $item) {
            // بررسی اینکه آیا داده از قبل وجود دارد یا نه
            $existing = HavaleData::where('tr_code', $item->iptr_Key)->first();

            if ($existing) {
                // اگر داده وجود داشت، بروزرسانی می‌کنیم
                $existing->update([
                    'tr_code' => $item->iptr_Key,
                    'date' => $item->date,
                    'product_code' => $item->product_code,
                    'product_name' => $item->product_name,
                    'namayande_code' => $item->namayande_code,
                    'product_MR' => $item->product_MR,
                    'product_carton_MR' => $item->product_carton_MR,
                    'mali' => $item->mali,
                    'tavali' => $item->tavali,
                    'Send_Info' => $item->Send_Info
                ]);
            } else {
                // اگر داده وجود نداشت، وارد می‌کنیم
                HavaleData::create([
                    'tr_code' => $item->iptr_Key,
                    'havale' => $item->havale,
                    'date' => $item->date,
                    'product_code' => $item->product_code,
                    'product_name' => $item->product_name,
                    'namayande_code' => $item->namayande_code,
                    'product_MR' => $item->product_MR,
                    'product_carton_MR' => $item->product_carton_MR,
                    'mali' => $item->mali,
                    'tavali' => $item->tavali,
                    'Send_Info' => $item->Send_Info
                ]);
            }
        }
    }
}
