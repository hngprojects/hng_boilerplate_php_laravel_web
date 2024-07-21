<?php

use App\Http\Controllers\Api\V1\Admin\BlogController;
use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ArticleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return 'api scaffold';
    });

    Route::apiResource('/users', UserController::class);      
    Route::middleware('throttle:10,1')->get('/topics/search', [ArticleController::class, 'search']);

    Route::middleware('auth:api')->group(function (){
        Route::apiResource('/blogs', BlogController::class);
    });

});
