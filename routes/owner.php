<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\Owner\LoginController;
use App\Http\Controllers\Api\Auth\Owner\RegisterController;
use App\Http\Controllers\Api\OwnerDashboardController;
use App\Http\Controllers\Api\OwnerBookmarkController;
use App\Http\Controllers\Api\SavedCalculationController;

Route::prefix('api/owner')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('dashboard', [OwnerDashboardController::class, 'dashboard']);

        Route::get('bookmarks', [OwnerBookmarkController::class, 'index']);
        Route::post('bookmarks', [OwnerBookmarkController::class, 'store']);
        Route::delete('bookmarks/{bookmark}', [OwnerBookmarkController::class, 'destroy']);

        Route::get('saved-calculations', [SavedCalculationController::class, 'index']);
        Route::post('saved-calculations', [SavedCalculationController::class, 'store']);
        Route::delete('saved-calculations/{savedCalculation}', [SavedCalculationController::class, 'destroy']);
    });
});
