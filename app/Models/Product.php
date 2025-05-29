<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_number',
        'name',
        'degree',
        'size',
        'model',
        'color',
        'color_code',
        'inventory',
        'state',
    ];

    public function disRequests()
    {
        return $this->hasMany(DisRequest::class);
    }
}
