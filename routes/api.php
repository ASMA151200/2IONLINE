<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LeconController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ExerciceController;


Route::prefix('v1')->group(function (){

    //Routes publiques
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    //Lecture publique sans token(Voir Formations et Categories)
    Route::apiResource('categories', CategorieController::class)->only(['index', 'show']);
    Route::apiResource('formations', FormationController::class)->only(['index', 'show']);

    //Routes protegees
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Tous les connectés — voir exercices
        Route::get('/exercices', [ExerciceController::class, 'index']);
        Route::get('/exercices/{exercice}', [ExerciceController::class, 'show']);
        Route::get('/exercices/{exercice}/resultats', [ExerciceController::class, 'resultats']);

        // Admin et Formateur
        Route::middleware('role:admin,formateur')->group(function () {

            Route::apiResource('categories',CategorieController::class)->except(['index','show']); //categories
            Route::apiResource('formations',FormationController::class)->except(['index','show']);;  //formations
            Route::apiResource('modules', ModuleController::class); //modules
            Route::apiResource('lecons', LeconController::class);   //lecons
            // Exercices — créer, modifier, supprimer
            Route::post('/exercices',  [ExerciceController::class, 'store']);
            Route::put('/exercices/{exercice}',  [ExerciceController::class, 'update']);
            Route::delete('/exercices/{exercice}', [ExerciceController::class, 'destroy']);

            // Corriger une réponse ouverte
            Route::put('/reponses/{reponse}/corriger', [ExerciceController::class, 'corriger']);

        });

        //Admin uniquement
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('formateurs', FormateurController::class); //creation de formateur
            Route::apiResource('etudiants', EtudiantController::class);  //creation d'etudiants


        });

        //Etudiant uniquement
        Route::middleware('role:etudiant')->group(function () {
            Route::get('/mes-inscriptions', [ExerciceController::class, 'mesInscriptions']);
            Route::post('/exercices/{exercice}/soumettre', [ExerciceController::class, 'soumettre']);
        });

    });


});
