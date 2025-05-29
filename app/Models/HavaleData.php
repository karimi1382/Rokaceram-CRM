<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HavaleData extends Model
{
    use HasFactory;

    protected $table = 'havale_data'; // نام دقیق جدول در دیتابیس

    protected $fillable = [
        'tr_code',
        'havale',
        'date',
        'product_code',
        'product_name',
        'namayande_code',
        'product_MR',
        'product_carton_MR',
        'mali',
        'tavali',
        'Send_Info',
    ]; // ستون‌هایی که قابل پر شدن هستند

    public $timestamps = false; // اگر جدول شما فیلدهای `created_at` و `updated_at` نداره
}
