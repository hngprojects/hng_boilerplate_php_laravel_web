<?php

namespace App\Http\Controllers\Prometheus;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;
use Prometheus\Storage\InMemory;

class MetricsController extends Controller
{
    protected $registry;

 

    public function metrics(CollectorRegistry $registry)
    {


        $renderer = new RenderTextFormat();
        $metrics = $renderer->render($registry->getMetricFamilySamples());

        return response($metrics, 200, ['Content-Type' => RenderTextFormat::MIME_TYPE]);
    }
}
