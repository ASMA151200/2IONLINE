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
    use \App\Traits\ChecksFormationOwnership;

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

        // CORRIGÉ (2 bugs): l'URL générée "/lecons/{id}" ne correspond à
        // AUCUNE route réelle du frontend (la vraie route est
        // "/cours/{formationId}/{lessonId}") — chaque résultat de
        // recherche sur une leçon menait donc à une page 404. De plus,
        // AUCUNE restriction d'accès n'existait : la recherche globale
        // remontait des leçons de formations auxquelles l'utilisateur
        // n'est même pas inscrit (fuite du titre de contenu payant).
        //
        // ATTENTION: cette route est PUBLIQUE (visiteur non connecté
        // possible) — scopeToAccessibleFormations() traite l'absence
        // d'utilisateur comme "aucune restriction" (pensé pour un usage
        // admin), ce qui exposerait TOUTES les leçons à un visiteur
        // anonyme si on l'utilisait telle quelle ici. Un visiteur non
        // connecté n'a donc simplement AUCUN résultat de type leçon.
        if ($request->user()) {
            $this->scopeToAccessibleFormations(
                Lecon::with('module:id,formation_id')->where('titre', 'like', "%{$q}%"),
                'module.formation_id'
            )->limit(10)->get()->each(function ($l) use (&$results) {
                    $results[] = [
                        'id' => (string) $l->id,
                        'type' => 'lesson',
                        'title' => $l->titre,
                        'description' => null,
                        'url' => "/cours/{$l->module->formation_id}/{$l->id}",
                    ];
                });
        }

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
