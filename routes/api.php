<?php

use App\Http\Controllers\Api\Auth\AuthController;
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
        Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verify_email')->withoutMiddleware('auth:api');
    });
});

Route::group(['prefix' => 'users'], function () {
    Route::post('create', [UserController::class, 'store']);
    Route::get('index', [UserController::class, 'index']);
    Route::get('show/{id}', [UserController::class, 'showUser']);
    Route::get('check-login', [UserController::class, 'checkLogin'])->name('checklLogin');
    Route::get('check-permission-fail-store', [UserController::class, 'checkPermissionFailStore'])->name('checkPermissionFailStore');
    Route::post('update/{id}', [UserController::class, 'update']);
});
