<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test if current_password is required.
     *
     * @return void
     */
    public function testCurrentPasswordIsRequired()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'new_password' => 'NewPassword123',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'unsuccessful',
                     'message' => 'The current password field is required.',
                 ]);
    }

    /**
     * Test if new_password is required.
     *
     * @return void
     */
    public function testNewPasswordIsRequired()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'unsuccessful',
                     'message' => 'The new password field is required.',
                 ]);
    }

    /**
     * Test if new_password meets security requirements.
     *
     * @return void
     */
    public function testNewPasswordMeetsSecurityRequirements()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'short',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'unsuccessful',
                     'message' => 'The new password field must be at least 8 characters.',
                 ]);
    }

    /**
     * Test if new_password contains at least one uppercase letter.
     *
     * @return void
     */
    public function testNewPasswordMustContainUppercaseLetter()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'newpassword123!',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'unsuccessful',
                     'message' => 'The new password field must contain at least one uppercase and one lowercase letter.',
                 ]);
    }

    /**
     * Test if new_password contains at least one lowercase letter.
     *
     * @return void
     */
    public function testNewPasswordMustContainLowercaseLetter()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NEWPASSWORD123!',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'unsuccessful',
                     'message' => 'The new password field must contain at least one uppercase and one lowercase letter.',
                 ]);
    }

    /**
     * Test if new_password contains at least one number.
     *
     * @return void
     */
    public function testNewPasswordMustContainNumber()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NewPassword!',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'unsuccessful',
                     'message' => 'The new password field must contain at least one number.',
                 ]);
    }

    /**
     * Test if new_password contains at least one symbol.
     *
     * @return void
     */
    public function testNewPasswordMustContainSymbol()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NewPassword123',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'unsuccessful',
                     'message' => 'The new password field must contain at least one symbol.',
                 ]);
    }

    /**
     * Test if the current password is correctly verified.
     *
     * @return void
     */
    public function testCurrentPasswordIsCorrectlyVerified()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'WrongPassword123',
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status_code' => 400,
                     'message' => 'Current password is incorrect',
                 ]);
    }

    /**
     * Test if the new password is correctly hashed and saved.
     *
     * @return void
     */
    public function testNewPasswordIsCorrectlyHashedAndSaved()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'status' => 'success',
                     'message' => 'Password updated successfully',
                 ]);

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }

    /**
     * Test if the endpoint rejects requests without a valid token.
     *
     * @return void
     */
    public function testEndpointRejectsRequestsWithoutValidToken()
    {
        $response = $this->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.',
                ]);
    }

    /**
     * Test if the endpoint allows requests with a valid token.
     *
     * @return void
     */
    public function testEndpointAllowsRequestsWithValidToken()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertStatus(201)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Password updated successfully',
                ]);
    }

    public function testPasswordsAreHashedBeforeBeingStored()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NewPassword123!',
        ]);

        $this->assertNotEquals('NewPassword123!', $user->fresh()->password);
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }

    public function testTokensAreInvalidatedAfterPasswordChange()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $token = auth('api')->attempt(['email' => $user->email, 'password' => 'CurrentPassword123']);

        $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'CurrentPassword123',
            'new_password' => 'NewPassword123!',
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
                        ->getJson('/api/v1/password-update');

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Unauthenticated.',
                ]);
    }

    public function testErrorMessagesDoNotRevealSensitiveInformation()
    {
        $user = User::factory()->create([
            'password' => Hash::make('CurrentPassword123'),
        ]);

        $response = $this->actingAs($user, 'api')->postJson('/api/v1/password-update', [
            'current_password' => 'WrongPassword123',
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'status_code' => 400,
                    'message' => 'Current password is incorrect',
                ]);

        // Ensure no detailed system information is exposed
        $this->assertArrayNotHasKey('file', $response->json());
        $this->assertArrayNotHasKey('trace', $response->json());
    }
}
