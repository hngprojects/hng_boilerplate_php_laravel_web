<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use App\Models\User;
use App\Models\Preference;
use Tymon\JWTAuth\Facades\JWTAuth;

class PreferenceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and authenticate them
        $this->user = User::factory()->create();
        // $this->token = JWTAuth::fromUser($this->user);
        $this->actingAs($this->user, 'api');
    }
}
