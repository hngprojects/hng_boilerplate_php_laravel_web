<?php

use App\Http\Controllers\Api\V1\Admin\BlogCategoriesController;
use App\Http\Controllers\Api\V1\Admin\BlogController;
use App\Http\Controllers\Api\V1\Admin\CustomerController;
use App\Http\Controllers\Api\V1\Admin\EmailTemplateController;
use App\Http\Controllers\Api\V1\Admin\Plan\FeatureController;
use App\Http\Controllers\Api\V1\Admin\Plan\SubscriptionController;
use App\Http\Controllers\Api\V1\Admin\FaqController;
use App\Http\Controllers\UserNotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\ForgetPasswordRequestController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ResetUserPasswordController;
use App\Http\Controllers\Api\V1\Auth\SocialAuthController;
use App\Http\Controllers\Api\V1\BlogSearchController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\HelpArticleController;
use App\Http\Controllers\Api\V1\NotificationPreferenceController;
use App\Http\Controllers\Api\V1\Organisation\OrganisationController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\Organisation\OrganizationMemberController;
use App\Http\Controllers\Api\V1\PreferenceController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SqueezeController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Controllers\Api\V1\User\AccountController;
use App\Http\Controllers\InvitationAcceptanceController;
use App\Http\Controllers\Api\V1\User\ExportUserController;


use App\Http\Controllers\Api\V1\Testimonial\TestimonialController;
use App\Http\Controllers\BillingPlanController;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\JobSearchController;


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
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/login-google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

    Route::apiResource('/users', UserController::class);
    
    //jobs
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/search', [JobSearchController::class, 'search']);
    Route::get('/jobs/{id}', [JobController::class, 'show']);

    Route::get('/products/categories', [CategoryController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/billing-plans', [BillingPlanController::class, 'index']);
    Route::get('/billing-plans/{id}', [BillingPlanController::class, 'getBillingPlan']);


    Route::middleware('throttle:10,1')->get('/topics/search', [ArticleController::class, 'search']);


    Route::middleware('auth:api')->group(function () {
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::delete('/products/{productId}', [ProductController::class, 'destroy']);
    });




    //comment
    Route::middleware('auth:api')->group(function () {
        Route::post('/blogs/{blogId}/comments', [CommentController::class, 'createComment']);
        Route::post('/comments/{commentId}/reply', [CommentController::class, 'replyComment']);
        Route::post('/comments/{commentId}/like', [CommentController::class, 'likeComment']);
        Route::post('/comments/{commentId}/dislike', [CommentController::class, 'dislikeComment']);
        Route::patch('/comments/edit/{commentId}', [CommentController::class, 'editComment']);
        Route::delete('/comments/{commentId}', [CommentController::class, 'deleteComment']);
        Route::get('/blogs/{blogId}/comments', [CommentController::class, 'getBlogComments']);
    });

    Route::middleware('throttle:10,1')->get('/help-center/topics/search', [ArticleController::class, 'search']);
    Route::post('/contact', [ContactController::class, 'sendInquiry']);

    Route::get('/blog/latest', [BlogController::class, 'latest']);
    Route::get('/blog/search', [BlogSearchController::class, 'search']);

    Route::post('/squeeze', [SqueezeController::class, 'store']);



    // Help Articles
    Route::post('/help-center/topics', [HelpArticleController::class, 'store']);
    Route::patch('/help-center/topics/{articleId}', [HelpArticleController::class, 'update']);
    Route::delete('/help-center/topics/{articleId}', [HelpArticleController::class, 'destroy']);
    Route::get('/help-center/topics', [HelpArticleController::class, 'getArticles']);
    Route::get('/help-center/topics/search', [HelpArticleController::class, 'search']);

    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::get('/email-templates', [EmailTemplateController::class, 'index']);
        Route::patch('/email-templates/{id}', [EmailTemplateController::class, 'update']);
    });


    Route::post('/invitations/generate', [InvitationAcceptanceController::class, 'generateInvitation']);
    Route::get('/invite/accept', [InvitationAcceptanceController::class, 'acceptInvitation']);
    Route::post('/invite', [InvitationAcceptanceController::class, 'acceptInvitationPost']);


    Route::middleware('auth:api')->group(function () {

        // Subscriptions, Plans and Features
        Route::apiResource('/features', FeatureController::class);
        Route::apiResource('/plans', SubscriptionController::class);
        Route::post('/users/plans/{user_subscription}/cancel', [\App\Http\Controllers\Api\V1\User\SubscriptionController::class, 'destroy']);


        // Organisations
        Route::post('/organisations', [OrganisationController::class, 'store']);
        Route::get('/organisations', [OrganisationController::class, 'index']);
        Route::put('/organisations/{org_id}', [OrganisationController::class, 'update']);
        Route::delete('/organisations/{org_id}', [OrganisationController::class, 'destroy']);
        Route::delete('/organisations/{org_id}/users/{user_id}', [OrganisationController::class, 'removeUser']);
        Route::get('/organisations/{organisation}/members', [OrganizationMemberController::class, 'index']);

        // members
        Route::get('/members/{org_id}/search', [OrganizationMemberController::class, 'searchMembers']);

        Route::delete('/organizations/{org_id}', [OrganisationController::class, 'destroy']);

        // Testimonials
        Route::post('/testimonials', [TestimonialController::class, 'store']);
        Route::get('/testimonials/{testimonial_id}', [TestimonialController::class, 'show']);
        Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy']);

        // Jobs
        Route::post('/jobs', [JobController::class, 'store']);
        Route::put('/jobs/{id}', [JobController::class, 'update']);
        Route::delete('/jobs/{id}', [JobController::class, 'destroy']);



        Route::get('/user/export/{format}', [ExportUserController::class, 'export']);

        // Accounts
        Route::patch('/accounts/deactivate', [AccountController::class, 'deactivate']);

        // Roles
        Route::put('/organisations/{org_id}/roles/{role_id}', [RoleController::class, 'update']);
        Route::put('/organisations/{org_id}/roles/{role_id}/disable', [RoleController::class, 'disableRole']);



        //Update Password
        Route::post('/password-update', [ProfileController::class, 'updatePassword']);
        //profile Update
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage']);

    });

    Route::middleware(['auth:api', 'admin'])->get('/customers', [CustomerController::class, 'index']);

    //Blogs
    Route::group(['middleware' => ['auth.jwt', 'admin']], function () {
        Route::post('/blogs', [BlogController::class, 'store']);
        Route::patch('/blogs/edit/{id}', [BlogController::class, 'update'])->name('admin.blogs.update');
        Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
        Route::post('/blogs/categories', [BlogCategoriesController::class, 'store'])->name('admin.blog-category.create');

    });


    Route::apiResource('faqs', FaqController::class);


    Route::get('/blogs/{id}', [BlogController::class, 'show']);
    Route::get('/blogs', [BlogController::class, 'index']);

    Route::group(['middleware' => ['auth:api']], function () {
        Route::post('/user/preferences', [PreferenceController::class, 'store']);
        Route::put('/user/preferences/{id}', [PreferenceController::class, 'update']);
        Route::get('/user/preferences', [PreferenceController::class, 'index']);
        Route::delete('/user/preferences/{id}', [PreferenceController::class, 'delete']);

    });

    // Notification settings
    Route::patch('/notification-settings/{user_id}', [NotificationPreferenceController::class, 'update']);


    Route::middleware(['auth:api', 'admin'])->group(function () {
        //Email Template
        Route::apiResource('email-templates', EmailTemplateController::class);
    });
    // User Notification
    Route::patch('/notifications/{notification}', [UserNotificationController::class, 'update']);
    Route::delete('/notifications', [UserNotificationController::class, 'destroy']);

});


Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/user/preferences', [PreferenceController::class, 'store']);
    Route::put('/user/preferences/{id}', [PreferenceController::class, 'update']);
    Route::get('/user/preferences', [PreferenceController::class, 'index']);
    Route::delete('/user/preferences/{id}', [PreferenceController::class, 'delete']);
});
