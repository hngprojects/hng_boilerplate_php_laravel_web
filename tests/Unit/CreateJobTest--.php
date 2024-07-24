<?php

// tests/Feature/CreateJobTest.php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_job_listing()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an organization
        $organization = Organisation::factory()->create();

        // Job data
        $data = [
            'title' => 'Senior Software Engineer',
            'description' => 'We are looking for a Senior Software Engineer with experience in Laravel.',
            'location' => 'Remote',
            'salary' => 100000,
            'job_type' => 'Full-time',
            'company_name' => 'Tech Innovators Inc.',
            'organization_id' => $organization->id, // Ensure this is a valid ID in your organizations table
        ];

        // Act as user and send POST request
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/jobs', $data);

        // Log the response for debugging
        \Log::info('Test Response:', $response->json());

        // Assert response status and structure
        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Job listing created successfully',
                     'data' => [
                         'title' => 'Senior Software Engineer',
                         'description' => 'We are looking for a Senior Software Engineer with experience in Laravel.',
                         'location' => 'Remote',
                         'salary' => 100000,
                         'job_type' => 'Full-time',
                         'company_name' => 'Tech Innovators Inc.',
                         'organization_id' => $organization->id,
                     ],
                 ]);

        // Assert the job is in the database
        $this->assertDatabaseHas('jobs', $data);
    }

    /** @test */
    public function it_returns_error_for_invalid_data()
    {
        // Create a user
        $user = User::factory()->create();

        // Invalid job data
        $data = [
            'title' => '',
            'description' => '',
            // Other fields omitted
        ];

        // Act as user and send POST request
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/jobs', $data);

        // Log the response for debugging
        \Log::info('Test Response:', $response->json());

        // Assert response status and structure
        $response->assertStatus(400)
                 ->assertJson([
                     'message' => 'Invalid request data',
                 ]);
    }

    /** @test */
    public function it_returns_unauthenticated_for_no_auth_token()
    {
        // Job data
        $data = [
            'title' => 'Senior Software Engineer',
            'description' => 'We are looking for a Senior Software Engineer with experience in Laravel.',
            'location' => 'Remote',
            'salary' => 100000,
            'job_type' => 'Full-time',
            'company_name' => 'Tech Innovators Inc.',
            'organization_id' => 'valid-organization-id',
        ];

        // Send POST request without acting as a user
        $response = $this->postJson('/api/v1/jobs', $data);

        // Log the response for debugging
        \Log::info('Test Response:', $response->json());

        // Assert response status and structure
        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Unauthenticated',
                 ]);
    }
}

