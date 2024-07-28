<?php

namespace App\Http\Middleware\Prometheus;

use Closure;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class PrometheusMetricsMiddleware
{
    protected $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function handle($request, Closure $next)
    {
        if (!defined('LARAVEL_START')) {
            define('LARAVEL_START', microtime(true));
        }

        $response = $next($request);
        
        // Increment a counter for each request
        $counter = $this->registry->getOrRegisterCounter(
            'app',
            'requests_total',
            'Total number of requests',
            ['method', 'endpoint']
        );
        $counter->inc([$request->method(), $request->path()]);

        // Record request duration
        $histogram = $this->registry->getOrRegisterHistogram(
            'app',
            'request_duration_seconds',
            'Request duration in seconds',
            ['method', 'endpoint']
        );
        $histogram->observe(microtime(true) - LARAVEL_START, [$request->method(), $request->path()]);
        
        return $response;
    }
}
