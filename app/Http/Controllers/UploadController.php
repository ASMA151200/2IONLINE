<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

/**
 * Envoi DIRECT des gros fichiers (vidéos, documents de leçon).
 *
 * Pourquoi : le relais Next.js hébergé sur Vercel refuse les requêtes de plus
 * de ~4,5 Mo et coupe les envois longs (413/504). Le navigateur envoie donc
 * le fichier lui-même à l'API, sans passer par le relais.
 *
 * Sécurité : le token de session reste dans un cookie httpOnly côté Next.js
 * (illisible par le navigateur). On n'expose donc jamais ce token : le
 * relais demande ici un TICKET à usage restreint (30 min, ne permet QUE de
 * déposer un fichier temporaire). La leçon référence ensuite ce fichier
 * via video_ref / document_ref (voir LeconService::promoteTemp).
 */
class UploadController extends Controller
{
    private const TICKET_TTL_MINUTES = 30;

    private const RULES = [
        'video' => 'required|file|mimes:mp4,mov,avi,webm,mpeg,mpg|max:51200',
        'document' => 'required|file|mimes:pdf,docx,doc,pptx,ppt|max:20480',
    ];

    /** Route authentifiée (admin/formateur), appelée via le relais. */
    public function ticket(Request $request)
    {
        $ticket = Str::random(48);

        Cache::put(
            'upload_ticket:' . hash('sha256', $ticket),
            $request->user()->id,
            now()->addMinutes(self::TICKET_TTL_MINUTES)
        );

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => $ticket,
                'expires_in' => self::TICKET_TTL_MINUTES * 60,
            ],
        ]);
    }

    /** Route publique (le ticket fait office d'authentification). */
    public function store(Request $request)
    {
        $ticket = (string) $request->header('X-Upload-Ticket', '');
        $userId = $ticket !== '' ? Cache::get('upload_ticket:' . hash('sha256', $ticket)) : null;

        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Ticket d\'envoi invalide ou expiré. Réessayez.'], 401);
        }

        $user = User::find($userId);
        if (!$user || !in_array($user->role, ['admin', 'formateur'], true)) {
            return response()->json(['success' => false, 'message' => 'Action non autorisée.'], 403);
        }

        $kind = (string) $request->input('kind');
        if (!isset(self::RULES[$kind])) {
            return response()->json(['success' => false, 'message' => 'Type de fichier non pris en charge.'], 422);
        }

        $validator = Validator::make($request->all(), ['file' => self::RULES[$kind]]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first('file'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $path = $request->file('file')->store('uploads/tmp/' . $user->id, 'public');

        return response()->json([
            'success' => true,
            'data' => ['ref' => $path],
        ], 201);
    }
}
