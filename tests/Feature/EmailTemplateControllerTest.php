<?php

namespace Tests\Feature;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmailTemplateControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function testAdminCanRetrieveTemplate()
    {
        $admin = User::factory()->create([
            'signup_type' => 'admin',
        ]);
        $template = EmailTemplate::factory()->create(['id' => Str::uuid()]);

        $response = $this->actingAs($admin, 'api')->getJson('/api/v1/email-templates/' . $template->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $template->id,
                'name' => $template->name,
                'subject' => $template->subject,
                'content' => $template->content,
            ]);
    }

    public function testInvalidTemplateIdFormat()
    {
        $admin = User::factory()->create([
            'signup_type' => 'admin',
        ]);

        $response = $this->actingAs($admin, 'api')->getJson('/api/v1/email-templates/invalid-uuid');

        $response->assertStatus(400)
            ->assertJson(['error' => 'Invalid template ID format']);
    }

    public function testTemplateNotFound()
    {
        $admin = User::factory()->create([
            'signup_type' => 'admin',
        ]);

        $response = $this->actingAs($admin, 'api')->getJson('/api/v1/email-templates/' . Str::uuid());

        $response->assertStatus(404)
            ->assertJson(['error' => 'Template not found']);
    }

    public function testUnauthorizedAccess()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->getJson('/api/v1/email-templates/' . Str::uuid());

        $response->assertStatus(403)
            ->assertJson(['error' => 'Unauthorized access']);
    }
}
