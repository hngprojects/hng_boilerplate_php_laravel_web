<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;
<<<<<<< HEAD

class RegionFactory extends Factory
{
    protected $model = Region::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
=======
use Illuminate\Support\Str;

/**
 * @extends 
 */
class RegionFactory extends Factory
{
    protected $model = Region::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'name' => $this->faker->city(),
     
>>>>>>> b1b3bda40bbf43d81694142e4fd86a819127c2ec
        ];
    }
}
