<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // public function render($request, Throwable $exception)
    // {
    //     if ($exception instanceof AuthenticationException) {
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'status_code' => 401,
    //                 'error' => 'Could not validate user credentials',
    //                 'message' => 'Unauthenticated'
    //             ], 401);
    //         }
    //     }

    //     return parent::render($request, $exception);
    // }
}
