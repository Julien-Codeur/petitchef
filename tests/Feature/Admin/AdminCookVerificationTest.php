<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\Dish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Admin Cook Verification Workflow Test
 */
class AdminCookVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Admin can access cook management
     */
    public function test_admin_can_access_cook_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($admin);
        $response = $this->get(route('admin.cooks.index'));
        $response->assertOk();

        // Client cannot access
        $this->actingAs($client);
        $response = $this->get(route('admin.cooks.index'));
        $this->assertTrue($response->status() === 403 || $response->status() === 404);
    }

    /**
     * Test: Admin can see pending cooks
     */
    public function test_admin_can_filter_pending_cooks()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create one verified and one pending cook
        User::factory()->create([
            'role' => 'cook',
            'is_verified' => true,
            'name' => 'Verified Cook',
        ]);
        
        User::factory()->create([
            'role' => 'cook',
            'is_verified' => false,
            'name' => 'Pending Cook',
        ]);

        $this->actingAs($admin);
        
        // View all
        $response = $this->get(route('admin.cooks.index', ['filter' => 'all']));
        $response->assertOk();
        
        // Filter pending only
        $response = $this->get(route('admin.cooks.index', ['filter' => 'pending']));
        $response->assertOk();
        $response->assertSee('Pending Cook');
    }

    /**
     * Test: Admin can view cook profile
     */
    public function test_admin_can_view_cook_profile()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cook = User::factory()->create([
            'role' => 'cook',
            'is_verified' => false,
            'name' => 'Pierre Dupont',
        ]);

        $this->actingAs($admin);
        $response = $this->get(route('admin.cooks.show', $cook));
        
        $response->assertOk();
        $response->assertSee('Pierre Dupont');
        $response->assertSee('EN ATTENTE');
    }

    /**
     * Test: Admin can approve a cook
     */
    public function test_admin_can_approve_cook()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cook = User::factory()->create([
            'role' => 'cook',
            'is_verified' => false,
        ]);

        $this->actingAs($admin);
        $response = $this->post(route('admin.cooks.approve', $cook));
        
        $response->assertRedirect();
        
        // Verify cook is now approved
        $cook->refresh();
        $this->assertTrue($cook->is_verified);
    }

    /**
     * Test: Admin can reject a cook
     */
    public function test_admin_can_reject_cook()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cook = User::factory()->create([
            'role' => 'cook',
            'is_verified' => false,
        ]);

        $this->actingAs($admin);
        $response = $this->post(route('admin.cooks.reject', $cook), [
            'reason' => 'Informations insuffisantes ou non fiables',
        ]);
        
        $response->assertRedirect();
        
        // Verify cook is still not verified
        $cook->refresh();
        $this->assertFalse($cook->is_verified);
    }

    /**
     * Test: Workflow - Pending cook cannot create dishes
     */
    public function test_pending_cook_cannot_create_dishes()
    {
        $cook = User::factory()->create([
            'role' => 'cook',
            'is_verified' => false, // NOT approved yet
        ]);

        $this->actingAs($cook);
        
        // Try to access create form
        $response = $this->get(route('dishes.create'));
        $this->assertTrue($response->status() === 403 || $response->status() === 404);

        // Try to create dish
        $dishData = [
            'name' => 'Coq au Vin',
            'description' => 'Plat traditionnel',
            'price' => 18.50,
            'available_qty' => 5,
            'served_date' => today()->toDateString(),
        ];
        
        $response = $this->post(route('dishes.store'), $dishData);
        $this->assertTrue($response->status() === 403 || $response->status() === 302);
    }

    /**
     * Test: Complete Workflow - Cook registration → Admin approval → Cook can create dishes
     */
    public function test_complete_cook_verification_workflow()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Step 1: Create a new cook (not verified yet)
        $cook = User::factory()->create([
            'role' => 'cook',
            'is_verified' => false,
            'name' => 'Chef Marcel',
            'email' => 'marcel@restaurant.local',
        ]);

        // Step 2: Admin views pending cooks
        $this->actingAs($admin);
        $response = $this->get(route('admin.cooks.index', ['filter' => 'pending']));
        $response->assertOk();
        $response->assertSee('Chef Marcel');

        // Step 3: Admin views cook profile
        $response = $this->get(route('admin.cooks.show', $cook));
        $response->assertOk();
        $response->assertSee('EN ATTENTE');

        // Step 4: Admin approves the cook
        $response = $this->post(route('admin.cooks.approve', $cook));
        $response->assertRedirect();

        // Verify cook is now approved
        $cook->refresh();
        $this->assertTrue($cook->is_verified);

        // Step 5: Cook can now create dishes
        $this->actingAs($cook);
        
        $dishData = [
            'name' => 'Pot-au-feu',
            'description' => 'Plat traditionnel français',
            'price' => 19.99,
            'available_qty' => 8,
            'served_date' => today()->toDateString(),
        ];

        $response = $this->post(route('dishes.store'), $dishData);
        $response->assertRedirect(route('dishes.index'));

        // Verify dish was created
        $this->assertDatabaseHas('dishes', [
            'name' => 'Pot-au-feu',
            'cook_id' => $cook->id,
        ]);
    }

    /**
     * Test: Admin can suspend verified cook
     */
    public function test_admin_can_suspend_verified_cook()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cook = User::factory()->create([
            'role' => 'cook',
            'is_verified' => true,
        ]);

        $this->actingAs($admin);
        
        // Suspend the cook
        $response = $this->post(route('admin.cooks.suspend', $cook), [
            'reason' => 'Comportement inapproprié signalé',
        ]);

        $response->assertRedirect();

        // Cook is no longer verified
        $cook->refresh();
        $this->assertFalse($cook->is_verified);
    }

    /**
     * Test: Non-admin cannot access admin routes
     */
    public function test_non_admin_cannot_access_admin_routes()
    {
        $cook = User::factory()->create(['role' => 'cook']);
        $client = User::factory()->create(['role' => 'client']);

        // Cook cannot access
        $this->actingAs($cook);
        $response = $this->get(route('admin.cooks.index'));
        $this->assertTrue($response->status() === 403);

        // Client cannot access
        $this->actingAs($client);
        $response = $this->get(route('admin.cooks.index'));
        $this->assertTrue($response->status() === 403);
    }

    /**
     * Test: Admin dashboard shows cook stats
     */
    public function test_admin_dashboard_shows_stats()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        
        // Create some test data
        User::factory(3)->create([
            'role' => 'cook',
            'is_verified' => true,
        ]);
        
        User::factory(2)->create([
            'role' => 'cook',
            'is_verified' => false,
        ]);

        $this->actingAs($admin);
        $response = $this->get(route('admin.dashboard'));
        
        $response->assertOk();
    }
}
