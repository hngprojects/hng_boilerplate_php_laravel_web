<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LanguagesTableSeeder extends Seeder
{
    public function run()
    {
        $languages = [
            ['id' => (string) Str::uuid(), 'language' => 'English', 'code' => 'en', 'description' => 'English Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Español (Spanish)', 'code' => 'es', 'description' => 'Spanish Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Français (French)', 'code' => 'fr', 'description' => 'French Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Deutsch (German)', 'code' => 'de', 'description' => 'German Language'],
            ['id' => (string) Str::uuid(), 'language' => '中文 (Chinese)', 'code' => 'zh', 'description' => 'Chinese Language'],
            ['id' => (string) Str::uuid(), 'language' => '日本語 (Japanese)', 'code' => 'ja', 'description' => 'Japanese Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Русский (Russian)', 'code' => 'ru', 'description' => 'Russian Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Português (Portuguese)', 'code' => 'pt', 'description' => 'Portuguese Language'],
            ['id' => (string) Str::uuid(), 'language' => 'العربية (Arabic)', 'code' => 'ar', 'description' => 'Arabic Language'],
            ['id' => (string) Str::uuid(), 'language' => 'हिन्दी (Hindi)', 'code' => 'hi', 'description' => 'Hindi Language'],
            ['id' => (string) Str::uuid(), 'language' => 'বাংলা (Bengali)', 'code' => 'bn', 'description' => 'Bengali Language'],
            ['id' => (string) Str::uuid(), 'language' => '한국어 (Korean)', 'code' => 'ko', 'description' => 'Korean Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Italiano (Italian)', 'code' => 'it', 'description' => 'Italian Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Türkçe (Turkish)', 'code' => 'tr', 'description' => 'Turkish Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Nederlands (Dutch)', 'code' => 'nl', 'description' => 'Dutch Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Ελληνικά (Greek)', 'code' => 'el', 'description' => 'Greek Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Svenska (Swedish)', 'code' => 'sv', 'description' => 'Swedish Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Dansk (Danish)', 'code' => 'da', 'description' => 'Danish Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Suomi (Finnish)', 'code' => 'fi', 'description' => 'Finnish Language'],
            ['id' => (string) Str::uuid(), 'language' => 'Polski (Polish)', 'code' => 'pl', 'description' => 'Polish Language'],
        ];
        


        DB::table('languages')->insert($languages);
    }
}
