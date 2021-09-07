<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Api;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('product',[Api\ProductController::class,'index']);
Route::get('product/{product}',[Api\ProductController::class,'show']);


Route::resource('basket','BasketController')->except(['destroy','create','show','edit']);
Route::delete('basket','BasketController@destroy')->name('basket.destroy');


Route::post('order',[Api\OrderController::class,'store']);



Route::get('category',[Api\CategoryController::class,'index']);
