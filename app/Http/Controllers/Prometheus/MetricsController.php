<?php

namespace App\Http\Controllers\Prometheus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class MetricsController extends Controller
{
    protected $registry;

 

    public function metrics()
    {

        if (env('APP_ENV') == 'production') {
            $registry=App::make(CollectorRegistry::class);
        $renderer = new RenderTextFormat();
        $metrics = $renderer->render($registry->getMetricFamilySamples());

        return response($metrics, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
        }
    }
}
