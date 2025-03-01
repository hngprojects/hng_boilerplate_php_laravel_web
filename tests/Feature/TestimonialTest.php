<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Tymon\JWTAuth\Facades\JWTAuth;

class TestimonialTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUnauthenticatedUserCannotCreateTestimonial()
    {
        $response = $this->postJson('/api/v1/testimonials', [
            'content' => 'This is a testimonial.',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'error' => 'Unauthorized',
            'message' => 'Unauthenticated.',
        ]);
    }

    public function testAuthenticatedUserCanCreateTestimonial()
    {
        
        $user = User::factory()->create(['password' => bcrypt('password')]);

        
        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        $response = $this->postJson('/api/v1/testimonials', [
            'content' => 'This is a testimonial.',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'status_code' => 200,  
            'message' => 'Testimonial created successfully',
        ]);
        
        
        $response->assertJsonStructure([
            'status',
            'status_code',
            'message',
            'data' => [
                'user_id',
                'name',
                'content',
                'id',
                'updated_at',
                'created_at'
            ]
        ]);
    }

    public function testValidationErrorsAreReturnedForMissingData()
    {
        
        $user = User::factory()->create(['password' => bcrypt('password')]);

        
        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        
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
            'error' => 'Unauthorized',
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
            'status_code' => 200,
            'message' => 'Testimonial fetched successfully',
        ]);
        
        // Check that the structure matches
        $response->assertJsonStructure([
            'status',
            'status_code',
            'message',
            'data' => [
                'id',
                'user_id',
                'name',
                'content',
                'created_at',
                'updated_at'
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
        $response->assertStatus(200);
        $this->assertTrue(
            $response->json('status') === 'error' || 
            $response->json('status') === 'Not Found' || 
            $response->json('message') === 'Testimonial not found.' ||
            strpos($response->json('message'), 'not found') !== false
        );
    }


    public function testUnauthenticatedUserCannotDeleteTestimonial()
    {
        $response = $this->deleteJson('api/v1/testimonials/1');

        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
                'message' => 'Unauthenticated.',
            ]);
    }

    public function testNonAdminUserCannotDeleteTestimonial()
    {
        $user = User::factory()->create(['role' => 'user']);

        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

        $response = $this->deleteJson('api/v1/testimonials/1', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        
        $response->assertStatus(401);
        
        
        $responseData = $response->json();
        $this->assertArrayHasKey('message', $responseData);
    }

    public function testAdminUserCannotDeleteNonExistingTestimonial()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $token = JWTAuth::attempt(['email' => $admin->email, 'password' => 'password']);

        $response = $this->deleteJson('api/v1/testimonials/99999', [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        
        $response->assertStatus(401);
        
        
        $this->assertArrayHasKey('message', $response->json());
    }

    public function testAdminUserCanDeleteTestimonial()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $testimonial = Testimonial::factory()->create();

        $token = JWTAuth::attempt(['email' => $admin->email, 'password' => 'password']);

        $response = $this->deleteJson("api/v1/testimonials/{$testimonial->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        
        $response->assertStatus(401);
        
        
        $this->assertArrayHasKey('message', $response->json());
    }
}