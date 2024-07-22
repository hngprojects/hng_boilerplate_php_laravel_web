<?php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserJobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(10)->create();

        foreach ($users as $user) {
            $jobs = Job::factory()->count(5)->create();
            foreach ($jobs as $job) {
                $user->jobs()->attach($job->id);
            }
        }
    }
}
