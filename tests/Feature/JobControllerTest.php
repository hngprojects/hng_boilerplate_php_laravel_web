<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;
    protected $organisation;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->organisation = Organisation::factory()->create();
        $this->user->organisations()->attach($this->organisation);
        $this->token = JWTAuth::fromUser($this->user);
    }

    public function test_index_returns_paginated_jobs()
    {
        Job::factory()->count(15)->create(['organisation_id' => $this->organisation->org_id]);

        $response = $this->withHeaders(['Authorization' => "Bearer $this->token"])
            ->getJson('/api/v1/jobs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data',
                'pagination' => ['current_page', 'per_page', 'total_pages', 'total_items']
            ])
            ->assertJsonCount(15, 'data');
    }

    public function test_store_creates_new_job()
    {
        $jobData = [
            'title' => 'Software Engineer',
            'description' => 'Develop amazing software',
            'location' => 'New York',
            'salary' => '100000',
            'deadline' => '2023-12-31',
            'work_mode' => 'Remote',
            'job_type' => 'Full-time',
            'experience_level' => 'Mid-level'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->token"])
            ->postJson('/api/v1/jobs', $jobData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'location', 'salary', 'deadline', 'company_name', 'work_mode', 'job_type', 'experience_level', 'user_id', 'organisation_id']
            ]);

        $this->assertDatabaseHas('jobs', array_merge($jobData, ['company_name' => $this->organisation->name]));
    }

    public function test_show_returns_job_details()
    {
        $job = Job::factory()->create(['organisation_id' => $this->organisation->org_id]);

        $response = $this->withHeaders(['Authorization' => "Bearer $this->token"])
            ->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'title', 'description', 'location', 'salary', 'deadline', 'company_name', 'work_mode', 'job_type', 'experience_level', 'user_id', 'organisation_id']
            ]);
    }

    public function test_update_modifies_job_details()
    {
        $job = Job::factory()->create(['organisation_id' => $this->organisation->org_id]);

        $updatedData = [
            'title' => 'Updated Job Title',
            'description' => 'Updated job description'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->token"])
            ->putJson("/api/v1/jobs/{$job->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description']
            ]);

        $this->assertDatabaseHas('jobs', $updatedData);
    }

    public function test_destroy_deletes_job()
    {
        $job = Job::factory()->create(['organisation_id' => $this->organisation->org_id]);

        $response = $this->withHeaders(['Authorization' => "Bearer $this->token"])
            ->deleteJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Job listing deleted successfully.'
            ]);

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }
}
