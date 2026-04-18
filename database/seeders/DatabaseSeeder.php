<?php

namespace Database\Seeders;

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
        // Create admin
        $this->call(AdminSeeder::class);

        // Create cooks
        $cook1 = User::create([
            'name' => 'Chef Pierre',
            'email' => 'pierre@petitchef.local',
            'password' => bcrypt('password'),
            'role' => 'cook',
            'phone' => '+33612345679',
            'is_verified' => true,
        ]);

        $cook2 = User::create([
            'name' => 'Chef Marie',
            'email' => 'marie@petitchef.local',
            'password' => bcrypt('password'),
            'role' => 'cook',
            'phone' => '+33612345680',
            'is_verified' => true,
        ]);

        // Create clients
        User::create([
            'name' => 'Client Jean',
            'email' => 'jean@petitchef.local',
            'password' => bcrypt('password'),
            'role' => 'client',
            'phone' => '+33612345681',
        ]);

        User::create([
            'name' => 'Client Sophie',
            'email' => 'sophie@petitchef.local',
            'password' => bcrypt('password'),
            'role' => 'client',
            'phone' => '+33612345682',
        ]);

        // Create test dishes
        \App\Models\Dish::create([
            'cook_id' => $cook1->id,
            'name' => 'Coq au vin',
            'description' => 'Délicieux coq au vin avec légumes de saison',
            'price' => 12.50,
            'available_qty' => 5,
            'served_date' => today(),
            'is_active' => true,
        ]);

        \App\Models\Dish::create([
            'cook_id' => $cook1->id,
            'name' => 'Blanquette de veau',
            'description' => 'Blanquette de veau à l\'ancienne',
            'price' => 14.00,
            'available_qty' => 3,
            'served_date' => today(),
            'is_active' => true,
        ]);

        \App\Models\Dish::create([
            'cook_id' => $cook2->id,
            'name' => 'Ratatouille',
            'description' => 'Ratatouille provençale maison',
            'price' => 10.00,
            'available_qty' => 8,
            'served_date' => today(),
            'is_active' => true,
        ]);

        \App\Models\Dish::create([
            'cook_id' => $cook2->id,
            'name' => 'Bouillabaisse',
            'description' => 'Authentique bouillabaisse marseillaise',
            'price' => 18.50,
            'available_qty' => 4,
            'served_date' => today(),
            'is_active' => true,
        ]);
    }
}
