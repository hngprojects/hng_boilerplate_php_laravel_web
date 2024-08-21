<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;


class LanguageControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the creation of a new language.
     *
     * @return void
     */
    public function testCreateLanguage()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/languages', [
            'language' => 'Italian',
            'code' => 'it',
            'description' => 'Italian Language',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status_code' => 201,
                'message' => 'Language Created Successfully',
                'data' => [
                    'language' => 'Italian',
                    'code' => 'it',
                    'description' => 'Italian Language',
                ],
            ]);

        $this->assertDatabaseHas('languages', [
            'language' => 'Italian',
            'code' => 'it',
            'description' => 'Italian Language',
        ]);
    }

    /**
     * Test the creation of a language with validation errors.
     *
     * @return void
     */
    public function testCreateLanguageValidationErrors()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/languages', []);

        $response->assertStatus(400)
            ->assertJson([
                'status_code' => 400,
                'message' => 'Bad Request',
                'errors' => [
                    'language' => ['The language field is required.'],
                    'code' => ['The code field is required.'],
                ],
            ]);
    }

    /**
     * Test updating an existing language.
     *
     * @return void
     */
    public function testUpdateLanguage()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $language = Language::create([
            'language' => 'French',
            'code' => 'fr',
            'description' => 'French Language',
        ]);

        $response = $this->putJson("/api/v1/languages/{$language->id}", [
            'language' => 'French(Updated)',
            'code' => 'fr-updated',
            'description' => 'Updated French Language',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'message' => 'Language Updated Successfully',
                'data' => [
                    'language' => 'French(Updated)',
                    'code' => 'fr-updated',
                    'description' => 'Updated French Language',
                ],
            ]);

        $this->assertDatabaseHas('languages', [
            'id' => $language->id,
            'language' => 'French(Updated)',
            'code' => 'fr-updated',
            'description' => 'Updated French Language',
        ]);
    }

    /**
     * Test updating a language that does not exist.
     *
     * @return void
     */
    public function testUpdateNonExistentLanguage()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $nonExistentId = (string) Str::uuid();

        $response = $this->putJson("/api/v1/languages/{$nonExistentId}", [
            'language' => 'NonExistent',
            'code' => 'nx',
            'description' => 'Non-Existent Language',
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'status_code' => 404,
                'message' => 'Language not found',
            ]);
    }

    /**
     * Test fetching the list of languages.
     *
     * @return void
     */
    public function testFetchLanguages()
{
    $user = User::factory()->create();
    $this->actingAs($user, 'api');

    // Manually create languages
    $languages = [
        Language::create([
            'id' => (string) Str::uuid(),
            'language' => 'English',
            'code' => 'en',
            'description' => 'English Language',
        ]),
        Language::create([
            'id' => (string) Str::uuid(),
            'language' => 'Spanish',
            'code' => 'es',
            'description' => 'Spanish Language',
        ]),
        Language::create([
            'id' => (string) Str::uuid(),
            'language' => 'French',
            'code' => 'fr',
            'description' => 'French Language',
        ]),
    ];

    $response = $this->getJson('/api/v1/languages');

    $response->assertStatus(200)
        ->assertJson([
            'status_code' => 200,
            'message' => 'Languages fetched successfully',
            'data' => array_map(function ($language) {
                return [
                    'id' => $language->id,
                    'language' => $language->language,
                    'code' => $language->code,
                ];
            }, $languages),
        ]);
}


    /**
     * Test unauthorized access to create language.
     *
     * @return void
     */
    public function testCreateLanguageUnauthorized()
    {
        $response = $this->postJson('/api/v1/languages', [
            'language' => 'Italian',
            'code' => 'it',
            'description' => 'Italian Language',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status_code' => 401,
                'message' => 'Unauthorized',
            ]);
    }

    /**
     * Test unauthorized access to update language.
     *
     * @return void
     */
    public function testUpdateLanguageUnauthorized()
    {
        $language = Language::create([
            'language' => 'French',
            'code' => 'fr',
            'description' => 'French Language',
        ]);

        $response = $this->putJson("/api/v1/languages/{$language->id}", [
            'language' => 'French(Updated)',
            'code' => 'fr-updated',
            'description' => 'Updated French Language',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'status_code' => 401,
                'message' => 'Unauthorized',
            ]);
    }

    /**
     * Test unauthorized access to fetch languages.
     *
     * @return void
     */
    public function testFetchLanguagesUnauthorized()
    {
        $response = $this->getJson('/api/v1/languages');

        $response->assertStatus(401)
            ->assertJson([
                'status_code' => 401,
                'message' => 'Unauthorized',
            ]);
    }
}