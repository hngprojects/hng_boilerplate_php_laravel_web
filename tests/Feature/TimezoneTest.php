<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Timezone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\V1\TimezoneController;

class TimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_all_timezones()
    {
        Timezone::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/timezones');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         '*' => ['id', 'name','offset']
                     ]
                 ]);
    }

    public function test_timezone_has_correct_structure()
    {
        $timezone = Timezone::factory()->create();

        $this->assertNotNull($timezone->id);
        $this->assertIsString($timezone->name);
        $this->assertIsString($timezone->offset);
    }
}
