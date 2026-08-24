<?php

namespace App\Http\Controllers;

use App\Models\Lecon;
use App\Models\Module;
use App\Http\Requests\StoreLeconRequest;
use App\Http\Requests\UpdateLeconRequest;
use App\Services\LeconService;
use App\Traits\ChecksFormationOwnership;
use Illuminate\Http\Request;

class LeconController extends Controller
{
    use ChecksFormationOwnership;

    public function __construct(protected LeconService $leconService)
    {}

    /**
     * Liste des lecons (accès filtré comme pour les modules : formateur =
     * ses formations, étudiant = ses inscriptions actives, admin = tout)
     */
    public function index(Request $request)
    {
        $query = Lecon::query()->with('module');

        if ($request->filled('module_id')) {
            $query->where('module_id', $request->input('module_id'));
        }

        // Filtrage par formation via la relation module.formation_id
        $this->scopeToAccessibleFormations($query, 'module.formation_id');

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    /**
     * Ajouter une lecon
     */
    public function store(StoreLeconRequest $request )
    {
        $data = $request->validated();

        $module = Module::findOrFail($data['module_id']);
        $this->authorizeFormationOwner($module->formation_id);

        if ($request->hasFile('video')) {

            $data['video'] =
                $request->file('video');
        }

        if ($request->hasFile('document')) {

            $data['document'] =
                $request->file('document');
        }

        $lecon = $this->leconService->create($data);

        return response()->json([
            'success' => true,
            'message' =>'Leçon créée avec succès',
            'data' => $lecon

        ], 201);
    }

    /**
     * Afficher une lecon (contenu complet : vidéo, document, texte) — le
     * point le plus sensible : un étudiant ne doit voir ce contenu QUE
     * s'il est inscrit activement à la formation correspondante.
     */
    public function show(Lecon $lecon)
    {
        $lecon->load('module');
        $this->authorizeFormationAccess($lecon->module->formation_id);

        return response()->json([
            'success' => true,
            'data' => $lecon
        ]);
    }

    /**
     * Modifier une lecon
     */
    public function update(UpdateLeconRequest $request, Lecon $lecon)
    {
        $lecon->load('module');
        $this->authorizeFormationOwner($lecon->module->formation_id);

        $data = $request->validated();

        if ($request->hasFile('video')) {

            $data['video'] = $request->file('video');
        }

        if ($request->hasFile('document')) {

            $data['document'] = $request->file('document');
        }

        $lecon = $this->leconService->update($lecon, $data);

        return response()->json([
            'success' => true,
            'message' =>'Leçon modifiée avec succès',
            'data' => $lecon
        ]);
    }

    /**
     * Supprimer une lecon
     */
    public function destroy(Lecon $lecon)
    {
        $lecon->load('module');
        $this->authorizeFormationOwner($lecon->module->formation_id);

        $this->leconService->delete($lecon);

        return response()->json([
            'success' => true,
            'message' =>'Leçon supprimée avec succès'
        ]);
    }
}
