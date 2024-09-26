<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guard extends Model
{
    use HasFactory, SoftDeletes, Compoships;

    protected $guarded = [];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function workingTimes()
    {
        return $this->hasMany(WorkingTime::class, ['guard_number', 'site_id'], ['guard_number', 'site_id']);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

}
