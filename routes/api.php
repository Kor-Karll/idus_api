<?php

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

Route::prefix('v1')->middleware('json.response')->group(function () {
    Route::prefix('user')->group(function () {
        Route::get('/{id}', [UserController::class, 'show']);
        Route::post('/search', [UserController::class, 'search']);
    });
});

Route::prefix('v1/user')->group(function () {
    Route::post('/join', [UserController::class, 'store']);
    Route::post('/login', [UserController::class, 'login']);
});
