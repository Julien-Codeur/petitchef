<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Complete End-to-End Workflow Test
 * Tests the entire user journey from registration to order completion
 */
class CompleteWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_workflow_from_registration_to_delivery()
    {
        // ============================================================
        // STEP 1: INSCRIPTION (Registration)
        // ============================================================
        // Create a verified cook directly (registration flow is tested elsewhere)
        $cook = User::factory()->create([
            'name' => 'Chef Pierre',
            'email' => 'pierre@example.com',
            'role' => 'cook',
            'is_verified' => true,
        ]);

        // ============================================================
        // STEP 2: CONNEXION (Login)
        // ============================================================
        $this->actingAs($cook);

        // ============================================================
        // STEP 3: COOK CRÉE PLATS (Create Dishes)
        // ============================================================
        $dishData = [
            'name' => 'Coq au Vin',
            'description' => 'Plat traditionnel français',
            'price' => 18.50,
            'available_qty' => 10,
            'served_date' => today()->toDateString(),
        ];

        $response = $this->post(route('dishes.store'), $dishData);
        $response->assertRedirect(route('dishes.index'));

        $dish = Dish::where('name', 'Coq au Vin')->firstOrFail();
        $this->assertEquals($cook->id, $dish->cook_id);
        $this->assertTrue($dish->is_active);
        $this->assertEquals(10, $dish->available_qty);

        // ============================================================
        // STEP 4: CLIENT SE CONNECTE & VOIT MENU (Client Sees Menu)
        // ============================================================
        // Create a client user
        $client = User::factory()->create([
            'role' => 'client',
            'is_verified' => true,
        ]);

        $this->actingAs($client);

        // Client views menu
        $response = $this->get(route('dishes.index'));
        $response->assertOk();
        $response->assertViewHas('dishes');

        // Verify dish is visible to client
        $dishes = $response->original->getData()['dishes'];
        $this->assertTrue($dishes->pluck('id')->contains($dish->id));

        // ============================================================
        // STEP 5: CLIENT AJOUTE AU PANIER (Add to Cart)
        // ============================================================
        $response = $this->post(route('cart.add', $dish), [
            'quantity' => 2,
        ]);
        $response->assertRedirect();

        // Verify cart contains item
        $cart = session()->get('cart');
        $this->assertNotNull($cart);
        $this->assertEquals(2, $cart[$dish->id]);

        // ============================================================
        // STEP 6: CLIENT VALIDE COMMANDE (Checkout)
        // ============================================================
        $response = $this->get(route('orders.create'));
        $response->assertOk();

        // ============================================================
        // STEP 7: DB VÉRIFIE STOCK (Database Validates Stock)
        // ============================================================
        $orderData = [
            'items' => [
                ['dish_id' => $dish->id, 'quantity' => 2],
            ],
            'pickup_time' => '18:00',
            'note_client' => 'Sans sauce, s\'il vous plaît',
        ];

        $response = $this->post(route('orders.store'), $orderData);
        $response->assertRedirect();

        // ============================================================
        // STEP 8: COMMANDE CRÉÉE (Order Created)
        // ============================================================
        $order = Order::where('client_id', $client->id)->firstOrFail();
        $this->assertEquals('received', $order->status);
        $this->assertEquals($cook->id, $order->cook_id);
        $this->assertEquals(37.00, $order->total_price); // 18.50 * 2
        $this->assertEquals('Sans sauce, s\'il vous plaît', $order->note_client);

        // Verify stock decreased
        $dish->refresh();
        $this->assertEquals(8, $dish->available_qty);

        // Verify order items created with unit_price preserved
        $orderItem = $order->items->first();
        $this->assertEquals(2, $orderItem->quantity);
        $this->assertEquals(18.50, $orderItem->unit_price);

        // Verify cart cleared
        $this->assertNull(session()->get('cart'));

        // ============================================================
        // STEP 9: CLIENT PEUT VOIR SA COMMANDE (View Order)
        // ============================================================
        $response = $this->get(route('orders.show', $order));
        $response->assertOk();
        $response->assertSee('Coq au Vin');
        $response->assertSee('Reçue'); // Status in French

        // ============================================================
        // STEP 10: COOK SE CONNECTE (Cook Logs In)
        // ============================================================
        $this->actingAs($cook);

        // Cook sees their orders
        $response = $this->get(route('orders.index'));
        $response->assertOk();

        // ============================================================
        // STEP 11: COOK PRÉPARE (Cook Updates to Preparing)
        // ============================================================
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => 'preparing',
        ]);
        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('preparing', $order->status);

        // ============================================================
        // STEP 12: COOK MARQUE PRÊTE (Cook Updates to Ready)
        // ============================================================
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => 'ready',
        ]);
        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('ready', $order->status);

        // ============================================================
        // STEP 13: CLIENT RÉCUPÈRE (Client Marks as Delivered)
        // ============================================================
        $this->actingAs($client);

        // Client verifies order is ready
        $response = $this->get(route('orders.show', $order));
        $response->assertOk();
        $response->assertSee('Prête'); // Status in French

        // ============================================================
        // STEP 14: COMMANDE LIVRÉE (Order Delivered)
        // ============================================================
        $this->actingAs($cook);

        $response = $this->patch(route('orders.update-status', $order), [
            'status' => 'delivered',
        ]);
        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('delivered', $order->status);

        // ============================================================
        // STEP 15: FIN DE JOURNÉE - CLÔTURE (End of Day - Close Service)
        // ============================================================
        // Cook closes service for the day
        $response = $this->post(route('dishes.close-service'));
        $response->assertRedirect();

        // Verify all dishes for today are inactive
        $dish->refresh();
        $this->assertFalse($dish->is_active);

        // Verify clients no longer see closed dishes
        $this->actingAs($client);
        $response = $this->get(route('dishes.index'));
        $dishesVisible = $response->original->getData()['dishes'];
        $this->assertFalse($dishesVisible->pluck('id')->contains($dish->id));

        // ============================================================
        // SUMMARY
        // ============================================================
        // Verify complete workflow
        $this->assertEquals('delivered', $order->status);
        $this->assertEquals(37.00, $order->total_price);
        $this->assertFalse($dish->is_active);
    }

    /**
     * Test: Client cannot order more stock than available
     */
    public function test_cannot_order_more_than_available_stock()
    {
        $cook = User::factory()->create(['role' => 'cook', 'is_verified' => true]);
        $client = User::factory()->create(['role' => 'client']);

        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'available_qty' => 3,
        ]);

        $this->actingAs($client);

        // Try to order more than available
        $response = $this->post(route('cart.add', $dish), [
            'quantity' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test: Client cannot cancel order after it's being prepared
     */
    public function test_cannot_cancel_order_after_preparing_started()
    {
        $cook = User::factory()->create(['role' => 'cook', 'is_verified' => true]);
        $client = User::factory()->create(['role' => 'client']);

        $order = Order::factory()->create([
            'client_id' => $client->id,
            'cook_id' => $cook->id,
            'status' => 'preparing',
        ]);

        $this->actingAs($client);

        $response = $this->delete(route('orders.destroy', $order));

        // Should be forbidden or redirected
        $this->assertTrue(
            $response->status() === 403 || $response->status() === 302
        );

        // Order should still exist and not be cancelled
        $order->refresh();
        $this->assertNotEquals('cancelled', $order->status);
    }

    /**
     * Test: Cook can only see their own orders
     */
    public function test_cook_only_sees_own_orders()
    {
        $cook1 = User::factory()->create(['role' => 'cook', 'is_verified' => true]);
        $cook2 = User::factory()->create(['role' => 'cook', 'is_verified' => true]);
        $client = User::factory()->create(['role' => 'client']);

        $order1 = Order::factory()->create(['cook_id' => $cook1->id, 'client_id' => $client->id]);
        $order2 = Order::factory()->create(['cook_id' => $cook2->id, 'client_id' => $client->id]);

        $this->actingAs($cook1);
        $response = $this->get(route('orders.index'));

        $orders = $response->original->getData()['orders'];
        
        // Cook1 should see their own order
        $this->assertTrue($orders->pluck('id')->contains($order1->id));
        
        // Cook1 should NOT see cook2's order
        $this->assertFalse($orders->pluck('id')->contains($order2->id));
    }

    /**
     * Test: Stock is restored if order is cancelled
     */
    public function test_stock_restored_when_order_cancelled()
    {
        $cook = User::factory()->create(['role' => 'cook', 'is_verified' => true]);
        $client = User::factory()->create(['role' => 'client']);

        $dish = Dish::factory()->create([
            'cook_id' => $cook->id,
            'available_qty' => 10,
        ]);

        // Create an order through the controller (which properly reduces stock)
        $this->actingAs($client);
        
        $orderData = [
            'items' => [
                ['dish_id' => $dish->id, 'quantity' => 3],
            ],
            'pickup_time' => '18:00',
            'note_client' => 'Test order',
        ];

        $this->post(route('orders.store'), $orderData);

        $order = Order::where('client_id', $client->id)->firstOrFail();

        // Verify stock was decreased
        $dish->refresh();
        $this->assertEquals(7, $dish->available_qty);

        // Cancel order (and stock should be restored)
        $response = $this->delete(route('orders.destroy', $order));
        
        $response->assertRedirect();

        // Verify stock was restored
        $dish->refresh();
        $this->assertEquals(10, $dish->available_qty);

        // Verify order is cancelled
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }
}
