<?php

namespace App\Traits;

use App\Models\Formation;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Vérifie qu'un formateur ne peut agir que sur le contenu des
 * formations dans lesquelles il a été explicitement autorisé à
 * intervenir — un admin passe toujours. Utilisé par ModuleController,
 * LeconController, ExerciceController, ExamenController, DirectController,
 * QuestionController, SearchController, ResultatController.
 *
 * RÉÉCRIT pour passer d'un modèle "un formateur = une seule formation"
 * (formations.user_id) à une vraie relation plusieurs-à-plusieurs (table
 * pivot formation_formateur) : un formateur peut désormais intervenir
 * dans plusieurs formations, chacune accordée indépendamment par
 * l'admin. Les signatures de toutes les méthodes restent identiques —
 * aucun des contrôleurs qui utilisent ce trait n'a besoin d'être modifié.
 */
trait ChecksFormationOwnership
{
    /**
     * @throws HttpException (403) si le formateur connecté n'est ni admin
     * ni autorisé à intervenir dans cette formation.
     */
    protected function authorizeFormationOwner(int|string $formationId): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Non authentifié');
        }

        if ($user->role === 'admin') {
            return;
        }

        $isAutorise = Formation::where('id', $formationId)
            ->whereHas('formateurs', fn ($q) => $q->where('users.id', $user->id))
            ->exists();

        if (!$isAutorise) {
            abort(403, "Vous n'êtes pas autorisé à gérer le contenu de cette formation.");
        }
    }

    /**
     * Restreint une requête Eloquent aux formations dans lesquelles le
     * formateur connecté est autorisé à intervenir (aucun effet pour un
     * admin, qui voit tout). $formationColumn peut être une colonne
     * directe ("formation_id") ou une relation imbriquée en notation
     * pointée ("module.formation_id").
     */
    protected function scopeToOwnFormations($query, string $formationColumn = 'formation_id')
    {
        $user = Auth::user();

        if ($user && $user->role !== 'admin') {
            $autoriseesIds = Formation::whereHas('formateurs', fn ($q) => $q->where('users.id', $user->id))->pluck('id');
            $this->applyFormationIdFilter($query, $formationColumn, $autoriseesIds);
        }

        return $query;
    }

    /**
     * Autorise l'accès à une formation en LECTURE :
     * - admin : toujours
     * - formateur : uniquement s'il est autorisé à y intervenir
     * - étudiant : uniquement s'il y a une inscription active
     * (statut = 'actif')
     *
     * @throws HttpException (403)
     */
    protected function authorizeFormationAccess(int|string $formationId): void
    {
        $user = Auth::user();

        if (!$user) {
            abort(401, 'Non authentifié');
        }

        if ($user->role === 'admin') {
            return;
        }

        if ($user->role === 'formateur') {
            $this->authorizeFormationOwner($formationId);
            return;
        }

        // étudiant : doit avoir une inscription active à CETTE formation
        $isEnrolled = \App\Models\Inscription::where('user_id', $user->id)
            ->where('formation_id', $formationId)
            ->where('statut', 'actif')
            ->exists();

        if (!$isEnrolled) {
            abort(403, "Vous devez être inscrit(e) à cette formation pour accéder à son contenu.");
        }
    }

    /**
     * Restreint une requête Eloquent en LECTURE selon le rôle :
     * - admin : tout
     * - formateur : les formations où il est autorisé à intervenir
     * - étudiant : les formations où il a une inscription active
     * $formationColumn peut être une colonne directe ("formation_id") ou
     * une relation imbriquée en notation pointée ("module.formation_id").
     */
    protected function scopeToAccessibleFormations($query, string $formationColumn = 'formation_id')
    {
        $user = Auth::user();

        if (!$user || $user->role === 'admin') {
            return $query;
        }

        if ($user->role === 'formateur') {
            $autoriseesIds = Formation::whereHas('formateurs', fn ($q) => $q->where('users.id', $user->id))->pluck('id');
            return $this->applyFormationIdFilter($query, $formationColumn, $autoriseesIds);
        }

        $enrolledFormationIds = \App\Models\Inscription::where('user_id', $user->id)
            ->where('statut', 'actif')
            ->pluck('formation_id');

        return $this->applyFormationIdFilter($query, $formationColumn, $enrolledFormationIds);
    }

    /**
     * Applique un whereIn sur une colonne directe, ou un whereHas sur une
     * relation en notation pointée ("relation.colonne").
     */
    private function applyFormationIdFilter($query, string $formationColumn, $formationIds)
    {
        if (str_contains($formationColumn, '.')) {
            [$relation, $column] = explode('.', $formationColumn, 2);
            return $query->whereHas($relation, function ($q) use ($column, $formationIds) {
                $q->whereIn($column, $formationIds);
            });
        }

        return $query->whereIn($formationColumn, $formationIds);
    }
}
