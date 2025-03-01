<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
   
    public function test_email_verification_fails_with_expired_link()
    {

        $user = User::factory()->create(['email_verified_at' => null]);

        $expiredUrl = URL::temporarySignedRoute(
            'verification.verify',  
            now()->subMinutes(10),  
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->actingAs($user)->getJson($expiredUrl);

        $response->assertStatus(403)
             ->assertJson([
                 'error' => 'InvalidSignature',
                'message' => 'Expired or Invalid Verification Link',
             ]);
    }
}
