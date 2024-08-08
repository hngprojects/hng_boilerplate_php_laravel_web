<?php

namespace Tests\Feature;

use App\Models\Organisation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;

use Tests\TestCase;

class GetAllRolesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api'); // Ensure user is authenticated
    }

    /**
     * Test fetching all roles successfully.
     *
     * @return void
     */
    public function testFetchRolesSuccessfully()
    {
        $organisation = Organisation::factory()->create(['user_id' => $this->user->id]);
        $roles = Role::factory()->count(3)->create(['org_id' => $organisation->org_id]);

        $response = $this->getJson("/api/v1/organisations/{$organisation->org_id}/roles");

        // Log response for debugging
        // \Log::info('Fetch Roles Response:', ['response' => $response->json()]);

        $rolesArray = $roles->map(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
            ];
        })->toArray();

        $response->assertStatus(200)
                 ->assertJson([
                     'status_code' => 200,
                     'data' => $rolesArray,
                 ]);
    }

    /**
     * Test fetching roles with invalid organisation ID format.
     *
     * @return void
     */
    public function testFetchRolesWithInvalidOrganisationIdFormat()
    {
        $response = $this->getJson('/api/v1/organisations/invalid_id/roles');

        // Log response for debugging
        // \Log::info('Invalid Org ID Response:', ['response' => $response->json()]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status_code' => 400,
                     'error' => 'Bad Request',
                     'message' => 'Invalid organisation ID format',
                 ]);
    }

    /**
     * Test fetching roles for non-existent organisation.
     *
     * @return void
     */
    public function testFetchRolesForNonExistentOrganisation()
    {
        $nonExistentOrgId = '00000000-0000-0000-0000-000000000999';

        // Ensure the organisation with the given ID does not exist
        $this->assertDatabaseMissing('organisations', ['org_id' => $nonExistentOrgId]);

        $response = $this->getJson("/api/v1/organisations/{$nonExistentOrgId}/roles");

        // Log response for debugging
        // \Log::info('Non-Existent Org ID Response:', ['response' => $response->json()]);

        $response->assertStatus(404)
                 ->assertJson([
                     'status_code' => 404,
                     'error' => 'Not Found',
                     'message' => "The organisation with ID {$nonExistentOrgId} does not exist",
                 ]);
    }
}
