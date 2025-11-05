<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\AjaxController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\AgentsController;
use App\Http\Controllers\Admin\CreditsController;
use App\Http\Controllers\Admin\LeadsController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\BlogsController;
use App\Http\Controllers\Admin\TestimonialsController;
use App\Http\Controllers\Admin\PreferencesController;
use App\Http\Controllers\Admin\InnerPagesController;
use App\Http\Controllers\Admin\FaqsController;
use App\Http\Controllers\Admin\StripePaymentController;
use App\Http\Controllers\Admin\TiersController;
use App\Http\Controllers\Admin\QuestionsController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\NewsletterController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\AccountManagerController;
use App\Http\Controllers\Admin\CronController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AgentRolesController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\StateLawController;
use App\Http\Controllers\Admin\DocumentTemplateController;


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

Route::prefix('admin')->group(function () {

    #account setup
    Route::get('/',[AdminController::class, 'login'])->name('admin.login');
    Route::get('/login',[AdminController::class, 'login'])->name('admin.login');
	Route::get('/login-user',[AdminController::class, 'loginWithUsernamePasword'])->name('admin.login.user');

    #dashboard setup
    Route::get('/dashboard',[AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin-login',[AdminController::class, 'admin_login'])->name('admin.admin_login');
	Route::post('/admin-otp-login',[AdminController::class, 'admin_otp_login'])->name('admin.admin_otp_login');
    Route::get('/logout',[AdminController::class, 'logout'])->name('admin.logout');
	Route::post('/admin-login-by-username',[AdminController::class, 'admin_login_by_username']);
	Route::get('/create-agent-account',[AdminController::class, 'createAgentAccount'])->name('admin.create_agent_account');
	Route::post('/register-agent',[AdminController::class, 'registerAgent'])->name('admin.registerAgent');
	Route::any('/verify-email/{id}',[AdminController::class, 'verifyEmail'])->name('admin.verifyEmail');
	Route::any('/forgot-password',[AdminController::class, 'forgotPassword'])->name('admin.forgotPassword');
	Route::any('/reset-password/{time}/{id}',[AdminController::class, 'resetPassword'])->name('admin.resetPassword');
	Route::any('/update-new-password',[AdminController::class, 'updateNewPassword'])->name('admin.updateNewPassword');
	
	#settings
    Route::get('/settings',[ProfileController::class, 'settings'])->name('admin.settings');
	Route::post('/save-setting',[ProfileController::class, 'saveSetting'])->name('admin.save-setting');
	Route::post('/save-credit-amt',[ProfileController::class, 'saveCreditAmt'])->name('admin.save-credit-amt');

    #update profile
    Route::get('/update-profile',[ProfileController::class, 'updateProfile'])->name('admin.update-profile');
    Route::post('/save-profile',[ProfileController::class, 'saveProfile'])->name('admin.save-profile');
	Route::get('/agent-profile',[ProfileController::class, 'agentProfile'])->name('admin.agent-profile');
	Route::get('/edit-agent-profile',[ProfileController::class, 'editAgentProfile'])->name('admin.edit-agent-profile');
	Route::post('/save-agent-profile',[ProfileController::class, 'saveAgentProfile'])->name('admin.save-agent-profile');
	
	#update status
    Route::post('/change-status',[AjaxController::class, 'changeStatus'])->name('admin.change-status');
	Route::post('/change-featured-status',[AjaxController::class, 'changeFeaturedStatus'])->name('admin.change-featured-status');
    Route::post('/delete-record',[AjaxController::class, 'deleteRecord'])->name('admin.delete-record');

    #change password
    Route::get('/change-password',[ProfileController::class, 'changePassword'])->name('admin.change-password');
    Route::post('/update-password',[ProfileController::class, 'updatePassword'])->name('admin.dashboard');
	
	Route::get('/coverage/{id}',[PricingController::class, 'getCoverage'])->name('admin.coverage');
	Route::any('/coverage_paginate',[PricingController::class, 'listCoveragePaginate'])->name('admin.coverage_paginate');
	Route::any('/coverage/set',[PricingController::class, 'setCoverage'])->name('admin.coverage_set');
	Route::any('/tier/set',[PricingController::class, 'setTier'])->name('admin.tier_set');
	
	Route::get('/pricings',[PricingController::class, 'getList'])->name('admin.pricings');
	Route::any('/pricings_paginate',[PricingController::class, 'listPaginate'])->name('admin.pricings_paginate');
	Route::any('/pricing/update',[PricingController::class, 'pricingUpdate'])->name('admin.pricingUpdate');
	Route::any('/category/update',[PricingController::class, 'categoryUpdate'])->name('admin.categoryUpdate');
	
	Route::get('/zipcodes/{id}',[PreferencesController::class, 'getList'])->name('admin.zipcodes');
	Route::any('/search-zipcode-auto',[PreferencesController::class, 'searchZipcode'])->name('admin.search-zipcode-auto');
	Route::any('/zipcodes_paginate',[PreferencesController::class, 'listPaginate'])->name('admin.zipcodes_paginate');
	Route::any('/set-user-zipcodes',[PreferencesController::class, 'setUserZipcode'])->name('admin.set-user-zipcodes');
	Route::any('/set-all-zipcodes',[PreferencesController::class, 'setAllZipcode'])->name('admin.set-all-zipcodes');
	Route::any('/remove-all-zipcodes',[PreferencesController::class, 'removeAllZipcode'])->name('admin.remove-all-zipcodes');
	Route::any('/get-single-country-data',[PreferencesController::class, 'getSingleCountyData'])->name('admin.get-single-country-data');
	
	Route::get('/payments',[PaymentController::class, 'getList'])->name('admin.payments');
	Route::any('/payments_paginate',[PaymentController::class, 'listPaginate'])->name('admin.payments_paginate');
	Route::any('/export-payments',[PaymentController::class, 'exportPayment'])->name('admin.export.payments');
	
	Route::get('/contacts',[ContactController::class, 'getList'])->name('admin.contacts');
	Route::any('/contacts_paginate',[ContactController::class, 'listPaginate'])->name('admin.contacts_paginate');
	
	Route::get('/newsletters',[NewsletterController::class, 'getList'])->name('admin.newsletters');
	Route::any('/newsletters_paginate',[NewsletterController::class, 'listPaginate'])->name('admin.newsletters_paginate');
	Route::any('/export-newsletters',[NewsletterController::class, 'exportData'])->name('admin.export.newsletters');
	
	Route::get('/leads',[LeadsController::class, 'getList'])->name('admin.leads');
	Route::any('/leads_paginate',[LeadsController::class, 'listPaginate'])->name('admin.leads_paginate');
	Route::any('/export_leads',[LeadsController::class, 'exportLeads'])->name('admin.export_leads');
	Route::any('/view-lead-history/{row_id}',[LeadsController::class, 'viewPage'])->name('admin.view-lead-history');
	Route::post('/lead/question-answer',[LeadsController::class, 'leadQuestionAnswer'])->name('admin.question-answer');
	Route::any('/send-leads',[LeadsController::class, 'sendLead']);
	
	Route::get('/blogs',[BlogsController::class, 'getList'])->name('admin.blogs');
	Route::any('/blogs_paginate',[BlogsController::class, 'listPaginate'])->name('admin.blogs_paginate');
	Route::any('/add-blog',[BlogsController::class, 'addPage'])->name('admin.add-blog');
	Route::any('/edit-blog/{row_id}',[BlogsController::class, 'editPage'])->name('admin.edit-blog');
	
	Route::get('/inner-pages',[InnerPagesController::class, 'getList'])->name('admin.inner-pages');
	Route::any('/inner_pages_paginate',[InnerPagesController::class, 'listPaginate'])->name('admin.inner_pages_paginate');
	Route::any('/edit-inner-page/{row_id}',[InnerPagesController::class, 'editPage'])->name('admin.edit-inner-page');
	
	Route::get('/testimonials',[TestimonialsController::class, 'getList'])->name('admin.testimonials');
	Route::any('/testimonials_paginate',[TestimonialsController::class, 'listPaginate'])->name('admin.testimonials_paginate');
	Route::any('/add-testimonial',[TestimonialsController::class, 'addPage'])->name('admin.add-testimonial');
	Route::any('/edit-testimonial/{row_id}',[TestimonialsController::class, 'editPage'])->name('admin.edit-testimonial');
	
	Route::get('/tiers',[TiersController::class, 'getList'])->name('admin.tiers');
	Route::any('/tiers_paginate',[TiersController::class, 'listPaginate'])->name('admin.tiers_paginate');
	Route::any('/add-tier',[TiersController::class, 'addPage'])->name('admin.add-tier');
	Route::any('/edit-tier/{row_id}',[TiersController::class, 'editPage'])->name('admin.edit-tier');
	
	Route::get('/cities',[CitiesController::class, 'getList'])->name('admin.cities');
	Route::any('/cities_paginate',[CitiesController::class, 'listPaginate'])->name('admin.cities_paginate');
	Route::any('/add-city',[CitiesController::class, 'addPage'])->name('admin.add-city');
	Route::any('/edit-city/{row_id}',[CitiesController::class, 'editPage'])->name('admin.edit-city');
	
	Route::get('/questions',[QuestionsController::class, 'getList'])->name('admin.questions');
	Route::any('/questions_paginate',[QuestionsController::class, 'listPaginate'])->name('admin.questions_paginate');
	Route::any('/add-question',[QuestionsController::class, 'addPage'])->name('admin.add-question');
	Route::any('/edit-question/{row_id}',[QuestionsController::class, 'editPage'])->name('admin.edit-question');
	
	Route::get('/faqs',[FaqsController::class, 'getList'])->name('admin.faqs');
	Route::any('/faqs_paginate',[FaqsController::class, 'listPaginate'])->name('admin.faqs_paginate');
	Route::any('/add-faq',[FaqsController::class, 'addPage'])->name('admin.add-faq');
	Route::any('/edit-faq/{row_id}',[FaqsController::class, 'editPage'])->name('admin.edit-faq');
	
	Route::get('/roles',[RolesController::class, 'getList'])->name('admin.roles');
	Route::any('/roles_paginate',[RolesController::class, 'listPaginate'])->name('admin.roles_paginate');
	Route::any('/add-role',[RolesController::class, 'addPage'])->name('admin.add-role');
	Route::any('/edit-role/{row_id}',[RolesController::class, 'editPage'])->name('admin.edit-role');
	
	Route::get('/agent-roles',[AgentRolesController::class, 'getList'])->name('admin.agent_roles');
	Route::any('/agent_roles_paginate',[AgentRolesController::class, 'listPaginate'])->name('admin.agent_roles_paginate');
	Route::any('/add-agent-role',[AgentRolesController::class, 'addPage'])->name('admin.add-agent-role');
	Route::any('/edit-agent-role/{row_id}',[AgentRolesController::class, 'editPage'])->name('admin.edit-agent-role');
	
	Route::get('/property-managers',[AgentsController::class, 'getList'])->name('admin.property-managers');
	Route::any('/property_managers_paginate',[AgentsController::class, 'listPaginate'])->name('admin.property_managers_paginate');
	Route::any('/add-property-managers',[AgentsController::class, 'addPage'])->name('admin.add-property-managers');
	Route::any('/edit-property-managers/{row_id}',[AgentsController::class, 'editPage'])->name('admin.edit-property-managers');
	Route::any('/view-property-managers/{row_id}',[AgentsController::class, 'viewPage'])->name('admin.view-property-managers');
	Route::any('/get-cities',[AgentsController::class, 'getCities'])->name('admin.get-cities');
	Route::any('/delete/property-managers',[AgentsController::class, 'deleteAgent']);
	Route::any('/export_property_managers',[AgentsController::class, 'exportPropertyManagers'])->name('admin.export_property_managers');
	Route::any('/resend-varification-email',[AgentsController::class, 'resendVarificationEmail'])->name('admin.resendVarificationEmail');
	
	Route::get('/account-managers',[AccountManagerController::class, 'getList'])->name('admin.account-managers');
	Route::any('/account_managers_paginate',[AccountManagerController::class, 'listPaginate'])->name('admin.account_managers_paginate');
	Route::any('/add-account-manager',[AccountManagerController::class, 'addPage'])->name('admin.add-account-managers');
	Route::any('/edit-account-managers/{row_id}',[AccountManagerController::class, 'editPage'])->name('admin.edit-account-managers');
	
	Route::get('/blog-categories',[BlogCategoryController::class, 'getList'])->name('admin.blog.categories');
	Route::any('/blog_categories_paginate',[BlogCategoryController::class, 'listPaginate'])->name('admin.blog_categories_paginate');
	Route::any('/add-blog-category',[BlogCategoryController::class, 'addPage'])->name('admin.add-blog-category');
	Route::any('/edit-blog-category/{row_id}',[BlogCategoryController::class, 'editPage'])->name('admin.edit-blog-category');
	
	Route::get('/users/{id}',[UsersController::class, 'getList'])->name('admin.users');
	Route::any('/users_paginate',[UsersController::class, 'listPaginate'])->name('admin.users_paginate');
	Route::any('/add-user',[UsersController::class, 'addPage'])->name('admin.add-user');
	Route::any('/edit-user/{row_id}',[UsersController::class, 'editPage'])->name('admin.edit-user');
	
	Route::any('/add-credit',[CreditsController::class, 'addCredit'])->name('admin.add-credit');
	Route::any('/get-total-credits',[CreditsController::class, 'getTotalCredits'])->name('admin.get-total-credits');
	
	Route::get('stripe/checkout', [StripePaymentController::class, 'stripeCheckout'])->name('stripe.checkout');
	Route::get('stripe/checkout/success', [StripePaymentController::class, 'stripeCheckoutSuccess'])->name('stripe.checkout.success');
	
	Route::get('lead/distribute', [CronController::class, 'leadDistribute']);

	// Multi-step form questions management (RESTful API)
	Route::get('/form-questions', [QuestionController::class, 'index'])->name('admin.form-questions.index');
	Route::post('/form-questions', [QuestionController::class, 'store'])->name('admin.form-questions.store');
	Route::get('/form-questions/{id}', [QuestionController::class, 'show'])->name('admin.form-questions.show');
	Route::put('/form-questions/{id}', [QuestionController::class, 'update'])->name('admin.form-questions.update');
	Route::delete('/form-questions/{id}', [QuestionController::class, 'destroy'])->name('admin.form-questions.destroy');
	Route::post('/form-questions/reorder', [QuestionController::class, 'reorder'])->name('admin.form-questions.reorder');

    Route::resource('laws', StateLawController::class)->names('admin.laws');
    Route::resource('templates', DocumentTemplateController::class)->names('admin.templates');

    // PM Verification Routes
    Route::get('/users/{userId}/verification', [UsersController::class, 'showVerification'])->name('admin.users.verification');
    Route::post('/users/{userId}/verification', [UsersController::class, 'updateVerification'])->name('admin.users.verification.update');
    Route::post('/users/{userId}/verification/upload', [UsersController::class, 'uploadVerificationDocument'])->name('admin.users.verification.upload');
    Route::delete('/users/{userId}/verification/documents/{documentIndex}', [UsersController::class, 'deleteVerificationDocument'])->name('admin.users.verification.delete');
    Route::get('/users/{userId}/verification/documents/{documentIndex}', [UsersController::class, 'downloadVerificationDocument'])->name('admin.users.verification.download');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
