<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id', 'user_id', 'request_size', 'request_type',
        'address', 'tel_number', 'request_owner', 'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requestDetails()
    {
        return $this->hasMany(RequestDetail::class);
    }
    public function disRequestHavales()
    {
        return $this->hasMany(DisRequestHavale::class);
    }
    public function havales()
{
    return $this->hasMany(DisRequestHavale::class, 'dis_request_id', 'id');
}
}
