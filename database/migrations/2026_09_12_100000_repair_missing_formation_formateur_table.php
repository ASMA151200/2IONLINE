<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * RÉPARATION: "php artisan migrate:status" affiche
     * 2026_09_10_120000_create_formation_formateur_pivot_table comme
     * "Ran" (donc marquée exécutée dans la table interne "migrations"),
     * mais la table formation_formateur n'existe pourtant PAS
     * physiquement sur la base de production — confirmé par l'erreur
     * "Base table or view not found: 1146 Table
     * '2ionline.formation_formateur' doesn't exist" survenue en
     * conditions réelles sur /dashboard/professor/exercises.
     *
     * Cause exacte non identifiable à distance (schema cache, restauration
     * partielle de base, ou incident lors du déploiement) — plutôt que
     * de manipuler l'historique des migrations (rollback risqué avec des
     * numéros de batch déjà mélangés), cette migration vérifie l'état
     * RÉEL de la base directement (information_schema, pas
     * Schema::hasTable() qui peut être sujette à un cache) et recrée la
     * table si elle manque vraiment, avec exactement la même structure
     * que prévu à l'origine (y compris l'index sur user_id ajouté
     * ensuite) et la même migration de données depuis formations.user_id.
     */
    public function up(): void
    {
        $existeVraiment = DB::table('information_schema.tables')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'formation_formateur')
            ->exists();

        if ($existeVraiment) {
            return;
        }

        Schema::create('formation_formateur', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')->constrained('formations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['formation_id', 'user_id']);
            $table->index('user_id');
        });

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
        // Pas de rollback destructif ici — voir la migration d'origine
        // (2026_09_10_120000) si un vrai retour arrière est nécessaire.
    }
};
