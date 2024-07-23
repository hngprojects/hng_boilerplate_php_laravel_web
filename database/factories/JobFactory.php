<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Job::class;
    public function definition(): array
    {
        $user = User::factory()->create();
        $organisation = Organisation::factory()->create();
        return [
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city,
            'salary' => $this->faker->numberBetween(30000, 150000) . ' per year',
            'job_type' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract']),
            'company_name' => $this->faker->company,
            'user_id' => User::factory(),
            'organisation_id' => User::factory()->create()->organisation_id,
        ];
    }
}
