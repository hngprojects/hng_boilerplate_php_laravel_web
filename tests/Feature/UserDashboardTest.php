<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserDashboardTest extends TestCase
{
    public function test_user_cards_data_is_returned_in_right_format()
    {
        $this->actingAs(User::factory()->create());
        $response = $this->get('/api/v1/user-statistics');

        $response->assertSuccessful();
        $response->assertJson([
            'message' => 'Dashboard retrieved successfully',
            'status_code' => 200,
            'data' => [
                'revenue' => [],
                'subscriptions' => [],
                'orders' => [],
                'active_users' => [],
            ]
        ]);
    }

    public function test_accurate_percentage_and_amount_is_returned_for_revenue_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $product = Product::factory()->create([
            'price' => 1000,
            'user_id' => $user->id,
        ]);

        $order = Order::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'total_amount' => 2000,
        ]);
        $order->created_at = Carbon::now()->subMonth();
        $order->save();
        Order::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 3,
            'total_amount' => 3000,
        ]);
        $response = $this->get('/api/v1/user-statistics');
        $response->assertJsonFragment([
            "current_month" => 3000,
            "previous_month" => 2000,
            "percentage_difference" => "50%"
        ]);
    }

    public function test_accurate_data_is_returned_for_graph_usage()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $product = Product::factory()->create([
            'price' => 1000,
            'user_id' => $user->id,
        ]);

        // Get current and last month
        $currentMonth = Carbon::now()->format('M');
        $lastMonth = Carbon::now()->subMonth()->format('M');

        // Create last month's order
        $lastMonthOrder = Order::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'total_amount' => 2000,
        ]);
        $lastMonthOrder->created_at = Carbon::now()->subMonth();
        $lastMonthOrder->save();

        // Create current month's order
        $currentMonthOrder = Order::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 3,
            'total_amount' => 3000,
        ]);

        $response = $this->get('/api/v1/user-analytics');

        // Create expected data with proper number format
        $expectedData = array_fill_keys(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 0);
        $expectedData[$lastMonth] = '2000.00';
        $expectedData[$currentMonth] = '3000.00';

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User analytics retrieved successfully',
                'status_code' => 200,
                'data' => $expectedData
            ]);
    }


    public function test_accurate_data_is_returned_for_recent_sales()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $product = Product::factory()->create([
            'price' => 1000,
            'user_id' => $user->id,
        ]);
        $order = Order::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 2,
            'total_amount' => 2000,
        ]);
        $order->created_at = Carbon::now()->subMonth();
        $order->save();
        $order1 = Order::factory()->create([
            'product_id' => $product->product_id,
            'quantity' => 3,
            'total_amount' => 3000,
        ]);
        $response = $this->get('/api/v1/user-sales');
        $response->assertSuccessful();
        $response->assertJson(
            [
                'message' => 'Recent sales retrieved successfully',
                'status_code' => 200,
                'data' => [
                    [
                        'id' => $order->id,
                        'user_id' => $order->user_id,
                        'quantity' => 2,
                        'total_amount' => '2000.00',
                        'user' => []
                    ],
                    [
                        'id' => $order1->id,
                        'user_id' => $order1->user_id,
                        'quantity' => 3,
                        'total_amount' => '3000.00',
                        'user' => []
                    ],
                ]
            ]
        );
    }
}
