<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkingTime extends Model
{
    use HasFactory, Compoships;

    protected $guarded = [];

    public function secguard()
    {
        return $this->belongsTo(Guard::class, ['guard_number', 'site_id'], ['guard_number', 'site_id'])->where('active', true);
    }

    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
}
