<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Administrateur',
            'email' => 'admin@petitchef.local',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '+33612345678',
            'is_verified' => true,
        ]);
    }
}
