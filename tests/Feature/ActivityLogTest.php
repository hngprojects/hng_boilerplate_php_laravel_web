<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Organisation;
use Laravel\Sanctum\Sanctum;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_org_successful_response_with_valid_orgId_and_userId()
    {
        $user = User::factory()->create();
        $organization = Organisation::factory()->create();
        $organization->users()->attach($user->id);
        ActivityLog::factory()->create([
            'user_id' => $user->id,
            'activity' => 'User logged in',
            'timestamp' => now()
        ]);
        $this->actingAs($user);

        $response = $this->getJson("/api/v1/organisations/{$organization->org_id}/users/{$user->id}/activity-logs");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Activity logs retrieved successfully',
                'status_code' => 200,
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'activity',
                        'timestamp'
                    ]
                ]
            ])
            ->assertJsonFragment([
                'activity' => 'User logged in'
            ]);
    }

    public function test_org_response_with_missing_orgId_or_userId()
    {
        $user = User::factory()->create();
        $organization = Organisation::factory()->create();
        $organization->users()->attach($user->id);
        $this->actingAs($user);

        $response = $this->getJson("/api/v1/organisations//users/{$user->id}/activity-logs");
        $response->assertStatus(404);

        $response = $this->getJson("/api/v1/organisations/{$organization->org_id}/users//activity-logs");
        $response->assertStatus(404);
    }

    public function test_org_valid_request_but_no_activity_logs_found()
    {
        $user = User::factory()->create();
        $organization = Organisation::factory()->create();
        $organization->users()->attach($user->id);
        $this->actingAs($user);

        $response = $this->getJson("/api/v1/organisations/{$organization->org_id}/users/{$user->id}/activity-logs");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Activity logs retrieved successfully',
                'status_code' => 200,
                'data' => []
            ]);
    }

    public function test_org_appropriate_status_code_for_unauthorized_access()
    {
        $user = User::factory()->create();
        $organization = Organisation::factory()->create();
        $organization->users()->attach($user->id);

        $unauthorized_user = User::factory()->create();
        $this->actingAs($unauthorized_user);

        $response = $this->getJson("/api/v1/organisations/{$organization->org_id}/users/{$user->id}/activity-logs");

        $response->assertStatus(403)
            ->assertJson([
                'status_code' => 403,
                'message' => 'Forbidden',
                'error' => 'You do not have permission to view these activity logs'
            ]);
    }
}
