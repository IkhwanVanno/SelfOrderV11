<?php

// UserSeeder.php
// php artisan make:seeder UserSeeder

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            [
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ],
            [
                'username' => 'user1',
                'password' => Hash::make('user123'),
                'role'     => 'user',
            ],
        ]);
    }
}
