<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Passage d'un modèle "un formateur = une seule formation"
     * (formations.user_id, colonne unique) à une vraie relation
     * plusieurs-à-plusieurs : un formateur doit pouvoir intervenir dans
     * plusieurs formations, et l'admin doit pouvoir lui donner ou
     * retirer l'accès à chacune indépendamment.
     *
     * formations.user_id est CONSERVÉE (pas supprimée) : elle continue
     * de représenter le "propriétaire principal / créateur" de la
     * formation à titre informatif, mais n'est PLUS la source de
     * vérité pour le contrôle d'accès — c'est désormais entièrement le
     * rôle de cette nouvelle table pivot (voir ChecksFormationOwnership
     * réécrit dans la foulée).
     */
    public function up(): void
    {
        if (!Schema::hasTable('formation_formateur')) {
            Schema::create('formation_formateur', function (Blueprint $table) {
                $table->id();
                $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['formation_id', 'user_id']);
            });
        }

        // Migration des données existantes : chaque formation ayant
        // déjà un formateur assigné (formations.user_id) obtient une
        // ligne correspondante dans la nouvelle table pivot, pour ne
        // perdre aucun accès actuellement en place.
        DB::table('formations')
            ->whereNotNull('user_id')
            ->select('id', 'user_id')
            ->orderBy('id')
            ->chunk(200, function ($formations) {
                $now = now();
                $rows = $formations->map(fn ($f) => [
                    'formation_id' => $f->id,
                    'user_id' => $f->user_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows) {
                    DB::table('formation_formateur')->insertOrIgnore($rows);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('formation_formateur');
    }
};
