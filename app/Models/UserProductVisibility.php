<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProductVisibility extends Model
{
    use HasFactory;

    protected $table = 'user_product_visibility';

    // Fillable fields
    protected $fillable = [
        'user_id', 'product_name', 'product_degree',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_product_visibility', 'product_degree_id', 'user_id');
    }
    
    
}
