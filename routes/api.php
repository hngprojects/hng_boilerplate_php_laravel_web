<?php

use App\Http\Controllers\Api\V1\User\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return 'api scaffold';
    });

    Route::apiResource('/users', UserController::class);
    Route::post('auth/register', [RegisterController::class, 'register']);
    Route::post('auth/login', [LoginController::class, 'login']);
});



Route::group([
    "middleware" => ["auth:api"]
], function(){
    // Route::get('v1/user-profile', [LoginController::class, 'profile']);
    Route::get('v1/auth/refresh', [LoginController::class, 'refreshToken']);
    Route::get('v1/auth/logout', [LoginController::class, 'logout']);
});
