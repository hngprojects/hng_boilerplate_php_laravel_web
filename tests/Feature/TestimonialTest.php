<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class TestimonialTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
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
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/v1/testimonials', [
            'content' => 'This is a testimonial.',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Testimonial created successfully',
            'data' => [
                'name' => $user->name,
                'content' => 'This is a testimonial.',
            ]

        ]);

        $this->assertDatabaseHas('testimonials', [
            'name' => $user->name,
            'content' => 'This is a testimonial.',
        ]);
    }


    public function testValidationErrorsAreReturnedForMissingData()
    {
        $user = User::factory()->create();
        $token = JWTAuth::fromUser($user);

        $response = $this->postJson('/api/v1/testimonials', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['content']);
    }



}
