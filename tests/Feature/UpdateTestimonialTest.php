<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\User;
use App\Models\Testimonial;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UpdateTestimonialTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser()
    {
        $user = User::factory()->create();

        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        return [$user, $token];
    }

    /**
     * Test updating a testimonial as an authenticated user.
     *
     * @return void
     */
    public function test_update_testimonial()
    {
        // Login
        [$user, $token] = $this->getAuthenticatedUser();

        // Create a testimonial for the user
        $testimonial = Testimonial::factory()->create(['user_id' => $user->id]);

        // Define the updated data
        $data = [
            'content' => 'This is an updated testimonial.',
        ];

        // Authenticate the user and send a PATCH request to update the testimonial
        $response = $this->patchJson('/api/v1/testimonials/' . $testimonial->id, $data, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert the response status and structure
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status_code',
                'data' => [
                    'id',
                    'user_id',
                    'name',
                    'content',
                    'created_at',
                    'updated_at',
                ],
            ]);

        // Assert the testimonial was updated in the database
        $this->assertDatabaseHas('testimonials', [
            'id' => $testimonial->id,
            'content' => 'This is an updated testimonial.',
        ]);
    }

    /**
     * Test updating a testimonial with invalid data.
     *
     * @return void
     */
    public function test_update_testimonial_with_invalid_data()
    {
        // Create a user
        [$user, $token] = $this->getAuthenticatedUser();

        // Create a testimonial for the user
        $testimonial = Testimonial::factory()->create(['user_id' => $user->id]);


        // Send a PATCH request with invalid data (missing content)
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->patchJson('/api/v1/testimonials/' . $testimonial->id);

        // Assert the response status and structure
        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => [
                    [
                        'field',
                        'message',
                    ],
                ],
            ]);

        // Assert the testimonial was not updated in the database
        $this->assertDatabaseMissing('testimonials', [
            'id' => $testimonial->id,
            'content' => '',
        ]);
    }

    /**
     * Test updating a testimonial as an unauthenticated user.
     *
     * @return void
     */
    public function test_update_testimonial_unauthenticated()
    {
        // Create a testimonial
        $testimonial = Testimonial::factory()->create();

        // Define the updated data
        $data = [
            'content' => 'This is an updated testimonial.',
        ];

        // Send a PATCH request without authentication
        $response = $this->patchJson('/api/v1/testimonials/' . $testimonial->id, $data);

        // Assert the response status
        $response->assertStatus(401);
    }

    /**
     * Test updating a testimonial that does not belong to the user.
     *
     * @return void
     */
    public function test_update_testimonial_not_owned_by_user()
    {
        // Create two users
        [$user1, $token1] = $this->getAuthenticatedUser();
        [$user2, $token2] = $this->getAuthenticatedUser();

        // Create a testimonial for the second user
        $testimonial2 = Testimonial::factory()->create(['user_id' => $user2->id]);

        // Define the updated data
        $data = [
            'content' => 'This is an updated testimonial.',
        ];

        // Authenticate the first user and send a PATCH request to update the testimonial
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->patchJson(
            '/api/v1/testimonials/' . $testimonial2->id,
            $data
        );

        // Assert the response status
        $response->assertStatus(403);
    }
}
