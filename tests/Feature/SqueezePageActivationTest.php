<?php



namespace Tests\Feature;

use App\Models\SqueezePage;
use App\Models\User;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;


class SqueezePageActivationTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $adminToken;
    // protected $squeezePage;

    public function setUp(): void
    {
        parent::setUp();

        // Create admin user and generate token
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->adminToken = JWTAuth::fromUser($this->adminUser);
    }

    public function test_admin_can_activate_squeeze_page()
    {
        // Create a squeeze page with all required fields
        $squeezePage = SqueezePage::create([
            'headline' => 'Increase Your Conversions',
            'sub_headline' => 'Discover Proven Techniques',
            'hero_image' => 'conversion_secrets.jpg',
            'content' => 'Find out how to turn visitors into customers...',
            'title' => 'Conversion Secrets',
            'slug' => 'conversion-secrets-1',
            'status' => 'online',
            'activate' => false,
        ]);

        $response = $this->withHeaders(['Authorization' => "Bearer {$this->adminToken}"])
            ->patchJson("/api/v1/squeeze-pages/{$squeezePage->id}/activate");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Squeeze page activated',
                'data' => [
                    'id' =>   $squeezePage->id,
                    'activate' => true
                ]
            ]);

        $this->assertTrue($squeezePage->fresh()->activate);
    }

    public function test_admin_can_deactivate_squeeze_page()
    {
        // Create a squeeze page with all required fields

        // Create a test squeeze page
        $squeezePage = SqueezePage::create([
            'headline' => 'Increase Your Conversions',
            'sub_headline' => 'Discover Proven Techniques',
            'hero_image' => 'conversion_secrets.jpg',
            'content' => 'Find out how to turn visitors into customers...',
            'title' => 'Conversion Secrets',
            'slug' => 'conversion-secrets-2',
            'status' => 'online',
            'activate' => true,
        ]);
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->adminToken}"])
            ->patchJson("/api/v1/squeeze-pages/{$squeezePage->id}/activate");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Squeeze page deactivated',
                'data' => [
                    'id' =>   $squeezePage->id,
                    'activate' => false
                ]
            ]);

        $this->assertFalse($squeezePage->fresh()->activate);
    }

    public function test_non_admin_cannot_activate_squeeze_page()
    {
        // Create a squeeze page with all required fields
        $squeezePage = SqueezePage::create([
            'id' => '9e535fc0-b9b7-4da7-8f6c-295b20997461',
            'headline' => 'Increase Your Conversions',
            'sub_headline' => 'Discover Proven Techniques',
            'hero_image' => 'conversion_secrets.jpg',
            'content' => 'Find out how to turn visitors into customers...',
            'title' => 'Conversion Secrets',
            'slug' => 'dev-tonia',
            'status' => 'online',
            'activate' => false,
        ]);

        $regularUser = User::factory()->create(['role' => 'user']);
        $userToken = JWTAuth::fromUser($regularUser);

        $response = $this->withHeaders(['Authorization' => "Bearer $userToken"])
            ->patchJson("/api/v1/squeeze-pages/{$squeezePage->id}/activate");

        $response->assertStatus(401);
    }

    public function test_activation_fails_for_non_existent_squeeze_page()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer $this->adminToken"])
            ->patchJson("/api/v1/squeeze-pages/999999/activate");

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'Failed to toggle activation status'
            ]);
    }
}
