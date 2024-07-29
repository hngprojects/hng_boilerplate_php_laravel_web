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
}
