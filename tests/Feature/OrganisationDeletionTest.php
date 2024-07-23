<?php 

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;


class OrganisationDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
public function it_requires_authentication(){
    $org = Organisation::factory()->create();

    $response = $this->deleteJson('/api/v1/organizations/'. $org->org_id);

    $response->assertStatus(401);
}

    /** @test */
    public function it_requires_the_user_to_be_the_owner(){
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $org = Organisation::factory()->create();

        $org->users()->attach($otherUser->id);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson('/api/v1/organizations/' . $org->org_id);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_marks_the_organization_as_deleted()
    {
        $user = User::factory()->create();
        $org = Organisation::factory()->create();
        $org->users()->attach($user->id);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson('/api/v1/organizations/' . $org->org_id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('organisations', ['org_id' => $org->org_id]);
    }
}