<?php

use App\Http\Controllers\Api\Admin\Room\RoomController as AdminRoomController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\Webhook\StripeWebhookController;
use App\Http\Controllers\Api\Admin\Order\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Categories\CategoriesController;
use App\Http\Controllers\Api\Admin\Movie\MovieController;
use App\Http\Controllers\Api\Movie\MovieController as MovieControllerClient;
use App\Http\Controllers\Api\Showtime\ShowtimeController;
use App\Http\Controllers\Api\User\LoginGoogleController;
use App\Http\Controllers\Api\User\UserController;
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
        Route::get('profile', [AuthController::class, 'profile']);
        Route::post('update', [AuthController::class, 'update']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
        Route::post('check-forgot-password', [AuthController::class, 'checkForgotPassword'])->withoutMiddleware('auth:api');
        Route::post('resetPassword', [AuthController::class, 'resetPassword'])->withoutMiddleware('auth:api');
        Route::get('verify-email', [AuthController::class, 'verifyEmail'])->name('verify_email')->withoutMiddleware('auth:api');
        Route::post('/resend-activation-email', [AuthController::class, 'resendActivationEmail'])->name('resendActivationEmail')->withoutMiddleware('auth:api');
    });

    Route::group(['prefix' => 'admin'], function () {
        Route::group(['prefix' => 'orders'], function () {
            Route::get('', [AdminOrderController::class, 'index']);
            Route::post('refund/{order}', [AdminOrderController::class, 'refund']);
        });

        Route::group(['prefix' => 'rooms'], function () {
            Route::get('', [AdminRoomController::class, 'index']);
            Route::get('{room}', [AdminRoomController::class, 'show']);
            Route::post('', [AdminRoomController::class, 'store']);
            Route::put('{room_id}', [AdminRoomController::class, 'update']);
            Route::delete('{room_id}', [AdminRoomController::class, 'delete']);
        });

        Route::group(['prefix' => 'users'], function () {
            Route::post('create', [UserController::class, 'store'])->middleware('role:admin');
            Route::get('index', [UserController::class, 'index'])->middleware('role:admin');
            Route::get('show/{id}', [UserController::class, 'showUser'])->middleware('role:admin');
            Route::post('update/{id}', [UserController::class, 'update'])->middleware('role:admin');
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::post('create', [CategoriesController::class, 'store'])->middleware('role:admin');
            Route::get('index', [CategoriesController::class, 'index'])->middleware('role:admin');
            Route::get('show/{id}', [CategoriesController::class, 'showCategories'])->middleware('role:admin');
            Route::post('update/{id}', [CategoriesController::class, 'update'])->middleware('role:admin');
        });
    });
    // Route::group(['prefix' => 'movie'], function () {
    //     Route::post('', [MovieController::class, 'addMovie'])->name('add_movie');
    //     Route::get('{slug}', [MovieController::class, 'showMovie'])->name('get_movie_detail');
    //     Route::get('', [MovieController::class, 'getListMovies'])->name('get_list_movie');
    //     Route::put('{id}', [MovieController::class, 'updateMovie'])->name('update_movie');
    //     Route::delete('{id}', [MovieController::class, 'deleteMovie'])->name('delete_movie');
    //     Route::get('change-status/{id}', [MovieController::class, 'changeStatusMovie'])->name('hide_movie');
    // });

    Route::group(['prefix' => 'showtime'], function () {
        Route::get('dates/{movie_id}', [ShowtimeController::class, 'getShowDate'])->name('get_show_date');
        Route::get('/', [ShowtimeController::class, 'getShowtimesByDate'])->name('get_showtimes_by_date');
        Route::get('{showtime_id}', [ShowtimeController::class, 'showShowtime'])->name('get_showtime');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('', [OrderController::class, 'index']);
        Route::get('{order}', [OrderController::class, 'show']);
    });

    Route::group(['prefix' => 'stripe'], function () {
        Route::post('process-payment', [PaymentController::class, 'processStripePayment']);
        Route::post('handle-webhook', [StripeWebhookController::class, 'handleStripeWebhook'])->withoutMiddleware('auth:api');
    });
});

// Google Sign In
Route::get('/google', [LoginGoogleController::class, 'google']);
Route::get('/google/callback', [LoginGoogleController::class, 'loginGoogleCallback']);

// Client
Route::group(['prefix' => 'movies'], function () {
    Route::get('list-showing-movies', [MovieController::class, 'listShowingMovies']);
    Route::get('list-upcoming-movie', [MovieController::class, 'listUpcomingMovies']);
});
