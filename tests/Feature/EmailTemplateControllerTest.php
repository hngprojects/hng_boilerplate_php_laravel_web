<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\TestCase;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\EmailTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;


class EmailTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser(string $role)
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return [$user, $token];
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
