<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SuperAdminProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_login_and_fetch_products()
    {


        // Attempt to login as the seeded admin user
        $loginResponse = $this->postJson('http://localhost:8000/api/v1/auth/login', [
            'email' => 'bulldozeradmin@hng.com',
            'password' => 'bulldozer',
        ]);

        // Assert login was successful and extract the access token
        $loginResponse->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful',
            ]);

        $accessToken = $loginResponse->json('data.access_token');

        // Use the access token to access the protected route
        $protectedResponse = $this->withHeader('Authorization', 'Bearer ' . $accessToken)
            ->getJson('http://localhost:8000/api/v1/superadmin/products');

        // Verify that the response is successful and contains the expected data
        $protectedResponse->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'name', 'username', 'quantity', 'date_added', 'in_stock', 'out_of_stock', 'total_revenue_total', 'revenue_per_month'
                    ]
                ]
            ]);
    }
}
