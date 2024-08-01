<?php

namespace Database\Factories;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmailRequest>
 */
class EmailRequestFactory extends Factory
{
    protected $model = \App\Models\EmailRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'sender_id' => User::factory(),
            'template_id' => EmailTemplate::factory(),
            'recipient' => $this->faker->email,
            'variables' => json_encode(['name' => $this->faker->name]),
            'status' => 'pending',
        ];
    }
}
