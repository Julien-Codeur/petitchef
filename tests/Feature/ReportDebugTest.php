<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportDebugTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user can create a report - Debug version
     */
    public function test_user_can_create_report_debug(): void
    {
        $user = User::factory()->create();
        $reportedUser = User::factory()->create();

        echo "\nUser ID: " . $user->id . "\n";
        echo "Reported User ID: " . $reportedUser->id . "\n";

        $response = $this->actingAs($user)->post(route('reports.store'), [
            'type' => 'cook',
            'category' => 'behavior',
            'description' => 'This cook was very rude and unprofessional during my order',
            'reported_user_id' => $reportedUser->id,
        ]);

        echo "Response Status: " . $response->getStatusCode() . "\n";
        
        if ($response->getStatusCode() === 500) {
            echo "ERROR Response:\n";
            echo $response->getContent() . "\n";
        }

        if ($response->getStatusCode() >= 300 && $response->getStatusCode() <= 399) {
            echo "Redirect Location: " . $response->headers->get('Location') . "\n";
        }

        // Check if any errors
        if ($response->getStatusCode() === 422) {
            echo "Validation Errors:\n";
            echo json_encode($response->json('errors') ?? $response->json()) . "\n";
        }

        // Check database
        echo "Reports in DB: " . Report::count() . "\n";

        $this->assertDatabaseHas('reports', [
            'reporter_id' => $user->id,
            'reported_user_id' => $reportedUser->id,
            'type' => 'cook',
            'category' => 'behavior',
            'status' => 'pending',
        ]);
    }
}
