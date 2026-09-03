<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ATTENTION: cette contrainte existait déjà physiquement en base
        // (probablement appliquée une première fois sans que la table de
        // suivi des migrations n'enregistre l'exécution — désynchro
        // entre l'historique Laravel et l'état réel de la base), ce qui
        // faisait échouer "php artisan migrate" avec une erreur
        // "Duplicate key name" à chaque tentative. On vérifie maintenant
        // explicitement son existence avant de tenter de la recréer.
        $indexExists = collect(DB::select("SHOW INDEX FROM inscriptions WHERE Key_name = 'inscriptions_user_id_formation_id_unique'"))->isNotEmpty();

        if (!$indexExists) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->unique(['user_id', 'formation_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            //
        });
    }
};
