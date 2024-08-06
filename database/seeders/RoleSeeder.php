<?php

namespace Database\Seeders;

use App\Models\Organisation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
          ["Guest", "Read-only access"],
          ["User", "Read, write, update"],
          ["Manager", "Read, write, approve"],
          ["Project Lead", "Manage, coordinate, oversee"],
          ["Administrator", "Full access, control"]
        ];
        $organisation = Organisation::factory()->create();
        foreach ($roles as $role) {
            $role = Role::firstOrCreate(['name' => $role[0], 'description' => $role[1], 'org_id' => $organisation->org_id]);
            Permission::inRandomOrder()->first()->roles()->attach($role->id);
        }
    }
}