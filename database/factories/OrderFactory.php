<?php

namespace Database\Factories;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => 1,
            'cook_id' => 1,
            'delivery_address_id' => 1,
            'served_date' => Carbon::today(),
            'pickup_time' => $this->faker->time('H:i'),
            'location_name' => $this->faker->company(),
            'total_price' => $this->faker->numberBetween(1500, 5000) / 100,
            'status' => $this->faker->randomElement(['reçue', 'en_préparation', 'prête', 'livrée']),
            'payment_method' => $this->faker->randomElement(['cash', 'ticket_partenaire']),
            'notes_client' => $this->faker->optional()->sentence(),
            'estimated_delivery_time' => $this->faker->time('H:i'),
        ];
    }

    /**
     * Mark as received
     */
    public function received(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reçue',
        ]);
    }

    /**
     * Mark as in preparation
     */
    public function inPreparation(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_préparation',
        ]);
    }

    /**
     * Mark as ready
     */
    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'prête',
        ]);
    }

    /**
     * Mark as delivered
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'livrée',
        ]);
    }
}
