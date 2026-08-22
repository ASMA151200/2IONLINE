<?php

namespace App\Http\Controllers;

use App\Models\LiveSession;
use App\Http\Requests\StoreLiveSessionRequest;
use App\Http\Requests\UpdateLiveSessionRequest;
use Illuminate\Http\Request;

class DirectController extends Controller
{
    /**
     * Liste des sessions live, filtrable par formation_id (utilisé par le
     * frontend étudiant/professeur pour n'afficher que les directs d'une
     * formation donnée).
     */
    public function index(Request $request)
    {
        $query = LiveSession::with(['formation', 'user']);

        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->input('formation_id'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->orderBy('scheduled_at')->get(),
        ]);
    }

    public function store(StoreLiveSessionRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = $data['status'] ?? 'scheduled';

        $session = LiveSession::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Session live créée avec succès',
            'data' => $session->load(['formation', 'user']),
        ], 201);
    }

    public function show(LiveSession $direct)
    {
        return response()->json([
            'success' => true,
            'data' => $direct->load(['formation', 'user']),
        ]);
    }

    public function update(UpdateLiveSessionRequest $request, LiveSession $direct)
    {
        $direct->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Session live modifiée avec succès',
            'data' => $direct->fresh()->load(['formation', 'user']),
        ]);
    }

    public function destroy(LiveSession $direct)
    {
        $direct->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session live supprimée avec succès',
        ]);
    }
}
