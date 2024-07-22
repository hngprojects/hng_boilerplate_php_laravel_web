<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Testimonial;
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


    public function testUnauthenticatedUserCannotFetchTestimonial()
    {
        $testimonial = Testimonial::factory()->create();

        $response = $this->getJson('/api/v1/testimonials/' . $testimonial->id);

        $response->assertStatus(401);
        $response->assertJson([
            // 'status' => 'Unauthenticated',
            // 'message' => 'Unauthenticated. Please log in.',
            // 'status_code' => 401,
            'message' => 'Unauthenticated.',
        ]);
    }

    public function testAuthenticatedUserCanFetchExistingTestimonial()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $testimonial = Testimonial::factory()->create();

        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        $response = $this->getJson('/api/v1/testimonials/' . $testimonial->id, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Testimonial fetched successfully',
            'data' => [
                'id' => $testimonial->id,
                'user_id' => $testimonial->user_id,
                'name' => $testimonial->name,
                'content' => $testimonial->content,
            ],
        ]);
    }

    public function testAuthenticatedUserCannotFetchNonExistingTestimonial()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        $response = $this->getJson('/api/v1/testimonials/99999', [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(404);
        $response->assertJson([
            'status' => 'Not Found',
            'message' => 'Testimonial not found.',
            'status_code' => 404,
        ]);
    }

}
