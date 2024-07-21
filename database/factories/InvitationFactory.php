<?php

namespace Database\Factories;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $email = $this->faker->unique()->safeEmail;
        $uniqueId = (string) Str::uuid();
        return [
            'invite_id' => (string) Str::uuid(),
            'link' => $this->generateLink($email, $uniqueId),
            'email' => $email,
            'org_id' => Organisation::factory(),
            'expires_at' => now()->addDay(),
        ];
    }

    private function generateLink($email, $uniqueId)
    {
        return hash('sha256', $email . $uniqueId);
    }
}
