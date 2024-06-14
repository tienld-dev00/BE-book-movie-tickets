<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Webhook\StripeWebhookController;
use App\Http\Controllers\Api\Admin\Order\OrderController as AdminOrderController;
use App\Jobs\SendEmail\Order\SendMailOrderSuccessJob;
use App\Models\Order;
use App\Http\Controllers\Api\Movie\MovieController;
use App\Http\Controllers\Api\Showtime\ShowtimeController;
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

Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware('auth:api');
        Route::post('register', [AuthController::class, 'register'])->withoutMiddleware('auth:api');
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verify_email')->withoutMiddleware('auth:api');
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::group(['prefix' => 'orders'], function () {
            Route::get('', [AdminOrderController::class, 'index']);
            Route::post('refund/{order}', [AdminOrderController::class, 'refund']);
        });
    });

    Route::group(['prefix' => 'movie'], function () {
        Route::get('{slug}', [MovieController::class, 'showMovie'])->name('get_movie_detail');
    });

    Route::group(['prefix' => 'showtime'], function () {
        Route::get('dates/{movie_id}', [ShowtimeController::class, 'getShowDate'])->name('get_show_date');
        Route::get('/', [ShowtimeController::class, 'getShowtimesByDate'])->name('get_showtimes_by_date');
        Route::get('{showtime_id}', [ShowtimeController::class, 'showShowtime'])->name('get_showtime');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::post('', [OrderController::class, 'store']);
        Route::get('{order}', [OrderController::class, 'show']);
    });

    Route::group(['prefix' => 'stripe'], function () {
        Route::post('process-payment', [PaymentController::class, 'processStripePayment']);
        Route::post('handle-webhook', [StripeWebhookController::class, 'handleStripeWebhook'])->withoutMiddleware('auth:api');
    });
});