<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class JobControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $adminToken;
    protected $regularToken;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
        $this->adminToken = JWTAuth::fromUser($this->adminUser);
        $this->regularToken = JWTAuth::fromUser($this->regularUser);
    }

    public function test_index_returns_paginated_jobs()
    {
        Job::factory()->count(20)->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->getJson('/api/v1/jobs?page=1&size=15');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'created_at', 'updated_at', 'title', 'description', 'location', 'deadline', 'salary_range', 'job_type', 'job_mode', 'company_name', 'is_deleted']
                ],
                'pagination' => ['current_page', 'total_pages', 'page_size', 'total_items']
            ])
            ->assertJsonCount(15, 'data');
    }

    public function test_store_creates_new_job_as_admin()
    {
        $jobData = [
            'title' => 'Software Engineer',
            'description' => 'Develop amazing software',
            'location' => 'New York',
            'salary_range' => '100000',
            'job_type' => 'Full-time',
            'job_mode' => 'Remote',
            'deadline' => '2023-12-31',
            'company_name' => 'Corp insurance',
            'experience_level' => 'Mid-level',
            'key_responsibilities' => 'Develop and maintain software',
            'qualifications' => 'Bachelor\'s degree in Computer Science'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->postJson('/api/v1/jobs', $jobData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'location', 'salary', 'job_type', 'work_mode', 'deadline', 'benefits', 'experience_level', 'key_responsibilities', 'qualifications', 'created_at', 'updated_at']
            ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Software Engineer',
            'description' => 'Develop amazing software',
            'location' => 'New York',
            'salary' => '100000',
            'job_type' => 'Full-time',
            'work_mode' => 'Remote',
            'deadline' => '2023-12-31',
            'benefits' => 'Corp insurance',
            'experience_level' => 'Mid-level',
            'key_responsibilities' => 'Develop and maintain software',
            'qualifications' => 'Bachelor\'s degree in Computer Science'
        ]);
    }

    public function test_store_fails_for_non_admin_user()
    {
        $jobData = [
            'title' => 'Software Engineer',
            'description' => 'Develop amazing software',
            'location' => 'New York',
            'job_type' => 'Full-time',
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->regularToken"])
            ->postJson('/api/v1/jobs', $jobData);

        $response->assertStatus(403);
    }

    public function test_show_returns_job_details()
    {
        $job = Job::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id', 'created_at', 'updated_at', 'title', 'description', 'location', 'deadline', 'salary_range', 'job_type', 'job_mode', 'company_name', 'is_deleted'
            ]);
    }

    public function test_update_modifies_job_details_as_admin()
    {
        $job = Job::factory()->create();
        $updatedData = [
            'title' => 'Updated Job Title',
            'description' => 'Updated job description',
            'salary_range' => '110000',
            'job_mode' => 'Hybrid'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->putJson("/api/v1/jobs/{$job->id}", $updatedData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id', 'title', 'description', 'salary', 'work_mode']
            ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job Title',
            'description' => 'Updated job description',
            'salary' => '110000',
            'work_mode' => 'Hybrid'
        ]);
    }

    public function test_update_fails_for_non_admin_user()
    {
        $job = Job::factory()->create();
        $updatedData = [
            'title' => 'Updated Job Title',
            'description' => 'Updated job description'
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $this->regularToken"])
            ->putJson("/api/v1/jobs/{$job->id}", $updatedData);

        $response->assertStatus(403);
    }

    public function test_destroy_deletes_job_as_admin()
    {
        $job = Job::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->deleteJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Job listing deleted successfully.'
            ]);

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    public function test_destroy_fails_for_non_admin_user()
    {
        $job = Job::factory()->create();

        $response = $this->withHeaders(['Authorization' => "Bearer $this->regularToken"])
            ->deleteJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(403);
    }
}
