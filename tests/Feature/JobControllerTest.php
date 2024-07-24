<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str; // Import the Str class
use Tests\TestCase;

class JobControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_job_details()
    {
        $user = User::factory()->create();
        $job = Job::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'location',
                'salary',
                'job_type',
                'company_name',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_returns_404_for_non_existent_job()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson("/api/v1/jobs/" . Str::uuid());

        $response->assertStatus(404)
            ->assertJson([
                'status_code' => 404,
                'message' => 'Job not found',
                'error' => 'Not Found'
            ]);
    }

    public function test_unauthenticated_user_cannot_access_job_details()
    {
        $job = Job::factory()->create();

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(401);
    }
}
