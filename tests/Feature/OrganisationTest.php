<?php

namespace Tests\Unit;

use App\Models\Organisation;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganisationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_update_organisation()
    {

      $main_user = User::factory()->create();
      $another_user = User::factory()->create();
      $organisation = Organisation::factory()->create();
      $organisation->users()->attach((string)$main_user->id);
      $token = JWTAuth::fromUser($main_user);

      $this->actingAs($main_user);
      $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                        ->putJson("/api/v1/organisations/{$organisation->org_id}", [
                            "name" => "Ruxy New Organisation",
                            "description" =>"New description like a big man",
                            "industry" => "Money",
                            "address" => "Money",
                            "type" => "Money",
                            "country" => "Money",
                            "state" => "Money"
                        ]);

      // Assert that a user can update their organisations
      $response->assertStatus(200);
      
      // Assert that organisation has the correct name and owner_id
      $this->assertDatabaseHas('organisations', [
          "name" => "Ruxy New Organisation",
          "description" =>"New description like a big man",
      ]);

      $this->actingAs($another_user);
      $new_token = JWTAuth::fromUser($another_user);
      $another_response = $this->withHeaders(['Authorization' => "Bearer $new_token"])
                        ->putJson("/api/v1/organisations/{$organisation->org_id}", [
                            "name" => "Ruxy New Organisation",
                            "description" =>"New description like a big man",
                            "industry" => "Money",
                            "address" => "Money",
                            "type" => "Money",
                            "country" => "Money",
                            "state" => "Money"
                        ]);
        // Ensure that another user cannot update an organisation he is not part of
        $another_response->assertStatus(403);
    } 
    
    public function test_user_create_organisation()
    {
      $user = User::factory()->create();
      $token = JWTAuth::fromUser($user);
      $this->actingAs($user);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])->postJson('/api/v1/organisations', [
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