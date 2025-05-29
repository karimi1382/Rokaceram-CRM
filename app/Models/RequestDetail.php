<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    use HasFactory;
    protected $fillable = ['dis_request_id', 'user_id', 'description','file_path'];

    public function disRequest()
    {
        return $this->belongsTo(DisRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
