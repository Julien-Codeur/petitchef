<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dish>
 */
class DishFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cook_id' => 1, // À assigner manuellement en général
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(10),
            'price' => $this->faker->numberBetween(800, 2500) / 100, // 8€ à 25€
            'initial_qty' => $this->faker->numberBetween(10, 50),
            'available_qty' => $this->faker->numberBetween(5, 50),
            'photo_path' => 'https://via.placeholder.com/300',
            'is_active' => true,
            'served_date' => Carbon::today(),
            'critical_threshold' => $this->faker->numberBetween(1, 5),
            'is_critical_notified' => false,
        ];
    }

    /**
     * Mark as exhausted
     */
    public function exhausted(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_qty' => 0,
        ]);
    }

    /**
     * Mark as critical
     */
    public function critical(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'available_qty' => $attributes['critical_threshold'] - 1,
            ];
        });
    }
}
