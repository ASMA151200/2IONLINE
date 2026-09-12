<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Probable cause des erreurs 504 (timeout) apparues sur les pages
     * professeur juste après le passage au modèle plusieurs-formations :
     * la table pivot formation_formateur n'a qu'un index composite
     * (formation_id, user_id) — formation_id en tête. Or
     * ChecksFormationOwnership (utilisé par quasiment TOUTE page
     * professeur : modules, leçons, exercices, examens, sessions live,
     * questions, recherche, résultats) cherche systématiquement par
     * user_id SEUL à chaque requête
     * (whereHas('formateurs', fn($q) => $q->where('users.id', $user->id))).
     * Sans index dont user_id est la colonne de tête, cette recherche ne
     * peut bénéficier d'aucun index existant — un balayage complet de la
     * table pivot est nécessaire à chaque appel, sur une bonne partie
     * des endpoints les plus utilisés de toute la plateforme.
     */
    public function up(): void
    {
        if (Schema::hasTable('formation_formateur') && !$this->indexExists('formation_formateur', 'formation_formateur_user_id_index')) {
            Schema::table('formation_formateur', function (Blueprint $table) {
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('formation_formateur')) {
            Schema::table('formation_formateur', function (Blueprint $table) {
                $table->dropIndex(['user_id']);
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]))->isNotEmpty();
    }
};
