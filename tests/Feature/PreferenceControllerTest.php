<?php

namespace Tests\Feature;

use App\Models\Preference;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class PreferenceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $preference;
    protected $region;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->preference = Preference::factory()->create(['user_id' => $this->user->id]);
        $this->region = Region::factory()->create();
    }

    public function test_get_region()
    {
        // Authenticate the user with a valid JWT token
        $token = JWTAuth::fromUser($this->user);

        // Scenario: Region is set
        $this->preference->region_id = $this->region->id;
        $this->preference->save();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson("/api/v1/regions/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Region retrieved successfully',
                'data' => [
                    'id' => $this->region->id,
                    'name' => $this->region->name,
                ],
            ]);

        // Scenario: Region is not set
        $this->preference->region_id = null;
        $this->preference->save();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->getJson("/api/v1/regions/{$this->user->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Region has not been set',
                'data' => [],
            ]);

        // Scenario: Preference not found
        $newUser = User::factory()->create();
        $newToken = JWTAuth::fromUser($newUser);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $newToken",
        ])->getJson("/api/v1/regions/{$newUser->id}");

        $response->assertStatus(404)
            ->assertJson([
                'status' => 404,
                'message' => 'Preference not found for user',
            ]);
    }
}
