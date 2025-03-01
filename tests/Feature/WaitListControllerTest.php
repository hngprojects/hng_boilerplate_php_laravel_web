<?php

namespace Tests\Feature;

use App\Mail\WaitlistConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use App\Models\User;
use App\Models\WaitList;
use Tymon\JWTAuth\Facades\JWTAuth;

class WaitListControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);
    }
    /**
     * Test fetching all waitlist entries.
     *
     * @return void
     */
    public function testIndex()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Generate a JWT token for the admin user
        $token = JWTAuth::fromUser($admin);

        // Create some waitlist entries
        WaitList::factory()->count(3)->create();

        // Act as the admin user with the generated token
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/v1/waitlists');

        // Assert that the response status is 200

        $response->assertStatus(200);

        // Assert that the response contains the correct data structure
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => ['id', 'name', 'email', 'created_at', 'updated_at']
            ]
        ]);
    }


    /**
     * Test storing a new waitlist entry.
     *
     * @return void
     */
    public function testStore()
    {
        // Fake the mail sending
        Mail::fake();

        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson('/api/v1/waitlists', $payload);

        // Enhanced debugging on failure
        if ($response->status() !== 201) {
            dump([
                'Status Code' => $response->status(),
                'Response Content' => $response->json(),
                'Validation Errors' => $response->status() === 422 ? $response->json()['message'] : null
            ]);
        }

        $response->assertStatus(201)
            ->assertJson([
                'status' => 201,
                'message' => 'You have been added to the waitlist and an email has been sent.',
                'data' => [
                    'name' => 'John Doe',
                    'email' => 'john.doe@example.com'
                ]
            ]);

        // Database assertions
        $this->assertDatabaseHas('wait_lists', [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com'
        ]);

        // Mail assertions
        Mail::assertSent(WaitlistConfirmation::class, function ($mail) {
            return $mail->hasTo('john.doe@example.com');
        });
    }

    public function testStoreValidationFailure()
    {
        Mail::fake();

        $payload = [
            'name' => '',  // Invalid: empty name
            'email' => 'invalid-email'  // Invalid: incorrect email format
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->postJson('/api/v1/waitlists', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message'
            ]);

        // Assert no mail was sent
        Mail::assertNothingSent();

        // Assert nothing was saved to database
        $this->assertDatabaseMissing('wait_lists', [
            'email' => 'invalid-email'
        ]);
    }
}
