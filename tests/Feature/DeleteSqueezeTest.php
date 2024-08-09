<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\SqueezePage;
use Tymon\JWTAuth\Facades\JWTAuth;

class DeleteSqueezeTest extends TestCase
{
    use RefreshDatabase;

    private function getAuthenticatedUser(string $role)
    {
        $user = User::factory()->create([
            'role' => $role,
            'is_active' => true,
        ]);

        $token = JWTAuth::fromUser($user);

        return [$user, $token];
    }

    /** @test */
    public function admin_can_delete_a_squeeze_page()
    {
        [$admin, $token] = $this->getAuthenticatedUser('admin');

        $squeezePage = SqueezePage::create([
            'title' => 'Digital Marketing',
            'slug' => 'digital-marketing',
            'status' => 'online',
            'activate' => true,
            'headline' => 'Master Digital Marketing',
            'sub_headline' => 'Unlock the Secrets of Online Success',
            'hero_image' => 'digital_marketing.jpg',
            'content' => 'Learn the best strategies to excel in digital marketing...',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson(route('squeeze-pages.destroy', ['squeeze_page' => $squeezePage->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Squeeze Page deleted successfully',
                'status' => 200,
            ]);

        $this->assertDatabaseMissing('squeeze_pages', [
            'id' => $squeezePage->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_delete_a_squeeze_page()
    {
        [$user, $token] = $this->getAuthenticatedUser('user');

        $squeezePage = SqueezePage::create([
            'title' => 'Digital Marketing',
            'slug' => 'digital-marketing',
            'status' => 'online',
            'activate' => true,
            'headline' => 'Master Digital Marketing',
            'sub_headline' => 'Unlock the Secrets of Online Success',
            'hero_image' => 'digital_marketing.jpg',
            'content' => 'Learn the best strategies to excel in digital marketing...',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->deleteJson(route('squeeze-pages.destroy', ['squeeze_page' => $squeezePage->id]));

        $response->assertStatus(401)
            ->assertJson([
                'status_code' => 401,
                'message' => 'Unauthorized, admin access only',
            ]);

        $this->assertDatabaseHas('squeeze_pages', [
            'id' => $squeezePage->id,
        ]);
    }
}
