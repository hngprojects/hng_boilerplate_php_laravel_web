<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Timezone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\TimezoneController;

class TimezoneTest extends TestCase
{
    use RefreshDatabase;


    public function test_it_can_create_a_timezone()
    {
        // Create and authenticate a user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Define the timezone data to be sent in the request
        $timezoneData = [
            'timezone' => 'America/New_York',
            'gmtoffset' => '-05:00',
            'description' => 'Eastern Standard Time',
        ];

        // Send a POST request to create a new timezone
        $response = $this->postJson('/api/v1/timezones', $timezoneData);

        // Assert the response status is 201 Created
        $response->assertStatus(201)
            ->assertJson([
                'timezone' => 'America/New_York',
                'gmtoffset' => '-05:00',
                'description' => 'Eastern Standard Time',
                'id' => $response->json('id'), // Dynamically assert the ID
            ]);

        // Optionally, you can verify that the timezone was actually created in the database
        $this->assertDatabaseHas('timezones', $timezoneData);
    }

    public function test_it_returns_error_when_timezone_already_exists()
    {
        // Create and authenticate a user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Create a timezone
        $response = $this->postJson('/api/v1/timezones', [
            'timezone' => 'America/New_York',
            'gmtoffset' => '-05:00',
            'description' => 'Eastern Standard Time',
        ]);

        $response->assertStatus(201);

        // Try to create the same timezone again
        $response = $this->postJson('/api/v1/timezones', [
            'timezone' => 'America/New_York',
            'gmtoffset' => '-05:00',
            'description' => 'Eastern Standard Time',
        ]);

        $response->assertStatus(409)
            ->assertJson([
                'Status' => 409,
                'Message' => 'Timezone already exists',
            ]);
    }

    public function test_it_can_get_all_timezones()
{
    // Create some timezones
    $this->postJson('/api/v1/timezones', [
        'timezone' => 'America/New_York',
        'gmtoffset' => '-05:00',
        'description' => 'Eastern Standard Time',
    ]);

    $response = $this->getJson('/api/v1/timezones');

    $response->assertStatus(200)
        ->assertJson([
            'status' => 'success',
            'message' => 'Timezones retrieved successfully',
        ]);
}



    public function test_it_can_update_an_existing_timezone()
    {
        // Create and authenticate a user
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // Create a timezone first
        $timezone = Timezone::create([
            'timezone' => 'America/New_York',
            'gmtoffset' => '-05:00',
            'description' => 'Eastern Standard Time',
        ]);

        // Update the timezone
        $response = $this->putJson("/api/v1/timezones/{$timezone->id}", [
            'timezone' => 'America/Chicago',
            'gmtoffset' => '-06:00',
            'description' => 'Central Standard Time',
        ]);

        // Assert the response status is 200 OK
        $response->assertStatus(200)
            ->assertJson([
                'timezone' => 'America/Chicago',
                'gmtoffset' => '-06:00',
                'description' => 'Central Standard Time',
                'id' => $timezone->id,
            ]);
    }




    public function test_it_can_store_timezone_and_preference()
{
    // Create and authenticate a user
    $user = \App\Models\User::factory()->create();
    $this->actingAs($user);

    // Define the timezone data to be sent in the request
    $timezoneData = [
        'timezone' => 'America/New_York',
        'gmtoffset' => '-05:00',
        'description' => 'Eastern Standard Time',
        'user_id' => $user->id,  // Include user_id
    ];

    // Send a POST request to create a new timezone
    $response = $this->postJson('/api/v1/timezones', $timezoneData);

    // Assert the response status is 201 Created
    $response->assertStatus(201)
        ->assertJson([
            'id' => true,  // Check if the id is present
            'timezone' => 'America/New_York',
            'gmtoffset' => '-05:00',
            'description' => 'Eastern Standard Time',
        ]);

    // Verify that the timezone was actually created in the database
    $this->assertDatabaseHas('timezones', $timezoneData);

    // Verify that the preference was stored in the database
    $this->assertDatabaseHas('preferences', [
        'user_id' => $user->id,
        'timezone_id' => $response->json('id'),
    ]);
}









}
