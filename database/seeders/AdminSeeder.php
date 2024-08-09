<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => "bulldozeradmin@hng.com"],
            [
                'name' => "Super Admin",
                'role' => "admin",
                'password' => Hash::make("@Bulldozer01"),
                'is_verified' => 1,
            ]
        );

        $admin->profile()->create([
            'first_name' => "Super",
            'last_name' => "Admin",
            'job_title' => "Super Admin",
            'bio' => "Super Admin bio",
        ]);

    }
}
