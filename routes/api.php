<?php

use App\Http\Controllers\Api\V1\Admin\BlogCategoriesController;
use App\Http\Controllers\Api\V1\Admin\BlogController;
use App\Http\Controllers\Api\V1\Admin\CustomerController;
use App\Http\Controllers\Api\V1\Admin\EmailTemplateController;
use App\Http\Controllers\Api\V1\Admin\Plan\FeatureController;
use App\Http\Controllers\Api\V1\Admin\Plan\SubscriptionController;
use App\Http\Controllers\Api\V1\Admin\FaqController;
use App\Http\Controllers\Api\V1\Admin\ProductsController;
use App\Http\Controllers\UserNotificationController;
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

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return 'language Learning Ai Game';
    });

    // Auth routes
    Route::post('/auth/register', [AuthController::class, 'store']);
    Route::post('/auth/login', [LoginController::class, 'login']);
    Route::post('/auth/logout', [LoginController::class, 'logout'])->middleware('auth:api');
    Route::post('/auth/password-reset-email', ForgetPasswordRequestController::class)->name('password.reset');
    Route::post('/auth/request-password-request/{token}', ResetUserPasswordController::class);
    
    // Social auth routes
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/login-google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    Route::post('/auth/google/callback', [SocialAuthController::class, 'saveGoogleRequest']);
    Route::get('/auth/login-facebook', [SocialAuthController::class, 'loginUsingFacebook']);
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'callbackFromFacebook']);
    Route::post('/auth/facebook/callback', [SocialAuthController::class, 'saveFacebookRequest']);

    // User routes
    Route::apiResource('/users', UserController::class);

    // Job routes
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/search', [JobSearchController::class, 'search']);
    Route::get('/jobs/{id}', [JobController::class, 'show']);

    // Product and category routes
    Route::get('/products/categories', [CategoryController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    
    // Billing plan routes
    Route::get('/billing-plans', [BillingPlanController::class, 'index']);
    Route::get('/billing-plans/{id}', [BillingPlanController::class, 'getBillingPlan']);

    // Article search route
    Route::middleware('throttle:10,1')->get('/topics/search', [ArticleController::class, 'search']);

    // Authenticated routes
    Route::middleware('auth:api')->group(function () {
        // Product routes
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/organizations/{org_id}/products', [ProductController::class, 'store']);
        Route::patch('/organizations/{org_id}/products/{product_id}', [ProductController::class, 'update']);
        Route::delete('/products/{productId}', [ProductController::class, 'destroy']);
        Route::get('/products/{product_id}', [ProductController::class, 'show']);

        // Comment routes
        Route::post('/blogs/{blogId}/comments', [CommentController::class, 'createComment']);
        Route::post('/comments/{commentId}/reply', [CommentController::class, 'replyComment']);
        Route::post('/comments/{commentId}/like', [CommentController::class, 'likeComment']);
        Route::post('/comments/{commentId}/dislike', [CommentController::class, 'dislikeComment']);
        Route::patch('/comments/edit/{commentId}', [CommentController::class, 'editComment']);
        Route::delete('/comments/{commentId}', [CommentController::class, 'deleteComment']);
        Route::get('/blogs/{blogId}/comments', [CommentController::class, 'getBlogComments']);

        // Subscription, Plan and Feature routes
        Route::apiResource('/features', FeatureController::class);
        Route::apiResource('/plans', SubscriptionController::class);
        Route::post('/users/plans/{user_subscription}/cancel', [\App\Http\Controllers\Api\V1\User\SubscriptionController::class, 'destroy']);

        // Organization routes
        Route::post('/organizations', [OrganisationController::class, 'store']);
        Route::get('/organizations', [OrganisationController::class, 'index']);
        Route::put('/organizations/{org_id}', [OrganisationController::class, 'update']);
        Route::delete('/organizations/{org_id}', [OrganisationController::class, 'destroy']);
        Route::delete('/organizations/{org_id}/users/{user_id}', [OrganisationController::class, 'removeUser']);
        Route::get('/organizations/{organisation}/users', [OrganizationMemberController::class, 'index']);

        // Member routes
        Route::get('/members/{org_id}/search', [OrganizationMemberController::class, 'searchMembers']);
        Route::get('/members/{org_id}/export', [OrganizationMemberController::class, 'download']);

        // Testimonial routes
        Route::post('/testimonials', [TestimonialController::class, 'store']);
        Route::get('/testimonials/{testimonial_id}', [TestimonialController::class, 'show']);
        Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy']);

        // Job routes
        Route::post('/jobs', [JobController::class, 'store']);
        Route::put('/jobs/{id}', [JobController::class, 'update']);
        Route::delete('/jobs/{id}', [JobController::class, 'destroy']);

        // User export route
        Route::get('/user/export/{format}', [ExportUserController::class, 'export']);

        // Account routes
        Route::patch('/accounts/deactivate', [AccountController::class, 'deactivate']);

        // Role routes
        Route::put('/organisations/{org_id}/roles/{role_id}', [RoleController::class, 'update']);
        Route::put('/organisations/{org_id}/roles/{role_id}/disable', [RoleController::class, 'disableRole']);
        Route::put('/organisations/{org_id}/users/{user_id}/roles', [RoleController::class, 'assignRole']);
        Route::put('/organisations/{org_id}/{role_id}/permissions', [RoleController::class, 'assignPermissions']);

        // Profile routes
        Route::post('/password-update', [ProfileController::class, 'updatePassword']);
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage']);

        // Preference routes
        Route::post('/user/preferences', [PreferenceController::class, 'store']);
        Route::put('/user/preferences/{id}', [PreferenceController::class, 'update']);
        Route::get('/user/preferences', [PreferenceController::class, 'index']);
        Route::delete('/user/preferences/{id}', [PreferenceController::class, 'delete']);

        // Notification routes
        Route::patch('/notifications/{notification}', [UserNotificationController::class, 'update']);
        Route::delete('/notifications', [UserNotificationController::class, 'destroy']);
        Route::post('/notifications', [UserNotificationController::class, 'create']);
        Route::get('/notifications', [UserNotificationController::class, 'getByUser']);
    });

    // Admin routes
    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::get('/customers', [CustomerController::class, 'index']);
        Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
        Route::post('/blogs/categories', [BlogCategoriesController::class, 'store'])->name('admin.blog-category.create');

        // Admin product routes
        Route::get('/products', [ProductsController::class, 'index']);
        Route::post('/products', [ProductsController::class, 'store']);
        Route::get('/products/{id}', [ProductsController::class, 'show']);
        Route::put('/products/{productId}', [ProductsController::class, 'update']);
        Route::delete('/products/{productId}', [ProductsController::class, 'destroy']);
        Route::get('/products/{product_id}/edit', [ProductsController::class, 'edit']);
        Route::get('/products/stats/total-revenue', [ProductsController::class, 'totalRevenue']);
        Route::get('/products/stats/total-price', [ProductsController::class, 'totalPrice']);

        // Email template routes
        Route::apiResource('email-templates', EmailTemplateController::class);
    });

    // Public routes
    Route::post('/contact', [ContactController::class, 'sendInquiry']);
    Route::get('/blogs/latest', [BlogController::class, 'latest']);
    Route::get('/blogs/search', [BlogSearchController::class, 'search']);
    Route::post('/squeeze', [SqueezeController::class, 'store']);
    Route::get('/blogs/{id}', [BlogController::class, 'show']);
    Route::get('/blogs', [BlogController::class, 'index']);

    // Help center routes
    Route::post('/help-center/topics', [HelpArticleController::class, 'store']);
    Route::patch('/help-center/topics/{articleId}', [HelpArticleController::class, 'update']);
    Route::delete('/help-center/topics/{articleId}', [HelpArticleController::class, 'destroy']);
    Route::get('/help-center/topics', [HelpArticleController::class, 'getArticles']);
    Route::get('/help-center/topics/search', [HelpArticleController::class, 'search']);

    // Invitation routes
    Route::post('/invitations/generate', [InvitationAcceptanceController::class, 'generateInvitation']);
    Route::get('/invite/accept', [InvitationAcceptanceController::class, 'acceptInvitation']);
    Route::post('/invite', [InvitationAcceptanceController::class, 'acceptInvitationPost']);

    // FAQ routes
    Route::apiResource('faqs', FaqController::class);

    // Notification settings route
    Route::patch('/notification-settings/{user_id}', [NotificationPreferenceController::class, 'update']);
});