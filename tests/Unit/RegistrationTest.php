<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Mockery;
use Illuminate\Support\Facades\DB;

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
            'admin_secret' => '',
        ];

        $response = $this->postJson('/api/v1/auth/register', $registrationData);

        // Check the status code
        $response->assertStatus(201);

        // Check the response structure
        $response->assertJsonStructure([
            'status',
            'message',
            'access_token',
            'data' => [
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'avatar_url',
                    'role'
                ]
            ]
        ]);

        // Optionally, decode and verify the token
        $token = $response->json('access_token');
        $token = $response->json('access_token');
        $this->assertNotEmpty($token);
    }

    public function test_fails_if_email_is_not_passed()
    {
        $registrationData = [
            'name' => 'Test User',
            'password' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'password_confirmation' => 'Ed8M7s*)?e:hTb^#&;C!<y',
            'first_name' => 'Test',
            'last_name' => 'User',
        ];

        $response = $this->postJson('/api/v1/auth/register', $registrationData);

        // Check the status code
        $response->assertStatus(400);
        $response->assertJson([
            'status_code' => 400,
            'message' => [
                'email' => [
                    'The email field is required.'
                ]
            ],
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
                     'status_code' => 200,
                     'message' => 'User successfully authenticated',
                 ]);

        // Assert user creation or update
        $user = User::where('email', 'john.doe@example.com')->first();
        // dd($user);
        $this->assertNotNull($user);
        $this->assertEquals('google-id-12345', $user->social_id);

        $profile = $user->profile;
        $this->assertNotNull($profile);
        $this->assertEquals('John', $profile->first_name);
        $this->assertEquals('Doe', $profile->last_name);
    }

    /** @test */
    public function google_login_creates_or_updates_user_and_profile_with_post()
    {
        // Mock Google user response
        $googleUser = [
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

        $response = $this->postJson('/api/v1/auth/google/callback', [
            'google_user' => $googleUser
        ]);

        $response->assertStatus(Response::HTTP_OK);

        // Verify user in the database
        $user = User::where('email', $googleUser['email'])->first();
        $this->assertNotNull($user);
        $this->assertEquals($googleUser['id'], $user->social_id);
        $this->assertEquals('Google', $user->signup_type);
        $this->assertEquals('John', $user->profile->first_name);
        $this->assertEquals('Doe', $user->profile->last_name);
        $this->assertEquals($googleUser['attributes']['avatar_original'], $user->profile->avatar_url);
    }

    /** @test */
    public function facebook_login_creates_or_updates_user_and_profile()
    {
        // Mock Facebook user response
        $facebookUser = (object) [
            'id' => '10220927895907350',
            'nickname' => null,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'avatar' => 'https://graph.facebook.com/v3.3/10220927895907350/picture',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'id' => '102907350'
            ],
            'attributes' => [
                'id' => '102209277350',
                'nickname' => null,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'avatar' => 'https://graph.facebook.com/v3.3/102209907350/picture',
                'avatar_original' => 'https://graph.facebook.com/v3.3/1022097350/picture?width=1920',
                'profileUrl' => null
            ],
            'token' => 'EAAXSQSZAEan4BO48hdLYs84YGRXkTzBCZC5Pkpx3J6TlrmakX3SiMRJoYR2FYwb5hR1otRJxuGglfBVRJc9J9LgcDBOmHdsdblKZAFmPo6ZAwex6KN3DuRN9cyRM7ZCKGNdOWgvZCBmp0qUjhkaFUCr71L43ExxSc8ZAHQalcEDQqar9mXeg3EzuBasQFnFxOqFw3ZBbZBM3O91wENlK4YwZDZD',
            'refreshToken' => null,
            'expiresIn' => 5169882,
            'approvedScopes' => [""]
        ];

        // Mock Socialite to return the mocked Facebook user
        Socialite::shouldReceive('driver->stateless->user')
            ->once()
            ->andReturn($facebookUser);

        // Simulate the Facebook login
        $response = $this->get('/api/v1/auth/facebook/callback');

        // Check for success response
        $response->assertStatus(200)
                 ->assertJson([
                     'status_code' => 200,
                     'message' => 'User successfully authenticated',
                 ]);

        // Assert user creation or update
        $user = User::where('email', 'john.doe@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('10220927895907350', $user->social_id);

        $profile = $user->profile;
        $this->assertNotNull($profile);
        $this->assertEquals('John', $profile->first_name);
        $this->assertEquals('Doe', $profile->last_name);
    }

    /** @test */
    public function facebook_login_creates_or_updates_user_and_profile_with_post()
    {
        // Mock Google user response
        $facebookUser = [
            'id' => '10220927895907350',
            'nickname' => null,
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'avatar' => 'https://graph.facebook.com/v3.3/10220927895907350/picture',
            'user' => [
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'id' => '102907350'
            ],
            'attributes' => [
                'id' => '102209277350',
                'nickname' => null,
                'name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'avatar' => 'https://graph.facebook.com/v3.3/102209907350/picture',
                'avatar_original' => 'https://graph.facebook.com/v3.3/1022097350/picture?width=1920',
                'profileUrl' => null
            ],
            'token' => 'EAAXSQSZAEan4BO48hdLYs84YGRXkTzBCZC5Pkpx3J6TlrmakX3SiMRJoYR2FYwb5hR1otRJxuGglfBVRJc9J9LgcDBOmHdsdblKZAFmPo6ZAwex6KN3DuRN9cyRM7ZCKGNdOWgvZCBmp0qUjhkaFUCr71L43ExxSc8ZAHQalcEDQqar9mXeg3EzuBasQFnFxOqFw3ZBbZBM3O91wENlK4YwZDZD',
            'refreshToken' => null,
            'expiresIn' => 5169882,
            'approvedScopes' => [""]
        ];

        $response = $this->postJson('/api/v1/auth/facebook/callback', [
            'facebook_user' => $facebookUser
        ]);

        $response->assertStatus(Response::HTTP_OK);

        // Verify user in the database
        $user = User::where('email', $facebookUser['email'])->first();
        $this->assertNotNull($user);
        $this->assertEquals($facebookUser['id'], $user->social_id);
        $this->assertEquals('Facebook', $user->signup_type);
        $this->assertEquals('John', $user->profile->first_name);
        $this->assertEquals('Doe', $user->profile->last_name);
        $this->assertEquals($facebookUser['avatar'], $user->profile->avatar_url);
    }
}
