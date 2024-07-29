<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Str;
use Mockery;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_registration_returns_jwt_token()
    {
        $registrationData = [
            'name' => 'Test User',
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'testuser@gmail.com',
            'password' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'password_confirmation' => 'Ed8M7s*)?e:hTb^#&;C!<y',
        ];

        $response = $this->postJson('/api/v1/auth/register', $registrationData);
        // Check the status code
        $response->assertStatus(201);

        // Check the response structure
        $response->assertJsonStructure([
            'message',
            'status_code',
            'data' => [
                'accessToken',
                'user' => [
                    'name',
                    'email',
                    'id',
                    'updated_at',
                    'created_at',
                ]
            ]
        ]);

        // Optionally, decode and verify the token
        $token = $response->json('data.accessToken');
        $this->assertNotEmpty($token);
    }

    public function test_fails_if_email_is_not_passed()
    {
        $registrationData = [
            'name' => 'Test User',
            'email' => '',
            'password' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'password_confirmation' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        $response = $this->postJson('/api/v1/auth/register', $registrationData);
        // Check the status code
        $response->assertStatus(422);
        $response->assertJson([
            'message' => [
                'email' => [
                    'The email field is required.'
                ]
            ],
            'status_code' => 422,
        ]);
    }

    /** @test */
    public function google_login_creates_or_updates_user_and_profile()
    {
        // Mock Google user response
        $googleUser = (object) [
            'email' => 'john.doe@example.com',
            'id' => 'google-id-12345',
            'user' => [
                'given_name' => 'John',
                'family_name' => 'Doe',
                'picture' => 'https://lh3.googleusercontent.com/a-/AOh14Gh2G_YHMAI' // Added picture URL
            ],
            'attributes' => [
                'avatar_original' => 'https://lh3.googleusercontent.com/a-/AOh14Gh2G_YHMAI'
            ]
        ];

        // Mock Socialite to return the mocked Google user
        Socialite::shouldReceive('driver->stateless->user')
            ->once()
            ->andReturn($googleUser);

        // Simulate the Google login
        $response = $this->get('/api/v1/auth/google/callback');

        // Check for success response
        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'User successfully authenticated',
                 ]);

        // Assert user creation or update
        $user = User::where('email', 'john.doe@example.com')->first();
        // dd($user);
        $this->assertNotNull($user);
        // $this->assertEquals('google-id-12345', $user->social_id); // Adjusted field name

        // Assert profile creation or update (if using a separate profile model)
        $profile = $user->profile; // Ensure you have a profile relationship
        $this->assertNotNull($profile);
        $this->assertEquals('John', $profile->first_name);
        $this->assertEquals('Doe', $profile->last_name);
    }
}
