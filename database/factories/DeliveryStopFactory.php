<?php

namespace Database\Factories;

use App\Models\DeliveryStop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryStop>
 */
class DeliveryStopFactory extends Factory
{
    protected $model = DeliveryStop::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'delivery_id' => 1,
            'delivery_address_id' => 1,
            'location_name' => $this->faker->company(),
            'sequence_order' => $this->faker->numberBetween(1, 5),
            'orders_ids' => json_encode([1, 2, 3]), // IDs des commandes
            'status' => 'en_attente',
            'estimated_arrival_time' => $this->faker->time('H:i'),
            'actual_arrival_time' => null,
        ];
    }

    /**
     * Mark as delivered
     */
    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'livrée',
            'actual_arrival_time' => $this->faker->time('H:i'),
        ]);
    }
}
