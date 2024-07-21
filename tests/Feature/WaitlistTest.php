<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WaitlistTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    // Waitlist posting test
    public function test_waitlist_posting(): void
    {
        $response = $this->post('api/v1/waitlist', [
            'email'=> fake()->safeEmail(),
            'full_name'=> fake()->name(),
        ]);

        $response->assertStatus(201);
    }
}
