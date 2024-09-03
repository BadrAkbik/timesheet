<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTitle extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function guards()
    {
        return $this->hasMany(Guard::class);
    }
}
