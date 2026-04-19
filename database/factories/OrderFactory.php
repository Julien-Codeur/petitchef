<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $client = User::factory()->create(['role' => 'client']);
        $cook = User::factory()->create(['role' => 'cook']);

        return [
            'client_id' => $client->id,
            'cook_id' => $cook->id,
            'total_price' => $this->faker->randomFloat(2, 10, 100),
            'pickup_time' => $this->faker->time('H:i'),
            'status' => 'received',
            'note_client' => $this->faker->optional()->sentence(),
        ];
    }

    public function preparing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'preparing',
        ]);
    }

    public function ready(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'ready',
        ]);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
