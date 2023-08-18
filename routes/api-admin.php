<?php

use App\Http\Controllers\Auth\AuthAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function () {

    Route::middleware('isAdmin')->get('/user', function (Request $request) {
        return Auth::guard('api-admin')->user();
    });


    Route::middleware('isAdmin')->group(function () {
        require __DIR__ . '/Branch.php';
    });

    Route::name('admin.')->middleware('guest:api-admin')->group(function () {

        Route::post('register', [AuthAdminController::class, 'store'])->name('regitser');
        Route::post('login', [AuthAdminController::class, 'login'])->name('login');
    });
});