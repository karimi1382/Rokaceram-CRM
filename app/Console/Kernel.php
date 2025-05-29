<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\SyncHavaleDataJob;
use Illuminate\Support\Facades\Artisan;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    // protected function schedule(Schedule $schedule): void
    // {
    //     // $schedule->command('inspire')->hourly();
    // }


    protected function schedule(Schedule $schedule)
    {
        set_time_limit(300);  // افزایش زمان اجرای جاب به 300 ثانیه
    
        // فراخوانی روت مورد نظر هر 10 دقیقه یکبار
        $schedule->call(function () {
            // اینجا کد اجرای روت شماست
            Artisan::call('route:call', ['route' => 'نام روت']);
        })->everyTenMinutes(); // اجرای هر 10 دقیقه یکبار
    }
    

    


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
