<?php

namespace Tests\Unit;


use App\Exports\UsersExport;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

class ExportAllUserDataTest extends TestCase
{
    use RefreshDatabase;
    /**  @test */
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

    /**  @test */
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

    /**  @test */
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
}
