<?php

use App\Http\Controllers\Api\V1\Admin\Plan\FeatureController;
use App\Http\Controllers\Api\V1\Admin\Plan\SubscriptionController;
use App\Http\Controllers\Api\V1\Admin\BlogController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\RoleController;

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
    
    Route::post('/roles', [RoleController::class, 'store']);

    Route::apiResource('/users', UserController::class);

    Route::get('/products/categories', [CategoryController::class, 'index']);

    Route::middleware('throttle:10,1')->get('/help-center/topics/search', [ArticleController::class, 'search']);
    
    Route::middleware('auth:api')->group(function () {
        Route::post('/password-update', [UserController::class, 'updatePassword']);
        Route::get('/password-update', function () {
            return 'You can proceed';
        });
    });

    Route::get('/blogs/latest', [BlogController::class, 'latest']);

    Route::middleware('auth:api')->group(function () {
        Route::apiResource('/features', FeatureController::class);
        Route::apiResource('/plans', SubscriptionController::class);
    });
});
