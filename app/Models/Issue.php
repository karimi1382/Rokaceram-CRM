<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function complaints()
    {
        return $this->belongsToMany(Complaint::class, 'complaint_issue_pivot', 'issue_id', 'complaint_id');
    }
}
