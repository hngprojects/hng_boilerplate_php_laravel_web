<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SqueezePageUser;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SqueezePageUser>
 */
class SqueezePageUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = SqueezePageUser::class;

    public function definition(): array
    {
        return [
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'email' => $this->faker->safeEmail,
            'title' => $this->faker->sentence(3),
        ];
    }
}
