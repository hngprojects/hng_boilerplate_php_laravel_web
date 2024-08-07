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
            'name' => $this->faker->timezone,
            'offset' => (string) $this->faker->numberBetween(-43200, 50400),

        ];
    }
}
