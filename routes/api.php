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
use App\Http\Controllers\PayDunyaController;
use App\Http\Controllers\DirectController;



Route::prefix('v1')->group(function (){

    //Routes publiques
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // reinitialisation et mot de passe oublie
    Route::post('/forgotPassword', [AuthController::class, 'forgotPassword']);
    Route::post('/resetPassword',  [AuthController::class, 'resetPassword']);

    // IPN PayDunya — appelée directement par les serveurs PayDunya, sans
    // session utilisateur. DOIT rester hors du groupe auth:sanctum, sinon
    // toutes les notifications de paiement seraient rejetées avec une 401.
    Route::post('/webhooks/paydunya', [PayDunyaController::class, 'ipn'])->name('paydunya.ipn');

    //Lecture publique (Voir les Categories et Formations disponibles)
    Route::apiResource('categories', CategorieController::class)->only(['index', 'show']);
    Route::apiResource('formations', FormationController::class)->only(['index', 'show']);

    //Routes protegees (l'utilisateur doit etre connecte)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/me', [AuthController::class, 'updateProfile']);
        Route::post('/changePassword', [AuthController::class, 'changePassword']);

        // Tous les connectés — voir exercices
        Route::get('/exercices', [ExerciceController::class, 'index']);
        Route::get('/exercices/{exercice}', [ExerciceController::class, 'show']);
        Route::get('/exercices/{exercice}/resultats', [ExerciceController::class, 'resultats']);

        // Tous les connectés — voir les sessions live (étudiants inclus)
        Route::get('/directs', [DirectController::class, 'index']);
        Route::get('/directs/{direct}', [DirectController::class, 'show']);

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

            // Sessions live : créer, modifier, supprimer
            Route::post('/directs', [DirectController::class, 'store']);
            Route::put('/directs/{direct}', [DirectController::class, 'update']);
            Route::delete('/directs/{direct}', [DirectController::class, 'destroy']);
        });

        //Admin uniquement
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('formateurs', FormateurController::class); //creation de formateur
            Route::apiResource('etudiants', EtudiantController::class);  //creation d'etudiants

            Route::put('/formateurs/{formateur}/toggle-active', [FormateurController::class, 'toggleActive']);
            Route::post('/formateurs/{formateur}/reset-password', [FormateurController::class, 'resetPassword']);
            Route::put('/etudiants/{etudiant}/toggle-active', [EtudiantController::class, 'toggleActive']);
            Route::post('/etudiants/{etudiant}/reset-password', [EtudiantController::class, 'resetPassword']);
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
        Route::post('/examens/{examen}/soumettre', [ExamenController::class, 'soumettre']);

        //certificats
        Route::get('/certificats/{certificat}/download', [CertificatController::class, 'download'])
       ->middleware('auth:sanctum');
        Route::apiResource('certificats', CertificatController::class);
        Route::post('/certificats/generate', [CertificatController::class, 'generate']);

        //actus
        Route::apiResource('actus', ActusController::class);

        //opportunites
        Route::apiResource('opportunites', OpportuniteController::class);

        //paiements
        Route::apiResource('paiements', PaiementController::class);
        Route::post('/paiements/{paiement}/paydunya', [PayDunyaController::class, 'initiate']);

        //alerteRoute::middleware('role:formateur')->group(function () {
        Route::post('/alertes', [AlerteController::class, 'store']);
        Route::get('/alertes', [AlerteController::class, 'index']);

        Route::post('/push-subscribe', [PushSubscriptionController::class, 'store']);
        Route::delete('/push-subscribe', [PushSubscriptionController::class, 'destroy']);
    });






});
