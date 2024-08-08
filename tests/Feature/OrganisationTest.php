<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;

//use Laravel\Passport\Passport;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganisationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_create_organisation()
    {

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/api/v1/organisations', [
            "email" => "mark.essienm@gmail.co.uk",
            "name" => "Ruxy Now Organisation",
            "description" => "With description like a big man",
            "industry" => "Money",
            "address" => "Money",
            "type" => "Money",
            "country" => "Money",
            "state" => "Money"
        ], ['accept' => 'application/json']);
        // Ensure organisaton is created successfully.
        $response->assertStatus(201);

        // Assert that organisation has the correct name and owner_id
        $this->assertDatabaseHas('organisations', [
            "name" => "Ruxy Now Organisation",
            "user_id" => $user->id
        ]);
    }

    public function test_authenticated_user_can_retrieve_organisations()
{
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/v1/{$user->id}/organisations");

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'message',
            'status_code',
            'data' => [
                'organisations' => [
                    '*' => [
                        'org_id',
                        'name',
                        'email',
                        'description',
                        'industry',
                        'type',
                        'country',
                        'address',
                        'state',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]
        ]);
}


public function test_authenticated_user_with_no_organisations()
{
    $user = User::factory()->create();

    $token = JWTAuth::fromUser($user);
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/v1/{$user->id}/organisations");

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'No organisations available',
            'data' => [
                'organisations' => []
            ]
        ]);
}

public function test_user_cannot_access_another_users_organisations()
{
    $user1 = User::factory()->create(['password' => bcrypt('password')]);
    $user2 = User::factory()->create(['password' => bcrypt('password')]);

    $organisation = Organisation::factory()->create();
    $user2->organisations()->attach($organisation);

    $token = JWTAuth::attempt(['email' => $user1->email, 'password' => 'password']);
    $response = $this->withHeader('Authorization', "Bearer $token")
        ->getJson("/api/v1/{$user2->id}/organisations"); // Accessing user2's organisations

    $response->assertStatus(403)
        ->assertJson([
            'status' => 'error',
            'message' => 'Forbidden',
            'status_code' => '403',

        ]);
}


}
