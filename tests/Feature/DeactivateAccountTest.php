<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountDeactivatedMail;

class DeactivateAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_deactivation()
    {
        Mail::fake();

        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user, 'api')->patchJson('/api/v1/accounts/deactivate', [
            'confirmation' => true,
            'reason' => 'No longer need the account'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'status_code' => 200,
                     'message' => 'Account Deactivated Successfully'
                 ]);
        
        // Refresh user to get latest data from db
        $user->refresh();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'deactivated'
        ]);

        Mail::assertSent(AccountDeactivatedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_missing_confirmation_field()
    {
        $user = User::factory()->create(['status' => 'active']);

        $response = $this->actingAs($user, 'api')->patchJson('/api/v1/accounts/deactivate', [
            'reason' => 'No longer need the account'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['confirmation']);
    }

    public function test_email_sent_on_deactivation()
    {
        Mail::fake();

        $user = User::factory()->create(['status' => 'active']);

        $this->actingAs($user, 'api')->patchJson('/api/v1/accounts/deactivate', [
            'confirmation' => true,
            'reason' => 'No longer need the account'
        ]);

        Mail::assertSent(AccountDeactivatedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_already_deactivated_user()
    {
        $user = User::factory()->create(['status' => 'deactivated']);

        $response = $this->actingAs($user, 'api')->patchJson('/api/v1/accounts/deactivate', [
            'confirmation' => true,
            'reason' => 'No longer need the account'
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status_code' => 400,
                     'error' => 'User has been deactivated'
                 ]);
    }
}
