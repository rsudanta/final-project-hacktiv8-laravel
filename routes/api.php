<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::group(['prefix' => 'category'], function () {
        Route::get('read', [CategoryController::class, 'getCategory']);
        Route::get('read/{id}', [CategoryController::class, 'getCategoryById']);
        Route::post('create', [CategoryController::class, 'storeCategory']);
        Route::put('update/{id}', [CategoryController::class, 'updateCategory']);
        Route::delete('delete/{id}', [CategoryController::class, 'destroyCategory']);
    });

    Route::group(['prefix' => 'product'], function () {
        Route::get('read', [ProductController::class, 'getProduct']);
        Route::get('read/{id}', [ProductController::class, 'getProductById']);
        Route::post('create', [ProductController::class, 'storeProduct']);
        Route::put('update/{id}', [ProductController::class, 'updateProduct']);
        Route::delete('delete/{id}', [ProductController::class, 'destroyProduct']);
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('read', [OrderController::class, 'getOrder']);
        Route::get('read/{id}', [OrderController::class, 'getOrderById']);
        Route::post('create', [OrderController::class, 'storeOrder']);
        Route::put('update/{id}', [OrderController::class, 'updateOrder']);
        Route::delete('delete/{id}', [OrderController::class, 'destroyOrder']);
        
        Route::get('report', [OrderController::class, 'getReport']);
    });
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
