<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserUpdateTest extends TestCase{
    use RefreshDatabase, WithFaker;
//php artisan test --filter UserUpdateTest

    /** @test */
    public function it_updates_user_successfully_with_valid_data()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $data = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'phone' => $this->faker->phoneNumber,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
        ];

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->patchJson("/api/v1/users/{$user->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                "status" => "success",
                'message' => 'User Updated Successfully'
            ]);

        // Check database for updated data
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name']
        ]);
    }

    /** @test */
    public function it_returns_400_for_invalid_data()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $data = [
            'name' => 'Test Name', 
            'email' => 'invalid-email', 
            'phone' => 'invalid-phone-number', 
        ];
       
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
            ->patchJson("/api/v1/users/{$user->id}", $data);

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'User failed to update',
            ]);
    }

    
}