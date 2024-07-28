<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Models\User;
use App\Models\Preference;
use Tymon\JWTAuth\Facades\JWTAuth;

class PreferenceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate them
        $this->user = User::factory()->create();
        // $this->token = JWTAuth::fromUser($this->user);
        $this->actingAs($this->user, 'api');
    }

    /** @test */
    public function it_can_store_a_preference()
    {
        $data = [
            'name' => 'theme',
            'value' => 'dark',
        ];

        $response = $this->postJson('/api/v1/user/preferences', $data);

        // dd($response);
        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'theme', 'value' => 'dark']);

        $this->assertDatabaseHas('preferences', ['name' => 'theme', 'value' => 'dark']);
    }

    /** @test */
    public function it_can_update_a_preference()
    {
        $preference = $this->user->preferences()->create([
            'name' => 'theme',
            'value' => 'dark',
        ]);

        $data = [
            'name' => 'theme',
            'value' => 'light',
        ];

        $response = $this->putJson("/api/v1/user/preferences/{$preference->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'theme', 'value' => 'light']);

        $this->assertDatabaseHas('preferences', ['name' => 'theme', 'value' => 'light']);
    }

    /** @test */
    public function it_cannot_create_a_preference_with_duplicate_name()
    {
        $this->user->preferences()->create([
            'name' => 'theme',
            'value' => 'dark',
        ]);

        $data = [
            'name' => 'theme',
            'value' => 'light',
        ];

        $response = $this->postJson('/api/v1/user/preferences', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function it_cannot_update_preference_with_invalid_id()
    {
        $this->user->preferences()->create([
            'name' => 'theme',
            'value' => 'dark',
        ]);

        $data = [
            'name' => 'theme',
            'value' => 'light',
        ];

        $response = $this->putJson('/api/v1/user/preferences/ac1724a3-0314-4580-938f-7b65009e2d84', $data);
        // dd($response);
        $response->assertStatus(422);
    }
}
