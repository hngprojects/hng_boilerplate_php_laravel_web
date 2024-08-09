<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\V1\NewsletterSubscriptionController as V1NewsletterSubscriptionController;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use App\Http\Controllers\NewsletterSubscriptionController;
use App\Models\NewsletterSubscription;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Validation\Validator;
use Mockery;
use Exception;

class NewsletterSubscriptionCreateTest extends TestCase
{
    
    use RefreshDatabase;

    public function test_store_method_validates_email()
    {
        // Mock the Validator
        $validatorMock = Mockery::mock(Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(true);
        $validatorMock->shouldReceive('errors')->andReturn(collect(['email' => 'Invalid email']));

        $validationFactoryMock = Mockery::mock(ValidationFactory::class);
        $validationFactoryMock->shouldReceive('make')->andReturn($validatorMock);

        // Create the Request
        $request = Request::create('/api/v1/newsletter-subscription', 'POST', [
            'email' => 'invalid-email'
        ]);

        // Create the Controller and Inject the Mock
        $controller = new V1NewsletterSubscriptionController($validationFactoryMock);

        // Call the store method
        $response = $controller->store($request);

        // Assertions
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Validation failed.', $response->getContent());
    }

    public function test_store_method_creates_newsletter_subscription()
    {
        // Mock the Validator
        $validatorMock = Mockery::mock(Validator::class);
        $validatorMock->shouldReceive('fails')->andReturn(false);

        $validationFactoryMock = Mockery::mock(ValidationFactory::class);
        $validationFactoryMock->shouldReceive('make')->andReturn($validatorMock);

        // Create the Request
        $request = Request::create('/api/v1/newsletter-subscription', 'POST', [
            'email' => 'test@example.com'
        ]);

        // Create the Controller and Inject the Mock
        $controller = new V1NewsletterSubscriptionController($validationFactoryMock);

        // Call the store method
        $response = $controller->store($request);

        // Assertions
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('test@example.com', $response->getContent());

        $this->assertDatabaseHas('newsletter_subscriptions', [
            'email' => 'test@example.com'
        ]);
    }
}
