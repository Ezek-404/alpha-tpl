<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Gagawa ng Admin Account
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@alpha.tpl',
            'password' => Hash::make('admin12345'), 
        ]);
    }
}