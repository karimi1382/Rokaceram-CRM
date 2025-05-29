<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'state',
        'country',
    ];

    public function users()
    {
        return $this->hasMany(UserData::class);  // Assuming UserData is the model for the user's profile
    }
   
 public function userData()
    {
        return $this->hasOne(UserData::class, 'user_id');
    }
    
}
