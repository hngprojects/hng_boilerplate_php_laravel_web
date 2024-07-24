<?php

namespace Tests\Feature;

use App\Models\AboutPage;
use App\Models\User;
use Database\Seeders\AboutPageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create or retrieve the admin and regular users for testing
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
    }

    // AboutPage authorized access test
    public function testRetrieveAboutPageContentSuccess()
    {
        // Seed the about page content
        AboutPage::factory()->create([
            'title' => 'More Than Just A BoilerPlate',
            'introduction' => 'Welcome to Hng Boilerplate, where passion meets innovation.',
            'years_in_business' => 10,
            'customers' => 75000,
            'monthly_blog_readers' => 100000,
            'social_followers' => 1200000,
            'services_title' => 'Trained to Give You The Best',
            'services_description' => 'Since our founding, Hng Boilerplate has been dedicated to constantly evolving to stay ahead of the curve.'
        ]);

        $response = $this->actingAs($this->adminUser)->getJson('/api/v1/content/about');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'title',
                'introduction',
                'custom_sections' => [
                    'stats' => [
                        'years_in_business',
                        'customers',
                        'monthly_blog_readers',
                        'social_followers'
                    ],
                    'services' => [
                        'title',
                        'description'
                    ]
                ],
                'status_code',
                'message'
            ]);
    }

    // AboutPage unathourized access test
    public function testRetrieveAboutPageContentUnauthorized()
    {
        $response = $this->actingAs($this->regularUser)->getJson('/api/v1/content/about');
        
        $response->assertStatus(403)
            ->assertJson([
                'message' => 'You do not have the necessary permissions to access this resource',
                'status_code' => 403
            ]);
    }

    // AboutPage factory test
    public function test_airline_factory()
    {
        $about_record = AboutPage::factory()->create();

        // Ensure the AboutPage is an instance of the AboutPage model
        $this->assertInstanceOf(AboutPage::class, $about_record);

        // Check that some of the attributes are set
        $this->assertNotNull($about_record->title);
        $this->assertNotNull($about_record->years_in_business);
    }

    // AboutPage seeder test
    public function test_AboutPage_seeder()
    {
        $responses = $this->seed(AboutPageSeeder::class);

        // Check that responses were created
        $this->assertDatabaseCount('bot_responses', count($responses));
    }
}
