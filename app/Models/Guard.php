<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guard extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function workingTimes()
    {
        return $this->hasMany(WorkingTime::class);
    }

    public function jobTitle()
    {
        return $this->belongsTo(JobTitle::class);
    }

}
