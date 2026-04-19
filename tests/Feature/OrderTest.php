<?php

namespace Tests\Feature;

use App\Models\Dish;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private User $cook;
    private User $client;
    private Dish $dish;

    protected function setUp(): void
    {
        parent::setUp();

        // Créer un cuisinier
        $this->cook = User::factory()->create([
            'role' => 'cook',
            'email' => 'cook@test.local',
        ]);

        // Créer un client
        $this->client = User::factory()->create([
            'role' => 'client',
            'email' => 'client@test.local',
        ]);

        // Créer un plat pour aujourd'hui
        $this->dish = Dish::factory()->create([
            'cook_id' => $this->cook->id,
            'available_qty' => 10,
            'is_active' => true,
        ]);
    }

    /**
     * Test 1: Créer une commande avec succès (happy path)
     */
    public function test_client_can_create_order_successfully()
    {
        $this->actingAs($this->client);

        // Préparer le panier en session
        session()->put('cart', [$this->dish->id => 2]);

        // Passer une commande
        $response = $this->post(route('orders.store'), [
            'items' => [
                [
                    'dish_id' => $this->dish->id,
                    'quantity' => 2,
                ]
            ],
            'pickup_time' => '18:00',
            'note_client' => 'Sans oignon',
        ]);

        // Vérifier redirection succès
        $response->assertRedirect();

        // Vérifier que la commande a été créée
        $this->assertDatabaseHas('orders', [
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'received',
            'pickup_time' => '18:00',
            'note_client' => 'Sans oignon',
            'total_price' => 2 * $this->dish->price,
        ]);

        // Vérifier que le stock a diminué
        $this->dish->refresh();
        $this->assertEquals(8, $this->dish->available_qty);

        // Vérifier que le panier a été vidé
        $this->assertEmpty(session()->get('cart', []));
    }

    /**
     * Test 2: Impossible de commander avec stock insuffisant
     */
    public function test_cannot_create_order_with_insufficient_stock()
    {
        $this->actingAs($this->client);

        // Le plat n'a que 5 en stock
        $this->dish->update(['available_qty' => 5]);

        session()->put('cart', [$this->dish->id => 10]);

        // Essayer de commander 10 plats
        $response = $this->post(route('orders.store'), [
            'items' => [
                [
                    'dish_id' => $this->dish->id,
                    'quantity' => 10,
                ]
            ],
            'pickup_time' => '18:00',
        ]);

        // Vérifier l'erreur flash
        $response->assertSessionHas('error');

        // Vérifier qu'aucune commande n'a été créée
        $this->assertDatabaseMissing('orders', [
            'client_id' => $this->client->id,
        ]);

        // Vérifier que le stock n'a pas changé (rollback)
        $this->dish->refresh();
        $this->assertEquals(5, $this->dish->available_qty);
    }

    /**
     * Test 3: Impossible de commander chez deux cuisiniers à la fois
     */
    public function test_cannot_order_from_multiple_cooks()
    {
        // Créer un deuxième cuisinier et un plat
        $cook2 = User::factory()->create(['role' => 'cook']);
        $dish2 = Dish::factory()->create([
            'cook_id' => $cook2->id,
            'available_qty' => 10,
        ]);

        $this->actingAs($this->client);

        session()->put('cart', [
            $this->dish->id => 1,
            $dish2->id => 1,
        ]);

        // Essayer de commander chez deux cuisiniers
        $response = $this->post(route('orders.store'), [
            'items' => [
                ['dish_id' => $this->dish->id, 'quantity' => 1],
                ['dish_id' => $dish2->id, 'quantity' => 1],
            ],
            'pickup_time' => '18:00',
        ]);

        // Vérifier l'erreur flash
        $response->assertSessionHas('error');

        // Vérifier qu'aucune commande n'a été créée
        $this->assertDatabaseMissing('orders', [
            'client_id' => $this->client->id,
        ]);
    }

    /**
     * Test 4: Client peut annuler une commande "received"
     */
    public function test_client_can_cancel_received_order()
    {
        // Créer une commande
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'received',
        ]);

        // Créer des items de commande
        $order->items()->create([
            'dish_id' => $this->dish->id,
            'quantity' => 3,
            'unit_price' => $this->dish->price,
        ]);

        $initialStock = $this->dish->available_qty;

        $this->actingAs($this->client);

        // Annuler la commande
        $response = $this->delete(route('orders.destroy', $order));

        // Vérifier redirection
        $response->assertRedirect();

        // Vérifier que le statut a changé en cancelled
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // Vérifier que le stock a été restauré
        $this->dish->refresh();
        $this->assertEquals($initialStock + 3, $this->dish->available_qty);
    }

    /**
     * Test 5: Client NE PEUT PAS annuler une commande en cours de préparation
     */
    public function test_client_cannot_cancel_preparing_order()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'preparing',
        ]);

        $this->actingAs($this->client);

        $response = $this->delete(route('orders.destroy', $order));

        // Vérifier l'accès refusé
        $response->assertForbidden();

        // Vérifier que le statut n'a pas changé
        $order->refresh();
        $this->assertEquals('preparing', $order->status);
    }

    /**
     * Test 6: Cuisinier peut changer le statut de la commande
     */
    public function test_cook_can_update_order_status()
    {
        $order = Order::factory()->create([
            'client_id' => $this->client->id,
            'cook_id' => $this->cook->id,
            'status' => 'received',
        ]);

        $this->actingAs($this->cook);

        // Passer au statut "preparing"
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => 'preparing',
        ]);

        $response->assertRedirect();

        $order->refresh();
        $this->assertEquals('preparing', $order->status);

        // Passer au statut "ready"
        $response = $this->patch(route('orders.update-status', $order), [
            'status' => 'ready',
        ]);

        $order->refresh();
        $this->assertEquals('ready', $order->status);
    }

    /**
     * Test 7: Client ne peut pas voir les commandes des autres clients
     */
    public function test_client_can_only_see_own_orders()
    {
        // Créer une autre client
        $otherClient = User::factory()->create(['role' => 'client']);

        // Créer une commande pour chaque client
        $myOrder = Order::factory()->create(['client_id' => $this->client->id]);
        $otherOrder = Order::factory()->create(['client_id' => $otherClient->id]);

        $this->actingAs($this->client);

        // Essayer d'accéder à la commande d'un autre client
        $response = $this->get(route('orders.show', $otherOrder));

        $response->assertForbidden();

        // Mais peut accéder à sa propre commande
        $response = $this->get(route('orders.show', $myOrder));
        $response->assertOk();
    }

    /**
     * Test 8: Validation de la pickup_time
     */
    public function test_order_requires_valid_pickup_time()
    {
        $this->actingAs($this->client);

        session()->put('cart', [$this->dish->id => 1]);

        // Sans pickup_time
        $response = $this->post(route('orders.store'), [
            'items' => [
                ['dish_id' => $this->dish->id, 'quantity' => 1],
            ],
        ]);

        $response->assertSessionHasErrors('pickup_time');
    }

    /**
     * Test 9: Vérifier que les items de commande gardent le prix au moment de la commande
     */
    public function test_order_saves_unit_price_at_order_time()
    {
        $originalPrice = $this->dish->price;
        $this->dish->update(['price' => $originalPrice + 5]); // Changer le prix

        $this->actingAs($this->client);
        session()->put('cart', [$this->dish->id => 1]);

        $this->post(route('orders.store'), [
            'items' => [
                ['dish_id' => $this->dish->id, 'quantity' => 1],
            ],
            'pickup_time' => '18:00',
        ]);

        // Vérifier que le prix sauvegardé est celui du moment de la commande
        $orderItem = Order::first()->items->first();
        $this->assertEqualsWithDelta(round($originalPrice + 5, 2), round($orderItem->unit_price, 2), 0.01);
    }
}
