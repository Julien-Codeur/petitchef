<?php

namespace Database\Factories;

use App\Models\DeliveryAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryAddress>
 */
class DeliveryAddressFactory extends Factory
{
    protected $model = DeliveryAddress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cook_id' => 1,
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'latitude' => $this->faker->latitude(48.8, 48.9), // Paris
            'longitude' => $this->faker->longitude(2.2, 2.4), // Paris
            'is_regular' => $this->faker->boolean(60), // 60% regular
        ];
    }

    /**
     * Create a residential address
     */
    public function residential(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->firstName() . ' - Domicile',
            'is_regular' => false,
        ]);
    }

    /**
     * Create a company address
     */
    public function company(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => $this->faker->company(),
            'is_regular' => true,
        ]);
    }
}
