<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'role', 'password','target'];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function userData()
    {
        return $this->hasOne(UserData::class, 'user_id');
    }

    public function childrenProfiles()
    {
        return $this->hasMany(UserData::class, 'personel_id');
    }

    public function disRequests()
    {
        return $this->hasMany(DisRequest::class, 'user_id');
    }

    public function children()
    {
        return $this->hasMany(UserData::class, 'personel_id'); // Assuming 'personnel_id' is the parent reference
    }
    public function visibleProducts()
{
    return $this->hasMany(UserProductVisibility::class);
}
public function productVisibilities()
{
    return $this->hasMany(UserProductVisibility::class);
}

public function targets()
{
    return $this->hasMany(UserTarget::class); // Assuming the Target model is called UserTarget
}



   


}
