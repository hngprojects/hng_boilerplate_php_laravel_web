<?php

use App\Http\Controllers\Prometheus\MetricsController;
use App\Http\Middleware\Prometheus\AllowLocalhostOnly;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route to get prometheus metrics
Route::middleware([AllowLocalhostOnly::class])->group(function () {
    Route::get('/prometheus/metrics/' . env('PROMETHEUS_SECRET', 'tzUDnGrzj5j3vp0HSz0HKj3LNrYf1cj9'), [MetricsController::class, 'metrics']);
});

