<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LeconController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ProgressionController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReponseController;
use App\Http\Controllers\ResultatController;
use App\Http\Controllers\ActusController;
use App\Http\Controllers\OpportuniteController;
use App\Http\Controllers\PaiementController;


Route::prefix('v1')->group(function (){


    Route::post('/register', [AuthController::class, 'register']);

    Route::post('/login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->group(function () {

        // Accessible uniquement par l'admin
        Route::middleware('role:admin')->group(function () {
            
            Route::apiResource('formateurs', FormateurController::class);
            Route::apiResource('etudiants', EtudiantController::class);

        });

        Route::post('/logout', [AuthController::class, 'logout']);

        //inscriptions
        Route::apiResource('inscriptions', InscriptionController::class);

        //categories
        Route::apiResource('categories',CategorieController::class);

        //formations
        Route::apiResource('formations',FormationController::class);

        //modules
        Route::apiResource('modules', ModuleController::class);

        //lecons
        Route::apiResource('lecons', LeconController::class);

        //progressions
        Route::apiResource('progressions', ProgressionController::class);

        //questions
        Route::apiResource('questions', QuestionController::class);

        //reponses
        Route::apiResource('reponses', ReponseController::class);

        //resultats
        Route::apiResource('resultats', ResultatController::class);

        //examens
        Route::apiResource('examens', ExamenController::class);

        //certificats
        Route::get('/certificats/{certificat}/download', [CertificatController::class, 'download'])
       ->middleware('auth:sanctum');
        Route::apiResource('certificats', CertificatController::class);

        //actus
        Route::apiResource('actus', ActusController::class);

        //opportunites
        Route::apiResource('opportunites', OpportuniteController::class);

        //paiements
        Route::apiResource('paiements', PaiementController::class);


    });


});
