<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomePageController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\CalculatorController;
use App\Http\Controllers\Api\PublicLawController;
use App\Http\Controllers\Api\PublicTemplateController;
use App\Http\Controllers\Api\PmDashboardController;
use App\Http\Controllers\Api\UserLeadController;
use App\Http\Controllers\Api\Auth\Owner\RegisterController as OwnerRegisterController;
use App\Http\Controllers\Api\Auth\Owner\LoginController as OwnerLoginController;
use App\Http\Controllers\Api\OwnerDashboardController;
use App\Http\Controllers\Api\OwnerBookmarkController;
use App\Http\Controllers\Api\SavedCalculationController;
use App\Http\Controllers\Api\PropertyManagerController;
use App\Http\Controllers\Api\V2;
use App\Http\Controllers\Admin;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::prefix('api')->group(function () {
	// Location/State endpoints (Next.js API requirements)
	Route::get('/states', [LocationController::class, 'getStates']);
	Route::get('/states/{stateCode}/cities', [LocationController::class, 'getCitiesByState']);

	// Testimonials endpoint (Next.js API requirements)
	Route::get('/testimonials/published', [TestimonialController::class, 'published']);

	// Blog endpoints (Next.js API requirements)
	Route::get('/blogs/latest', [BlogController::class, 'latest']);
	Route::get('/blogs/search', [BlogController::class, 'search']);
	Route::get('/blogs/{slug}', [BlogController::class, 'show']);
	Route::get('/blogs', [BlogController::class, 'index']);

	// Lead submission endpoint (Next.js API requirements)
	Route::post('/home-page-lead', [LeadController::class, 'store']);

	// Question endpoints for multi-step form (Next.js API requirements)
	Route::get('/questions', [QuestionController::class, 'index']);
	Route::get('/questions/step/{step}', [QuestionController::class, 'getByStep']);

	// Calculator endpoints (Next.js API requirements)
	Route::post('/calculator-logs', [CalculatorController::class, 'logUsage']);
	Route::get('/calculator-stats', [CalculatorController::class, 'getStatistics']);
    Route::post('/calculator/roi', [CalculatorController::class, 'calculateRoi']);
    Route::post('/calculator/mortgage', [CalculatorController::class, 'calculateMortgage']);
    Route::post('/calculator/rent-vs-buy', [CalculatorController::class, 'calculateRentVsBuy']);

    // Public Law endpoints
    Route::get('/laws', [PublicLawController::class, 'index']);
    Route::get('/laws/{state_slug}', [PublicLawController::class, 'show']);

    // Public Template endpoints
    Route::get('/templates', [PublicTemplateController::class, 'index']);
    Route::get('/templates/{template}/download', [PublicTemplateController::class, 'download']);

    // Property Manager endpoints (Next.js API requirements)
    Route::get('/property-managers', [PropertyManagerController::class, 'index']);
    Route::get('/property-managers/{id}', [PropertyManagerController::class, 'getById']);
    Route::get('/property-managers/{state}/{city}/{slug}', [PropertyManagerController::class, 'show']);

    // PM Dashboard endpoint
    Route::middleware(['auth:sanctum', 'is_pm'])->group(function () {
        Route::get('/pm/dashboard', [PmDashboardController::class, 'dashboard']);
        Route::put('/user-leads/{userLead}', [UserLeadController::class, 'updateStatus']);
        Route::put('/user-leads/{userLead}/notes', [UserLeadController::class, 'updateNotes']);

        // V1 Preferences Management
        Route::get('/v1/preferences', [\App\Http\Controllers\Api\V1\PreferencesController::class, 'show']);
        Route::put('/v1/preferences', [\App\Http\Controllers\Api\V1\PreferencesController::class, 'update']);

        // V1 AI Lead Scoring & Market Insights
        Route::get('/v1/leads/scores', [\App\Http\Controllers\Api\V1\LeadScoringController::class, 'getMyLeadScores']);
        Route::get('/v1/leads/{leadId}/score', [\App\Http\Controllers\Api\V1\LeadScoringController::class, 'getLeadScore']);
        Route::get('/v1/market-insights', [\App\Http\Controllers\Api\V1\LeadScoringController::class, 'getMarketInsights']);
    });

    // Owner Authentication endpoints
    Route::post('/owner/register', [OwnerRegisterController::class, 'register']);
    Route::post('/owner/login', [OwnerLoginController::class, 'login']);

    // Owner Dashboard endpoints (protected)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/owner/logout', [OwnerLoginController::class, 'logout']);
        Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index']);

        // Owner Bookmarks
        Route::get('/owner/bookmarks', [OwnerBookmarkController::class, 'index']);
        Route::post('/owner/bookmarks', [OwnerBookmarkController::class, 'store']);
        Route::delete('/owner/bookmarks/{bookmark}', [OwnerBookmarkController::class, 'destroy']);

        // Saved Calculations
        Route::get('/owner/calculations', [SavedCalculationController::class, 'index']);
        Route::post('/owner/calculations', [SavedCalculationController::class, 'store']);
        Route::delete('/owner/calculations/{calculation}', [SavedCalculationController::class, 'destroy']);
    });

    // API V2 Routes
    Route::prefix('v2')->group(function () {
        // Legal Content
        Route::get('/legal/states', [V2\LegalContentController::class, 'getStates']);
        Route::get('/legal/topics', [V2\LegalContentController::class, 'getTopics']);
        Route::get('/legal/states/{state}/laws', [V2\LegalContentController::class, 'getStateLaws']);
        Route::get('/legal/states/{state}/laws/{topic}', [V2\LegalContentController::class, 'getStateLawByTopic']);
        Route::get('/legal/search', [V2\LegalContentController::class, 'search']);

        // Document Templates
        Route::get('/templates/categories', [V2\DocumentTemplateController::class, 'getCategories']);
        Route::get('/templates', [V2\DocumentTemplateController::class, 'getTemplates']);
        Route::get('/templates/{id}', [V2\DocumentTemplateController::class, 'getTemplate']);
        Route::post('/templates/{id}/download', [V2\DocumentTemplateController::class, 'download'])
            ->middleware('check.template.access');

        // Lead Distribution
        Route::get('/leads/{id}/matches', [V2\LeadDistributionController::class, 'findMatches']);
        Route::post('/leads/{id}/distribute', [V2\LeadDistributionController::class, 'distribute']);
        Route::post('/leads/{id}/redistribute', [V2\LeadDistributionController::class, 'redistribute']);
        Route::get('/leads/{id}/distribution-stats', [V2\LeadDistributionController::class, 'getStats']);

        // Property Manager Leads
        Route::get('/property-managers/{pmId}/leads', [V2\LeadDistributionController::class, 'getPropertyManagerLeads']);
        Route::post('/property-managers/{pmId}/leads/{leadId}/view', [V2\LeadDistributionController::class, 'markAsViewed']);

        // Leads (Protected - requires auth)
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/leads', function () {
                return response()->json([
                    'data' => \App\Models\Lead::orderBy('created_at', 'desc')->limit(50)->get()
                ]);
            });
        });

        // Lead Responses (Protected - requires auth)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/leads/{leadId}/responses', [V2\LeadResponseController::class, 'submitResponse']);
            Route::get('/leads/{leadId}/responses', [V2\LeadResponseController::class, 'getResponses']);
        });

        // Authentication
        Route::post('/auth/register', [V2\AuthController::class, 'register']);
        Route::post('/auth/login', [V2\AuthController::class, 'login']);
        Route::post('/auth/logout', [V2\AuthController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/auth/user', [V2\AuthController::class, 'user'])->middleware('auth:sanctum');
        Route::post('/auth/forgot-password', [V2\AuthController::class, 'forgotPassword']);
        Route::post('/auth/reset-password', [V2\AuthController::class, 'resetPassword']);
        Route::get('/auth/verify-email/{id}/{hash}', [V2\AuthController::class, 'verifyEmail'])->name('verification.verify');
        Route::post('/auth/resend-verification', [V2\AuthController::class, 'resendVerificationEmail'])->middleware('auth:sanctum');
        // Test-only endpoint for email verification by email address
        Route::post('/auth/test/verify-email', [V2\AuthController::class, 'verifyEmailByEmail']);
    });

    // Admin Routes (protected with auth:sanctum and is_admin middleware)
    Route::prefix('admin')->middleware(['auth:sanctum', 'is_admin'])->group(function () {
        // Admin Dashboard
        Route::get('/dashboard', [\App\Http\Controllers\Api\AdminDashboardController::class, 'dashboard']);
        Route::post('/dashboard/clear-cache', [\App\Http\Controllers\Api\AdminDashboardController::class, 'clearCache']);

        // Admin Analytics
        Route::prefix('analytics')->group(function () {
            Route::get('/leads', [\App\Http\Controllers\Api\AdminAnalyticsController::class, 'leadAnalytics']);
            Route::get('/property-managers', [\App\Http\Controllers\Api\AdminAnalyticsController::class, 'propertyManagerAnalytics']);
            Route::get('/templates', [\App\Http\Controllers\Api\AdminAnalyticsController::class, 'templateAnalytics']);
            Route::get('/calculators', [\App\Http\Controllers\Api\AdminAnalyticsController::class, 'calculatorAnalytics']);
            Route::get('/revenue', [\App\Http\Controllers\Api\AdminAnalyticsController::class, 'revenueAnalytics']);
            Route::get('/export', [\App\Http\Controllers\Api\AdminAnalyticsController::class, 'exportAnalytics']);
        });

        // User Management
        Route::prefix('users')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'store']);
            Route::get('/{id}', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'show']);
            Route::put('/{id}', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'destroy']);
            Route::post('/{id}/verify', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'verify']);
            Route::post('/{id}/assign-role', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'assignRole']);
            Route::post('/{id}/impersonate', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'impersonate']);
            Route::post('/bulk-update', [\App\Http\Controllers\Api\AdminUserManagementController::class, 'bulkUpdate']);
        });

        // Role Management
        Route::prefix('roles')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminRoleManagementController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\AdminRoleManagementController::class, 'store']);
            Route::get('/{id}', [\App\Http\Controllers\Api\AdminRoleManagementController::class, 'show']);
            Route::put('/{id}', [\App\Http\Controllers\Api\AdminRoleManagementController::class, 'update']);
            Route::delete('/{id}', [\App\Http\Controllers\Api\AdminRoleManagementController::class, 'destroy']);
            Route::post('/{id}/clone', [\App\Http\Controllers\Api\AdminRoleManagementController::class, 'clone']);
        });

        // Permission Management
        Route::get('/permissions/available', [\App\Http\Controllers\Api\AdminRoleManagementController::class, 'getAvailablePermissions']);

        // Resource Management
        Route::apiResource('legal-topics', Admin\LegalTopicController::class);
        Route::apiResource('state-profiles', Admin\StateProfileController::class);
        Route::apiResource('state-law-contents', Admin\StateLawContentController::class);
        Route::apiResource('document-categories', Admin\DocumentCategoryController::class);
    });

	// Legacy endpoints (keep for Angular compatibility)
	Route::post('/lead/save',[HomePageController::class, 'leadSave']);
	Route::post('/agent/save',[HomePageController::class, 'agentSave']);
	Route::post('/newsletter/save',[HomePageController::class, 'newsletterSave']);
	Route::post('/blog-category/list',[HomePageController::class, 'blogCategoryList']);
	Route::post('/blogs/list',[HomePageController::class, 'blogList']);
	Route::post('/blog/get',[HomePageController::class, 'blogGet']);
	Route::post('/testimonial/list',[HomePageController::class, 'testimonialList']);
	Route::post('/state/code/list',[HomePageController::class, 'stateCodeList']);
	Route::post('/city/list',[HomePageController::class, 'cityList']);
	Route::post('/faq/list',[HomePageController::class, 'faqList']);
	Route::post('/page/get',[HomePageController::class, 'getPageContent']);
	Route::post('/agents/list',[HomePageController::class, 'getAgentList']);
	Route::post('/agent/get',[HomePageController::class, 'getAgent']);
	Route::post('/contact/save',[HomePageController::class, 'contactSave']);
	Route::post('/question/list',[HomePageController::class, 'questionList']);
	Route::post('/law/get',[HomePageController::class, 'getLaw']);
	Route::any('/testimonial/get',[HomePageController::class, 'getTestimonial']);
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});