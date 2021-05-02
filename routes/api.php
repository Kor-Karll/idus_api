<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
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

Route::middleware(['json.response', 'cors',])->prefix('v1')->group(function () {
    // public
    Route::prefix('user')->group(function () {
        Route::post('/join', [UserController::class, 'store']);
        Route::post('/login', [UserController::class, 'login']);
    });

    Route::middleware('auth:api')->prefix('user')->group(function () {
        Route::post('/logout', [UserController::class, 'logout']);
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/search', [UserController::class, 'search']);
    });

    Route::middleware('auth:api')->prefix('order')->group(function () {
        Route::get('/{customer_id}', [OrderController::class, 'index']);
    });
});


