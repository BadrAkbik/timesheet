<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $role_id = Role::firstOrCreate(['name' => 'owner'])->id;


/*         $permissions = include base_path('data/permissions.php');
        foreach ($permissions as $key => $value) {
            Permission::firstOrCreate([
                'name' => $key,
                'name_ar' => $value,
            ]);
        } */
        $user = User::where('email', 'admin@admin.com')->first();
        if (!$user) {
            User::create([
                'name' => 'admin',
                'email' => 'admin@admin.com',
                'password' => bcrypt('admin123456'),
                'role_id' => $role_id
            ]);
        }
    }
}
