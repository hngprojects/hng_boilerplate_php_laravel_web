<?php

namespace Tests\Unit\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Api\V1\Auth\LoginController;
use Illuminate\Http\JsonResponse;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class LogoutTest extends TestCase
{
    protected $loginController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginController = new LoginController();
    }

    public function testSuccessfulLogout()
    {
        JWTAuth::shouldReceive('parseToken->invalidate')
            ->once()
            ->with(true)
            ->andReturn(true);

        $response = $this->loginController->logout();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Logout successful', json_decode($response->getContent())->message);
    }

    public function testLogoutWithExpiredToken()
    {
        JWTAuth::shouldReceive('parseToken->invalidate')
            ->once()
            ->with(true)
            ->andThrow(new TokenExpiredException('Token has expired'));

        $response = $this->loginController->logout();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Token has expired', json_decode($response->getContent())->message);
        $this->assertEquals('token_expired', json_decode($response->getContent())->error);
    }

    public function testLogoutWithInvalidToken()
    {
        JWTAuth::shouldReceive('parseToken->invalidate')
            ->once()
            ->with(true)
            ->andThrow(new TokenInvalidException('Token is invalid'));

        $response = $this->loginController->logout();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Token is invalid', json_decode($response->getContent())->message);
        $this->assertEquals('token_invalid', json_decode($response->getContent())->error);
    }

    public function testLogoutWithMissingToken()
    {
        JWTAuth::shouldReceive('parseToken->invalidate')
            ->once()
            ->with(true)
            ->andThrow(new JWTException('Token is missing'));

        $response = $this->loginController->logout();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Token is missing', json_decode($response->getContent())->message);
        $this->assertEquals('token_absent', json_decode($response->getContent())->error);
    }
}
