<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUser("Admin", "admin", "bulldozeradmin@hng.com");
        $this->createUser("Super Admin", "superadmin", "bulldozersuperadmin@hng.com");
    }

    private function createUser($name, $role, $email): void
    {
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => $role,
                'password' => Hash::make("@Bulldozer01"),
                'is_verified' => 1,
            ]
        );

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => explode(' ', $name)[0],
                'last_name' => explode(' ', $name)[1],
                'job_title' => $name,
                'bio' => "$name bio",
            ]
        );
    }
}
