<?php

use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\WaitlistConfirmation;
use Database\Seeders\WaitlistUserSeeder;
use App\Models\WaitlistUser;
use Tests\TestCase;

class WaitlistTest extends TestCase
{

    // Waitlist posting test
    public function test_waitlist_posting(): void
    {
        $response = $this->post('api/v1/waitlist', [
            'email'=> fake()->safeEmail(),
            'full_name'=> fake()->name(),
        ]);

        $response->assertStatus(201);
    }

    // Waitlist mailing test
    public function test_waitlist_confirmation_mailing(): void
    {
        // Fake the mail sending
        Mail::fake();

        // Prepare the data
        $waitlist_user = [
            'name' => 'Paulson Bosah',
            'email' => 'paulsonbosah@example.com'
        ];

        // Send the email
        Mail::to($waitlist_user['email'])->send(new WaitlistConfirmation($waitlist_user));

        // Assert that the email was sent
        Mail::assertSent(WaitlistConfirmation::class, function ($mail) use ($waitlist_user) {
            return $mail->hasTo($waitlist_user['email']) &&
                   $mail->waitlist_user['name'] === $waitlist_user['name'];
        });
    }

    // Waitlist factory test
    public function test_waitlist_factory(): void
    {
        // Create a single WaitlistUser using the factory
        $waitlistUser = WaitlistUser::factory()->create();

        // Assert the user is stored in the database
        $this->assertDatabaseHas('waitlist_users', [
            'id' => $waitlistUser->id,
            'email' => $waitlistUser->email,
            'full_name' => $waitlistUser->full_name,
        ]);
    }

    // Waitlist seeder test
    public function test_waitlist_seeder(): void
    {
        // Run the specific seeder
        $this->seed(WaitlistUserSeeder::class);

        // Assert the database contains the expected number of users
        $this->assertGreaterThan(0, WaitlistUser::count());
    }

     /** @test */
     public function only_admins_can_access_waitlist_users()
     {
         $admin = User::factory()->create(['role' => 'admin']);
         $nonAdmin = User::factory()->create(['role' => 'user']);
 
         $this->actingAs($nonAdmin, 'api')
             ->getJson('/api/v1/waitlist-users')
             ->assertStatus(403);
 
         $this->actingAs($admin, 'api')
             ->getJson('/api/v1/waitlist-users')
             ->assertStatus(200);
     }
 
     /** @test */
     public function it_returns_paginated_waitlist_users()
     {
         $admin = User::factory()->create(['role' => 'admin']);
         WaitlistUser::factory()->count(15)->create();
 
         $this->actingAs($admin, 'api')
             ->getJson('/api/v1/waitlist-users?page=1&limit=10')
             ->assertJson([
                 'page' => 1,
                 'limit' => 10,
                 'total_users' => 15,
                 'status_code' => 200,
                 'message' => 'Retrieved waitlist users successfully'
             ]);
     }

}

