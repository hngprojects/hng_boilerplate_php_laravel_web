<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;
use Throwable;

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
                // Optionally log the error
                logger()->error($e);

                throw $e;
            }
        });
    }
}
