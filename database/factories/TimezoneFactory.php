<?php

namespace Database\Factories;

use App\Models\Timezone;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimezoneFactory extends Factory
{
    protected $model = Timezone::class;

    public function definition()
    {
        return [
            'timezone' => $this->faker->timezone,  // Corrected column name
            'gmtoffset' => $this->faker->timezone, // Adjusted to reflect correct GMT offset format
            'description' => $this->faker->sentence, // Added description
        ];
    }
}
