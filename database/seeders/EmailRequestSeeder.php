<?php

namespace Database\Seeders;

use App\Models\EmailRequest;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmailTemplate::factory()->count(3)->create();
        User::factory()->count(3)->create();
        EmailRequest::factory()->count(3)->create();
    }
}
