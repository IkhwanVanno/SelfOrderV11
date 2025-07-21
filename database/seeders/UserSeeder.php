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
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
            [
                'username' => 'user',
                'password' => Hash::make('password'),
                'role' => 'user',
            ],
        ]);
    }
}
