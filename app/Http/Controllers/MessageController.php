<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /** Liste des conversations (dernier message par interlocuteur) */
    public function conversations(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $messages = Message::with(['sender', 'receiver'])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->latest()
            ->get();

        $conversations = $messages
            ->groupBy(fn ($m) => $m->sender_id === $userId ? $m->receiver_id : $m->sender_id)
            ->map(function ($group) use ($userId) {
                $last = $group->first();
                $interlocuteur = $last->sender_id === $userId ? $last->receiver : $last->sender;
                return [
                    'userId' => $interlocuteur->id,
                    'prenom' => $interlocuteur->prenom,
                    'nom' => $interlocuteur->nom,
                    'dernierMessage' => $last->content,
                    'dernierMessageDate' => $last->created_at,
                    'nonLus' => $group->where('receiver_id', $userId)->whereNull('read_at')->count(),
                ];
            })
            ->values();

        return response()->json(['success' => true, 'data' => $conversations]);
    }

    /** Messages échangés avec un utilisateur précis */
    public function withUser(Request $request, int $userId): JsonResponse
    {
        $myId = $request->user()->id;

        $messages = Message::where(function ($q) use ($myId, $userId) {
            $q->where('sender_id', $myId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($myId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $myId);
        })->orderBy('created_at')->get();

        // Marque comme lus les messages reçus dans cette conversation
        Message::where('sender_id', $userId)->where('receiver_id', $myId)->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    /**
     * Contacts avec qui l'utilisateur connecté peut légitimement démarrer
     * une NOUVELLE conversation (pas déjà présents dans la liste des
     * conversations existantes) — sans ça, il n'existe aucun moyen de
     * contacter quelqu'un pour la première fois.
     * - étudiant  : le(s) formateur(s) de ses formations actives + les admins
     * - formateur : les étudiants activement inscrits à ses formations + les admins
     * - admin     : tous les formateurs et étudiants
     * - partenaire: les admins uniquement
     */
    public function mesContacts(Request $request): JsonResponse
    {
        $user = $request->user();
        $contacts = collect();

        if ($user->role === 'etudiant') {
            $formationIds = \App\Models\Inscription::where('user_id', $user->id)
                ->where('statut', 'actif')
                ->pluck('formation_id');

            // CORRIGÉ: utilisait Formation::pluck('user_id') (le seul
            // "propriétaire principal") — un formateur intervenant dans
            // cette formation SANS en être le propriétaire principal
            // (table pivot formation_formateur) n'apparaissait jamais
            // comme contact possible pour cet étudiant.
            $contacts = User::where('role', 'formateur')
                ->whereHas('formationsEnseignees', fn ($q) => $q->whereIn('formations.id', $formationIds))
                ->get();
        } elseif ($user->role === 'formateur') {
            $formationIds = $user->formationsEnseignees()->pluck('formations.id');

            $contacts = User::where('role', 'etudiant')
                ->whereIn('id', \App\Models\Inscription::whereIn('formation_id', $formationIds)->where('statut', 'actif')->pluck('user_id'))
                ->get();
        } elseif ($user->role === 'admin') {
            $contacts = User::whereIn('role', ['formateur', 'etudiant'])->get();
        }

        // Les admins sont toujours des contacts valides pour tout le
        // monde (sauf pour un admin lui-même, évidemment).
        if ($user->role !== 'admin') {
            $contacts = $contacts->concat(User::where('role', 'admin')->get());
        }

        $data = $contacts->unique('id')->values()->map(fn ($u) => [
            'userId' => $u->id,
            'prenom' => $u->prenom,
            'nom' => $u->nom,
            'role' => $u->role,
        ]);

        return response()->json(['success' => true, 'data' => $data]);
    }


    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string|max:2000',
        ]);

        if ((int) $data['receiver_id'] === $request->user()->id) {
            return response()->json(['success' => false, 'message' => "Vous ne pouvez pas vous envoyer un message à vous-même."], 422);
        }

        $message = Message::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $data['receiver_id'],
            'content' => $data['content'],
        ]);

        return response()->json(['success' => true, 'data' => $message->load('sender')], 201);
    }
}
