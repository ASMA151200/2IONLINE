<?php

namespace App\Http\Controllers;

use App\Models\Resultat;
use App\Http\Requests\StoreResultatRequest;

class ResultatController extends Controller
{
    use \App\Traits\ChecksFormationOwnership;

    public function index(\Illuminate\Http\Request $request)
    {
        $query = Resultat::with(['user', 'examen']);

        // SÉCURITÉ: sans ça, n'importe quel étudiant connecté pouvait
        // voir les résultats d'examen de N'IMPORTE QUEL AUTRE
        // utilisateur en passant ?user_id=<un autre id> — une vraie
        // fuite de confidentialité. Un étudiant/partenaire est
        // désormais toujours forcé sur ses propres résultats, quel que
        // soit le user_id demandé ; admin/formateur gardent la
        // visibilité complète nécessaire à leur rôle.
        $role = $request->user()->role;
        if (in_array($role, ['etudiant', 'partenaire'])) {
            $query->where('user_id', $request->user()->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->filled('examen_id')) {
            // Un formateur ne doit voir les résultats que d'un examen
            // dont il possède réellement la formation — sans ce
            // contrôle, il pouvait consulter les résultats de
            // n'importe quel examen d'un autre formateur en devinant
            // son ID. Ne s'applique qu'aux formateurs : un étudiant qui
            // filtre SES PROPRES résultats par examen_id (déjà forcé
            // sur son user_id juste au-dessus) reste évidemment
            // autorisé sans vérification de propriété de formation.
            if ($role === 'formateur') {
                $examen = \App\Models\Examen::find($request->input('examen_id'));
                if ($examen) {
                    $this->authorizeFormationOwner($examen->formation_id);
                }
            }
            $query->where('examen_id', $request->input('examen_id'));
        }

        return response()->json([
            'data' => $query->latest()->get()
        ]);
    }

    public function store(StoreResultatRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $resultat = Resultat::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Résultat enregistré',
            'data' => $resultat
        ], 201);
    }

    public function show(Resultat $resultat)
    {
        return response()->json([
            'data' => $resultat->load(['user','examen'])
        ]);
    }
}