<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class RegionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_store_a_region()
    {

        $user = \App\Models\User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/regions', [
            'name' => $this->faker->word,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'status_code' => 201,
                     'message' => 'Region created successfully.',
                 ]);

        $this->assertDatabaseHas('regions', [
            'name' => $response->json('data.name'),
        ]);
    }

    /** @test */
    public function it_cannot_store_a_region_with_invalid_data()
    {

        $user = \App\Models\User::factory()->create(['role' => 'admin']);
        $response = $this->actingAs($user, 'api')->postJson('/api/v1/regions', [

        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'status' => 'error',
                     'status_code' => 422,
                 ]);
    }

 /** @test */
    public function it_can_fetch_all_regions()
    {
        // Create some regions
        $regions = Region::factory()->count(3)->create();

        // Create a regular user (does not have to be an admin)
        $user = User::factory()->create();

        // Generate a JWT token for the user
        $token = $this->generateJwtTokenForUser($user);

        // Debugging: Check token validity
        $this->assertNotEmpty($token, 'Token should not be empty.');

        // Act as the authenticated user with the generated token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/regions');

        // Debugging: Output response for inspection
        $response->dump();

        // Assert that the response status is 200
        $response->assertStatus(200)
                ->assertJson([
                    'status' => "success",
                    'status_code' => 200,
                    'data' => $regions->map(function ($region) {
                        return [
                            'id' => $region->id,
                            'name' => $region->name,
                        ];
                    })->toArray(),
                ]);
    }

    protected function generateJwtTokenForUser(User $user)
    {
        // Generate a token for the user
        return JWTAuth::fromUser($user);
    }






}
