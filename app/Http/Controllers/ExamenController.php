<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Http\Requests\StoreExamenRequest;
use App\Http\Requests\UpdateExamenRequest;
use App\Services\ExamenService;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\Request;

class ExamenController extends Controller
{
    use ChecksFormationOwnership;

    public function __construct(protected ExamenService $examenService)
    {
    }

    /**
     * Liste des examens (admin = tout — utilisé par la vue d'ensemble
     * admin — formateur = ses formations, étudiant = ses inscriptions
     * actives)
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $query = Examen::with('formation')->latest();

        // CORRIGÉ: le paramètre ?formation_id= était accepté par le
        // frontend (examenService.getExamensByFormation()) mais
        // totalement ignoré ici — aucun Request n'était même injecté
        // dans cette méthode. Résultat : impossible de lister les
        // certifications d'UNE formation précise, seul un mélange de
        // toutes les formations accessibles était renvoyé.
        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->input('formation_id'));
        }

        $this->scopeToAccessibleFormations($query);

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    /**
     * Créer examen (avec questions/choix imbriqués, optionnel)
     */
    public function store(StoreExamenRequest $request)
    {
        $data = $request->validated();
        $this->authorizeFormationOwner($data['formation_id']);

        $examen = $this->examenService->create($data);

        return response()->json([
            'success' => true,
            'message' => 'Examen créé avec succès',
            'data' => $examen->load('formation')
        ],201);
    }

    /**
     * Afficher un examen
     */
    public function show(Examen $examen)
    {
        $this->authorizeFormationAccess($examen->formation_id);

        return response()->json([
            'success' => true,
            'data' => $examen->load([
                'formation',
                'questions.choix',
                'resultats'
            ])
        ]);
    }

    /**
     * Modifier
     */
    public function update(
        UpdateExamenRequest $request,
        Examen $examen
    )
    {
        $this->authorizeFormationOwner($examen->formation_id);

        $examen->update(
            $request->validated()
        );

        return response()->json([
            'success'=>true,
            'message'=>'Examen modifié avec succès',
            'data'=>$examen
        ]);
    }

    /**
     * Supprimer
     */
    public function destroy(Examen $examen)
    {
        $this->authorizeFormationOwner($examen->formation_id);

        $examen->delete();

        return response()->json([
            'success'=>true,
            'message'=>'Examen supprimé avec succès'
        ]);
    }

    /**
     * Passer un examen (etudiant) — POST /v1/examens/{examen}/soumettre
     * Body attendu: { reponses: [{ question_id, choix_id?, reponse_texte? }, ...] }
     */
    public function soumettre(Request $request, Examen $examen)
    {
        $this->authorizeFormationAccess($examen->formation_id);

        $data = $request->validate([
            'reponses' => 'required|array|min:1',
            'reponses.*.question_id' => 'required|exists:exercice_questions,id',
            'reponses.*.choix_id' => 'nullable|exists:choix,id',
            // CORRIGÉ: le texte des réponses ouvertes n'était pas
            // accepté du tout — jeté silencieusement avant même
            // d'atteindre le service, aucune correction manuelle
            // n'était donc possible ensuite.
            'reponses.*.reponse_texte' => 'nullable|string',
        ]);

        $resultat = $this->examenService->soumettre($examen, $request->user()->id, $data['reponses']);

        return response()->json([
            'success' => true,
            'message' => 'Examen soumis avec succès',
            'data' => $resultat,
        ], 201);
    }

    /**
     * Détail des réponses d'un étudiant à un examen (une par question) —
     * pour que le formateur puisse lire les réponses ouvertes avant de
     * les corriger. userId optionnel : par défaut l'utilisateur connecté
     * (un étudiant consultant ses propres réponses).
     */
    public function resultatsDetail(Request $request, Examen $examen)
    {
        $this->authorizeFormationAccess($examen->formation_id);

        $userId = $request->query('user_id', $request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $this->examenService->resultatsDetail($examen, (int) $userId),
        ]);
    }

    /**
     * Correction manuelle d'une réponse ouverte d'examen (formateur/admin).
     */
    public function corrigerReponse(\App\Http\Requests\CorrigerReponseRequest $request, \App\Models\Reponse $reponse)
    {
        $reponse->load('examen');
        $this->authorizeFormationOwner($reponse->examen->formation_id);

        $reponse = $this->examenService->corriger($reponse, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Réponse corrigée avec succès',
            'data' => $reponse,
        ]);
    }
}