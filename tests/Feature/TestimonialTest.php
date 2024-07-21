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
    public function test_unauthenticated_user_cannot_create_testimonial()
    {
        $response = $this->postJson('/api/v1/testimonials', [
            'content' => 'This is a testimonial.',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'status' => 'Unauthorized',
            'message' => 'Unauthorized. Please log in.',
            'status_code' => 401,
        ]);
    }

    public function test_authenticated_user_can_create_testimonial()
    {
        $user = User::factory()->create();
        Log::info($user);
        $token = auth()->login($user);
        Log::info($token);
        $response = $this->postJson('/api/v1/testimonials', [
            'content' => 'This is a testimonial.',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        Log::info($response->getContent());
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Testimonial created successfully',
            'data' => [
                'name' => $user->name,
                'content' => 'This is a testimonial.',
            ],
        ]);

        $this->assertDatabaseHas('testimonials', [
            'name' => $user->name,
            'content' => 'This is a testimonial.',
        ]);
    }


    public function test_validation_errors_are_returned_for_missing_data()
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this->postJson('/api/v1/testimonials', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'status' => 'Bad Request',
            'message' => 'Please check the submitted data.',
            'status_code' => 400,
        ]);
    }


}
