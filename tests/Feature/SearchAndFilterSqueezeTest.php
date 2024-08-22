<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\SqueezePage;
use Tymon\JWTAuth\Facades\JWTAuth;

class SearchAndFilterSqueezeTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser(string $role)
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true
        ]);

        $token = JWTAuth::fromUser($user);

        return [$user, $token];
    }

    /** @test */
    public function it_can_search_and_filter_squeeze_pages()
    {
        [$admin, $token] = $this->getAuthenticatedUser('admin');

        // Create some squeeze pages
        SqueezePage::factory()->create([
            'title' => 'Digital Marketing',
            'slug' => 'digital-marketing',
            'status' => 'online',
            'activate' => true,
            'headline' => 'Master Digital Marketing',
            'sub_headline' => 'Unlock the Secrets of Online Success',
            'hero_image' => 'digital_marketing.jpg',
            'content' => 'Learn the best strategies to excel in digital marketing...',
        ]);

        SqueezePage::factory()->create([
            'title' => 'Conversion Secrets',
            'slug' => 'conversion-secrets',
            'status' => 'online',
            'activate' => true,
            'headline' => 'Increase Your Conversions',
            'sub_headline' => 'Discover Proven Techniques',
            'hero_image' => 'conversion_secrets.jpg',
            'content' => 'Find out how to turn visitors into customers...',
        ]);

        // Test search functionality
        $searchResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/squeeze-pages/search?q=Digital');

        $searchResponse->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Search result retrieved',
            ])->assertJsonFragment([
                'title' => 'Digital Marketing'
            ])->assertJsonMissing([
                'title' => 'Content Marketing'
            ]);

        // Test filter functionality
        $filterResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/squeeze-pages/filter?status=online');

        $filterResponse->assertStatus(200)
            ->assertJson([
                'status' => 200,
                'message' => 'Filtered results',
            ])->assertJsonFragment([
                'title' => 'Digital Marketing'
            ])->assertJsonMissing([
                'title' => 'Content Marketing'
            ]);
    }

    /** @test */
    public function it_requires_q_parameter_for_search()
    {
        [$admin, $token] = $this->getAuthenticatedUser('admin');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/squeeze-pages/search');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['q']);
    }

    /** @test */
    public function it_requires_status_parameter_for_filter()
    {
        [$admin, $token] = $this->getAuthenticatedUser('admin');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->getJson('/api/v1/squeeze-pages/filter');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }
}
