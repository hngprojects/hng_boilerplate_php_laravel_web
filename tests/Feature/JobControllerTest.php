<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function getToken($user)
    {
        return JWTAuth::fromUser($user);
    }

    public function test_can_get_job_details()
    {
        $user = User::factory()->create();
        $token = $this->getToken($user);

        $job = Job::factory()->create([
            'id' => Str::uuid()->toString(),
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'title',
                'description',
                'location',
                'salary',
                'job_type',
                'company_name',
                'user_id',
                'organization_id',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_returns_404_for_non_existent_job()
    {
        $user = User::factory()->create();
        $token = $this->getToken($user);

        $nonExistentId = Str::uuid()->toString();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/jobs/{$nonExistentId}");

        $response->assertStatus(404)
            ->assertJson([
                'status_code' => 404,
                'message' => 'Job not found',
                'error' => 'Not Found'
            ]);
    }

    public function test_unauthenticated_user_cannot_access_job_details()
    {
        $job = Job::factory()->create([
            'id' => Str::uuid()->toString(),
        ]);

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(401);
    }
}
