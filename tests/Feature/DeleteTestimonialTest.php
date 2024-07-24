<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Testimonial;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteTestimonialTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser()
    {
        $user = User::factory()->create();

        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        return [$user, $token];
    }

    /**
     * Test deleting a testimonial as an authenticated user.
     *
     * @return void
     */
    public function test_delete_testimonial()
    {
        // Login
        [$user, $token] = $this->getAuthenticatedUser();

        // Create a testimonial for the user
        $testimonial = Testimonial::factory()->create(['user_id' => $user->id]);

        // Authenticate the user and send a DELTE request to delete the testimonial
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/testimonials/' . $testimonial->id);

        // Assert the response status and structure
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     * Test deleting a testimonial as an unauthenticated user.
     *
     * @return void
     */
    public function test_delete_testimonial_unauthenticated()
    {
        // Create a testimonial
        $testimonial = Testimonial::factory()->create();

        // Send a DELETE request without authentication
        $response = $this->deleteJson('/api/v1/testimonials/' . $testimonial->id);

        // Assert the response status
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test deleting a testimonial that does not belong to the user.
     *
     * @return void
     */
    public function test_delete_testimonial_not_owned_by_user()
    {
        // Create two users
        [$user1, $token1] = $this->getAuthenticatedUser();
        [$user2, $token2] = $this->getAuthenticatedUser();

        // Create a testimonial for the second user
        $testimonial2 = Testimonial::factory()->create(['user_id' => $user2->id]);

        // Authenticate the first user and send a DELETE request to delete the testimonial
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->deleteJson(
            '/api/v1/testimonials/' . $testimonial2->id
        );

        // Assert the response status
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
    /**
     * Test deleting a testimonial that does not exist.
     *
     * @return void
     */
    public function test_delete_testimonial_not_exist()
    {
        // Login
        [$user, $token] = $this->getAuthenticatedUser();

        // Authenticate the user and send a DELTE request to delete the testimonial
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/v1/testimonials/1');

        // Assert the response status and structure
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
