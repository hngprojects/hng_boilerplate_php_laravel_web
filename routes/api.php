<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\NotificationSettingController;
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
    Route::post('/auth/register', [AuthController::class, 'store']);


    Route::apiResource('/users', UserController::class);

    Route::get('/products/categories', [CategoryController::class, 'index']);
      
    Route::middleware('throttle:10,1')->get('/help-center/topics/search', [ArticleController::class, 'search']);
   
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('notification-settings/{user_id}', [NotificationSettingController::class, 'update']);
    });
});


