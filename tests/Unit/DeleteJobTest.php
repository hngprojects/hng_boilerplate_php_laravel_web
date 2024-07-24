<?php

// tests/Feature/DeleteJobTest.php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class DeleteJobTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_deletes_a_job_successfully()
    {
        // Create an organization
        $organization = Organisation::factory()->create();

        // Create a user
        $user = User::factory()->create();

        // Associate user with organization
        $user->organisations()->attach($organization->id);

        // Create a job
        $job = Job::create([
            'id' => (string) Str::uuid(),
            'title' => 'Sample Job',
            'description' => 'This is a test job',
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'company_name' => 'Test Company',
            'user_id' => $user->id,
            'organisation_id' => $organization->id,
        ]);

        // Authenticate the user
        $this->actingAs($user);

        // Send DELETE request to delete the job
        $response = $this->json('DELETE', route('jobs.destroy', $job->id));

        // Assert the job is deleted
        $response->assertStatus(200);
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);

        // Assert response message
        $response->assertJson([
            'message' => 'Job deleted successfully',
        ]);
    }

    /** @test */
    public function it_returns_unauthorized_if_user_is_not_authenticated()
    {
        // Create an organization
        $organization = Organisation::factory()->create();

        // Create a job
        $job = Job::create([
            'id' => (string) Str::uuid(),
            'title' => 'Sample Job',
            'description' => 'This is a test job',
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'company_name' => 'Test Company',
            'user_id' => 1,
            'organisation_id' => $organization->id,
        ]);

        // Send DELETE request to delete the job without authentication
        $response = $this->json('DELETE', route('jobs.destroy', $job->id));

        // Assert unauthorized response
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Unauthorized',
            'error' => 'Bad Request',
        ]);
    }

    /** @test */
    public function it_returns_not_found_if_job_does_not_exist()
    {
        // Create an organization
        $organization = Organisation::factory()->create();

        // Create a user
        $user = User::factory()->create();

        // Authenticate the user
        $this->actingAs($user);

        // Send DELETE request to delete a non-existing job
        $response = $this->json('DELETE', route('jobs.destroy', 'non-existing-job-id'));

        // Assert not found response
        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Job not found',
            'error' => 'Not Found',
        ]);
    }

    /** @test */
    public function it_returns_unauthorized_if_user_is_not_part_of_the_organization()
    {
        // Create an organization
        $organization = Organisation::factory()->create();

        // Create another organization
        $anotherOrganization = Organisation::factory()->create();

        // Create a user
        $user = User::factory()->create();

        // Associate user with another organization
        $user->organisations()->attach($anotherOrganization->id);

        // Create a job
        $job = Job::create([
            'id' => (string) Str::uuid(),
            'title' => 'Sample Job',
            'description' => 'This is a test job',
            'location' => 'Remote',
            'job_type' => 'Full-time',
            'company_name' => 'Test Company',
            'user_id' => $user->id,
            'organisation_id' => $organization->id,
        ]);

        // Authenticate the user
        $this->actingAs($user);

        // Send DELETE request to delete the job
        $response = $this->json('DELETE', route('jobs.destroy', $job->id));

        // Assert unauthorized response
        $response->assertStatus(400);
        $response->assertJson([
            'message' => 'Unauthorized',
            'error' => 'Bad Request',
        ]);
    }
}
