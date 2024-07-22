<?php

namespace Database\Seeders;

use App\Models\WaitlistUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WaitlistUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        WaitlistUser::factory()->count(10)->create();
    }
}
