<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'personnel_id',
        'product_name',
        'degree',
        'size',
        'model',
        'color',
        'color_code',
        'customer_name',
        'tel_number',
        'address',
        'complaint_text',
        'complaint_type',
        'status',
        'supervisor_comment',
        'attachments',
        'tracking_number',
        'product_code',

    ];

    // ارتباط با کاربر نماینده فروش
    public function distributor()
    {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    // ارتباط با کاربر سرپرست
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'personnel_id');
    }
    public function issues() {
        return $this->belongsToMany(Issue::class, 'complaint_issue_pivot', 'complaint_id', 'issue_id');
    }

    public function comments() {
        return $this->hasMany(ComplaintComment::class);
    }
}
