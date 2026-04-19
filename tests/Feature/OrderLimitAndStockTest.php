<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class OrderLimitAndStockTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Cook can create 15 orders on same day
     */
    public function test_cook_can_create_15_orders_same_day()
    {
        $cook = User::factory()->cook()->create();
        $client = User::factory()->client()->create();

        // Create 15 dishes
        $dishes = Dish::factory(15)->for($cook, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 100,
        ]);

        // Create 15 orders (one per dish)
        $count = 0;
        foreach ($dishes as $dish) {
            $response = $this->actingAs($client)->post(route('orders.store'), [
                'items' => [['dish_id' => $dish->id, 'quantity' => 1]],
                'pickup_time' => '18:00',
            ]);
            
            // Should redirect to order show page
            if ($response->status() === 302) {
                $count++;
            }
        }

        $this->assertEquals(15, $count, 'Should successfully create 15 orders');
        $this->assertEquals(15, Order::where('cook_id', $cook->id)->whereDate('created_at', today())->count());
    }

    /**
     * Test 2: Cook cannot create 16th order on same day
     */
    public function test_cook_cannot_create_16th_order_same_day()
    {
        $cook = User::factory()->cook()->create();
        $client = User::factory()->client()->create();

        // Create 15 orders first
        for ($i = 0; $i < 15; $i++) {
            $dish = Dish::factory()->for($cook, 'cook')->create([
                'served_date' => today(),
                'is_active' => true,
                'available_qty' => 100,
            ]);

            $this->actingAs($client)->post(route('orders.store'), [
                'items' => [['dish_id' => $dish->id, 'quantity' => 1]],
                'pickup_time' => '18:00',
            ]);
        }

        // Try 16th order - should fail
        $dish16 = Dish::factory()->for($cook, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 100,
        ]);

        $response = $this->actingAs($client)->post(route('orders.store'), [
            'items' => [['dish_id' => $dish16->id, 'quantity' => 1]],
            'pickup_time' => '18:00',
        ]);

        $this->assertEquals(302, $response->status(), 'Should redirect');
        $this->assertStringContainsString('maximum de 15 commandes', session('error'));
    }

    /**
     * Test 3: Order limit resets next day
     */
    public function test_order_limit_resets_next_day()
    {
        $cook = User::factory()->cook()->create();
        $client = User::factory()->client()->create();

        // Create 15 orders today
        for ($i = 0; $i < 15; $i++) {
            $dish = Dish::factory()->for($cook, 'cook')->create([
                'served_date' => today(),
                'is_active' => true,
                'available_qty' => 100,
            ]);

            $this->actingAs($client)->post(route('orders.store'), [
                'items' => [['dish_id' => $dish->id, 'quantity' => 1]],
                'pickup_time' => '18:00',
            ]);
        }

        // Verify today's count is 15
        $this->assertEquals(15, Order::where('cook_id', $cook->id)->whereDate('created_at', today())->count());

        // Move to tomorrow
        Carbon::setTestNow(Carbon::tomorrow());

        // Create new dish for tomorrow
        $dishTomorrow = Dish::factory()->for($cook, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 100,
        ]);

        // Should be able to create order tomorrow
        $response = $this->actingAs($client)->post(route('orders.store'), [
            'items' => [['dish_id' => $dishTomorrow->id, 'quantity' => 1]],
            'pickup_time' => '18:00',
        ]);

        $this->assertEquals(302, $response->status(), 'Should allow order on next day');
        $this->assertEquals(1, Order::where('cook_id', $cook->id)->whereDate('created_at', today())->count());
    }

    /**
     * Test 4: Different cooks have independent limits
     */
    public function test_different_cooks_have_independent_limits()
    {
        $cook1 = User::factory()->cook()->create();
        $cook2 = User::factory()->cook()->create();
        $client = User::factory()->client()->create();

        // Create 15 orders for cook1
        for ($i = 0; $i < 15; $i++) {
            $dish = Dish::factory()->for($cook1, 'cook')->create([
                'served_date' => today(),
                'is_active' => true,
                'available_qty' => 100,
            ]);

            $this->actingAs($client)->post(route('orders.store'), [
                'items' => [['dish_id' => $dish->id, 'quantity' => 1]],
                'pickup_time' => '18:00',
            ]);
        }

        // Cook2 should still be able to create orders
        $dish2 = Dish::factory()->for($cook2, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 100,
        ]);

        $response = $this->actingAs($client)->post(route('orders.store'), [
            'items' => [['dish_id' => $dish2->id, 'quantity' => 1]],
            'pickup_time' => '18:00',
        ]);

        $this->assertEquals(302, $response->status(), 'Cook2 should be able to create order');
        $this->assertEquals(1, Order::where('cook_id', $cook2->id)->whereDate('created_at', today())->count());
    }

    /**
     * Test 5: Stock rupture - pessimistic locking prevents overselling
     */
    public function test_stock_not_oversold_with_pessimistic_locking()
    {
        $cook = User::factory()->cook()->create();
        $client1 = User::factory()->client()->create();
        $client2 = User::factory()->client()->create();

        // Create dish with limited stock
        $dish = Dish::factory()->for($cook, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 5, // Only 5 available
        ]);

        // Create 5 orders
        for ($i = 0; $i < 5; $i++) {
            $client = $i < 3 ? $client1 : $client2;
            $response = $this->actingAs($client)->post(route('orders.store'), [
                'items' => [['dish_id' => $dish->id, 'quantity' => 1]],
                'pickup_time' => '18:00',
            ]);

            $this->assertEquals(302, $response->status(), "Order $i should succeed");
        }

        // Verify exactly 5 orders created
        $this->assertEquals(5, Order::count());

        // Try 6th order - should fail
        $client3 = User::factory()->client()->create();
        $response = $this->actingAs($client3)->post(route('orders.store'), [
            'items' => [['dish_id' => $dish->id, 'quantity' => 1]],
            'pickup_time' => '18:00',
        ]);

        $this->assertStringContainsString('Stock insuffisant', session('error'));

        // Verify stock is exactly 0 (not negative!)
        $dish->refresh();
        $this->assertEquals(0, $dish->available_qty);
    }

    /**
     * Test 6: Stock validation with pessimistic lock
     */
    public function test_stock_validation_with_exact_quantity()
    {
        $cook = User::factory()->cook()->create();
        $client = User::factory()->client()->create();

        $dish = Dish::factory()->for($cook, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 10,
        ]);

        // Order exactly 10 items (full stock)
        $response = $this->actingAs($client)->post(route('orders.store'), [
            'items' => [['dish_id' => $dish->id, 'quantity' => 10]],
            'pickup_time' => '18:00',
        ]);

        $this->assertEquals(302, $response->status(), 'Should allow order for exact stock');

        // Verify stock is 0
        $dish->refresh();
        $this->assertEquals(0, $dish->available_qty);

        // Try to order 1 more - should fail
        $client2 = User::factory()->client()->create();
        $response = $this->actingAs($client2)->post(route('orders.store'), [
            'items' => [['dish_id' => $dish->id, 'quantity' => 1]],
            'pickup_time' => '18:00',
        ]);

        $this->assertStringContainsString('Stock insuffisant', session('error'));
    }

    /**
     * Test 7: Multiple items from same cook
     */
    public function test_order_multiple_items_same_cook()
    {
        $cook = User::factory()->cook()->create();
        $client = User::factory()->client()->create();

        $dish1 = Dish::factory()->for($cook, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 50,
        ]);

        $dish2 = Dish::factory()->for($cook, 'cook')->create([
            'served_date' => today(),
            'is_active' => true,
            'available_qty' => 50,
        ]);

        // Order both dishes in single order
        $response = $this->actingAs($client)->post(route('orders.store'), [
            'items' => [
                ['dish_id' => $dish1->id, 'quantity' => 3],
                ['dish_id' => $dish2->id, 'quantity' => 2],
            ],
            'pickup_time' => '18:00',
        ]);

        $this->assertEquals(302, $response->status());

        // Only 1 order should be created (not 2)
        $this->assertEquals(1, Order::count());

        // Verify stock decreased for both dishes
        $this->assertEquals(47, $dish1->fresh()->available_qty);
        $this->assertEquals(48, $dish2->fresh()->available_qty);
    }
}
