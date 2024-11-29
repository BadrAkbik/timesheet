<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;

class Site extends Model
{
    use HasFactory;

    protected $guarded = [];

    /*     public function periods()
        {
            return $this->hasMany(Period::class);
        }

        public function reports()
        {
            return $this->hasMany(Report::class);
        } */

    public function guards()
    {
        return $this->hasMany(Guard::class);
    }

    public function usersPermissions()
    {
        return $this->belongsToMany(Permission::class, 'permissions_users_sites')->withPivot('user_id');
    }
}