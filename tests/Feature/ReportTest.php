<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use App\Models\Dish;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can create a report
     */
    public function test_user_can_create_report(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'type' => 'cook',
            'category' => 'behavior',
            'description' => 'This cook was very rude and unprofessional during my order',
            'reported_user_id' => $reportedUser->id,
        ]);

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'type' => 'cook',
            'category' => 'behavior',
            'status' => 'pending',
        ]);

        $response->assertRedirect();
    }

    /**
     * Test user cannot report themselves
     */
    public function test_user_cannot_report_themselves(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'type' => 'cook',
            'category' => 'behavior',
            'description' => 'Self report',
            'reported_user_id' => $user->id,
        ]);

        $response->assertRedirect()->withErrors();
        $this->assertDatabaseMissing('reports', [
            'reporter_id' => $user->id,
            'reported_user_id' => $user->id,
        ]);
    }

    /**
     * Test user can view their own reports
     */
    public function test_user_can_view_own_reports(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();
        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertStatus(200);
        $response->assertViewIs('reports.show');
    }

    /**
     * Test user cannot view others reports
     */
    public function test_user_cannot_view_others_reports(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $reportedUser = User::factory()->create();
        $report = Report::factory()->create([
            'reporter_id' => $otherUser->id,
            'reported_user_id' => $reportedUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.show', $report));

        $response->assertForbidden();
    }

    /**
     * Test user can list their reports
     */
    public function test_user_can_list_own_reports(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();
        Report::factory(3)->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
        $this->assertCount(3, $response->viewData('reports'));
    }

    /**
     * Test user can delete pending report
     */
    public function test_user_can_delete_pending_report(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();
        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->delete(route('reports.destroy', $report));

        $response->assertRedirect();
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }

    /**
     * Test user cannot delete resolved report
     */
    public function test_user_cannot_delete_resolved_report(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'resolved',
            'resolved_by' => $admin->id,
        ]);

        $response = $this->actingAs($user)->delete(route('reports.destroy', $report));

        $response->assertForbidden();
        $this->assertDatabaseHas('reports', ['id' => $report->id]);
    }

    /**
     * Test admin can view all reports
     */
    public function test_admin_can_view_all_reports(): void
    {
        $admin = User::factory()->admin()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Report::factory(2)->create([
            'reporter_id' => $user1->id,
            'reported_user_id' => $user2->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.reports.index');
    }

    /**
     * Test non-admin cannot view admin reports
     */
    public function test_non_admin_cannot_view_admin_reports(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.reports.index'));

        $response->assertForbidden();
    }

    /**
     * Test admin can update report status
     */
    public function test_admin_can_update_report_status(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();
        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.reports.update', $report), [
            'status' => 'resolved',
            'admin_comment' => 'User was warned about behavior',
        ]);

        $report->refresh();
        $this->assertEquals('resolved', $report->status);
        $this->assertEquals('User was warned about behavior', $report->admin_comment);
        $this->assertNotNull($report->resolved_at);
        $this->assertEquals($admin->id, $report->resolved_by);
    }

    /**
     * Test admin can update priority
     */
    public function test_admin_can_update_priority(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();
        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'priority' => 1,
        ]);

        $response = $this->actingAs($admin)->patch(route('admin.reports.priority', $report), [
            'priority' => '4',
        ]);

        $report->refresh();
        $this->assertEquals(4, $report->priority);
    }

    /**
     * Test report priority is calculated correctly
     */
    public function test_report_priority_calculated_correctly(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();

        // Critical category
        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'category' => 'fraud',
        ]);
        $this->assertEquals(4, $report->priority);

        // High priority category
        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'category' => 'behavior',
        ]);
        $this->assertEquals(3, $report->priority);
    }

    /**
     * Test report scopes work correctly
     */
    public function test_report_scopes(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();

        // Create pending reports
        Report::factory(2)->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'pending',
        ]);

        // Create resolved reports
        Report::factory(2)->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'resolved',
        ]);

        // Create high priority reports
        Report::factory(3)->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'priority' => 3,
        ]);

        $this->assertEquals(2, Report::pending()->count());
        $this->assertEquals(2, Report::resolved()->count());
        $this->assertEquals(3, Report::highPriority()->count());
        $this->assertEquals(4, Report::unresolved()->count());
        $this->assertEquals(5, Report::byReporter($user->id)->count());
        $this->assertEquals(5, Report::aboutUser($reportedUser->id)->count());
    }

    /**
     * Test report can be created for different types
     */
    public function test_report_can_be_created_for_different_types(): void
    {
        $user = User::factory()->create();

        // Report cook
        $this->actingAs($user)->post(route('reports.store'), [
            'type' => 'cook',
            'category' => 'behavior',
            'description' => 'Cook behavior issue',
            'reported_user_id' => User::factory()->create()->id,
        ]);

        // Report dish
        $this->actingAs($user)->post(route('reports.store'), [
            'type' => 'dish',
            'category' => 'expired',
            'description' => 'Expired dish',
            'reported_dish_id' => Dish::factory()->create()->id,
        ]);

        // Report order
        $this->actingAs($user)->post(route('reports.store'), [
            'type' => 'order',
            'category' => 'missing_items',
            'description' => 'Order was incomplete',
            'reported_order_id' => Order::factory()->create()->id,
        ]);

        $this->assertDatabaseHas('reports', ['type' => 'cook']);
        $this->assertDatabaseHas('reports', ['type' => 'dish']);
        $this->assertDatabaseHas('reports', ['type' => 'order']);
    }

    /**
     * Test report filtering by status
     */
    public function test_admin_can_filter_reports_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();

        Report::factory(3)->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'pending',
        ]);

        Report::factory(2)->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'resolved',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $this->assertCount(3, $response->viewData('reports'));
    }

    /**
     * Test report model relationships
     */
    public function test_report_relationships(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $dish = Dish::factory()->create();
        $order = Order::factory()->create();

        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'reported_dish_id' => $dish->id,
            'reported_order_id' => $order->id,
            'status' => 'resolved',
            'resolved_by' => $admin->id,
        ]);

        $this->assertInstanceOf(User::class, $report->reporter);
        $this->assertInstanceOf(User::class, $report->reportedUser);
        $this->assertInstanceOf(Dish::class, $report->reportedDish);
        $this->assertInstanceOf(Order::class, $report->reportedOrder);
        $this->assertInstanceOf(User::class, $report->resolvedBy);
    }

    /**
     * Test report helper methods
     */
    public function test_report_helper_methods(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();

        $report = Report::factory()->create([
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'status' => 'pending',
            'category' => 'behavior',
            'priority' => 3,
        ]);

        // Test status badge
        $badge = $report->getStatusBadge();
        $this->assertStringContainsString('pending', $badge);

        // Test category label
        $label = $report->getCategoryLabel();
        $this->assertIsString($label);
        $this->assertNotEmpty($label);

        // Test priority label
        $priorityLabel = $report->getPriorityLabel();
        $this->assertIsString($priorityLabel);
        $this->assertNotEmpty($priorityLabel);

        // Test priority color
        $color = $report->getPriorityColor();
        $this->assertIsString($color);
        $this->assertMatchesRegularExpression('/#[0-9a-f]{6}/', $color);
    }
}
