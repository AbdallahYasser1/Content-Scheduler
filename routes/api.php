<?php

use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\API\DashboardController;
use App\Http\Controllers\Api\PlatformController;
use App\Http\Controllers\API\PostController;
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
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->group(callback: function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    Route::apiResource('/posts', PostController::class);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::apiResource('/platforms', PlatformController::class);
    Route::post('/platforms/{platform}/toggle', [PlatformController::class, 'toggleActivePlatform'])->name(
        'platforms.toggle'
    );
    Route::get('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
});
