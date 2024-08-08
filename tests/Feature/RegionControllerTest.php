<?php

namespace Tests\Feature;

use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
