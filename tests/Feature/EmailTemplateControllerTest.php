<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class EmailTemplateControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    private function getAuthenticatedUser(string $role)
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return [$user, $token];
    }

    public function testAdminCanRetrieveTemplate()
    {
        // Create an admin user
        [$admin, $token] = $this->getAuthenticatedUser('admin');

        // Create a sample email template
        $template = EmailTemplate::factory()->create(['id' => Str::uuid()]);

        // Send a GET request to the API endpoint
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/v1/email-templates/' . $template->id);

        // Assert the response status and content
        $response->assertStatus(200)
            ->assertJson([
                'id' => $template->id,
                'title' => $template->title,
                'template' => $template->template,
                'status' => $template->status,
            ]);
    }

    public function testInvalidTemplateIdFormat()
    {
        // Create an admin user
        [$admin, $token] = $this->getAuthenticatedUser('admin');

        // Send a GET request with an invalid UUID format
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/v1/email-templates/invalid-uuid');

        // Assert the response status and content
        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid template ID format']);
    }

    public function testTemplateNotFound()
    {
        // Create an admin user
        [$admin, $token] = $this->getAuthenticatedUser('admin');

        // Send a GET request for a non-existing template
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/v1/email-templates/' . Str::uuid());

        // Assert the response status and content
        $response->assertStatus(404)
            ->assertJson(['error' => 'Template not found']);
    }

    public function testUnauthorizedAccess()
    {
        // Create a regular user
        [$user, $token] = $this->getAuthenticatedUser('user');

        // Send a GET request to the endpoint
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson('/api/v1/email-templates/' . Str::uuid());

        // Assert the response status and content
        $response->assertStatus(401)
        ->assertJson([
            'status_code' => 401,
            'message' => 'Unauthorized',
            'error' => 'Bad Request'
        ]);
    }
    /** @test */
    public function only_super_admin_can_fetch_email_templates()
    {
        [$user, $token] = $this->getAuthenticatedUser('admin');

        EmailTemplate::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/email-templates?limit=10&page=1');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'templates' => [
                    '*' => [
                        'id',
                        'title',
                        'template',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'total',
                'page',
                'limit'
            ]);
    }

    /** @test */
    public function non_super_admin_cannot_fetch_email_templates()
    {
        [$user, $token] = $this->getAuthenticatedUser('user'); // Use a non-super admin role

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/email-templates?limit=10&page=1');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized'
            ]);
    }




    private function createAuthenticatedUser($role)
    {
        $user = User::factory()->create(['role' => $role, 'is_active' => true]);
        $this->actingAs($user);
        return $user;
    }

    public function test_super_admin_can_update_email_template()
    {
        $user = $this->createAuthenticatedUser('admin');
        $template = EmailTemplate::factory()->create();

        $response = $this->patchJson("/api/v1/email-templates/{$template->id}", [
            'title' => 'Updated Template Title',
            'template' => 'Updated Template Content',
            'status' => true,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'message' => 'Email template updated successfully',
                'data' => [
                    'id' => $template->id,
                    'title' => 'Updated Template Title',
                    'template' => 'Updated Template Content',
                    'status' => true,
                ],
            ]);

        $this->assertDatabaseHas('email_templates', [
            'id' => $template->id,
            'title' => 'Updated Template Title',
            'template' => 'Updated Template Content',
            'status' => true,
        ]);
    }

    public function test_non_super_admin_cannot_update_email_template()
    {
        $user = $this->createAuthenticatedUser('user');
        $template = EmailTemplate::factory()->create();

        $response = $this->patchJson("/api/v1/email-templates/{$template->id}", [
            'title' => 'Updated Template Title',
            'template' => 'Updated Template Content',
            'status' => true,
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    }

    public function test_update_with_invalid_data()
    {
        $user = $this->createAuthenticatedUser('admin');
        $template = EmailTemplate::factory()->create();

        $response = $this->patchJson("/api/v1/email-templates/{$template->id}", [
            'title' => '',
            'template' => '',
            'status' => 'invalid',
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid input data']);
    }

    public function test_update_non_existent_template()
    {
        $user = $this->createAuthenticatedUser('admin');
        $nonExistentId = (string) Str::uuid();

        $response = $this->patchJson("/api/v1/email-templates/{$nonExistentId}", [
            'title' => 'Updated Template Title',
            'template' => 'Updated Template Content',
            'status' => true,
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Template not found']);
    }
}
