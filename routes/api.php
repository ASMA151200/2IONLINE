<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LeconController;




Route::prefix('v1')->group(function (){


    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        //categories
        Route::apiResource('categories',CategorieController::class);

        //formations
        Route::apiResource('formations',FormationController::class);

        //modules
        Route::apiResource('modules', ModuleController::class);

        //lecons
        Route::apiResource('lecons', LeconController::class);


    });


});
