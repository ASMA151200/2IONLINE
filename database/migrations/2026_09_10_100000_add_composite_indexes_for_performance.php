<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Optimisation de performance : deux tables parmi les plus
     * fréquemment interrogées de toute la plateforme n'avaient que des
     * index individuels (créés automatiquement par foreignId()) mais
     * aucun index composite couvrant leurs vrais filtres réels :
     *
     * - inscriptions : filtrée en permanence par (formation_id, statut)
     *   — liste des apprenants actifs d'une formation, utilisée par le
     *   dashboard professeur, le dashboard partenaire, les présences,
     *   la recherche. Sans index composite, MySQL doit soit parcourir
     *   tous les enregistrements de la formation puis filtrer le statut
     *   à la volée, soit l'inverse.
     *
     * - progressions : interrogée par (user_id, lecon_id) à chaque
     *   affichage de leçon et à chaque "marquer comme terminé" — aucun
     *   index composite ni contrainte d'unicité n'existait, alors que
     *   la logique applicative (ProgressionService) suppose déjà qu'il
     *   ne peut exister qu'UNE progression par utilisateur et par
     *   leçon. La contrainte unique ajoutée ici sert doublement
     *   d'index de performance ET de garde-fou d'intégrité des
     *   données, au cas où deux requêtes concurrentes créeraient
     *   sinon deux lignes en double.
     *
     * Vérifie l'existence de chaque index avant de le créer (même
     * précaution que les autres migrations correctives de cette
     * session) pour rester rejouable sans erreur en cas de
     * désynchronisation entre l'historique des migrations et l'état
     * réel de la base.
     */
    public function up(): void
    {
        if (Schema::hasTable('inscriptions') && !$this->indexExists('inscriptions', 'inscriptions_formation_id_statut_index')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->index(['formation_id', 'statut']);
            });
        }

        if (Schema::hasTable('progressions') && !$this->indexExists('progressions', 'progressions_user_id_lecon_id_unique')) {
            // Sécurité: si des doublons existent déjà (créés avant que
            // cette contrainte n'existe, par exemple via une requête
            // concurrente), ajouter une contrainte UNIQUE ferait
            // échouer toute la migration. On garde uniquement la ligne
            // la plus récente par (user_id, lecon_id) avant de créer la
            // contrainte.
            $doublons = DB::table('progressions')
                ->select('user_id', 'lecon_id')
                ->groupBy('user_id', 'lecon_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            foreach ($doublons as $d) {
                $idsATrier = DB::table('progressions')
                    ->where('user_id', $d->user_id)
                    ->where('lecon_id', $d->lecon_id)
                    ->orderByDesc('updated_at')
                    ->pluck('id');

                // Garde le premier (le plus récent), supprime le reste.
                DB::table('progressions')->whereIn('id', $idsATrier->skip(1))->delete();
            }

            Schema::table('progressions', function (Blueprint $table) {
                $table->unique(['user_id', 'lecon_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inscriptions')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->dropIndex(['formation_id', 'statut']);
            });
        }

        if (Schema::hasTable('progressions')) {
            Schema::table('progressions', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'lecon_id']);
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        return collect(DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]))->isNotEmpty();
    }
};
