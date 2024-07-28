<?php

namespace Tests\Feature;

use App\Enums\PlanType;
use App\Models\Feature;
use App\Models\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class SubscriptionPlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_sub_route_is_properly_protected()
    {
        $response = $this->post('/api/v1/plans', [], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_sub_returns_validation_error_when_name_is_missing_or_incorrect()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/plans', [
            'price' => 1000,
            'duration' => 'yearly',
            'description' => 'xxxxxxxx'
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');

        $response = $this->post('/api/v1/plans', [
            'name' => 'xero',
            'price' => 1000,
            'duration' => 'yearly',
            'description' => 'xxxxxxxx'
        ], [
            'accept' => 'application/json'
        ]);
        $plans = implode(', ', array_column(PlanType::cases(), 'value'));

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('name');
        $response->assertJsonValidationErrors(["name" => "The name plan name not allowed. Allowed plans are basic, premium"]);
    }

    public function test_sub_returns_validation_error_when_price_is_missing_or_invalid()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'duration' => 'yearly',
            'description' => 'xxxxxxxx'
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('price');

        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 'string',
            'duration' => 'yearly',
            'description' => 'xxxxxxxx'
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('price');
    }

    public function test_sub_returns_validation_error_when_duration_is_missing_or_invalid()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 2000,
            'description' => 'xxxxxxxx'
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('duration');

        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 2000,
            'duration' => 'month',
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('duration');
    }

    public function test_sub_returns_validation_error_when_feature_is_missing_or_invalid()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 2000,
            'description' => 'xxxxxxxx',
            'duration' => 'yearly'
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrorFor('features');

        $fakeUUid = Factory::create()->uuid();
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 2000,
            'description' => 'xxxxxxxx',
            'duration' => 'yearly',
            'features' => [
                [
                    'id' => $fakeUUid,
                    'status' => 0
                ]
            ]
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertJsonValidationErrorFor('features.0.id');
    }

    public function test_user_can_create_sub_plan_successfully()
    {
        $feature = Feature::factory()->create();
        $feature1 = Feature::factory()->create();
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 2000,
            'description' => 'xxxxxxxx',
            'duration' => 'yearly',
            'features' => [
                [
                    'id' => $feature->id,
                    'status' => 0
                ],
                [
                    'id' => $feature1->id,
                    'status' => 0
                ]
            ]
        ], [
            'accept' => 'application/json'
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            'data' => [
                'name' => 'basic',
                'price' => 2000,
                'description' => 'xxxxxxxx',
                'duration' => 'yearly',
            ],
            'status_code' => Response::HTTP_CREATED,
            'message' => 'subscription plan created successfully'
        ]);
    }

    public function test_duplicate_sub_plan_with_same_duration_cannot_be_created()
    {
        $feature = Feature::factory()->create();
        $this->actingAs(User::factory()->create());
        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 2000,
            'description' => 'xxxxxxxx',
            'duration' => 'yearly',
            'features' => [
                [
                    'id' => $feature->id,
                    'status' => 0
                ]
            ]
        ], [
            'accept' => 'application/json'
        ]);

        $response = $this->post('/api/v1/plans', [
            'name' => 'basic',
            'price' => 2000,
            'description' => 'xxxxxxxx',
            'duration' => 'yearly',
            'features' => [
                [
                    'id' => $feature->id,
                    'status' => 0
                ]
            ]
        ], [
            'accept' => 'application/json'
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson(['error' => 'basic plan already exists for yearly']);
    }
}
