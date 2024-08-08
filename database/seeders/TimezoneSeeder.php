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
            $dateTimeZone = new DateTimeZone($timezone);
            $gmtoffset = $dateTimeZone->getOffset(new \DateTime());
            $description = "Description for {$timezone}";

            Timezone::create([
                'timezone' => $timezone,
                'gmtoffset' => $gmtoffset,
                'description' => $description,
            ]);
    }
}
}