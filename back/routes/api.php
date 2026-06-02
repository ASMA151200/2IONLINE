<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LeconController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\EtudiantController;


Route::prefix('v1')->group(function (){


    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->group(function () {

        // Accessible uniquement par l'admin
        Route::middleware('role:admin')->group(function () {

            //creation de professeurs
            Route::apiResource('formateurs', FormateurController::class);
            //creation d'etudiants
            Route::apiResource('etudiants', EtudiantController::class);
            //categories
            Route::apiResource('categories',CategorieController::class);
            //formations
            Route::apiResource('formations',FormationController::class);
            //modules
            Route::apiResource('modules', ModuleController::class);
            //lecons
            Route::apiResource('lecons', LeconController::class);



        });

        Route::middleware('role:professeur')->group(function () {

            //categories
            //Route::apiResource('categories',CategorieController::class);
            //formations
            //Route::apiResource('formations',FormationController::class);
            //modules
            //Route::apiResource('modules', ModuleController::class);
            //lecons
            //Route::apiResource('lecons', LeconController::class);

        });


    });

});
