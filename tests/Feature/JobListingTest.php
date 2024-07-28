<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_retrieve_job_listings()
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        Job::factory()->count(2)->create([
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
            ->assertJsonPath('pagination.total_items', 2);
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
}
