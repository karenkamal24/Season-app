<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'karenkamal46@gmail.com'],
            [
                'name' => 'Karen Kamal',
                'nickname' => 'karenkamal46',
                'role' => 'admin',
                'is_blocked' => false,
                'email_verified_at' => now(),
                'password' => Hash::make('Test@1234'),

            ]
        );


    }
}
