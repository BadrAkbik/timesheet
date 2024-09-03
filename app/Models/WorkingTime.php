<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingTime extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function secguard()
    {
        return $this->belongsTo(Guard::class, 'guard_id')->where('active', true);
    }
}
