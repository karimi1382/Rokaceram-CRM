<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTemp extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_number', 'name', 'degree', 'size', 'model', 'color', 'color_code', 'inventory'
    ];
}
