<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;
use App\Http\Controllers\Api\V1\Auth\OrganisationController;

class DeleteJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletes_a_job_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an organization
        $organisation = Organisation::factory()->create();

        // Create a job associated with the user and organization
        $job = Job::create([
            'id' => (string) Str::uuid(),
            'title' => 'Senior Software Engineer',
            'description' => 'We are looking for a Senior Software Engineer with experience in Laravel.',
            'location' => 'Remote',
            'salary' => 100000,
            'job_type' => 'Full-time',
            'company_name' => 'Tech Innovators Inc.',
            'organisation_id' => $organisation->org_id,
            'user_id' => $user->id,
        ]);

       // Act as user and send DELETE request
       $response = $this->actingAs($user, 'api')->deleteJson("/api/v1/jobs/{$job->id}");

       // Log the response for debugging
       \Log::info('Test Response:', $response->json());

       // Assert response status and message
       $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Job deleted successfully',
                ]);

       // Assert the job is no longer in the database
       $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }

    public function test_it_returns_not_found_if_job_does_not_exist()
    {
        // Create a user
        $user = User::factory()->create();

        // Use a non-existent job ID
        $nonExistentJobId = (string) Str::uuid();

        // Act as user and send DELETE request
        $response = $this->actingAs($user, 'api')->deleteJson("/api/v1/jobs/{$nonExistentJobId}");

        // Log the response for debugging
        \Log::info('Test Response for non-existent job:', $response->json());

        // Assert response status and message
        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Job not found',
                 ]);
    }

    public function test_it_returns_unauthorized_if_user_is_not_authenticated()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an organization
        $organisation = Organisation::factory()->create();

        // Create a job associated with the user and organization
        $job = Job::create([
            'id' => (string) Str::uuid(),
            'title' => 'Senior Software Engineer',
            'description' => 'We are looking for a Senior Software Engineer with experience in Laravel.',
            'location' => 'Remote',
            'salary' => 100000,
            'job_type' => 'Full-time',
            'company_name' => 'Tech Innovators Inc.',
            'organisation_id' => $organisation->org_id,
            'user_id' => $user->id, // Use the created user's ID
        ]);

        // Send DELETE request without authenticating the user
        $response = $this->deleteJson("/api/v1/jobs/{$job->id}");

        // Log the response for debugging
        \Log::info('Test Response for unauthenticated user:', $response->json());

        // Assert response status and message
        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.',
                ]);
    }


    // public function test_it_returns_unauthorized_if_user_is_not_part_of_the_organization()
    // {
    //     // Create two users
    //     $user1 = User::factory()->create();
    //     $user2 = User::factory()->create();

    //     // Create an organization
    //     $organisation = Organisation::factory()->create();

    //     // Create a job associated with the first user and organization
    //     $job = Job::create([
    //         'id' => (string) Str::uuid(),
    //         'title' => 'Senior Software Engineer',
    //         'description' => 'We are looking for a Senior Software Engineer with experience in Laravel.',
    //         'location' => 'Remote',
    //         'salary' => 100000,
    //         'job_type' => 'Full-time',
    //         'company_name' => 'Tech Innovators Inc.',
    //         'organisation_id' => $organisation->org_id,
    //         'user_id' => $user1->id,
    //     ]);

    //     // Act as the second user and send DELETE request
    //     $response = $this->actingAs($user2, 'api')->deleteJson("/api/v1/jobs/{$job->id}");

    //     // Log the response for debugging
    //     \Log::info('Test Response for unauthorized user:', $response->json());

    //     // Assert response status and message
    //     $response->assertStatus(401)
    //              ->assertJson([
    //                  'message' => 'Unauthorized',
    //              ]);
    // }
}
