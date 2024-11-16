<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'SIS Admin',
            'email' => 'sis_admin@yopmail.com',
            'password' => Hash::make('Qa_111111'),
        ])->assignRole('admin');

        // Create Student user
        User::create([
            'name' => 'SIS Std1',
            'email' => 'sis_std1@yopmail.com',
            'password' => Hash::make('Qa_111111'),
        ])->assignRole('student');
    }
}
