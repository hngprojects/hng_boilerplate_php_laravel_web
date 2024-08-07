<?php

use App\Http\Controllers\Api\V1\Admin\BlogController;
use App\Http\Controllers\Api\V1\Admin\CustomerController;
use App\Http\Controllers\Api\V1\Admin\DashboardController;
use App\Http\Controllers\Api\V1\Admin\EmailTemplateController;
use App\Http\Controllers\Api\V1\Admin\Plan\FeatureController;
use App\Http\Controllers\Api\V1\Admin\Plan\SubscriptionController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\Admin\FaqController;
use App\Http\Controllers\NotificationSettingController;
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
use App\Http\Controllers\Api\V1\Auth\ForgotResetPasswordController;
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
use App\Http\Controllers\Api\V1\WaitListController;
use App\Http\Controllers\Api\V1\CookiePreferencesController;
use App\Http\Controllers\Api\V1\SqueezePageCoontroller;
use App\Http\Controllers\Api\V1\TimezoneController;

use App\Http\Controllers\QuestController;
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
    Route::post('/', function (Request $request) {
        // dd($request);
        return 'language Learning Ai Game';
    });
    Route::post('/auth/register', [AuthController::class, 'store']);
    Route::post('/auth/login', [LoginController::class, 'login']);
    Route::post('/auth/logout', [LoginController::class, 'logout'])->middleware('auth:api');
    Route::post('/auth/password-reset-email', ForgetPasswordRequestController::class)->name('password.reset');
    Route::post('/auth/request-password-request/{token}', ResetUserPasswordController::class);
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/login-google', [SocialAuthController::class, 'redirectToGoogle']);
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
    Route::post('/auth/google/callback', [SocialAuthController::class, 'saveGoogleRequest']);
    /* Forget and Reset Password using OTP */
    Route::post('/auth/forgot-password', [ForgotResetPasswordController::class, 'forgetPassword']);
    Route::post('/auth/reset-forgot-password', [ForgotResetPasswordController::class, 'resetPassword']);
    Route::post('/auth/verify-otp', [ForgotResetPasswordController::class, 'verifyUserOTP']);

    Route::post('/roles', [RoleController::class, 'store']);

    Route::get('/auth/login-facebook', [SocialAuthController::class, 'loginUsingFacebook']);
    Route::get('/auth/facebook/callback', [SocialAuthController::class, 'callbackFromFacebook']);
    Route::post('/auth/facebook/callback', [SocialAuthController::class, 'saveFacebookRequest']);

    Route::apiResource('/users', UserController::class);

    //jobs
    Route::get('/jobs', [JobController::class, 'index']);
    Route::get('/jobs/search', [JobSearchController::class, 'search']);
    Route::get('/jobs/{id}', [JobController::class, 'show']);

    Route::get('/products/categories', [CategoryController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{product_id}', [ProductController::class, 'show']);
    Route::get('/billing-plans', [BillingPlanController::class, 'index']);
    Route::get('/billing-plans/{id}', [BillingPlanController::class, 'getBillingPlan']);

    Route::middleware('throttle:10,1')->get('/topics/search', [ArticleController::class, 'search']);

    Route::middleware('auth:api')->group(function () {

        Route::post('/organizations/{org_id}/products', [ProductController::class, 'store']);
        Route::patch('/organizations/{org_id}/products/{product_id}', [ProductController::class, 'update']);
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
    Route::post('/inquiries', [ContactController::class, 'sendInquiry']);
    Route::get('/inquiries', [ContactController::class, 'index']);

    Route::get('/blogs/latest', [BlogController::class, 'latest']);
    Route::get('/blogs/search', [BlogSearchController::class, 'search']);

    Route::post('/squeeze', [SqueezeController::class, 'store']);

    //Cookies Preference
    Route::post('/cookies/preferences', [CookiePreferencesController::class, 'update']);
    Route::get('/cookies/preferences', [CookiePreferencesController::class, 'getPreferences']);


    // Help Articles
    Route::post('/help-center/topics', [HelpArticleController::class, 'store']);
    Route::patch('/help-center/topics/{articleId}', [HelpArticleController::class, 'update']);
    Route::delete('/help-center/topics/{articleId}', [HelpArticleController::class, 'destroy']);
    Route::get('/help-center/topics', [HelpArticleController::class, 'getArticles']);
    Route::get('/help-center/topics/search', [HelpArticleController::class, 'search']);

    Route::middleware(['auth:api', 'admin'])->group(function () {
        Route::get('/email-templates', [EmailTemplateController::class, 'index']);
        Route::post('/email-templates', [EmailTemplateController::class, 'store']);
        Route::patch('/email-templates/{id}', [EmailTemplateController::class, 'update']);
        Route::delete('/email-templates/{id}', [EmailTemplateController::class, 'destroy']);
    });


    Route::post('/invitations/generate', [InvitationAcceptanceController::class, 'generateInvitation']);
    Route::get('/invite/accept', [InvitationAcceptanceController::class, 'acceptInvitation']);
    Route::post('/invite', [InvitationAcceptanceController::class, 'acceptInvitationPost']);


    Route::middleware('auth:api')->group(function () {

        // Subscriptions, Plans and Features
        Route::apiResource('/features', FeatureController::class);
        Route::apiResource('/plans', SubscriptionController::class);
        Route::post('/payments/paystack', [PaymentController::class, 'initiatePaymentForPayStack']);
        Route::get('/payments/paystack/{organisation_id}/verify/{id}', [PaymentController::class, 'handlePaystackCallback']);
        Route::post('/payments/flutterwave', [PaymentController::class, 'initiatePaymentForFlutterWave']);
        Route::get('/payments/flutterwave/{organisation_id}/verify/{id}', [PaymentController::class, 'handleFlutterwaveCallback']);
        Route::get('/payments/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        Route::post('/users/plans/{user_subscription}/cancel', [\App\Http\Controllers\Api\V1\User\SubscriptionController::class, 'destroy']);
        Route::get('/users/plan', [\App\Http\Controllers\Api\V1\User\SubscriptionController::class, 'userPlan']);


        // Organisations
        Route::post('/organizations', [OrganisationController::class, 'store']);
        Route::get('/organizations', [OrganisationController::class, 'index']);
        Route::put('/organizations/{org_id}', [OrganisationController::class, 'update']);
        Route::delete('/organizations/{org_id}', [OrganisationController::class, 'destroy']);
        Route::delete('/organizations/{org_id}/users/{user_id}', [OrganisationController::class, 'removeUser']);
        Route::get('/organizations/{organisation}/users', [OrganizationMemberController::class, 'index']);

        // members
        Route::get('/members/{org_id}/search', [OrganizationMemberController::class, 'searchMembers']);
        Route::get('/members/{org_id}/export', [OrganizationMemberController::class, 'download']);

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
        Route::put('/organisations/{org_id}/users/{user_id}/roles', [RoleController::class, 'assignRole']);
        Route::put('/organisations/{org_id}/{role_id}/permissions', [RoleController::class, 'assignPermissions']);

        //Update Password
        Route::post('/password-update', [ProfileController::class, 'updatePassword']);
        //profile Update
        Route::patch('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/upload-image', [ProfileController::class, 'uploadImage']);

        Route::get('/notification-settings', [NotificationSettingController::class, 'show']);
        Route::patch('/notification-settings', [NotificationSettingController::class, 'update']);
    });
    Route::get('/notification-settings', [NotificationSettingController::class, 'show']);

    Route::middleware(['auth:api', 'admin'])->get('/customers', [CustomerController::class, 'index']);

    //Blogs
    Route::group(['middleware' => ['auth.jwt', 'admin']], function () {
        Route::post('/blogs', [BlogController::class, 'store']);
        Route::patch('/blogs/edit/{id}', [BlogController::class, 'update'])->name('admin.blogs.update');
        Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);
        Route::get('/waitlists', [WaitListController::class, 'index']);
        Route::apiResource('squeeze-pages', SqueezePageCoontroller::class);
        Route::get('/dashboard-cards', [DashboardController::class, 'index']);
    });

    Route::post('/waitlists', [WaitListController::class, 'store']);
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
    Route::post('/notifications', [UserNotificationController::class, 'create'])->middleware('auth.jwt');
    Route::get('/notifications', [UserNotificationController::class, 'getByUser'])->middleware('auth.jwt');
    //Timezone
    Route::get('/timezones', [TimezoneController::class, 'index']);



//    quest
    Route::get('/quests/{id}/messages', [QuestController::class, 'getQuestMessages']);

});
