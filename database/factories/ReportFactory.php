<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\User;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement(['cook', 'client', 'dish', 'order', 'platform']);

        return [
            'reporter_id' => User::factory(),
            'reported_user_id' => $type !== 'dish' && $type !== 'order' && $type !== 'platform' ? User::factory() : null,
            'reported_dish_id' => $type === 'dish' ? Dish::factory() : null,
            'reported_order_id' => $type === 'order' ? Order::factory() : null,
            'type' => $type,
            'category' => $this->getRandomCategory($type),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'investigating', 'resolved', 'rejected', 'action_taken']),
            'priority' => $this->faker->randomElement([1, 2, 3, 4]),
            'admin_comment' => $this->faker->optional()->sentence(),
            'resolved_at' => $this->faker->optional()->dateTimeBetween('-1 month'),
            'resolved_by' => $this->faker->optional()->randomElement([User::factory()->admin(), null]),
        ];
    }

    /**
     * Get random category for a type
     */
    private function getRandomCategory(string $type): string
    {
        return match($type) {
            'cook', 'client' => $this->faker->randomElement(['behavior', 'abusive', 'fraud']),
            'dish' => $this->faker->randomElement(['quality', 'expired', 'hygiene']),
            'order' => $this->faker->randomElement(['quality_issue', 'missing_items', 'late']),
            'platform' => 'other',
        };
    }

    /**
     * Mark as pending
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'resolved_at' => null,
            'resolved_by' => null,
        ]);
    }

    /**
     * Mark as investigating
     */
    public function investigating(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'investigating',
        ]);
    }

    /**
     * Mark as resolved
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => User::factory()->admin(),
        ]);
    }

    /**
     * Mark as high priority
     */
    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $this->faker->randomElement([3, 4]),
        ]);
    }

    /**
     * For a specific reporter
     */
    public function byReporter(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'reporter_id' => $user->id,
        ]);
    }

    /**
     * Against a specific user
     */
    public function aboutUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'reported_user_id' => $user->id,
        ]);
    }
}
