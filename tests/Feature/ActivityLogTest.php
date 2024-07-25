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

    /** @test */
    public function successful_response_with_valid_orgId_and_userId()
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

    /** @test */
    // public function response_with_invalid_orgId_or_userId()
    // {
    //     // Create a user and authenticate
    //     $user = User::factory()->create();
    //     $this->actingAs($user);

    //     // Define invalid IDs
    //     $invalidOrgId = 'invalid-org-id';
    //     $invalidUserId = 'invalid-user-id';

    //     // Test invalid orgId
    //     $response = $this->getJson("/api/v1/organisations/{$invalidOrgId}/users/{$user->id}/activity-logs");
    //     $response->assertStatus(404)
    //         ->assertJson([
    //             'status_code' => 404,
    //             'message' => 'Organization or user not found',
    //             'error' => 'Not Found',
    //         ]);

    //     // Test invalid userId
    //     $organization = Organisation::factory()->create();
    //     $response = $this->getJson("/api/v1/organisations/{$organization->org_id}/users/{$invalidUserId}/activity-logs");
    //     $response->assertStatus(404)
    //         ->assertJson([
    //             'status_code' => 404,
    //             'message' => 'Organization or user not found',
    //             'error' => 'Not Found',
    //         ]);
    // }

    /** @test */
    public function response_with_missing_orgId_or_userId()
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

    /** @test */
    public function valid_request_but_no_activity_logs_found()
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

    /** @test */
    public function appropriate_status_code_for_unauthorized_access()
    {
        $user = User::factory()->create();
        $organization = Organisation::factory()->create();
        $organization->users()->attach($user->id);

        $unauthorizedUser = User::factory()->create();
        $this->actingAs($unauthorizedUser);

        $response = $this->getJson("/api/v1/organisations/{$organization->org_id}/users/{$user->id}/activity-logs");

        $response->assertStatus(403)
            ->assertJson([
                'status_code' => 403,
                'message' => 'Forbidden',
                'error' => 'You do not have permission to view these activity logs'
            ]);
    }
}
