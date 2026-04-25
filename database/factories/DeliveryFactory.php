<?php

namespace Database\Factories;

use App\Models\Delivery;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Delivery>
 */
class DeliveryFactory extends Factory
{
    protected $model = Delivery::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cook_id' => 1,
            'served_date' => Carbon::today(),
            'planned_time' => '12:00',
            'status' => 'en_préparation',
            'route_order' => json_encode([1, 2, 3]), // IDs des adresses
            'estimated_total_duration' => $this->faker->numberBetween(30, 120),
        ];
    }

    /**
     * Mark as ready to leave
     */
    public function readyToLeave(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'prête_à_partir',
        ]);
    }

    /**
     * Mark as in route
     */
    public function inRoute(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'en_route',
        ]);
    }

    /**
     * Mark as complete
     */
    public function complete(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'complète',
        ]);
    }
}
