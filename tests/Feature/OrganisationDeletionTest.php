<?php 

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organisation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class OrganisationDeletionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
public function it_requires_authentication(){
    $org = Organisation::factory()->create();

    $response = $this->deleteJson('/api/v1/organisations/'. $org->org_id);

    $response->assertStatus(401);
}

    /** @test */
  public function it_requires_the_user_to_be_the_owner(){
    $user = User::factory()->create();
    $owner = User::factory()->create();

    $org = Organisation::factory()->create(['user_id' => $owner->id]);

    $token = JWTAuth::fromUser($user);
    $response = $this->withHeaders(['Authorization' => "Bearer $token"])
    ->deleteJson('/api/v1/organisations/' . $org->org_id);

    $response->assertStatus(401);
}

    /** @test */
    public function it_marks_the_organisation_as_deleted()
    {
        $user = User::factory()->create();
        $org = Organisation::factory()->create();
        $org->users()->attach($user->id);

        $token = JWTAuth::fromUser($user);
        $response = $this->withHeaders(['Authorization' => "Bearer $token"])
        ->deleteJson('/api/v1/organisations/' . $org->org_id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('organisations', ['org_id' => $org->org_id]);
    }
}