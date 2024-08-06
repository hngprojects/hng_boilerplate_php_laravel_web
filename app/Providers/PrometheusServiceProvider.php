<?php

namespace App\Providers;

use App\Prometheus\Redis;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;


class PrometheusServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        if (env('APP_ENV') == 'production') {
            $this->app->singleton(CollectorRegistry::class, function () {
                $adapter =  new APC();
                return new CollectorRegistry($adapter);
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
