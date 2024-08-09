<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;


class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Region::factory()->count(10)->create(); // Creates 10 regions
    }
}
