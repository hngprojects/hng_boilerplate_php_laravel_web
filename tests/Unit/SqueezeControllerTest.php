<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class SqueezeControllerTest extends TestCase
{
    public function testValidDataPassesValidation()
    {
        $data = [
            'email' => 'user@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '08098761234',
            'location' => 'Lagos, Nigeria',
            'job_title' => 'Software Engineer',
            'company' => 'X-Corp',
            'interests' => ['Web Development', 'Cloud Computing'],
            'referral_source' => 'LinkedIn',
        ];

        $rules = [
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'interests' => 'required|array',
            'referral_source' => 'required|string|max:255',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function testMissingEmailFailsValidation()
    {
        $data = [
            // 'email' is missing
            'first_name' => 'John',
            'last_name' => 'Doe',
            'phone' => '08098761234',
            'location' => 'Lagos, Nigeria',
            'job_title' => 'Software Engineer',
            'company' => 'X-Corp',
            'interests' => ['Web Development', 'Cloud Computing'],
            'referral_source' => 'LinkedIn',
        ];

        $rules = [
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'location' => 'required|string|max:255',
            'job_title' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'interests' => 'required|array',
            'referral_source' => 'required|string|max:255',
        ];

        $validator = Validator::make($data, $rules);

        $this->assertTrue($validator->fails());
    }
}
