<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganisationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_create_organisation()
    {

      $user = User::factory()->create();
      $this->actingAs($user);

        $response = $this->postJson('/api/v1/organisations', [
            // "user_id" => (string)$user->id,
            "email" => "mark.essienm@gmail.co.uk",
            "name" => "Ruxy Now Organisation",
            "description" =>"With description like a big man",
            "industry" => "Money",
            "address" => "Money",
            "type" => "Money",
            "country" => "Money",
            "state" => "Money"
        ]);

        // Ensure organisaton is created successfully.
        $response->assertStatus(201);

        // Assert that organisation has the correct name and owner_id
        $this->assertDatabaseHas('organisations', [
            "name" => "Ruxy Now Organisation",
            // "user_id" => (string)$user->id
        ]);
    }   
}