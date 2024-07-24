<?php

namespace Tests\Unit;

use App\Exports\UsersExport;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;


class ExportUserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function it_can_export_user_data_as_json()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->getJson('/api/v1/user/export/json');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'User data returns successfully',
                'status_code' => 200,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
            ]);
    }

    public function it_can_export_user_data_as_csv()
    {
        Excel::fake();

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->get('/api/v1/user/export/csv');

        $response->assertStatus(200);

        Excel::assertDownloaded('user_data.csv', function (UsersExport $export) use ($user) {
            $collection = $export->collection();
            return $collection->contains(function ($exportedUser) use ($user) {
                return $exportedUser->id === $user->id;
            });
        });
    }


    public function it_returns_error_for_invalid_format()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->get('/api/v1/user/export/invalidformat');

        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
                'message' => 'Invalid format',
                'status-code' => 400,
            ]);
    }

    public function it_returns_error_if_user_not_found()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $nonExistentUserId = 99999; // Assuming this ID does not exist
        $response = $this->getJson("/api/v1/user/export/json/{$nonExistentUserId}");

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'User not found',
                'status-code' => 404,
            ]);
    }
}
