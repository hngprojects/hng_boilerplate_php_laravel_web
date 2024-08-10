<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\User;
use App\Models\Faq;

class FaqDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an admin can delete a FAQ.
     */
    public function test_if_admin_can_delete_faq()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);
        dd($admin);

        // Create a FAQ
        $faq = Faq::create([
            'question' => 'What is the safe policy?',
            'answer' => 'Our safe policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ]);

        // Act as the admin and make a DELETE request
        $response = $this->actingAs($admin)->delete(route('admin.faq.delete', $faq->id));
        dd($response);

        // Assert that the FAQ is deleted
        $response->assertStatus(Response::HTTP_OK)
                 ->assertJson([
                     'status_code' => 200,
                     'message' => 'FAQ successfully deleted.',
                 ]);

        $this->assertDatabaseMissing('faqs', ['id' => $faq->id]);
    }

    /**
     * Test that a non-admin user cannot delete a FAQ.
     */
    public function test_non_admin_cannot_delete_faq()
    {
        // Create a non-admin user
        $user = User::factory()->create(['role' => 'user']);

        // Create a FAQ
        $faq = Faq::create([
            'question' => 'What is the safe policy?',
            'answer' => 'Our safe policy allows returns within 30 days of purchase.',
            'category' => 'Policies'
        ]);

        // Act as the non-admin user and make a DELETE request
        $response = $this->actingAs($user)->delete(route('admin.faq.delete', $faq->id));

        // Assert that the request is forbidden
        $response->assertStatus(Response::HTTP_FORBIDDEN)
                 ->assertJson([
                     'status_code' => Response::HTTP_FORBIDDEN,
                     'message' => 'Only admin users can delete a faq',
                 ]);

        // Assert that the FAQ is still in the database
        $this->assertDatabaseHas('faqs', ['id' => $faq->id]);
    }

    /**
     * Test that trying to delete a non-existent FAQ returns a bad request.
     */
    public function test_delete_non_existent_faq()
    {
        // Create an admin user
        $admin = User::factory()->create(['role' => 'admin']);

        // Act as the admin and try to delete a non-existent FAQ
        $response = $this->actingAs($admin)->delete(route('admin.faq.delete', 9999)); // Assume 9999 doesn't exist

        // Assert that the request returns a bad request status
        $response->assertStatus(400)
                 ->assertJson([
                     'status_code' => 400,
                     'message' => 'Bad Request.',
                 ]);
    }
}
