<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestMessageSeeder extends Seeder
{
    public function run()
    {
        DB::table('quest_messages')->insert([
            [
                'quest_id' => 1,
                'key' => 'message_1',
                'question' => 'I need to ask where the baby is',
                'answer' => 'The baby is on the second floor.',
                'sequence' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'quest_id' => 1,
                'key' => 'message_2',
                'question' => 'How can I get there?',
                'answer' => 'Take the stairs on your left.',
                'sequence' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
