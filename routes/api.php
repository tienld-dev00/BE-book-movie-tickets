<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Categories\CategoriesController;
use App\Http\Controllers\Api\User\LoginFacebookController;
use App\Http\Controllers\Api\User\LoginGoogleController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Payment\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::POST('payment', [PaymentController::class, 'payment']);

Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth:api');
        Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth:api');
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verify_email')->withoutMiddleware('auth:api');
    });

    // CRUD user
    Route::group(['prefix' => 'users'], function () {
        Route::post('create', [UserController::class, 'store'])->middleware('role:admin');
        Route::get('index', [UserController::class, 'index'])->middleware('role:admin');
        Route::get('show/{id}', [UserController::class, 'showUser'])->middleware('role:user');
        Route::post('update/{id}', [UserController::class, 'update'])->middleware('role:user');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::post('create', [CategoriesController::class, 'store'])->middleware('role:admin');
        Route::get('index', [CategoriesController::class, 'index'])->middleware('role:admin');
        Route::get('show/{id}', [CategoriesController::class, 'showCategories'])->middleware('role:admin');
        Route::post('update/{id}', [CategoriesController::class, 'update'])->middleware('role:admin');
    });
});

// Google Sign In
Route::get('google', [LoginGoogleController::class, 'google']);
Route::get('google/callback', [LoginGoogleController::class, 'loginGoogleCallback']);

// Login by facebook
Route::get('facebook-sign-in-url', [LoginFacebookController::class, 'facebookSignInUrl']);
Route::get('facebook/callback', [LoginFacebookController::class, 'loginFacebookCallback']);
