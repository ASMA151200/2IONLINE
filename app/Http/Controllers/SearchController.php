<?php

namespace App\Http\Controllers;

use App\Models\Actus;
use App\Models\Formation;
use App\Models\Lecon;
use App\Models\Opportunite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Recherche simple (LIKE) à travers formations, leçons, actus et
 * opportunités — aucune nouvelle table, aucun moteur de recherche externe.
 */
class SearchController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $results = [];

        Formation::where('titre', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->limit(10)->get()->each(function ($f) use (&$results) {
                $results[] = [
                    'id' => (string) $f->id,
                    'type' => 'formation',
                    'title' => $f->titre,
                    'description' => $f->description,
                    'url' => "/formations/{$f->id}",
                ];
            });

        Lecon::where('titre', 'like', "%{$q}%")
            ->limit(10)->get()->each(function ($l) use (&$results) {
                $results[] = [
                    'id' => (string) $l->id,
                    'type' => 'lesson',
                    'title' => $l->titre,
                    'description' => null,
                    'url' => "/lecons/{$l->id}",
                ];
            });

        if (class_exists(Actus::class)) {
            Actus::where('titre', 'like', "%{$q}%")
                ->where('statut', 'publie')
                ->limit(10)->get()->each(function ($a) use (&$results) {
                    $results[] = [
                        'id' => (string) $a->id,
                        'type' => 'post',
                        'title' => $a->titre,
                        'description' => $a->description,
                        'url' => "/actualites/{$a->id}",
                    ];
                });
        }

        if (class_exists(Opportunite::class)) {
            Opportunite::where('titre', 'like', "%{$q}%")
                ->where('statut', 'ouvert')
                ->limit(10)->get()->each(function ($o) use (&$results) {
                    $results[] = [
                        'id' => (string) $o->id,
                        'type' => 'resource',
                        'title' => $o->titre,
                        'description' => $o->description,
                        'url' => "/opportunites/{$o->id}",
                    ];
                });
        }

        return response()->json(['success' => true, 'data' => $results]);
    }
}
