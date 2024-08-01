<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\WaitList;
use Tymon\JWTAuth\Facades\JWTAuth;

class WaitListControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test fetching all waitlist entries.
     *
     * @return void
     */
    // public function testIndex()
    // {

    //     $admin = User::factory()->create(['role' => 'admin']);

    //     // Create some waitlist entries
    //     WaitList::factory()->count(3)->create();
    //     $response = $this->actingAs($admin)->getJson('/api/v1/waitlists');
    //     $response->assertStatus(200);
    //     $response->assertJsonStructure([
    //         'status',
    //         'data' => [
    //             '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
    //         ]
    //     ]);
    // }

//     public function testIndex()
// {
//     // Create an admin role
//     $adminRole = \App\Models\Role::create(['name' => 'admin']);

//     // Create a user and assign the admin role
//     $admin = \App\Models\User::factory()->create(); // Create user without role
//     $admin->roles()->attach($adminRole->id); // Attach the admin role to the user

//     // Create some waitlist entries
//     \App\Models\WaitList::factory()->count(3)->create();

//     // Act as the admin user and make a GET request to the index endpoint
//     $response = $this->actingAs($admin)->getJson('/api/v1/waitlists');

//     // Assert that the response status is 200
//     $response->assertStatus(200);

//     // Assert that the response contains the correct data structure
//     $response->assertJsonStructure([
//         'status',
//         'data' => [
//             '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
//         ]
//     ]);
// }


public function testIndex()
{
    // Create an admin user
    $admin = User::factory()->create(['role' => 'admin']);

    // Generate a JWT token for the admin user
    $token = JWTAuth::fromUser($admin);

    // Create some waitlist entries
    WaitList::factory()->count(3)->create();

    // Act as the admin user with the generated token
    $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
                     ->getJson('/api/v1/waitlists');

    // Assert that the response status is 200
    $response->assertStatus(200);

    // Assert that the response contains the correct data structure
    $response->assertJsonStructure([
        'status',
        'data' => [
            '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
        ]
    ]);
}


    /**
     * Test storing a new waitlist entry.
     *
     * @return void
     */
    public function testStore()
    {

        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ];

        $response = $this->postJson('/api/v1/waitlists', $payload);
        $response->assertStatus(201);
        $response->assertJson([
            'status' => 201,
            'message' => 'You have been added to the waitlist and an email has been sent.',
            'data' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com'
            ]
        ]);

        $this->assertDatabaseHas('wait_lists', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ]);
    }

    /**
     * Test validation failure when storing a new waitlist entry.
     *
     * @return void
     */
    public function testStoreValidationFailure()
    {
        $payload = [
            'name' => " ",
            'email' => 'invalid-email'
        ];

        $response = $this->postJson('/api/v1/waitlists', $payload);
        $response->assertStatus(422);
        $response->assertJson([
            'status' => 422,
            'message' => 'The name field is required. The email field must be a valid email address.'
        ]);
    }
}
