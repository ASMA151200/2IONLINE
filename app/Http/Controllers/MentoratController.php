<?php

namespace App\Http\Controllers;

use App\Models\Mentorat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MentoratController extends Controller
{
    /**
     * Mentors potentiels : alumni ayant choisi d'être visibles dans
     * l'annuaire (alumni_visible), ou formateurs de la plateforme.
     */
    public function mentorsDisponibles(): JsonResponse
    {
        $alumni = \App\Models\Etudiant::with('user')
            ->where('alumni_visible', true)
            ->get()
            ->map(fn ($e) => [
                'userId' => $e->user->id,
                'prenom' => $e->user->prenom,
                'nom' => $e->user->nom,
                'type' => 'alumni',
                'posteActuel' => $e->poste_actuel,
            ]);

        $formateurs = \App\Models\Formateur::with('user')
            ->get()
            ->map(fn ($f) => [
                'userId' => $f->user->id,
                'prenom' => $f->user->prenom,
                'nom' => $f->user->nom,
                'type' => 'formateur',
                'specialite' => $f->specialite,
            ]);

        return response()->json(['success' => true, 'data' => $alumni->concat($formateurs)->values()]);
    }

    /** Un étudiant demande un mentorat à un alumni/formateur */
    public function demander(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mentor_id' => 'required|exists:users,id',
            'message_demande' => 'nullable|string|max:1000',
        ]);

        if ((int) $data['mentor_id'] === $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Vous ne pouvez pas être votre propre mentor.'], 422);
        }

        $mentorat = Mentorat::firstOrCreate(
            ['mentor_id' => $data['mentor_id'], 'mentore_id' => $request->user()->id],
            ['message_demande' => $data['message_demande'] ?? null, 'statut' => 'en_attente'],
        );

        return response()->json(['success' => true, 'message' => 'Demande de mentorat envoyée', 'data' => $mentorat], 201);
    }

    /** Mentorats où je suis mentoré */
    public function mesMentorats(Request $request): JsonResponse
    {
        $mentorats = Mentorat::with('mentor')->where('mentore_id', $request->user()->id)->get();
        return response()->json(['success' => true, 'data' => $mentorats]);
    }

    /** Demandes reçues en tant que mentor */
    public function demandesRecues(Request $request): JsonResponse
    {
        $mentorats = Mentorat::with('mentore')->where('mentor_id', $request->user()->id)->get();
        return response()->json(['success' => true, 'data' => $mentorats]);
    }

    /** Le mentor accepte/refuse/termine une demande */
    public function updateStatut(Request $request, Mentorat $mentorat): JsonResponse
    {
        if ($mentorat->mentor_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Non autorisé'], 403);
        }

        $data = $request->validate(['statut' => 'required|in:actif,termine,refuse']);
        $mentorat->update($data);

        return response()->json(['success' => true, 'message' => 'Statut mis à jour', 'data' => $mentorat]);
    }
}
