<?php

use App\Http\Controllers\Api\Auth\UserLoginController;
use App\Http\Controllers\Api\Auth\UserRegisterController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UserLoginController::class, 'login']);
Route::post('/register', [UserRegisterController::class, 'register']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [UserLoginController::class, 'logout']);
});
