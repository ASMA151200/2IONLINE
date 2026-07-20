<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LeconController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ExerciceController;
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
use App\Http\Controllers\Api\{AlerteController, PushSubscriptionController};



Route::prefix('v1')->group(function (){

    //Routes publiques
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // reinitialisation et mot de passe oublie
    Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
    Route::post('/resetPassword',  [AuthController::class, 'resetPassword']);

    //Lecture publique (Voir les Categories et Formations disponibles)
    Route::apiResource('categories', CategorieController::class)->only(['index', 'show']);
    Route::apiResource('formations', FormationController::class)->only(['index', 'show']);

    //Routes protegees (l'utilisateur doit etre connecte)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/changePassword', [AuthController::class, 'changePassword']);

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

            // Exercices : créer, modifier, supprimer
            Route::post('/exercices',  [ExerciceController::class, 'store']);
            Route::put('/exercices/{exercice}',  [ExerciceController::class, 'update']);
            Route::delete('/exercices/{exercice}', [ExerciceController::class, 'destroy']);

            // Corriger une question ouverte
            Route::put('/reponses/{reponse}/corriger', [ExerciceController::class, 'corriger']);
        });

        //Admin uniquement
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('formateurs', FormateurController::class); //creation de formateur
            Route::apiResource('etudiants', EtudiantController::class);  //creation d'etudiants
        });

        //Etudiant uniquement
        Route::middleware('role:etudiant')->group(function () {
            Route::get('/mescours', [EtudiantController::class, 'voirCours']);
            Route::post('/exercices/{exercice}/soumettre', [ExerciceController::class, 'soumettre']);
        });


        //inscriptions
        Route::apiResource('inscriptions', InscriptionController::class);

        

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

        //alerteRoute::middleware('role:formateur')->group(function () {
        Route::post('/alertes', [AlerteController::class, 'store']);
        Route::get('/alertes', [AlerteController::class, 'index']);

        Route::post('/push-subscribe', [PushSubscriptionController::class, 'store']);
        Route::delete('/push-subscribe', [PushSubscriptionController::class, 'destroy']);
    });






});
