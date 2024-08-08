<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestSeeder extends Seeder
{
    public function run()
    {
        DB::table('quests')->insert([
            'title' => 'The Burning Building',
            'description' => 'Welcome! A fire has broken out in an apartment building in your neighborhood. A baby is trapped inside and needs your help. Your mission: Go into the burning building and rescue the baby. Every second counts! To succeed, you must learn to understand and speak the words that would serve as your tool.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
