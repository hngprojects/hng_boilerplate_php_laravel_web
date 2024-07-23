<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_retrieve_job_listings()
    {
        $user = User::factory()->create();
        $jobs = Job::factory()->count(15)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jobs?page=1&size=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'title', 'description', 'location', 'job_type', 'company_name']
                ],
                'pagination' => ['current_page', 'total_pages', 'page_size', 'total_items']
            ])
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('pagination.total_items', 15);
    }

    public function test_unauthenticated_user_cannot_retrieve_job_listings()
    {
        $response = $this->getJson('/api/v1/jobs');

        $response->assertStatus(401);
    }

    public function test_job_listings_are_paginated()
    {
        $user = User::factory()->create();
        Job::factory()->count(30)->create();

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/jobs?page=2&size=15');

        $response->assertStatus(200)
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('pagination.current_page', 2)
            ->assertJsonPath('pagination.total_pages', 2)
            ->assertJsonPath('pagination.page_size', 15)
            ->assertJsonPath('pagination.total_items', 30);
    }
}
