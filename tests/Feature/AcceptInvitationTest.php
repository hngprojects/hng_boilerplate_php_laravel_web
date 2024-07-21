<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Invitation;
use App\Models\Organisation;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;


    public function test_accept_invitation()
    {
        // Create an invitation with a valid link
        $invitation = Invitation::factory()->create();

        // Perform a POST request with the invitation link
        $response = $this->postJson('api/v1/invite', [
            'invitationLink' => $invitation->link,
        ]);

        // Assert that the response status code is 200
        $response->assertStatus(200);

        // Assert that the response contains the expected structure and message
        $response->assertJson([
            "message" => 'Invitation accepted, you have been added to the organization',
            "status_code" => 200
        ]);
    }

    public function test_validation_fails()
    {
        // Perform post require without invitation link
        $response = $this->postJson('api/v1/invite', []);

        // Assert that the response status code is 400
        $response->assertStatus(400);

        // Assert that the response JSON structure matches the expected format
        $response->assertJson([
            "message" => "Invalid request data",
            "errors" =>
            [
                "invitationLink" => [
                    "The invitation link field is required."
                ]
            ],
            "status_code" => 400
        ]);
    }

    public function test_invitation_link_does_not_exist()
    {
        // Send a POST request with a non-existent invitation link
        $response = $this->postJson('api/v1/invite', [
            "invitationLink" => "http://www.localhost/api/invite/lukas34@example.net/2001b21c-2e8f-4d4b-bc72-4cd0bd878314"
        ]);

        // Assert that the response status code is 400
        $response->assertStatus(400);

        // Assert that the response JSON structure matches the expected format
        $response->assertJson([
            "message" => "Invalid or expired invitation link",
            "errors" => [
                "Invalid invitation link format"
            ],
            "status_code" => 400
        ]);
    }


    public function test_invitation_link_expired()
    {
        // Create an expired invitation
        $expiredInvitation = Invitation::factory()->create([
            'expires_at' => Carbon::now()->subDays(1), // Set expiration date to 1 day in the past
        ]);

        // Send a POST request with the expired invitation link
        $response = $this->postJson('api/v1/invite', [
            "invitationLink" => $expiredInvitation->link
        ]);

        // Assert that the response status code is 400
        $response->assertStatus(400);

        // Assert that the response JSON structure matches the expected format
        $response->assertJson([
            "message" => "Invalid or expired invitation link",
            "errors" => [
                "Expired invitation link"
            ],
            "status_code" => 400
        ]);
    }

    public function test_organisation_does_not_exist()
    {
        // Create a valid organisation
        $organisation = Organisation::factory()->create();

        // Create an invitation with a non-existent organisation
        $invitation = Invitation::factory()->create(
            [
                'org_id' => $organisation
            ]
        );

        $this->assertDatabaseHas('invitations', [
            'invite_id' => $invitation->invite_id,
            'link' => $invitation->link,
            'org_id' => $organisation->org_id
        ]);
        // delete the organisation
        $organisation->delete();

        $response = $this->postJson('api/v1/invite', [
            "invitationLink" => $invitation->link
        ]);

        // Assert that the response status code is 400
        $response->assertStatus(400);

        //Assert that the response JSON structure matches the expected format
        $response->assertJson([
            'message' => 'Invalid or expired invitation link',
            'errors' => ['Organization not found'],
            'status_code' => 400
        ]);
        print_r($response);
    }
}
