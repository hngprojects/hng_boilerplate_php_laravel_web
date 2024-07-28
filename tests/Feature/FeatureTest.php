<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class FeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_sub_route_is_properly_protected()
    {
        $response = $this->post('/api/v1/features', [], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_feature_name_is_required()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/features', [
            'description' => 'xxxxxxxx'
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('feature');
    }

    public function test_feature_can_be_created_successfully()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/features', [
            'feature' => 'xero',
            'description' => 'xxxxxxxx'
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
//        dd($response);
        $response->assertJson([
            'message' => 'Feature created successfully',
            'status_code' => Response::HTTP_CREATED,
            'data' => [
                'feature' => 'xero',
                'description' => 'xxxxxxxx'
            ]
        ]);
    }
}
