<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Log;

use App\Models\User;
use App\Models\Business;
use App\Http\Controllers\business_info_controller;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\csrfController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/all-users', function(){
    return User::all();
});

Route::get('/all-business', function(){
    return Business::all();
});

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('products/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
//Dedicated for seniorPWD_discoubtable
Route::put('/products/{id}/discountable', [ProductController::class, 'updateDiscountable']);

Route::get('/Business', [BusinessController::class, 'show']);
Route::post('/Business', [BusinessController::class, 'store']);
Route::put('/Business/{id}', [BusinessController::class, 'update']);
Route::delete('/Business/{id}', [BusinessController::class, 'destroy']);
Route::get('/business_info', [BusinessController::class, 'showBusiness']);


Route::apiResource('categories', CategoryController::class);

