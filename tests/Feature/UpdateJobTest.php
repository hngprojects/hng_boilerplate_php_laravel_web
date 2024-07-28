<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_successful_response_with_valid_id()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $job = Job::factory()->create();

        $user->jobs()->attach($job->id);

        $updateJob = [
            'title' => 'Music Idustry',
            'description' => 'To update music industry',
            'location' => 'Las Vegas',
            'salary' => '40000',
            'job_type' => 'Contract',
            'company_name' => 'HNG',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('patch', "api/v1/jobs/{$job->id}", $updateJob);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status_code',
                'data' => [
                    'title',
                    'description',
                    'location',
                    'salary',
                    'job_type',
                    'company_name',
                    'created_at',
                    'updated_at'
                ],
            ]);
    }


    public function test_it_returns_error_response_with_invalid_job_post_id()
    {

        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $job = Job::factory()->create();
        $user->jobs()->attach($job->id);

        $user2 = User::factory()->create();
        JWTAuth::fromUser($user);
        $job2 = Job::factory()->create();
        $user2->jobs()->attach($job2->id);

        $invalidJobId = $job2->id;
        $updateJob = [
            'title' => 'Music Idustry',
            'description' => 'To update music industry',
            'location' => 'Las Vegas',
            'salary' => '40000',
            'job_type' => 'Contract',
            'company_name' => 'HNG',
        ];


        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('patch', "api/v1/jobs/{$invalidJobId}", $updateJob);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }


    public function test_it_returns_error_response_with_missing_job_post_id()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('patch', 'api/v1/jobs/');

        $response->assertStatus(405);
    }


    public function test_it_returns_error_response_with_invalid_request_body()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);


        $job = Job::factory()->create();
        $invalidJobData = [
            'title' => '',
            'description' => '',
            'location' => '',
            'salary' => '',
            'job_type' => '',
            'company_name' => '',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('patch', "api/v1/jobs/{$job->id}", $invalidJobData);

        $response->assertStatus(400)
            ->assertJsonStructure([
                'status_code',
                'message',
                'error'
            ]);
    }
}
