<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisRequestHavale extends Model
{
    use HasFactory;

    // The table associated with the model (optional if it follows Laravel's convention)
    protected $table = 'dis_request_havales';

    // Define the inverse relationship with DisRequest
    public function disRequest()
    {
        return $this->belongsTo(DisRequest::class);
    }
    
}
