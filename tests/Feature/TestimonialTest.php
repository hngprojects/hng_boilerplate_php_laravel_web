<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    public function testUnauthenticatedUserCannotCreateTestimonial()
    {
        $response = $this->postJson('/api/v1/testimonials', [
            'content' => 'This is a testimonial.',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function testAuthenticatedUserCanCreateTestimonial()
    {
        // Create a user with a known password
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Attempt to log in the user and get a token
//        $token = auth()->login($user);
        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        // Make an authenticated request
        $response = $this->postJson('/api/v1/testimonials', [
            'content' => 'This is a testimonial.',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
//        dump($response);
        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Testimonial created successfully',
            'data' => [
                'name' => $user->name,
                'content' => 'This is a testimonial.',
            ],
        ]);
    }

    public function testValidationErrorsAreReturnedForMissingData()
    {
        // Create a user with a known password
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Attempt to log in the user and get a token
        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        // Make an authenticated request with missing data
        $response = $this->postJson('/api/v1/testimonials', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(400);
        $response->assertJsonValidationErrors(['content']);
    }
}
