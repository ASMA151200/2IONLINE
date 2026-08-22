<?php

namespace App\Http\Controllers;

use App\Models\Favori;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /** GET /v1/notes?lecon_id=... — notes de l'utilisateur connecté sur une leçon */
    public function index(Request $request): JsonResponse
    {
        $query = Note::where('user_id', $request->user()->id);

        if ($request->filled('lecon_id')) {
            $query->where('lecon_id', $request->input('lecon_id'));
        }

        return response()->json(['success' => true, 'data' => $query->orderBy('timestamp_seconds')->get()]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lecon_id' => 'required|exists:lecons,id',
            'content' => 'required|string',
            'timestamp_seconds' => 'nullable|integer|min:0',
        ]);
        $data['user_id'] = $request->user()->id;

        $note = Note::create($data);

        return response()->json(['success' => true, 'message' => 'Note créée', 'data' => $note], 201);
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        if ($note->user_id !== $request->user()->id) {
            abort(403, 'Accès interdit');
        }

        $data = $request->validate([
            'content' => 'sometimes|string',
            'timestamp_seconds' => 'sometimes|integer|min:0',
        ]);

        $note->update($data);

        return response()->json(['success' => true, 'message' => 'Note modifiée', 'data' => $note]);
    }

    public function destroy(Request $request, Note $note): JsonResponse
    {
        if ($note->user_id !== $request->user()->id) {
            abort(403, 'Accès interdit');
        }

        $note->delete();

        return response()->json(['success' => true, 'message' => 'Note supprimée']);
    }
}
