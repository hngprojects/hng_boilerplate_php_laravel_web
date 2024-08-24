<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    { $organisation = Organisation::factory()->create();


        return [
            'name' => $this->faker->jobTitle,
            'org_id' => $organisation->org_id,
            'description' => $this->faker->sentence,
            'is_active' => true,
            'is_default' => false,
        ];
    }
}
