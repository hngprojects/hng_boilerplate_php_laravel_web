<?php

namespace Tests\Feature;

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
}
