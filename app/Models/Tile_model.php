<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tile_model extends Model
{
    use HasFactory;

    protected $fillable = [
        'model',
        'color',
        'color_code',
    ];
}
