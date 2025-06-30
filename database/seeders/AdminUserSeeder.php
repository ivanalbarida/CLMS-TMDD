<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // Import the User model
use Illuminate\Support\Facades\Hash; // Import the Hash facade

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin', // The admin's full name
            'username' => 'admin', // The username they will log in with
            'password' => Hash::make('password'), // CHANGE THIS to a secure password
            'role' => 'Admin', // Set the role to 'Admin'
        ]);
    }
}