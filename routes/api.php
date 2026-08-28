<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\FormationController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\LeconController;
use App\Http\Controllers\FormateurController;
use App\Http\Controllers\PartenaireController;
use App\Http\Controllers\PartenaireDashboardController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\ExerciceController;
use App\Http\Controllers\CertificatController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ProgressionController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ResultatController;
use App\Http\Controllers\ActusController;
use App\Http\Controllers\OpportuniteController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\Api\{AlerteController, PushSubscriptionController};
use App\Http\Controllers\PayDunyaController;
use App\Http\Controllers\DirectController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\FavoriController;
use App\Http\Controllers\ForumController;



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
    Route::get('/search', [SearchController::class, 'index']);

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

        // Tous les connectés — voir modules/leçons (le contrôleur filtre
        // lui-même selon le rôle : formateur=ses formations,
        // étudiant=inscriptions actives, admin=tout). Auparavant ces
        // routes étaient entièrement dans le groupe role:admin,formateur,
        // ce qui bloquait même la LECTURE pour les étudiants (403
        // automatique) — cassant le lecteur de cours côté étudiant.
        Route::get('/modules', [ModuleController::class, 'index']);
        Route::get('/modules/{module}', [ModuleController::class, 'show']);
        Route::get('/lecons', [LeconController::class, 'index']);
        Route::get('/lecons/{lecon}', [LeconController::class, 'show']);

        // Tous les connectés — voir les sessions live (étudiants inclus)
        Route::get('/directs', [DirectController::class, 'index']);
        Route::get('/directs/{direct}', [DirectController::class, 'show']);

        // Tous les connectés — activer/désactiver les notifications push
        // sur leur propre navigateur (un étudiant DOIT pouvoir s'abonner,
        // c'est même le cas d'usage principal)
        Route::post('/push-subscribe', [PushSubscriptionController::class, 'store']);
        Route::delete('/push-subscribe', [PushSubscriptionController::class, 'destroy']);

        // Tous les connectés — voir les alertes (le contrôleur filtre
        // lui-même selon le rôle, comme pour modules/leçons)
        Route::get('/alertes', [AlerteController::class, 'index']);

        // Admin et Formateur
        Route::middleware('role:admin,formateur')->group(function () {

            Route::apiResource('categories',CategorieController::class)->except(['index','show']); //categories
            Route::apiResource('formations',FormationController::class)->except(['index','show']);;  //formations

            // Modules/leçons : créer, modifier, supprimer (le contrôleur
            // vérifie en plus que le formateur est bien propriétaire de la
            // formation concernée — un formateur ne peut pas modifier le
            // contenu d'une formation qui n'est pas la sienne)
            Route::post('/modules', [ModuleController::class, 'store']);
            Route::put('/modules/{module}', [ModuleController::class, 'update']);
            Route::delete('/modules/{module}', [ModuleController::class, 'destroy']);
            Route::post('/lecons', [LeconController::class, 'store']);
            Route::put('/lecons/{lecon}', [LeconController::class, 'update']);
            Route::delete('/lecons/{lecon}', [LeconController::class, 'destroy']);

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

            // Alertes : créer (déclenche l'envoi push aux inscrits actifs)
            Route::post('/alertes', [AlerteController::class, 'store']);

            // Certificats : créer/modifier/supprimer/générer (un étudiant ne
            // peut ni s'auto-délivrer, ni modifier/supprimer un certificat)
            Route::post('/certificats', [CertificatController::class, 'store']);
            Route::put('/certificats/{certificat}', [CertificatController::class, 'update']);
            Route::delete('/certificats/{certificat}', [CertificatController::class, 'destroy']);
            Route::post('/certificats/generate', [CertificatController::class, 'generate']);
        });

        //Admin uniquement
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('formateurs', FormateurController::class); //creation de formateur
            Route::apiResource('etudiants', EtudiantController::class);  //creation d'etudiants
            Route::apiResource('partenaires', PartenaireController::class); //creation de partenaire

            Route::put('/formateurs/{formateur}/toggle-active', [FormateurController::class, 'toggleActive']);
            Route::post('/formateurs/{formateur}/reset-password', [FormateurController::class, 'resetPassword']);
            Route::put('/etudiants/{etudiant}/toggle-active', [EtudiantController::class, 'toggleActive']);
            Route::post('/etudiants/{etudiant}/reset-password', [EtudiantController::class, 'resetPassword']);
            Route::put('/partenaires/{partenaire}/toggle-active', [PartenaireController::class, 'toggleActive']);
            Route::post('/partenaires/{partenaire}/reset-password', [PartenaireController::class, 'resetPassword']);

            // Financement d'une formation par un partenaire
            Route::post('/partenaires/{partenaire}/financer', [PartenaireController::class, 'financer']);
            Route::delete('/partenaires/{partenaire}/financer/{formation}', [PartenaireController::class, 'retirerFinancement']);

            // Analytics (agrégées depuis les données existantes, aucune
            // nouvelle table)
            Route::get('/analytics/admin', [AnalyticsController::class, 'admin']);
            Route::get('/analytics/formations', [AnalyticsController::class, 'allFormations']);
            Route::get('/analytics/formations/{formation}', [AnalyticsController::class, 'formation']);
            Route::get('/analytics/students', [AnalyticsController::class, 'students']);
        });

        // Partenaire uniquement — son propre espace (lecture seule sur ce
        // qu'il finance, vérifié via la table pivot dans le contrôleur,
        // pas juste par rôle)
        Route::middleware('role:partenaire')->group(function () {
            Route::get('/mes-financements', [PartenaireDashboardController::class, 'mesFinancements']);
            Route::get('/mes-financements/stats', [PartenaireDashboardController::class, 'stats']);
            Route::get('/mes-financements/{formation}/etudiants', [PartenaireDashboardController::class, 'etudiantsFormation']);
        });

        //Etudiant uniquement
        Route::middleware('role:etudiant')->group(function () {
            Route::get('/mescours', [EtudiantController::class, 'voirCours']);
            Route::post('/exercices/{exercice}/soumettre', [ExerciceController::class, 'soumettre']);
        });


        //inscriptions
        Route::apiResource('inscriptions', InscriptionController::class);

        //notes personnelles et favoris (chaque utilisateur gère les siens)
        Route::apiResource('notes', NoteController::class)->except(['show']);
        Route::apiResource('favoris', FavoriController::class)->only(['index', 'store', 'destroy']);

        //forum (discussions par formation)
        Route::get('/forum/posts', [ForumController::class, 'index']);
        Route::post('/forum/posts', [ForumController::class, 'store']);
        Route::get('/forum/posts/{post}', [ForumController::class, 'show']);
        Route::delete('/forum/posts/{post}', [ForumController::class, 'destroy']);
        Route::post('/forum/posts/{post}/replies', [ForumController::class, 'storeReply']);
        Route::delete('/forum/replies/{reply}', [ForumController::class, 'destroyReply']);
        Route::post('/forum/posts/{post}/like', [ForumController::class, 'toggleLikePost']);
        Route::post('/forum/replies/{reply}/like', [ForumController::class, 'toggleLikeReply']);

        

        //progressions
        Route::apiResource('progressions', ProgressionController::class);

        //questions
        Route::apiResource('questions', QuestionController::class);

        //reponses — PAS de CRUD générique exposé ici volontairement.
        // L'ancien Route::apiResource('reponses', ReponseController::class)
        // permettait à N'IMPORTE QUEL utilisateur connecté (y compris un
        // étudiant) de voir les réponses de tous les autres étudiants
        // (index), de modifier le score/statut de n'importe quelle réponse
        // — y compris la sienne, pour s'auto-attribuer une note parfaite —
        // (update), ou d'en supprimer (destroy). Le vrai flux passe par
        // POST /exercices/{id}/soumettre (soumission) et
        // PUT /reponses/{id}/corriger (correction, admin/formateur
        // uniquement, plus haut dans ce fichier) — jamais utilisé par le
        // frontend, donc rien n'est perdu en le retirant.

        //resultats
        Route::apiResource('resultats', ResultatController::class);

        //examens
        Route::apiResource('examens', ExamenController::class);
        Route::post('/examens/{examen}/soumettre', [ExamenController::class, 'soumettre']);

        //certificats — lecture (liste/détail/téléchargement) ouverte à
        // tout utilisateur connecté ; création/modification/suppression
        // réservées à admin/formateur (voir plus bas, groupe role:admin,formateur)
        Route::get('/certificats/{certificat}/download', [CertificatController::class, 'download'])
       ->middleware('auth:sanctum');
        Route::apiResource('certificats', CertificatController::class)->only(['index', 'show']);

        //actus
        Route::apiResource('actus', ActusController::class);

        //opportunites
        Route::apiResource('opportunites', OpportuniteController::class);

        //paiements
        Route::apiResource('paiements', PaiementController::class);
        Route::post('/paiements/{paiement}/paydunya', [PayDunyaController::class, 'initiate']);
    });






});
