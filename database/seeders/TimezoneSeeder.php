<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Timezone;
use DateTimeZone;

class TimezoneSeeder extends Seeder
{
    public function run()
    {
        $timezones = DateTimeZone::listIdentifiers();

        foreach ($timezones as $timezone) {
            Timezone::create([
                'name' => $timezone,
                'offset' => (new DateTimeZone($timezone))->getOffset(new \DateTime()),
            ]);
        }
    }
}
