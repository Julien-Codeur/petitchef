<?php

namespace Database\Factories;

use App\Models\Dish;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DishFactory extends Factory
{
    protected $model = Dish::class;

    public function definition(): array
    {
        return [
            'cook_id' => User::factory()->create(['role' => 'cook'])->id,
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 5, 50),
            'available_qty' => $this->faker->numberBetween(0, 20),
            'photo_path' => null,
            'served_date' => now()->toDateString(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withoutStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'available_qty' => 0,
        ]);
    }
}
