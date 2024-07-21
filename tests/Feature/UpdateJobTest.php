<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_successful_response_with_valid_id()
    {
        $user =  User::create([
            'id' => uniqid(),
            'name' => 'Alems_hng Baja',
            'email' => 'ec2_hng@task2.com',
            'password' => Hash::make('password'),
        ]);

        $token = JWTAuth::fromUser($user);


        $job = Job::factory()->create();

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
            ->assertJson([
                'message' => 'Job details updated successfully',
                'status_code' => 200,
                'data' => $updateJob
            ]);

        $this->assertDatabaseHas('jobs', $updateJob);
    }


    public function test_it_returns_error_response_with_invalid_job_post_id()
    {
        $user =  User::create([
            'id' => uniqid(),
            'name' => 'Alems_hng Baja',
            'email' => 'ec2_hng@task2.com',
            'password' => Hash::make('password'),
        ]);

        $token = JWTAuth::fromUser($user);


        $invalidId = 999;
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
        ])->json('patch', "api/v1/jobs/{$invalidId}", $updateJob);

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No query results for model [App\Models\Job] 999',
            ]);
    }


    public function test_it_returns_error_response_with_missing_job_post_id()
    {
        $user = User::create([
            'id' => uniqid(),
            'name' => 'Alems_hng Baja',
            'email' => 'ec2_hng@task2.com',
            'password' => Hash::make('password'),
        ]);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('patch', 'api/v1/jobs/');

        $response->assertStatus(404);
    }


    public function test_it_returns_error_response_with_invalid_request_body()
    {
        $user =  User::create([
            'id' => uniqid(),
            'name' => 'Alems_hng Baja',
            'email' => 'ec2_hng@task2.com',
            'password' => Hash::make('password'),
        ]);

        $token = JWTAuth::fromUser($user);

        $job = Job::factory()->create();
        $invalidData = [
            'title' => '',
            'description' => '',
            'location' => '',
            'salary' => '',
            'job_type' => '',
            'company_name' => '',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->json('patch', "api/v1/jobs/{$job->id}", $invalidData);

        $response->assertStatus(400)
            ->assertJson([
                'status_code' => 400,
                'message' => [
                    'The job title is required.',
                    'The job description is required.',
                    'The job location is required.',
                    'The salary is required.',
                    'The job type is required.',
                    'The company name is required.'
                ],
                'error' => 'Bad Request'
            ]);
    }
}
