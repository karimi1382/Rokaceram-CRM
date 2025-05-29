<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    // Explicitly define the table name to match the 'profiles' table
    protected $table = 'profiles';

    protected $fillable = [
        'user_id',
        'phone',
        'city',
        'profile_picture',
        'personel_id',       // Added field for self-referencing user
        'customer_type',     // Added field for user type (admin, manager, etc.)
        'last_login_at',     // Added field for storing last login date
    ];

    // Each UserData belongs to one User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Self-referencing relationship for parent user (personnel)
    public function personel()
    {
        return $this->belongsTo(User::class, 'personel_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);  // Each UserData belongs to one City
    }

    public function personnel()
    {
        return $this->belongsTo(User::class, 'personnel_id');
    }


   

    public function parent()
    {
        return $this->belongsTo(User::class, 'personel_id');
    }

    public function disRequests()
    {
        return $this->hasMany(DisRequest::class);
    }


}
