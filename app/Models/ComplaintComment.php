<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplaintComment extends Model
{
    protected $fillable = ['complaint_id', 'user_id', 'comment_text'];

    public function complaint() {
        return $this->belongsTo(Complaint::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    
}
