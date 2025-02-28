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
            'message' => 'Unauthenticated.',
        ]);
    }

    public function testAuthenticatedUserCanCreateTestimonialWithAnonymousName()
{
    // Create a user with a known password
    $user = User::factory()->create(['password' => bcrypt('password')]);

    // Get a JWT token
    $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);

    // Make an authenticated request without a name
    $response = $this->postJson('/api/v1/testimonials', [
        'content' => 'This is a testimonial without a name.',
    ], [
        'Authorization' => 'Bearer ' . $token,
    ]);

    $response->assertStatus(201);
    $response->assertJson([
        'status' => 'success',
        'message' => 'Testimonial created successfully',
        'data' => [
            'name' => 'Anonymous User', // Expecting the fallback
            'content' => 'This is a testimonial without a name.',
            'user_id' => $user->id,
        ],
    ]);

    // Verify the testimonial exists in the database
    $this->assertDatabaseHas('testimonials', [
        'user_id' => $user->id,
        'name' => 'Anonymous User',
        'content' => 'This is a testimonial without a name.',
    ]);
}

    public function testValidationErrorsAreReturnedForMissingData()
    {
        // Create a user with a known password
        $user = User::factory()->create(['password' => bcrypt('password')]);

        // Get a JWT token
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

    public function testUnauthenticatedUserCannotDeleteTestimonial()
    {
        $response = $this->deleteJson('api/v1/testimonials/1');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }


    public function testAdminUserCanDeleteTestimonial()
    {
        $admin = User::factory()->create(['role' => 'admin', 'password' => bcrypt('password')]);
        $testimonial = Testimonial::factory()->create();
        
        $token = JWTAuth::attempt(['email' => $admin->email, 'password' => 'password']);

        $response = $this->deleteJson("api/v1/testimonials/{$testimonial->id}", [], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Testimonial deleted successfully',
                'status_code' => 200,
            ]);

        $this->assertDatabaseMissing('testimonials', [
            'id' => $testimonial->id,
        ]);
    }
    
    public function testAuthenticatedUserCanGetAllTestimonials()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $testimonials = Testimonial::factory()->count(3)->create();
        
        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);
        
        $response = $this->getJson('/api/v1/testimonials', [
            'Authorization' => 'Bearer ' . $token,
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Testimonials fetched successfully',
        ]);
        
        $this->assertCount(3, $response->json('data'));
    }
    
    public function testUserCanUpdateOwnTestimonial()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $testimonial = Testimonial::factory()->create(['user_id' => $user->id]);
        
        $token = JWTAuth::attempt(['email' => $user->email, 'password' => 'password']);
        
        $response = $this->patchJson("/api/v1/testimonials/{$testimonial->id}", [
            'content' => 'Updated testimonial content'
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Testimonial updated successfully',
            'data' => [
                'content' => 'Updated testimonial content'
            ]
        ]);
    }
    
    public function testAdminCanUpdateAnyTestimonial()
    {
        $admin = User::factory()->create(['password' => bcrypt('password'), 'role' => 'admin']);
        $user = User::factory()->create();
        $testimonial = Testimonial::factory()->create(['user_id' => $user->id]);
        
        $token = JWTAuth::attempt(['email' => $admin->email, 'password' => 'password']);
        
        $response = $this->patchJson("/api/v1/testimonials/{$testimonial->id}", [
            'content' => 'Admin updated content'
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);
        
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message' => 'Testimonial updated successfully',
            'data' => [
                'content' => 'Admin updated content'
            ]
        ]);
    }
}