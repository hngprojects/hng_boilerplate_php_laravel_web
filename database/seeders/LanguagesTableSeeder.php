<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LanguagesTableSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            ['id' => (string) Str::uuid(), 'language' => 'English', 'code' => 'en', 'description' => 'English'],
            ['id' => (string) Str::uuid(), 'language' => 'Spanish', 'code' => 'es', 'description' => 'Español'],
            ['id' => (string) Str::uuid(), 'language' => 'French', 'code' => 'fr', 'description' => 'Français'],
            ['id' => (string) Str::uuid(), 'language' => 'German', 'code' => 'de', 'description' => 'Deutsch'],
            ['id' => (string) Str::uuid(), 'language' => 'Chinese', 'code' => 'zh', 'description' => '中文'],
            ['id' => (string) Str::uuid(), 'language' => 'Japanese', 'code' => 'ja', 'description' => '日本語'],
            ['id' => (string) Str::uuid(), 'language' => 'Russian', 'code' => 'ru', 'description' => 'Русский'],
            ['id' => (string) Str::uuid(), 'language' => 'Portuguese', 'code' => 'pt', 'description' => 'Português'],
            ['id' => (string) Str::uuid(), 'language' => 'Arabic', 'code' => 'ar', 'description' => 'العربية'],
            ['id' => (string) Str::uuid(), 'language' => 'Hindi', 'code' => 'hi', 'description' => 'हिन्दी'],
            ['id' => (string) Str::uuid(), 'language' => 'Bengali', 'code' => 'bn', 'description' => 'বাংলা'],
            ['id' => (string) Str::uuid(), 'language' => 'Korean', 'code' => 'ko', 'description' => '한국어'],
            ['id' => (string) Str::uuid(), 'language' => 'Italian', 'code' => 'it', 'description' => 'Italiano'],
            ['id' => (string) Str::uuid(), 'language' => 'Turkish', 'code' => 'tr', 'description' => 'Türkçe'],
            ['id' => (string) Str::uuid(), 'language' => 'Dutch', 'code' => 'nl', 'description' => 'Nederlands'],
            ['id' => (string) Str::uuid(), 'language' => 'Greek', 'code' => 'el', 'description' => 'Ελληνικά'],
            ['id' => (string) Str::uuid(), 'language' => 'Swedish', 'code' => 'sv', 'description' => 'Svenska'],
            ['id' => (string) Str::uuid(), 'language' => 'Danish', 'code' => 'da', 'description' => 'Dansk'],
            ['id' => (string) Str::uuid(), 'language' => 'Finnish', 'code' => 'fi', 'description' => 'Suomi'],
            ['id' => (string) Str::uuid(), 'language' => 'Polish', 'code' => 'pl', 'description' => 'Polski'],
        ];


        DB::table('languages')->insert($languages);
    }
}
