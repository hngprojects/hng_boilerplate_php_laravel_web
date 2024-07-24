<?php

namespace Database\Factories;

use App\Models\Job;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
=======
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
>>>>>>> temp-branch
    {
        return [
            'title' => $this->faker->jobTitle,
            'description' => $this->faker->paragraph,
            'location' => $this->faker->city,
<<<<<<< HEAD
            'job_type' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship']),
            'company_name' => $this->faker->company,
=======
            'salary' => $this->faker->numberBetween(30000, 150000) . ' per year',
            'job_type' => $this->faker->randomElement(['Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship']),
            'company_name' => $this->faker->company,
            'user_id' => User::factory(),
            'organisation_id' => Organisation::factory(),
>>>>>>> temp-branch
        ];
    }
}
