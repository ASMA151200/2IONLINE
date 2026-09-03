<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La vraie table "inscriptions" en base n'a pas de colonne "date",
     * contrairement à ce que dit la migration originale
     * create_inscriptions_table (qui l'inclut pourtant) — désynchro entre
     * le fichier de migration actuel et la structure réellement créée à
     * l'origine sur ce serveur (même famille de problème que la table
     * "alertes" corrigée juste avant). Tout le code applicatif
     * (EtudiantService, webhook PayDunya, InscriptionService...) suppose
     * que cette colonne existe. Ajoutée ici si elle manque, sans jamais
     * recréer la table.
     */
    public function up(): void
    {
        if (Schema::hasTable('inscriptions') && !Schema::hasColumn('inscriptions', 'date')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->date('date')->nullable()->after('id');
            });

            // Comble les lignes déjà existantes (le cas échéant) avec la
            // date de création, pour ne pas laisser de valeurs nulles sur
            // une colonne censée toujours être renseignée par le code.
            \DB::table('inscriptions')->whereNull('date')->update(['date' => \DB::raw('DATE(created_at)')]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inscriptions') && Schema::hasColumn('inscriptions', 'date')) {
            Schema::table('inscriptions', function (Blueprint $table) {
                $table->dropColumn('date');
            });
        }
    }
};
