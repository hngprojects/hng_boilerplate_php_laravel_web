<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Organisation;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class organisationMemberControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected $user;
    protected $organisation;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and log them in
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        // Create an organisation
        $this->organisation = Organisation::factory()->create();
    }


    public function test_it_returns_users_when_searching_with_valid_organisation_id()
    {
        // Create users belonging to the organisation
        $user1 = $this->organisation->users()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        $user2 = $this->organisation->users()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
        ]);

        // Search for users in the organisation
        $response = $this->getJson('/api/v1/members/' . $this->organisation->org_id . '/search?search=Jane');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Users retrieved successfully',
                'status_code' => 200,
                'data' => [
                    [
                        'name' => 'Jane Smith',
                        'email' => 'jane@example.com',
                    ]
                ]
            ]);
    }

    public function test_it_returns_an_error_when_organisation_id_is_invalid()
    {
        // Pass an invalid organisation ID
        $invalidOrgId = Str::random(10);

        $response = $this->getJson('/api/v1/members/' . $invalidOrgId . '/search');

        $response->assertStatus(400)
            ->assertJson([
                'message' => 'Invalid organisation ID',
                'status_code' => 400,
            ]);
    }

    public function test_it_returns_an_error_when_organisation_does_not_exist()
    {
        // Create a non-existing organisation ID
        $nonExistingOrgId = Str::uuid()->toString();

        $response = $this->getJson('/api/v1/members/' . $nonExistingOrgId . '/search');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'organisation does not exist',
                'status_code' => 404,
            ]);
    }

    public function testDownloadCsv()
    {

        $user = $this->user;
        // Create an organisation
        $organisation = Organisation::factory()->create();


        // Attach user to the organisation
        $organisation->users()->attach($user);

        // Mock the storage
        Storage::fake('local');

        //dd($organisation->org_id);

        // Call the download endpoint
        $response = $this->get("/api/v1/members/{$organisation->org_id}/export");


        $response->assertOk();

        $now = Carbon::today()->isoFormat('DD_MMMM_YYYY');
        $fileName = "users_$now.csv";
        $filePath = 'csv/' . $fileName;

        // Assert the file was created
        $this->assertTrue(Storage::disk('local')->exists($filePath));

        // Assert the file content
        $csvContent = Storage::disk('local')->get($filePath);
        $lines = explode("\n", trim($csvContent));

        // Assert the CSV header
        $this->assertEquals('UserName,UserEmail,UserStatus,CreatedDate', $lines[0]);

        // Assert the response headers
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=' . $fileName);

        // Clean up: Remove the file
        Storage::disk('local')->delete($filePath);
    }
}
// Added comment so i can push again
