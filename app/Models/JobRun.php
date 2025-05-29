<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobRun extends Model
{
    // مشخص کردن جدول مرتبط با مدل
    protected $table = 'job_runs';  // نام جدول در پایگاه داده

    // در صورتی که نمی‌خواهید از فیلدهای created_at و updated_at استفاده کنید، آن‌ها را غیرفعال کنید.
    public $timestamps = true;

    // لیست فیلدهایی که می‌توانند پر شوند
    protected $fillable = [
        'job_name',  // نام جاب
        'run_at'     // زمان اجرا
    ];
}
