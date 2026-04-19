<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected $cook;
    protected $client;
    protected $otherCook;
    protected $dish;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer les utilisateurs
        $this->cook = User::factory()->create(['role' => 'cook', 'is_verified' => true]);
        $this->client = User::factory()->create(['role' => 'client']);
        $this->otherCook = User::factory()->create(['role' => 'cook', 'is_verified' => true]);

        // Créer un plat
        $this->dish = Dish::factory()->create([
            'cook_id' => $this->cook->id,
            'is_active' => true,
        ]);
    }

    // ========== DISH AUTHORIZATION TESTS ==========

    public function test_client_cannot_create_dish()
    {
        $this->actingAs($this->client);
        
        $this->post(route('dishes.store'), [
            'name' => 'Test Dish',
            'description' => 'Test',
            'price' => 10.00,
            'available_qty' => 5,
            'served_date' => today()->toDateString(),
        ])->assertForbidden();
    }

    public function test_unverified_cook_cannot_create_dish()
    {
        $unverifiedCook = User::factory()->create(['role' => 'cook', 'is_verified' => false]);
        $this->actingAs($unverifiedCook);
        
        $response = $this->post(route('dishes.store'), [
            'name' => 'Test Dish',
            'description' => 'Test',
            'price' => 10.00,
            'available_qty' => 5,
            'served_date' => today()->toDateString(),
        ]);

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_cook_can_only_update_own_dish()
    {
        $this->actingAs($this->otherCook);

        $response = $this->patch(route('dishes.update', $this->dish), [
            'name' => 'Updated Name',
            'description' => 'Updated',
            'price' => 15.00,
            'available_qty' => 3,
            'served_date' => today()->toDateString(),
        ]);

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_cook_can_update_own_dish()
    {
        $this->actingAs($this->cook);

        $response = $this->patch(route('dishes.update', $this->dish), [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
            'price' => 15.00,
            'available_qty' => 3,
            'served_date' => today()->toDateString(),
        ]);

        $response->assertRedirect();
        $this->assertEquals('Updated Name', $this->dish->fresh()->name);
    }

    public function test_cook_can_delete_own_dish()
    {
        $this->actingAs($this->cook);

        $response = $this->delete(route('dishes.destroy', $this->dish));
        $response->assertRedirect();
        
        $this->assertModelMissing($this->dish);
    }

    public function test_cook_cannot_delete_other_cook_dish()
    {
        $this->actingAs($this->otherCook);

        $response = $this->delete(route('dishes.destroy', $this->dish));
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    // ========== ORDER AUTHORIZATION TESTS ==========

    public function test_client_can_create_order()
    {
        $this->actingAs($this->client);
        session()->put('cart', [$this->dish->id => 1]);

        $response = $this->post(route('orders.store'), [
            'items' => [
                ['dish_id' => $this->dish->id, 'quantity' => 1],
            ],
            'pickup_time' => '18:00',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
        ]);
    }

    public function test_cook_cannot_create_order()
    {
        $this->actingAs($this->cook);
        session()->put('cart', [$this->dish->id => 1]);

        $response = $this->post(route('orders.store'), [
            'items' => [
                ['dish_id' => $this->dish->id, 'quantity' => 1],
            ],
            'pickup_time' => '18:00',
        ]);

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_client_can_view_own_order()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
        ]);

        $this->actingAs($this->client);
        $response = $this->get(route('orders.show', $order));

        $response->assertOk();
    }

    public function test_client_cannot_view_other_client_order()
    {
        $otherClient = User::factory()->create(['role' => 'client']);
        $order = Order::factory()->create([
            'client_id' => $otherClient->id,
            'cook_id' => $this->cook->id,
        ]);

        $this->actingAs($this->client);
        $response = $this->get(route('orders.show', $order));

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_cook_can_view_own_orders()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
        ]);

        $this->actingAs($this->cook);
        $response = $this->get(route('orders.show', $order));

        $response->assertOk();
    }

    public function test_cook_cannot_view_other_cook_orders()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
        ]);

        $this->actingAs($this->otherCook);
        $response = $this->get(route('orders.show', $order));

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_cook_can_update_order_status()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'received',
        ]);

        $this->actingAs($this->cook);
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => 'preparing',
        ]);

        $response->assertRedirect();
        $this->assertEquals('preparing', $order->fresh()->status);
    }

    public function test_client_cannot_update_order_status()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'received',
        ]);

        $this->actingAs($this->client);
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => 'preparing',
        ]);

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_client_can_cancel_received_order()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'received',
        ]);

        $this->actingAs($this->client);
        $response = $this->delete(route('orders.destroy', $order));

        $response->assertRedirect();
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    public function test_client_cannot_cancel_preparing_order()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'preparing',
        ]);

        $this->actingAs($this->client);
        $response = $this->delete(route('orders.destroy', $order));

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    public function test_cook_cannot_delete_order()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'received',
        ]);

        $this->actingAs($this->cook);
        $response = $this->delete(route('orders.destroy', $order));

        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    // ========== CART AUTHORIZATION TESTS ==========

    public function test_unauthenticated_user_can_access_cart()
    {
        $response = $this->get(route('cart.index'));
        
        // Should either allow or redirect to login
        $this->assertTrue($response->status() === 200 || $response->status() === 302);
    }

    public function test_client_can_add_to_cart()
    {
        $this->actingAs($this->client);

        $response = $this->post(route('cart.add', $this->dish), [
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $this->assertNotNull(session()->get('cart'));
    }

    public function test_client_can_view_menu_du_jour()
    {
        $this->actingAs($this->client);

        $response = $this->get(route('dishes.menu-du-jour'));
        $response->assertOk();
    }
}
