<?php

use App\Http\Controllers\Auth\AuthUserController;
use App\Http\Controllers\Ratting\RattingController;
use App\Http\Controllers\Review\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

Route::prefix('user')->group(function () {

    Route::middleware('isUser')->get('/user', function (Request $request) {
        return Auth::guard('api-user')->user();
    });


    Route::middleware('isUser')->group(function () {
        Route::apiResource('ratting', RattingController::class);

        Route::apiResource('review', ReviewController::class);
    });

    Route::name('user.')->middleware('guest:api-user')->group(function () {

        Route::post('register', [AuthUserController::class, 'store'])->name('regitser');
        Route::post('login', [AuthUserController::class, 'login'])->name('login');
    });
});;

require __DIR__ . '/api-admin.php';