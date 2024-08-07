<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Timezone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\TimezoneController;

class TimezoneTest extends TestCase
{
    use RefreshDatabase;


    public function test_it_can_store_timezone_and_preference()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);
    
        $timezoneData = [
            'timezone' => 'America/New_York',
            'gmtoffset' => '-05:00',
            'description' => 'Eastern Standard Time',
        ];
    
        $response = $this->postJson('/api/v1/timezones', $timezoneData);
    
        $response->assertStatus(201)
            ->assertJson([
                'id' => true,
                'timezone' => 'America/New_York',
                'gmtoffset' => '-05:00',
                'description' => 'Eastern Standard Time',
            ]);
    
        $this->assertDatabaseHas('timezones', $timezoneData);
    
        $this->assertDatabaseHas('preferences', [
            'user_id' => $user->id,
            'timezone_id' => $response->json('id'),
            'name' => 'America/New_York', // Corrected value
            'value' => 'America/New_York' // Corrected value
        ]);
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

      
        $response->assertStatus(200)
            ->assertJson([
                'timezone' => 'America/Chicago',
                'gmtoffset' => '-06:00',
                'description' => 'Central Standard Time',
                'id' => $timezone->id,
            ]);
    }












}
