<?php

namespace Database\Factories;

<<<<<<< HEAD
=======
use App\Models\Organisation;
>>>>>>> 117d01c4b7c244b16d71069c6dd6868d27886a77
use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation>
 */
class OrganisationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Organisation::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company,
            'org_id' => $this->faker->uuid,
            'email' => $this->faker->companyEmail,
            'industry' => Str::ucfirst($this->faker->word()),
            'country' => $this->faker->country,
            'address' => $this->faker->address,
            'state' => $this->faker->state,
            'description' => $this->faker->text,
            'email' => $this->faker->unique()->companyEmail,
            'industry' => $this->faker->randomElement(['Technology', 'Finance', 'Healthcare', 'Education', 'Manufacturing', 'Retail', 'Agriculture', 'Entertainment']),
            'type' => $this->faker->randomElement(['Public', 'Private', 'Non-profit', 'Government']),
            'country' => $this->faker->country,
            'address' => $this->faker->address,
            'state' => $this->faker->state,
            'user_id' => User::factory(),
        ];
    }
}
