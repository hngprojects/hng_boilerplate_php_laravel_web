<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Prometheus\Storage\Redis;
use Prometheus\Storage\InMemory;
use Prometheus\CollectorRegistry;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

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

            if (env('APP_ENV') == 'production') {

                $request = request(); // Get the current request instance
                $registry = App::make(CollectorRegistry::class);
                $counter = $registry->getOrRegisterCounter(
                    'app',
                    'errors_total',
                    'Total number of errors',
                    ['type', 'endpoint','message']
                );
                $counter->inc([get_class($e), $request->path(),$e->getMessage()]);

            }
        });


        $this->renderable(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $method = $request->method();
                return response()->json([
                    "error" => "Bad request",
                    "message" => "$method Method not allowed"
                ], 405);
            }
        });
        $this->renderable(function (MethodNotAllowedException $e, Request $request) {
          if ($request->is('api/*')) {
              $method = $request->method();
              return response()->json([
                  "error" => "Bad request",
                  "message" => "$method Method not allowed"
              ], 405);
          }
        });
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
          if ($request->is('api/*')) {
              return response()->json([
                  "error" => "Not found",
                  "message" => "Route not found"
              ], 404);
          }
        });
        $this->renderable(function (AuthenticationException $e, Request $request) {
          if ($request->is('api/*')) {
              return response()->json([
                  "error" => "Unauthorized",
                  'message' => $e->getMessage(),
              ], 401);
          }
        });
    }
}