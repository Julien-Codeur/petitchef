<?php

namespace Database\Factories;

use App\Models\OrderDish;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderDishFactory extends Factory
{
    protected $model = OrderDish::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory()->id,
            'dish_id' => Dish::factory()->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'unit_price' => $this->faker->randomFloat(2, 5, 50),
        ];
    }
}
