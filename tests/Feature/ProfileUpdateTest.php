<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $profile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create([
            'password' => Hash::make('password') // Ensure the user has a password
        ]);

        // Create a profile for the user
        $this->profile = Profile::factory()->create([
            'user_id' => $this->user->id
        ]);

        // Authenticate the user
        Auth::login($this->user);
    }

    /** @test */
    public function it_can_update_profile()
    {
        $response = $this->patch('/api/v1/profile', [
            'first_name' => 'UpdatedFirstName',
            'last_name' => 'UpdatedLastName',
            'email' => 'updated@example.com',
            'avatar_url' => 'https://example.com/new-avatar.png'
        ]);

        $response->assertStatus(200);
        $this->assertEquals('UpdatedFirstName', $this->profile->fresh()->first_name);
        $this->assertEquals('UpdatedLastName', $this->profile->fresh()->last_name);
        $this->assertEquals('https://example.com/new-avatar.png', $this->profile->fresh()->avatar_url);
        $this->assertEquals('updated@example.com', $this->user->fresh()->email);
    }

    /** @test */
    public function it_can_upload_image()
    {
       
        $file = UploadedFile::fake()->image('avatar.jpg');
    
        $response = $this->post('/api/v1/profile/upload-image', [
            'file' => $file
        ]);
 
        $response->assertStatus(200);
    
        $response->assertJsonStructure([
            'Status',
            'Message',
            'Data' => ['avatar_url']
        ]);

        $uploadedFileUrl = $response->json('Data.avatar_url');

        $this->assertFileExists(public_path('uploads/' . basename($uploadedFileUrl)));

        $this->assertStringStartsWith(url('uploads/'), $uploadedFileUrl);
    }
    
    
}
