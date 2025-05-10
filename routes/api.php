<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class,'register']);

route::group([
    'prefix' => 'auth',
    'middleware' => ['auth.guardFromToken']
], function () {
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class,'profile']);
});
