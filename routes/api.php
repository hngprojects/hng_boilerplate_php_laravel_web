<?php

use App\Http\Controllers\Api\V1\Auth\ResetUserPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\Organisation\OrganisationController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SqueezeController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\Admin\BlogController;
use App\Http\Controllers\Api\V1\Admin\CustomerController;
use App\Http\Controllers\Api\V1\Admin\Plan\FeatureController;
use App\Http\Controllers\Api\V1\Admin\Plan\SubscriptionController;
use App\Http\Controllers\Api\V1\Testimonial\TestimonialController;

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ForgetPasswordRequestController;

use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\BlogSearchController;
use App\Http\Controllers\Api\V1\User\ExportUserController;


use App\Http\Controllers\Api\V1\User\AccountController;

use App\Http\Controllers\Api\V1\Organisation\OrganizationMemberController;

use App\Http\Controllers\InvitationAcceptanceController;



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
        return 'language Learning Ai Game';
    });
    Route::post('/auth/register', [AuthController::class, 'store']);
    Route::post('/auth/login', [LoginController::class, 'login']);
    Route::post('/auth/logout', [LoginController::class, 'logout'])->middleware('auth:api');
    Route::post('/auth/password-reset-email', ForgetPasswordRequestController::class)->name('password.reset');
    Route::post('/auth/request-password-request/{token}', ResetUserPasswordController::class);
    Route::post('/roles', [RoleController::class, 'store']);

    Route::apiResource('/users', UserController::class);

    Route::get('/products/categories', [CategoryController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);

    Route::middleware('throttle:10,1')->get('/topics/search', [ArticleController::class, 'search']);


    Route::middleware('auth:api')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{productId}', [ProductController::class, 'destroy']);
    });

    Route::middleware('throttle:10,1')->get('/help-center/topics/search', [ArticleController::class, 'search']);
    Route::post('/contact', [ContactController::class, 'sendInquiry']);

    Route::get('/blogs/latest', [BlogController::class, 'latest']);
    Route::get('/blogs/search', [BlogSearchController::class, 'search']);

    Route::post('/squeeze', [SqueezeController::class, 'store']);


    Route::post('/invitations/generate', [InvitationAcceptanceController::class, 'generateInvitation']);
    Route::get('/invite/accept', [InvitationAcceptanceController::class, 'acceptInvitation']);
    Route::post('/invite', [InvitationAcceptanceController::class, 'acceptInvitationPost']);


    Route::middleware('auth:api')->group(function () {

        // Subscriptions, Plans and Features
        Route::apiResource('/features', FeatureController::class);
        Route::apiResource('/plans', SubscriptionController::class);
        // Organisations
        Route::post('/organisations', [OrganisationController::class, 'store']);
        Route::get('/organisations', [OrganisationController::class, 'index']);
        Route::delete('/organisations/{org_id}', [OrganisationController::class, 'destroy']);
        Route::delete('/organisations/{org_id}/users/{user_id}', [OrganisationController::class, 'removeUser']);
        Route::get('/organisations/{organisation}/members', [OrganizationMemberController::class, 'index']);

        Route::post('/blogs', [BlogController::class, 'store']);

        // Testimonials
        Route::post('/testimonials', [TestimonialController::class, 'store']);
        Route::get('/testimonials/{testimonial_id}', [TestimonialController::class, 'show']);
        Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy']);

        // Jobs
        Route::get('/jobs', [JobController::class, 'index']);

        Route::get('/user/export/{format}', [ExportUserController::class, 'export']);

        // Accounts
        Route::patch('/accounts/deactivate', [AccountController::class, 'deactivate']);

        // Roles
        Route::put('/organisations/{org_id/roles/{role_id}/disable', [RoleController::class, 'disableRole']);
    });

    Route::middleware(['auth:api', 'admin'])->get('/customers', [CustomerController::class, 'index']);
});
