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
        $this->createUser("Super Admin", "superadmin", "bulldozersuperadmin@hng.com",  true);
    }

    private function createUser($name, $role, $email): void
    {
        $isSuperAdmin = ($role === 'superadmin');
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'role' => $role,
                'password' => Hash::make("@Bulldozer01"),
                'is_verified' => 1,
            ]
        );
    
        $nameParts = explode(' ', $name);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';
    
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'job_title' => $name,
                'bio' => "$name bio",
            ]
        );
    }
    
}
