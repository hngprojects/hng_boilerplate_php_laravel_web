<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    private $accessToken;
    private $organisationId;
    private $invitationLink;

    public function setUp(): void
    {
        parent::setUp();

        // Register a new user
        $this->postJson('/api/v1/auth/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'test1234',
        ]);

        // Login the user
        $loginResponse = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'test1234',
        ]);

        $this->accessToken = $loginResponse['data']['access_token'];

        // Create an organisation
        $orgResponse = $this->postJson('/api/v1/organisations', [
            'name' => 'test organisations',
            'description' => 'test org description',
            'email' => 'test.main.org@example.com',
            'industry' => 'test industry',
            'type' => 'test type',
            'country' => 'test country',
            'address' => 'test address',
            'state' => 'test state'
        ], ['Authorization' => 'Bearer ' . $this->accessToken]);

        $this->organisationId = $orgResponse['data']['org_id'];

        // Generate an invitation
        $invitationResponse = $this->postJson('/api/v1/invitations/generate', [
            'org_id' => $this->organisationId,
            'email' => 'test@example.com'
        ]);

        $this->invitationLink = $invitationResponse['invitation']['link'];
    }

    public function test_accept_invitation_via_get()
    {
        $response = $this->getJson('/api/v1/invite/accept?token=' . $this->invitationLink);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Invitation accepted, you have been added to the organization',
                'status' => 200
            ]);
    }

    public function test_accept_invitation_via_post()
    {
        $response = $this->postJson('/api/v1/invite', [
            'invitation_link' => $this->invitationLink
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Invitation accepted, you have been added to the organization',
                'status' => 200
            ]);
    }
}
