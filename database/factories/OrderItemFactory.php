<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => 1,
            'dish_id' => 1,
            'quantity' => $this->faker->numberBetween(1, 5),
            'unit_price' => $this->faker->numberBetween(800, 2500) / 100,
            'customization_notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }
}
