<?php

namespace Database\Seeders;

use App\Models\Delivery;
use App\Models\DeliveryAddress;
use App\Models\DeliveryStop;
use App\Models\Dish;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        $admin = User::factory()->create([
            'name' => 'Admin PetitChef',
            'email' => 'admin@petitchef.local',
            'role' => 'admin',
        ]);

        // Create Cook (Cuisinier)
        $cook = User::factory()->create([
            'name' => 'Jean-Luc Traiteur',
            'email' => 'cook@petitchef.local',
            'phone' => '06 12 34 56 78',
            'role' => 'cook',
        ]);

        // Create Clients
        $client1 = User::factory()->create([
            'name' => 'Alice Martin',
            'email' => 'alice@example.com',
            'role' => 'client',
        ]);

        $client2 = User::factory()->create([
            'name' => 'Bob Dupont',
            'email' => 'bob@example.com',
            'role' => 'client',
        ]);

        // Create Delivery Addresses (Entreprises + résidentiel)
        $address_company_1 = DeliveryAddress::factory()->create([
            'cook_id' => $cook->id,
            'name' => 'Entreprise X - Rue Paix',
            'address' => '123 Rue de la Paix, 75001 Paris',
            'latitude' => 48.8566,
            'longitude' => 2.3522,
            'is_regular' => true,
        ]);

        $address_company_2 = DeliveryAddress::factory()->create([
            'cook_id' => $cook->id,
            'name' => 'Startup Y - Avenue Central',
            'address' => '45 Boulevard Central, 75002 Paris',
            'latitude' => 48.8580,
            'longitude' => 2.3550,
            'is_regular' => true,
        ]);

        $address_residential = DeliveryAddress::factory()->create([
            'cook_id' => $cook->id,
            'name' => 'Alice - Domicile',
            'address' => '789 Rue Principale, 75003 Paris',
            'latitude' => 48.8630,
            'longitude' => 2.3450,
            'is_regular' => false,
        ]);

        // Create Dishes for Today
        $dish_courgettes = Dish::factory()->create([
            'cook_id' => $cook->id,
            'name' => 'Courgettes farcies',
            'description' => 'Courgettes bio farcies au riz, oignons et herbes aromatiques',
            'price' => 12.00,
            'initial_qty' => 25,
            'available_qty' => 18, // 7 déjà commandées
            'is_active' => true,
            'served_date' => Carbon::today(),
            'critical_threshold' => 5,
        ]);

        $dish_poulet = Dish::factory()->create([
            'cook_id' => $cook->id,
            'name' => 'Poulet rôti fermier',
            'description' => 'Poulet fermier rôti herbes de Provence, accompagné de légumes',
            'price' => 15.00,
            'initial_qty' => 30,
            'available_qty' => 28, // 2 déjà commandées
            'is_active' => true,
            'served_date' => Carbon::today(),
            'critical_threshold' => 3,
        ]);

        $dish_salade = Dish::factory()->create([
            'cook_id' => $cook->id,
            'name' => 'Salade composée',
            'description' => 'Mélange de laitue, tomate, concombre, carotte avec vinaigrette maison',
            'price' => 8.00,
            'initial_qty' => 12,
            'available_qty' => 11, // 1 déjà commandée
            'is_active' => true,
            'served_date' => Carbon::today(),
            'critical_threshold' => 2,
        ]);

        // Create Orders (Commandes)
        $order1 = Order::factory()->create([
            'client_id' => $client1->id,
            'cook_id' => $cook->id,
            'delivery_address_id' => $address_company_1->id,
            'served_date' => Carbon::today(),
            'pickup_time' => '12:00',
            'location_name' => 'Entreprise X - Rue Paix',
            'total_price' => 24.00,
            'status' => 'prête',
            'payment_method' => 'cash',
            'estimated_delivery_time' => '12:10',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order1->id,
            'dish_id' => $dish_courgettes->id,
            'quantity' => 2,
            'unit_price' => 12.00,
        ]);

        // Order 2
        $order2 = Order::factory()->create([
            'client_id' => $client2->id,
            'cook_id' => $cook->id,
            'delivery_address_id' => $address_company_1->id,
            'served_date' => Carbon::today(),
            'pickup_time' => '12:00',
            'location_name' => 'Entreprise X - Rue Paix',
            'total_price' => 24.00,
            'status' => 'en_préparation',
            'payment_method' => 'cash',
            'estimated_delivery_time' => '12:10',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order2->id,
            'dish_id' => $dish_courgettes->id,
            'quantity' => 2,
            'unit_price' => 12.00,
        ]);

        // Order 3
        $order3 = Order::factory()->create([
            'client_id' => $client1->id,
            'cook_id' => $cook->id,
            'delivery_address_id' => $address_company_2->id,
            'served_date' => Carbon::today(),
            'pickup_time' => '12:15',
            'location_name' => 'Startup Y - Avenue Central',
            'total_price' => 15.00,
            'status' => 'reçue',
            'payment_method' => 'cash',
            'estimated_delivery_time' => '12:25',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order3->id,
            'dish_id' => $dish_poulet->id,
            'quantity' => 1,
            'unit_price' => 15.00,
        ]);

        // Order 4 - Résidentiel
        $order4 = Order::factory()->create([
            'client_id' => $client2->id,
            'cook_id' => $cook->id,
            'delivery_address_id' => $address_residential->id,
            'served_date' => Carbon::today(),
            'pickup_time' => '12:30',
            'location_name' => 'Alice - Domicile',
            'total_price' => 8.00,
            'status' => 'reçue',
            'payment_method' => 'ticket_partenaire',
            'estimated_delivery_time' => '12:40',
        ]);

        OrderItem::factory()->create([
            'order_id' => $order4->id,
            'dish_id' => $dish_salade->id,
            'quantity' => 1,
            'unit_price' => 8.00,
        ]);

        // Create Delivery for Today
        $delivery = Delivery::factory()->create([
            'cook_id' => $cook->id,
            'served_date' => Carbon::today(),
            'planned_time' => '12:00',
            'status' => 'en_préparation',
            'route_order' => json_encode([$address_company_1->id, $address_company_2->id, $address_residential->id]),
            'estimated_total_duration' => 45,
        ]);

        // Create Delivery Stops
        DeliveryStop::factory()->create([
            'delivery_id' => $delivery->id,
            'delivery_address_id' => $address_company_1->id,
            'location_name' => 'Entreprise X - Rue Paix',
            'sequence_order' => 1,
            'orders_ids' => json_encode([$order1->id, $order2->id]),
            'status' => 'en_préparation',
            'estimated_arrival_time' => '12:10',
        ]);

        DeliveryStop::factory()->create([
            'delivery_id' => $delivery->id,
            'delivery_address_id' => $address_company_2->id,
            'location_name' => 'Startup Y - Avenue Central',
            'sequence_order' => 2,
            'orders_ids' => json_encode([$order3->id]),
            'status' => 'en_attente',
            'estimated_arrival_time' => '12:25',
        ]);

        DeliveryStop::factory()->create([
            'delivery_id' => $delivery->id,
            'delivery_address_id' => $address_residential->id,
            'location_name' => 'Alice - Domicile',
            'sequence_order' => 3,
            'orders_ids' => json_encode([$order4->id]),
            'status' => 'en_attente',
            'estimated_arrival_time' => '12:40',
        ]);

        // Output results
        $this->command->info('✅ Database seeded successfully!');
        $this->command->info("👤 Admin: {$admin->email}");
        $this->command->info("🍳 Cook: {$cook->email}");
        $this->command->info("👥 Clients: {$client1->email}, {$client2->email}");
        $this->command->info("🍲 Dishes: {$dish_courgettes->name}, {$dish_poulet->name}, {$dish_salade->name}");
        $this->command->info("📦 Orders created: 4");
        $this->command->info("🚚 Delivery routes created: 1");
    }
}

