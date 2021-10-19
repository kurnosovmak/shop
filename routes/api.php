<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api;

// Only Auth
Route::middleware('auth:api')->group(function (){

    Route::resource('basket','BasketController')->except(['destroy','create','show','edit']);
    Route::delete('basket','BasketController@destroy')->name('basket.destroy');

    Route::prefix('password')->group(function () {

        // reset password
        Route::prefix('reset')->group(function () {
            Route::post('/sendResetByEmail',[Api\Auth\ResetPasswordController::class,'sendResetByEmail']);
            Route::post('/sendResetByPhone',[Api\Auth\ResetPasswordController::class,'sendResetByPhone']);
            Route::post('/changePassword',[Api\Auth\ResetPasswordController::class,'changePassword']);
            Route::get('/getNumberAttempts',[Api\Auth\ResetPasswordController::class,'getNumberAttempts']);
        });
    });

    Route::post('order',[Api\OrderController::class,'store']);
});


// Only Guest
Route::middleware(['guest'])->group(function () {

    Route::post('/login',[Api\Auth\LoginController::class,'login'])->name('login');

    Route::post('/register',[Api\Auth\RegisterController::class,'register'])->name('register');

    Route::post('/logout',[Api\Auth\LogoutController::class,'logout'])->name('logout');


});



// All
Route::get('product',[Api\ProductController::class,'index']);
Route::get('product/{product}',[Api\ProductController::class,'show']);








Route::get('category',[Api\CategoryController::class,'index']);
