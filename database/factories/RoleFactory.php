<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition()
    {
        return [
            'name' => $this->faker->jobTitle,
            'org_id' => Organisation::factory(),
            'is_active' => true,
            'is_default' => false,
        ];
    }
}
