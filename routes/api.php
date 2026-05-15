<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/admin.php';

Route::post('/login', [AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){

    Route::post(
        '/logout',
        [AuthController::class,'logout']
    );

    Route::get(
        '/user',
        [AuthController::class,'me']
    );

});