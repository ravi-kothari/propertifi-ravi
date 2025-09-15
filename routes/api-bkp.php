<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomePageController;

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
	Route::post('/lead/save',[HomePageController::class, 'leadSave']);
	Route::post('/agent/save',[HomePageController::class, 'agentSave']);
	Route::post('/newsletter/save',[HomePageController::class, 'newsletterSave']);
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
});
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});