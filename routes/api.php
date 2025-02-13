<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', action: function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post("/users", [UserController::class,"store"]);

Route::post("/login",[UserController::class,"login"]);

Route::post("/users/{user}/profile-update", [UserController::class,"profileImageUpdate"])->middleware('auth:sanctum');

// Route::post("/roles", )