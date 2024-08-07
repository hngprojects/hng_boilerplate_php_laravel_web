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
            'timezone' => $this->faker->timezone, 
            'gmtoffset' => $this->faker->timezone, 
            'description' => $this->faker->sentence, 
        ];
    }
}
