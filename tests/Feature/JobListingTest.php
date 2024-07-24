<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class JobListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_retrieve_job_listings()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        Job::factory()->count(15)->create([
            'user_id' => $user->id,
            'organisation_id' => $organisation->org_id
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jobs?page=1&size=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'title', 'description', 'location', 'salary', 'job_type']
                ],
                'pagination' => ['current_page', 'total_pages', 'page_size', 'total_items']
            ])
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('pagination.total_items', 15);
    }

    public function test_job_listings_are_paginated()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        Job::factory()->count(30)->create([
            'user_id' => $user->id,
            'organisation_id' => $organisation->org_id
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jobs?page=2&size=15');

        $response->assertStatus(200)
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('pagination.current_page', 2)
            ->assertJsonPath('pagination.total_pages', 2)
            ->assertJsonPath('pagination.page_size', 15)
            ->assertJsonPath('pagination.total_items', 30);
    }

    protected function authenticateUser($user)
    {
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
    }

    public function testUpdateJobWithValidData()
    {
        $user = User::factory()->create();
        $job = Job::factory()->create(['user_id' => $user->id]);
        $organisation = Organisation::factory()->create();

        $this->authenticateUser($user);

        $response = $this->patchJson("/api/v1/jobs/{$job->id}", [
            'title' => 'Updated Job Title',
            'description' => 'Updated Job Description',
            'location' => 'Updated Location',
            'job_type' => 'Updated Type',
            'company_name' => 'Updated Company',
            'organisation_id' => $organisation->org_id,
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Job listing updated successfully',
                     'status_code' => 200,
                 ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job Title',
            'description' => 'Updated Job Description',
            'location' => 'Updated Location',
            'job_type' => 'Updated Type',
            'company_name' => 'Updated Company',
        ]);
    }

    public function testUpdateJobWithInvalidJobId()
    {
        $user = User::factory()->create();
        $invalidJobId = Str::uuid();

        $this->authenticateUser($user);

        $response = $this->patchJson("/api/v1/jobs/{$invalidJobId}", [
            'title' => 'Updated Job Title',
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Job not found',
                     'status_code' => 404,
                 ]);
    }

    public function testUpdateJobWithInvalidData()
    {
        $user = User::factory()->create();
        $job = Job::factory()->create(['user_id' => $user->id]);

        $this->authenticateUser($user);

        $response = $this->patchJson("/api/v1/jobs/{$job->id}", [
            'title' => '',
            'description' => '',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'Invalid request data',
                     'status_code' => 400,
                 ]);
    }
}
