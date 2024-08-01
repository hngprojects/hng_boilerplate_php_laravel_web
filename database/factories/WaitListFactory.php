<?php

namespace Database\Factories;

use App\Models\WaitList;
use Illuminate\Database\Eloquent\Factories\Factory;

class WaitListFactory extends Factory
{
    protected $model = WaitList::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
